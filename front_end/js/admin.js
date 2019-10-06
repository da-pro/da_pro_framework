var admin = {
	constants : {
		ADMIN_LOGOUT_PATH : '/admin/logout',
		WINDOW_WIDTH : $(window).width(),
		WINDOW_HEIGHT : $(window).height(),
		FOREGROUND_TOP_POSITION : 250,
		BACKGROUND_OPACITY : 0.5
	},
	is_textarea_focused : false,
	construct : function()
	{
		this.account();
		this.setInterface();

		if (typeof set.timeout === 'number')
		{
			this.setSessionCounter();
			setInterval(this.setSessionCounter, 1000);
		}

		if (typeof set.session === 'string')
		{
			this.setConfirmMessage();
		}

		if (typeof set.search_fields === 'object')
		{
			this.setSearchFields();
		}
	},
	account : function()
	{
		$('body').on('click', 'form#account>input[name=submit_authenticate_user]', function()
		{
			admin.setAccountErrors();

			var object = {
				username : $('input[name=username]').val().trim(),
				password : $('input[name=password]').val().trim(),
				locale : set.locale
			};

			$.post({
				url : '/admin/account/authenticateUser',
				dataType : 'json',
				data : object,
				success : function(response)
				{
					if (response.hasOwnProperty('errors'))
					{
						admin.setAccountErrors(response.errors);
					}

					if (response.hasOwnProperty('location'))
					{
						window.location = response.location;
					}
				}
			});
		});

		$('body').on('click', 'form#account>input[name=submit_create_user]', function()
		{
			admin.setAccountErrors();

			var object = {
				profile : $('select[name=profile]').val().trim(),
				username : $('input[name=username]').val().trim(),
				password : $('input[name=password]').val().trim(),
				confirm_password : $('input[name=confirm_password]').val().trim(),
				locale : $('select[name=locale]').val().trim(),
				e_mail : $('input[name=e_mail]').val().trim()
			};

			$.post({
				url : '/admin/account/createUser',
				dataType : 'json',
				data : object,
				success : function(response)
				{
					if (response.hasOwnProperty('errors'))
					{
						admin.setAccountErrors(response.errors);
					}

					if (response.hasOwnProperty('location'))
					{
						window.location = response.location;
					}
				}
			});
		});

		$('body').on('click', 'form#account>input[name=submit_change_password]', function()
		{
			admin.setAccountErrors();

			var object = {
				old_password : $('input[name=old_password]').val().trim(),
				new_password : $('input[name=new_password]').val().trim(),
				confirm_new_password : $('input[name=confirm_new_password]').val().trim()
			};

			$.post({
				url : '/admin/account/changePassword',
				dataType : 'json',
				data : object,
				success : function(response)
				{
					if (response.hasOwnProperty('errors'))
					{
						admin.setAccountErrors(response.errors);
					}

					if (response.hasOwnProperty('location'))
					{
						window.location = response.location;
					}
				}
			});
		});

		$('body').on('click', 'form#account>input[name=submit_change_mail]', function()
		{
			admin.setAccountErrors();

			var object = {
				password : $('input[name=password]').val().trim(),
				new_e_mail : $('input[name=new_e_mail]').val().trim()
			};

			$.post({
				url : '/admin/account/changeMail',
				dataType : 'json',
				data : object,
				success : function(response)
				{
					if (response.hasOwnProperty('errors'))
					{
						admin.setAccountErrors(response.errors);
					}

					if (response.hasOwnProperty('location'))
					{
						window.location = response.location;
					}
				}
			});
		});

		$('body').on('click', 'form#account>input[name=submit_change_locale]', function()
		{
			admin.setAccountErrors();

			var object = {
				new_locale : $('select[name=new_locale]').val().trim()
			};

			$.post({
				url : '/admin/account/changeLocale',
				dataType : 'json',
				data : object,
				success : function(response)
				{
					if (response.hasOwnProperty('errors'))
					{
						admin.setAccountErrors(response.errors);
					}

					if (response.hasOwnProperty('location'))
					{
						window.location = response.location;
					}
				}
			});
		});

		$('input[type=text][name$=e_mail]').on('focusin', function()
		{
			$(this).animate({'width' : '+=175px'});
		});

		$('input[type=text][name$=e_mail]').on('focusout', function()
		{
			$(this).animate({'width' : '-=175px'});
		});
	},
	setAccountErrors : function()
	{
		$('form#account>p').remove();
		$('form#account').children().removeClass('error');

		if (typeof arguments[0] === 'object')
		{
			var i,
				name = [],
				keys = Object.keys(arguments[0]),
				string = '',
				values = Object.values(arguments[0]);

			for (i in keys)
			{
				if (!keys[i].includes('~'))
				{
					var key = keys[i];

					if (key.includes('|'))
					{
						key = key.split('|');
					}

					if (typeof key === 'string')
					{
						name.push(key);
					}
					else
					{
						name.push(key[0], key[1]);
					}
				}
			}

			for (i in name)
			{
				$('input[name='+name[i]+'], select[name='+name[i]+']', 'form#account').addClass('error');
			}

			for (i in values)
			{
				string += '<p>'+values[i]+'</p>';
			}

			if ($('form#account>h1').length)
			{
				$('form#account>h1').after(string);
			}
			else
			{
				$('form#account').prepend(string);
			}
		}
	},
	setInterface : function()
	{
		$('textarea').on('focusin', function()
		{
			admin.is_textarea_focused = true;
		});

		$('textarea').on('focusout', function()
		{
			admin.is_textarea_focused = false;
		});

		if ($('form#interface>input[type=button]').length)
		{
			$('body').on('click', 'form#interface>input[type=button]', function()
			{
				if ($('form#interface>input[type=button][name=search_records]').length)
				{
					$('form#interface').append($('<input>').attr({'type' : 'hidden', 'name' : 'search_records'})).submit();

					return;
				}

				admin.setInterfaceErrors();

				var i,
					object = {},
					form = document.getElementById('interface'),
					form_action = form.attributes.action.nodeValue,
					form_elements = form.elements;

				for (i in form_elements)
				{
					if (typeof form_elements[i] === 'object')
					{
						if (form_elements[i].name !== '')
						{
							object[$(form_elements[i]).attr('name')] = $(form_elements[i]).val().trim();
						}
					}
				}

				$.post({
					url : form_action,
					dataType : 'json',
					data : object,
					success : function(response)
					{
						if (response.hasOwnProperty('errors'))
						{
							admin.setInterfaceErrors(response.errors);
						}

						if (response.hasOwnProperty('location'))
						{
							window.location = response.location;
						}
					}
				});
			});
		}
	},
	setInterfaceErrors : function()
	{
		$('form#interface>table tr').children().children().removeClass('error');

		if (typeof arguments[0] === 'object')
		{
			var i,
				keys = Object.keys(arguments[0]),
				values = Object.values(arguments[0]);

			for (i in keys)
			{
				if (!keys[i].includes('~'))
				{
					$('input[name='+keys[i]+'], select[name='+keys[i]+'], textarea[name='+keys[i]+']', 'form#interface').addClass('error');
				}
			}

			this.modal.error_text = values.join('\n');

			this.modal.setAlertDialog();
		}
	},
	keydown : function()
	{
		$(document).on('keydown', function(event)
		{
			if (event.which === 13)
			{
				if ($('form#account>input[type=button]').length)
				{
					$('form#account>input[type=button]').trigger('click');

					return false;
				}

				if ($('form#interface>input[type=button]').length && !$('div#background, div#foreground').length && !admin.is_textarea_focused)
				{
					$('form#interface>input[type=button]').trigger('click');

					return false;
				}
			}
		});
	}(),
	setSessionCounter : function()
	{
		var timeout = set.timeout;

		if (timeout < 1)
		{
			window.location.href = admin.constants.ADMIN_LOGOUT_PATH;

			return;
		}

		--set.timeout;

		var hours = Math.floor(timeout / 3600);
		hours = (hours.toString().length === 1) ? '0'+hours : hours;

		var minutes = Math.floor((timeout % 3600) / 60);
		minutes = (minutes.toString().length === 1) ? '0'+minutes : minutes;

		var seconds = timeout % 60;
		seconds = (seconds.toString().length === 1) ? '0'+seconds : seconds;

		if (timeout < 600)
		{
			$('div.timeout>span', 'div#top-frame').addClass('expire');
		}

		$('div.timeout>span', 'div#top-frame').text(hours+':'+minutes+':'+seconds);
	},
	setActiveLink : function()
	{
		var path = window.location.pathname;

		$('nav>a', 'div#bottom-frame').each(function()
		{
			var href = $(this).attr('href');

			if (path.includes(href))
			{
				$(this).addClass('active');

				return false;
			}
		});
	}(),
	modal : {
		confirm_dialog : 'confirm',
		alert_dialog : 'alert',
		dialog_type : '',
		title_text : '',
		error_text : '',
		content_text : '',
		form_id : '',
		url_path : '',
		setDialog : function()
		{
			$('body').append('<div id="background"></div><div id="foreground"></div>');
			$('div#foreground').append('<h1></h1><p></p><span></span><nav></nav>');
			$('nav', 'div#foreground').append('<a class="confirm">'+set.modal.confirm+'</a><a class="close">'+set.modal.close+'</a><a class="cancel">'+set.modal.cancel+'</a>');

			$('h1', 'div#foreground').text(this.title_text);
			$('p', 'div#foreground').text(this.error_text);
			$('span', 'div#foreground').text(this.content_text);

			var X = Math.ceil($('div#foreground').outerWidth());
			var Y = Math.ceil($('div#foreground').outerHeight());

			var z = this.getDimension(X, Y);

			$('div#background').css('opacity', admin.constants.BACKGROUND_OPACITY);
			$('div#foreground').css({'top' : z[1], 'left' : z[0]});

			switch (this.dialog_type)
			{
				case this.confirm_dialog:
					$('nav a.close', 'div#foreground').css('visibility', 'hidden');
				break;

				case this.alert_dialog:
					$('nav a.confirm, nav a.cancel', 'div#foreground').css('visibility', 'hidden');
				break;
			}

			this.displayDialog();
		},
		getDimension : function(width, height)
		{
			var x = (admin.constants.WINDOW_WIDTH > width) ? (admin.constants.WINDOW_WIDTH - width) / 2 : 0;
			var y = (admin.constants.WINDOW_HEIGHT > height) ? (admin.constants.WINDOW_HEIGHT - height) / 2 : 0;
			y = (admin.constants.FOREGROUND_TOP_POSITION > y) ? y : admin.constants.FOREGROUND_TOP_POSITION;

			return [x, y];
		},
		displayDialog : function()
		{
			$('div#background, div#foreground').show();

			this.getEvent();
		},
		getEvent : function()
		{
			$('nav a.confirm, nav a.close, nav a.cancel', 'div#foreground').on('click', function()
			{
				if ($(this).attr('class') === admin.modal.confirm_dialog)
				{
					admin.modal.confirmOperation();
				}
				else
				{
					admin.modal.closeDialog();
				}
			});

			$(document).on('keydown', function(event)
			{
				switch (event.which)
				{
					case 13:
						if (admin.modal.dialog_type === admin.modal.alert_dialog)
						{
							admin.modal.closeDialog();

							return;
						}

						admin.modal.confirmOperation();
					break;

					case 27:
						admin.modal.closeDialog();
					break;
				}
			});
		},
		confirmOperation : function()
		{
			if (this.form_id.length)
			{
				var i,
					object = {},
					form = document.getElementById(this.form_id),
					form_action = form.attributes.action.nodeValue,
					form_elements = form.elements;

				for (i in form_elements)
				{
					if (typeof form_elements[i] === 'object')
					{
						object[$(form_elements[i]).attr('name')] = $(form_elements[i]).val().trim();
					}
				}

				if (object.hasOwnProperty('submit_clear_cache'))
				{
					form.submit();

					return;
				}

				$.post({
					url : form_action,
					dataType : 'json',
					data : object,
					success : function(response)
					{
						admin.modal.closeDialog();

						if (response.hasOwnProperty('errors'))
						{
							admin.modal.error_text = response.errors.join('\n');

							admin.modal.setAlertDialog();
						}

						if (response.hasOwnProperty('location'))
						{
							window.location = response.location;
						}
					}
				});
			}

			if (this.url_path.length)
			{
				$.getJSON({
					url : admin.modal.url_path,
					success : function(response)
					{
						admin.modal.closeDialog();

						if (response.hasOwnProperty('errors'))
						{
							admin.modal.error_text = response.errors.join('\n');

							admin.modal.setAlertDialog();
						}

						if (response.hasOwnProperty('location'))
						{
							window.location = response.location;
						}
					}
				});
			}
		},
		closeDialog : function()
		{
			$('div#background, div#foreground').remove();

			if ($('div#sandbox').length)
			{
				$('div#sandbox').remove();
			}

			this.dialog_type = '';
			this.title_text = '';
			this.error_text = '';
			this.content_text = '';
			this.form_id = '';
			this.url_path = '';
		},
		setConfirmDialog : function()
		{
			if (this.dialog_type.length)
			{
				return;
			}

			this.dialog_type = this.confirm_dialog;
			this.setDialog();
		},
		setAlertDialog : function()
		{
			if (this.dialog_type.length)
			{
				return;
			}

			this.dialog_type = this.alert_dialog;
			this.setDialog();
		}
	},
	deleteAccessElement : function(delete_option, element_name, id)
	{
		this.modal.url_path = '/admin/access/action-delete/option-'+delete_option+'/id-'+id;

		$.post({
			url : admin.modal.url_path,
			dataType : 'json',
			data : {message : null},
			success : function(response)
			{
				if (response.hasOwnProperty('message'))
				{
					admin.modal.title_text = response.message;
					admin.modal.content_text = element_name;

					admin.modal.setConfirmDialog();
				}
			}
		});
	},
	updateAccessElement : function(form_id)
	{
		var selector = $('input[name=commit_option]', 'form#'+form_id);

		if (selector.attr('type') === 'text')
		{
			$.post({
				url : document.getElementById(form_id).attributes.action.nodeValue,
				dataType : 'json',
				data : {message : null},
				success : function(response)
				{
					if (response.hasOwnProperty('message'))
					{
						admin.modal.title_text = response.message;
						admin.modal.content_text = selector.val();
						admin.modal.form_id = form_id;

						admin.modal.setConfirmDialog();
					}
				}
			});
		}
		else
		{
			selector.attr('type', 'text');

			var td_html = $('form#'+form_id).parent().html(),
				span_html = td_html.indexOf('<span>'),
				td_form = (span_html === -1) ? td_html : td_html.substr(0, span_html);

			$('form#'+form_id).parent().html(td_form);
		}
	},
	insertAccessElement : function(insert_option, element_name, form_id)
	{
		$.post({
			url : document.getElementById(form_id).attributes.action.nodeValue,
			dataType : 'json',
			data : {message : null},
			success : function(response)
			{
				if (response.hasOwnProperty('message'))
				{
					admin.modal.title_text = response.message;
					admin.modal.content_text = element_name;
					admin.modal.form_id = form_id;

					admin.modal.setConfirmDialog();
				}
			}
		});
	},
	updatePermission : function(element_name, form_id, uid, form_action)
	{
		var data = element_name+'\n',
			select_insert_text = ($('select[name=insert_'+uid+']').length) ? $('select[name=insert_'+uid+'] option:selected').text() : null,
			select_update_text = ($('select[name=update_'+uid+']').length) ? $('select[name=update_'+uid+'] option:selected').text() : null,
			select_delete_text = ($('select[name=delete_'+uid+']').length) ? $('select[name=delete_'+uid+'] option:selected').text() : null,
			insert_value = ($('select[name=insert_'+uid+']').length) ? $('select[name=insert_'+uid+'] option:selected').val() : null,
			update_value = ($('select[name=update_'+uid+']').length) ? $('select[name=update_'+uid+'] option:selected').val() : null,
			delete_value = ($('select[name=delete_'+uid+']').length) ? $('select[name=delete_'+uid+'] option:selected').val() : null;

		data += $('table#content tr th:eq(1)').text()+' : '+select_insert_text+'\n';
		data += $('table#content tr th:eq(2)').text()+' : '+select_update_text+'\n';
		data += $('table#content tr th:eq(3)').text()+' : '+select_delete_text;

		var i,
			form_elements = {
				table_insert : insert_value,
				table_update : update_value,
				table_delete : delete_value
			},
			sandbox = $('<div>').attr('id', 'sandbox').hide(),
			dynamic_form = $('<form>').attr({action : form_action, method : 'post', id : form_id});

		$('body').append(sandbox);
		$('div#sandbox').append(dynamic_form);

		for (i in form_elements)
		{
			var input = $('<input>').attr({type : 'text', name : i, value : form_elements[i]});

			$('form#'+form_id).append(input);
		}

		$.post({
			url : form_action,
			dataType : 'json',
			data : {message : null},
			success : function(response)
			{
				if (response.hasOwnProperty('message'))
				{
					admin.modal.title_text = response.message;
					admin.modal.content_text = data;
					admin.modal.form_id = form_id;

					admin.modal.setConfirmDialog();
				}
			}
		});
	},
	setPermissionTable : function(id)
	{
		$('table#content').each(function()
		{
			if ($(this).attr('class'))
			{
				$(this).hide();
			}
		});

		$('table#content.js-'+id).show();
	},
	clearCache : function(message, value, form_id)
	{
		this.modal.title_text = message;
		this.modal.content_text = value;
		this.modal.form_id = form_id;

		this.modal.setConfirmDialog();
	},
	deleteRecord : function(delete_option, element_name, id)
	{
		this.modal.url_path = '/admin/commit/action-delete/option-'+delete_option+'/id-'+id;

		$.post({
			url : admin.modal.url_path,
			dataType : 'json',
			data : {message : null},
			success : function(response)
			{
				if (response.hasOwnProperty('message'))
				{
					admin.modal.title_text = response.message;
					admin.modal.content_text = element_name;

					admin.modal.setConfirmDialog();
				}
			}
		});
	},
	setConfirmMessage : function()
	{
		this.modal.title_text = set.session;

		this.modal.setAlertDialog();
	},
	setSearchFields : function()
	{
		var i;

		for (i in set.search_fields)
		{
			if ($('input[name='+i+'], select[name='+i+']', 'form#interface').length)
			{
				$('input[name='+i+'], select[name='+i+']', 'form#interface').val(set.search_fields[i]);
			}
		}
	}
};
admin.construct();