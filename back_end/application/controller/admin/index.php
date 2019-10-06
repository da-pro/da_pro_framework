<?php
final class Index extends Controller
{
	public function __construct(){}

	public function index($option)
	{
		if (in_array($option, ['review_files', 'permissions_of_users', 'clear_cache', 'create_user']))
		{
			if (!isAdministrator())
			{
				setLocation(ADMIN_ROUTE . '/system_properties');
			}
		}

		if (in_array($option, ['system_properties', 'all_users', 'review_files', 'permissions_of_users']))
		{
			$this->model('Index_Model', 'index');

			$this->index->setTableColumn(SUBDOMAIN_FILE_DATABASE_TABLE_NAME);
		}

		if (in_array($option, ['create_user']))
		{
			$this->model('Account_Model', 'account');

			$this->account->setTableColumn(ACCOUNT_DATABASE_TABLE_NAME);
		}

		if (parent::$page_url !== ADMIN_ROUTE . '/permissions_of_users')
		{
			if (isset($_SESSION['account_permission']))
			{
				unset($_SESSION['account_permission']);
			}
		}

		if (parent::$page_url !== ADMIN_ROUTE . '/clear_cache')
		{
			if (isset($_SESSION['clear_cache']))
			{
				unset($_SESSION['clear_cache']);
			}
		}

		$content = '';

		switch ($option)
		{
			case 'system_properties':
				$server_software = $_SERVER['SERVER_SOFTWARE'];

				$server_software_array = explode(' ', $server_software);

				$web_server = reset($server_software_array);

				$server_side_scripting_language = end($server_software_array);

				$data = '';
				$data .= parse('tr_data', ['th' => locale('text_web_server'), 'td' => $web_server], true);
				$data .= parse('tr_data', ['th' => locale('text_server_side_scripting_language'), 'td' => $server_side_scripting_language], true);
				$data .= parse('tr_data', ['th' => locale('text_database_management_system'), 'td' => $this->index->getDBMS()], true);
				$data .= parse('tr_data', ['th' => locale('text_display_errors'), 'td' => boolval(COMPILER_DISPLAY_ERRORS) ? locale('text_yes') : locale('text_no')]);

				$content .= parse('table', ['id' => 'summary', 'data' => $data]);
			break;

			case 'all_users':
				$th_array = [
					'_20_|' . locale('text_name'),
					'_20_|' . locale('text_profile'),
					'_25_|' . locale('text_mail'),
					'_25_|' . locale('text_last_login')
				];
				$td_array = ['username', 'profile', 'e_mail', 'last_login'];
				$is_options = isAdministrator();

				$thead = setTableHead($th_array, $is_options);
				$tbody = '';

				$array = $this->index->getAccounts();

				foreach ($array as $value)
				{
					$origin_id_array[$value['account_id']] = [
						'origin' => intval($value['origin_id']),
						'profile' => $value['profile']
					];
				}

				$delete_origin_id_array = setOriginIteration($origin_id_array);

				foreach ($array as $value)
				{
					$tdata = '';

					foreach ($td_array as $td)
					{
						if ($td === 'last_login')
						{
							$value[$td] = getDateFormat($value[$td]);
						}

						$tdata .= parse('td', ['class' => null, 'data' => $value[$td]]);
					}

					if ($is_options)
					{
						$delete = [
							'delete_option' => ACCOUNT_DATABASE_TABLE_NAME,
							'element_name' => $value['username'],
							'id' => $value['account_id']
						];

						if (!in_array(intval($value['account_id']), $delete_origin_id_array))
						{
							unset($delete);
						}

						$data = parse('td', ['class' => null, 'data' => '&bullet;']);

						if (isset($delete))
						{
							$onclick_function = setJavaScriptFunction('admin.deleteAccessElement', array_values($delete));

							$link = parse('a_onclick', ['onclick' => $onclick_function, 'text' => locale('table_delete')]);

							$data = parse('td', ['class' => 'right', 'data' => $link]);
						}

						$tdata .= $data;
					}

					if ($_SESSION['authenticate']['username'] === $value['username'])
					{
						$tbody .= PHP_EOL . parse('tr_class', ['class' => 'active', 'data' => $tdata]);
					}
					else
					{
						$tbody .= PHP_EOL . parse('tr', ['data' => $tdata]);
					}
				}

				$content .= parse('table', ['id' => 'content', 'data' => $thead . $tbody]);
			break;

			case 'review_files':
				$th_array = [
					'_40_|' . locale('text_file'),
					'_50_|' . locale('text_commit_option')
				];

				$thead = setTableHead($th_array);
				$tbody = '';

				$array = $this->index->getInterfaceFiles();

				$compatible_array = [];
				$interface_files = getInterfaceFiles();

				if (!empty($array))
				{
					foreach ($array as $value)
					{
						$compatible_array[] = $value['name'];
					}

					$missing_interface_file_array = array_diff($compatible_array, $interface_files);
					$missing_file_array = [];
					$existing_file_array = [];

					foreach ($array as $value)
					{
						if (in_array($value['name'], $missing_interface_file_array))
						{
							$missing_file_array[] = $value;
						}
						else
						{
							$existing_file_array[] = $value;
						}
					}

					$td_array = ['name', 'commit_option'];
					$counter = 0;

					foreach ($existing_file_array as $value)
					{
						++$counter;
						$tdata = '';

						foreach ($td_array as $td)
						{
							if ($td === 'commit_option')
							{
								$input_name = parse('input_hidden', ['name' => 'name', 'value' => $value['name']], true);

								$input_commit_option = parse('input_hidden', ['name' => 'commit_option', 'value' => $value['commit_option'] . '" maxlength="' . $this->index->table_column[SUBDOMAIN_FILE_DATABASE_TABLE_NAME]['commit_option']]);

								$elements = $input_name . $input_commit_option;

								$action = [
									'action' => 'update',
									'option' => SUBDOMAIN_FILE_DATABASE_TABLE_NAME,
									'id' => $value['subdomain_file_id']
								];

								$form_array = [
									'action' => setFormAction($action),
									'method' => null,
									'id' => 'js-subdomain-file-update-' . $counter,
									'elements' => $elements
								];
								$form = parse('form', $form_array);

								$commit_option = parse('span', ['text' => '[']) . $value[$td] . parse('span', ['text' => ']']);

								$tdata .= parse('td', ['class' => null, 'data' => $form . $commit_option]);
							}
							else
							{
								$tdata .= parse('td', ['class' => null, 'data' => $value[$td]]);
							}
						}

						$update['form_id'] = $form_array['id'];

						$onclick_function = setJavaScriptFunction('admin.updateAccessElement', array_values($update));

						$link = parse('a_onclick', ['onclick' => $onclick_function, 'text' => locale('table_update')]);

						$tdata .= parse('td', ['class' => 'right', 'data' => $link]);

						$tbody .= PHP_EOL . parse('tr', ['data' => $tdata]);
					}

					foreach ($missing_file_array as $value)
					{
						$tdata = '';

						foreach ($td_array as $td)
						{
							if ($td === 'commit_option')
							{
								$commit_option = parse('span', ['text' => '[']) . $value[$td] . parse('span', ['text' => ']']);

								$tdata .= parse('td', ['class' => null, 'data' => $commit_option]);
							}
							else
							{
								$tdata .= parse('td', ['class' => null, 'data' => $value[$td] . parse('span', ['text' => '[' . locale('text_file_missing') . ']'])]);
							}
						}

						$delete = [
							'delete_option' => SUBDOMAIN_FILE_DATABASE_TABLE_NAME,
							'element_name' => $value['name'],
							'id' => $value['subdomain_file_id']
						];

						$onclick_function = setJavaScriptFunction('admin.deleteAccessElement', array_values($delete));

						$link = parse('a_onclick', ['onclick' => $onclick_function, 'text' => locale('table_delete')]);

						$tdata .= parse('td', ['class' => 'right', 'data' => $link]);

						$tbody .= PHP_EOL . parse('tr', ['data' => $tdata]);
					}
				}

				$surplus_file_array = array_diff($interface_files, $compatible_array);
				$counter = 0;

				foreach ($surplus_file_array as $value)
				{
					++$counter;
					$tdata = '';
					$tdata .= parse('td', ['class' => null, 'data' => $value]);

					$input_name = parse('input_hidden', ['name' => 'name', 'value' => $value], true);

					$input_commit_option_array = [
						'name' => 'commit_option',
						'value' => null,
						'maxlength' => $this->index->table_column[SUBDOMAIN_FILE_DATABASE_TABLE_NAME]['commit_option']
					];
					$input_commit_option = parse('input_text', $input_commit_option_array);

					$elements = $input_name . $input_commit_option;

					$action = [
						'action' => 'insert',
						'option' => SUBDOMAIN_FILE_DATABASE_TABLE_NAME
					];

					$form_array = [
						'action' => setFormAction($action),
						'method' => null,
						'id' => 'js-subdomain-file-insert-' . $counter,
						'elements' => $elements
					];
					$form = parse('form', $form_array);

					$tdata .= parse('td', ['class' => null, 'data' => $form]);

					$insert = [
						'insert_option' => $action['option'],
						'element_name' => $value,
						'form_id' => $form_array['id']
					];

					$onclick_function = setJavaScriptFunction('admin.insertAccessElement', array_values($insert));

					$link = parse('a_onclick', ['onclick' => $onclick_function, 'text' => locale('table_insert')]);

					$tdata .= parse('td', ['class' => 'right', 'data' => $link]);

					$tbody .= PHP_EOL . parse('tr', ['data' => $tdata]);
				}

				$content .= parse('table', ['id' => 'content', 'data' => $thead . $tbody]);
			break;

			case 'permissions_of_users':
				$array = $this->index->getInterfaceFiles();

				$interface_files = [];

				foreach ($array as $value)
				{
					$interface_files[$value['subdomain_file_id']] = $value['name'];
				}

				$array = $this->index->getAccountPermissions();

				$account_permissions = [];

				foreach ($array as $value)
				{
					$account_permissions[$value['account_id']][$value['name']] = [
						'id' => $value['id'],
						'insert' => $value['table_insert'],
						'update' => $value['table_update'],
						'delete' => $value['table_delete']
					];
				}

				$moderator_array = $this->index->getModerators();

				$permission_array = [];

				foreach ($moderator_array as $value)
				{
					$moderator_name_array[$value['account_id']] = $value['username'];

					foreach ($interface_files as $subdomain_file_id => $file)
					{
						if (isset($account_permissions[$value['account_id']][$file]))
						{
							$account_permissions[$value['account_id']][$file]['subdomain_file_id'] = $subdomain_file_id;

							$permission_array[$value['account_id']][$file] = $account_permissions[$value['account_id']][$file];
						}
						else
						{
							$permission_array[$value['account_id']][$file] = [
								'insert' => null,
								'update' => null,
								'delete' => null,
								'subdomain_file_id' => $subdomain_file_id
							];
						}
					}
				}

				foreach ($permission_array as $account_id => $array)
				{
					$th_array = [
						'_40_|' . locale('text_file') . ' - ' . $moderator_name_array[$account_id],
						'_15_|' . locale('text_insert'),
						'_15_|' . locale('text_update'),
						'_15_|' . locale('text_delete')
					];

					$thead = setTableHead($th_array);
					$tbody = '';

					foreach ($array as $file => $value)
					{
						$tdata = '';
						$tdata .= parse('td', ['class' => null, 'data' => $file], true);

						$uid = $account_id . $value['subdomain_file_id'];

						if (is_null($value['insert']) && is_null($value['update']) && is_null($value['delete']))
						{
							$tdata .= str_repeat(parse('td', ['class' => null, 'data' => '&bullet;']), 3);

							$input_account_id = parse('input_hidden', ['name' => 'account_id', 'value' => $account_id], true);

							$input_subdomain_file_id = parse('input_hidden', ['name' => 'subdomain_file_id', 'value' => $value['subdomain_file_id']]);

							$elements = $input_account_id . $input_subdomain_file_id;

							$action = [
								'action' => 'insert',
								'option' => ACCOUNT_PERMISSION_DATABASE_TABLE_NAME
							];

							$form_array = [
								'action' => setFormAction($action),
								'method' => null,
								'id' => 'js-account-permission-insert-' . $uid,
								'elements' => $elements
							];
							$form = parse('form', $form_array);

							$insert = [
								'insert_option' => $action['option'],
								'element_name' => $file,
								'form_id' => $form_array['id']
							];

							$onclick_function = setJavaScriptFunction('admin.insertAccessElement', array_values($insert));

							$link = parse('a_onclick', ['onclick' => $onclick_function, 'text' => locale('table_insert')]);

							$tdata .= parse('td', ['class' => 'right', 'data' => $form . $link]);
						}
						else
						{
							$form_elements = [
								'insert_' => intval($value['insert']),
								'update_' => intval($value['update']),
								'delete_' => intval($value['delete'])
							];

							foreach ($form_elements as $name => $_value)
							{
								$option_text = ($_value === 1) ? locale('text_yes') : locale('text_no');

								$inverse_value = ($_value === 1) ? 0 : 1;
								$inverse_option_text = ($_value === 1) ? locale('text_no') : locale('text_yes');

								$options = '';
								$options .= parse('option_selected', ['value' => $_value, 'text' => $option_text]);
								$options .= PHP_EOL . parse('option', ['value' => $inverse_value, 'text' => $inverse_option_text]);

								$select = parse('select', ['name' => $name . $uid, 'options' => $options]);

								$tdata .= parse('td', ['class' => null, 'data' => $select], true);
							}

							$form_action = [
								'action' => 'update',
								'option' => ACCOUNT_PERMISSION_DATABASE_TABLE_NAME,
								'id' => $value['id']
							];

							$update = [
								'element_name' => $file,
								'form_id' => 'js-account-permission-update-' . $uid,
								'uid' => $uid,
								'form_action' => setFormAction($form_action)
							];

							$onclick_function = setJavaScriptFunction('admin.updatePermission', array_values($update));

							$link_update = parse('a_onclick', ['onclick' => $onclick_function, 'text' => locale('table_update')]);

							$delete = [
								'delete_option' => $form_action['option'],
								'element_name' => $file,
								'id' => $value['id']
							];

							$onclick_function = setJavaScriptFunction('admin.deleteAccessElement', array_values($delete));

							$link_delete = parse('a_onclick', ['onclick' => $onclick_function, 'text' => locale('table_delete')]);

							$tdata .= parse('td', ['class' => 'right', 'data' => $link_update . $link_delete]);
						}

						$tbody .= PHP_EOL . parse('tr', ['data' => $tdata]);
					}

					$class = 'hide js-' . $account_id;

					if (isset($_SESSION['account_permission']))
					{
						if ($account_id === intval($_SESSION['account_permission']))
						{
							$class = 'js-' . $account_id;
						}
					}

					$content .= parse('table', ['id' => 'content" class="' . $class, 'data' => $thead . $tbody], true);
				}

				$th_array = ['_80_|' . locale('text_moderator_name')];

				$thead = setTableHead($th_array);
				$tbody = '';

				foreach ($moderator_array as $value)
				{
					$tdata = '';
					$tdata .= parse('td', ['class' => null, 'data' => $value['username']]);

					$onclick_function = setJavaScriptFunction('admin.setPermissionTable', [$value['account_id']]);

					$link = parse('a_onclick', ['onclick' => $onclick_function, 'text' => locale('table_review')]);

					$tdata .= parse('td', ['class' => 'right', 'data' => $link]);

					$tbody .= PHP_EOL . parse('tr', ['data' => $tdata]);
				}

				$content .= parse('table', ['id' => 'content', 'data' => $thead . $tbody]);
			break;

			case 'clear_cache':
				$th_array = ['_80_|' . locale('text_last_update')];

				$thead = setTableHead($th_array);
				$tbody = '';

				$xml = Storage::XML('revision');

				$date_posted = intval($xml['object']->date_posted);

				$tdata = '';
				$tdata .= parse('td', ['class' => null, 'data' => getDateFormat($date_posted)]);

				$input_clear_cache = parse('input_hidden', ['name' => 'submit_clear_cache', 'value' => null]);

				$form_array = [
					'action' => FORWARD_SLASH . ADMIN_ROUTE . '/clear_cache',
					'method' => 'post',
					'id' => 'js-clear-cache',
					'elements' => $input_clear_cache
				];
				$form = parse('form', $form_array);

				$hash = md5(UNIX_TIME);

				if (!isset($_SESSION['clear_cache']))
				{
					$_SESSION['clear_cache'] = [
						'unix_time' => UNIX_TIME,
						'hash' => substr($hash, 0, 4)
					];
				}

				$update = [
					'message' => locale('text_update_revision'),
					'value' => $_SESSION['clear_cache']['hash'],
					'form_id' => $form_array['id']
				];

				$onclick_function = setJavaScriptFunction('admin.clearCache', array_values($update));

				$link = parse('a_onclick', ['onclick' => $onclick_function, 'text' => locale('table_update')]);

				$tdata .= parse('td', ['class' => 'right', 'data' => $form . $link]);

				$tbody .= PHP_EOL . parse('tr', ['data' => $tdata]);

				if (isset($_POST['submit_clear_cache']))
				{
					$xml['object']->date_posted = $_SESSION['clear_cache']['unix_time'];
					$xml['object']->asXML($xml['file_path']);

					$revision_filename = realpath(PATH_STORAGE . 'revision' . PHP_FILE_EXTENSION);

					if ($handle = fopen($revision_filename, 'r+'))
					{
						ftruncate($handle, 12);

						fseek($handle, 12);

						fwrite($handle, ' \'' . $_SESSION['clear_cache']['hash'] . '\';');

						fclose($handle);

						$_SESSION['alert_box'] = locale('successful_query_operation');

						setLocation(ADMIN_ROUTE . '/clear_cache');
					}
				}

				$content .= parse('table', ['id' => 'content', 'data' => $thead . $tbody]);
			break;

			case 'create_user':
				$elements = '';

				if (isset($_SESSION['create_user']))
				{
					$elements .= parse('p', ['text' => $_SESSION['create_user']], true);

					unset($_SESSION['create_user']);
				}

				$label_profile = parse('label', ['text' => locale('label_profile')]);

				$options = '';
				$options .= parse('option_selected', ['value' => '', 'text' => locale('select')]);
				$options .= PHP_EOL . parse('option', ['value' => 'administrator', 'text' => locale('text_administrator')]);
				$options .= PHP_EOL . parse('option', ['value' => 'moderator', 'text' => locale('text_moderator')]);

				$select_profile = parse('select', ['name' => 'profile', 'options' => $options], true);

				$elements .= $label_profile . $select_profile;

				$label_username = parse('label', ['text' => locale('label_username')]);

				$input_username_array = [
					'name' => 'username',
					'value' => null,
					'maxlength' => $this->account->table_column[ACCOUNT_DATABASE_TABLE_NAME]['username']
				];
				$input_username = parse('input_text', $input_username_array, true);

				$elements .= $label_username . $input_username;

				$label_password = parse('label', ['text' => locale('label_password')]);

				$input_password_array = [
					'name' => 'password',
					'maxlength' => Account_Model::LENGTH['MAXIMUM_PASSWORD']
				];
				$input_password = parse('input_password', $input_password_array, true);

				$elements .= $label_password . $input_password;

				$label_confirm_password = parse('label', ['text' => locale('label_confirm_password')]);

				$input_confirm_password_array = [
					'name' => 'confirm_password',
					'maxlength' => Account_Model::LENGTH['MAXIMUM_PASSWORD']
				];
				$input_confirm_password = parse('input_password', $input_confirm_password_array, true);

				$elements .= $label_confirm_password . $input_confirm_password;

				$label_locale = parse('label', ['text' => locale('label_locale')]);

				$options = '';
				$options .= parse('option_selected', ['value' => '', 'text' => locale('select')]);
				$options .= PHP_EOL . parse('option', ['value' => 'en', 'text' => 'english']);
				$options .= PHP_EOL . parse('option', ['value' => 'bg', 'text' => 'български']);

				$select_locale = parse('select', ['name' => 'locale', 'options' => $options], true);

				$elements .= $label_locale . $select_locale;

				$label_mail = parse('label', ['text' => locale('label_mail')]);

				$input_mail_array = [
					'name' => 'e_mail',
					'value' => null,
					'maxlength' => $this->account->table_column[ACCOUNT_DATABASE_TABLE_NAME]['e_mail']
				];
				$input_mail = parse('input_text', $input_mail_array, true);

				$elements .= $label_mail . $input_mail;

				$submit = parse('input_button', ['name' => 'submit_create_user', 'value' => locale('submit_create')]);

				$elements .= $submit;

				$form_array = [
					'action' => null,
					'method' => null,
					'id' => 'account',
					'elements' => $elements
				];
				$form = parse('form', $form_array);

				$content .= $form;
			break;
		}

		$path = FORWARD_SLASH . ADMIN_ROUTE . FORWARD_SLASH;

		$href_system_properties = [
			'href' => $path . 'system_properties',
			'class' => null,
			'text' => locale('link_system_properties')
		];
		$href_all_users = [
			'href' => $path . 'all_users',
			'class' => null,
			'text' => locale('link_all_users')
		];

		$href = '';
		$href .= parse('a', $href_system_properties, true);
		$href .= parse('a', $href_all_users, true);

		if (isAdministrator())
		{
			$href_review_files = [
				'href' => $path . 'review_files',
				'class' => null,
				'text' => locale('link_review_files')
			];
			$href_permissions_of_users = [
				'href' => $path . 'permissions_of_users',
				'class' => null,
				'text' => locale('link_permissions_of_users')
			];
			$href_clear_cache = [
				'href' => $path . 'clear_cache',
				'class' => null,
				'text' => locale('link_clear_cache')
			];
			$href_create_user = [
				'href' => $path . 'create_user',
				'class' => null,
				'text' => locale('link_create_user')
			];

			$href .= parse('a', $href_review_files, true);
			$href .= parse('a', $href_permissions_of_users, true);
			$href .= parse('a', $href_clear_cache, true);
			$href .= parse('a', $href_create_user);
		}

		$nav = parse('nav', ['data' => $href], true);
		$section = parse('section', ['data' => $content]);

		$bottom_frame = parse('bottom_frame', ['data' => $nav . $section]);

		parent::$admin_data .= $bottom_frame;

		Output::html(locale('document_title_home'));
	}
}