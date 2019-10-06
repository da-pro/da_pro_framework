<?php
# return whether user profile type is administrator
function isAdministrator() : bool
{
	$profile = ($_SESSION['authenticate']['profile'] === 'administrator') ? true : false;

	return $profile;
}

# return files from [PATH_CONTROLLER_ADMIN_INTERFACE] without [PHP_FILE_EXTENSION]
function getInterfaceFiles() : array
{
	$files = [];

	if ($handle = opendir(realpath(PATH_CONTROLLER_ADMIN_INTERFACE)))
	{
		while ($file = readdir($handle))
		{
			if (!in_array($file, [CURRENT_DIRECTORY, PARENT_DIRECTORY]))
			{
				$files[] = basename($file, PHP_FILE_EXTENSION);
			}
		}

		closedir($handle);
	}

	return $files;
}

# return <div id="top_frame"> in admin panel
function setTopFrame() : string
{
	$path = FORWARD_SLASH . ADMIN_ROUTE . FORWARD_SLASH;

	$href_system_properties = [
		'href' => $path . 'system_properties',
		'class' => null,
		'text' => locale('link_home')
	];
	$href_interface = [
		'href' => $path . 'interface',
		'class' => null,
		'text' => locale('link_interface')
	];
	$href_logout = [
		'href' => $path . 'logout',
		'class' => 'right',
		'text' => locale('link_logout')
	];
	$href_settings = [
		'href' => $path . 'settings/account_summary',
		'class' => 'right',
		'text' => locale('link_settings')
	];

	$data = '';
	$data .= parse('a', $href_system_properties, true);
	$data .= parse('a', $href_interface, true);
	$data .= parse('a', $href_logout, true);
	$data .= parse('a', $href_settings, true);
	$data .= parse('div_class', ['class' => 'timeout', 'data' => locale('text_end_of_session') . parse('span', ['text' => '00:00:00'])], true);
	$data .= parse('div_class', ['class' => 'welcome', 'data' => locale('text_welcome') . ' ' . parse('span', ['text' => $_SESSION['authenticate']['username']])]);

	$nav = parse('nav', ['data' => $data]);

	$top_frame = parse('top_frame', ['data' => $nav], true);

	$object_literal_array['timeout'] = $_SESSION['authenticate']['timeout'] - UNIX_TIME;

	if (isset($_SESSION['authenticate']))
	{
		$object_literal_array['modal'] = [
			'confirm' => locale('button_confirm'),
			'cancel' => locale('button_cancel'),
			'close' => locale('button_close')
		];
	}

	if (isset($_SESSION['search_records']))
	{
		$object_literal_array['search_fields'] = $_SESSION['search_records'];
	}

	if (isset($_SESSION['alert_box']))
	{
		$object_literal_array['session'] = $_SESSION['alert_box'];

		unset($_SESSION['alert_box']);
	}

	$object_literal = parse('script_inline', ['data' => setObjectLiteral($object_literal_array, 'set')], true);

	$top_frame .= $object_literal;

	return $top_frame;
}

# return JS object
function setObjectLiteral(array $array, $variable_name) : string
{
	$object_literal = 'var ' . $variable_name . ' = ' . json_encode($array, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE) . ';';

	return $object_literal;
}

# return permission to access interface file
function getInterfaceFileClass($filename) : bool
{
	if (isAdministrator())
	{
		return true;
	}
	else
	{
		$href = (array_key_exists($filename, $_SESSION['authenticate']['permission'])) ? true : false;

		return $href;
	}
}

# return <table> header
function setTableHead(array $th_array, $is_options = true) : string
{
	$tdata = '';

	foreach ($th_array as $th)
	{
		if (boolval(preg_match('/\|/', $th)))
		{
			$array = explode('|', $th);

			$tdata .= parse('th', ['class' => $array[0], 'text' => $array[1]]);
		}
		else
		{
			$tdata .= parse('th', ['class' => null, 'text' => $th]);
		}
	}

	if ($is_options)
	{
		$tdata .= parse('th', ['class' => null, 'text' => locale('text_options')]);
	}

	$thead = parse('tr', ['data' => $tdata]);

	return $thead;
}

# return accounts appropriate to delete
function setOriginIteration(array $array) : array
{
	$delete_origin_id_array = [];
	$account_id = $_SESSION['authenticate']['id'];

	foreach ($array as $key => $value)
	{
		if ($account_id === intval($key) || $value['origin'] === 0)
		{
			continue;
		}

		if ($account_id === $value['origin'] || $value['profile'] === 'moderator')
		{
			$delete_origin_id_array[] = intval($key);
		}
		else
		{
			$iterate_key = $array[$value['origin']]['origin'];

			while (true)
			{
				if ($account_id === $iterate_key)
				{
					$delete_origin_id_array[] = intval($key);

					break;
				}
				else
				{
					if (isset($array[$iterate_key]['origin']))
					{
						$iterate_key = $array[$iterate_key]['origin'];
					}
					else
					{
						break;
					}
				}
			}
		}
	}

	return $delete_origin_id_array;
}

# return appropriate date and time format
function getDateFormat($unix_time) : string
{
	if (is_null($unix_time))
	{
		$time_format = '&bullet;';
	}
	else
	{
		$array = getdate($unix_time);

		$day = str_pad($array['mday'], 2, 0, STR_PAD_LEFT);
		$month = str_pad($array['mon'], 2, 0, STR_PAD_LEFT);
		$hours = str_pad($array['hours'], 2, 0, STR_PAD_LEFT);
		$minutes = str_pad($array['minutes'], 2, 0, STR_PAD_LEFT);

		$time_format = $day . FORWARD_SLASH . $month . FORWARD_SLASH . $array['year'] . ' ' . $hours . ':' . $minutes;
	}

	return $time_format;
}

# return [path] to send <form>
function setFormAction(array $array, $model_handle = true) : string
{
	$model_handle_file = ($model_handle) ? 'access' : 'commit';

	$path = FORWARD_SLASH . ADMIN_ROUTE . FORWARD_SLASH . $model_handle_file;

	foreach ($array as $key => $value)
	{
		$path .= setKeyValue($key, $value);
	}

	return $path;
}

# return JS function with arguments
function setJavaScriptFunction($function_name, array $array) : string
{
	$string = '';
	$separator = ',';

	foreach ($array as $value)
	{
		if ($value === end($array))
		{
			$separator = '';
		}

		if (is_numeric($value))
		{
			$string .= $value . $separator;
		}
		else
		{
			$string .= '\'' . $value . '\'' . $separator;
		}
	}

	$js_function = $function_name . '(' . $string . ')';

	return $js_function;
}

# return view according to user permission
function setInterfaceView() : string
{
	$view = $_GET['view'];

	if (isAdministrator())
	{
		return $view;
	}
	else
	{
		$path = ADMIN_INTERFACE_ROUTE . Controller::$file_class;

		if (isset($_GET['option']))
		{
			$array['option'] = $_GET['option'];
		}

		$array['view'] = 'records';

		foreach ($array as $key => $value)
		{
			$path .= setKeyValue($key, $value);
		}

		switch ($view)
		{
			case 'table_structure':
				setLocation($path);
			break;

			case 'insert_record':
			case 'update_record':
				if (!getPermission(substr($view, 0, 6)))
				{
					setLocation($path);
				}
			break;
		}

		return $view;
	}
}

# return links for interface file according to user permission
function setInterfaceLinks($title, $records, $option = null) : string
{
	$path = PATH_ADMIN_INTERFACE . Controller::$file_class . setKeyValue('option', $option);

	$heading = parse('span', ['text' => $title]);

	$href_records = [
		'href' => $path . setKeyValue('view', 'records'),
		'class' => null,
		'text' => locale('link_records') . ' (' . $records . ')'
	];
	$href_search_records = [
		'href' => $path . setKeyValue('view', 'search_records'),
		'class' => null,
		'text' => locale('link_search_records')
	];
	$href_table_structure = [
		'href' => $path . setKeyValue('view', 'table_structure'),
		'class' => null,
		'text' => locale('link_table_structure')
	];
	$href_insert_record = [
		'href' => $path . setKeyValue('view', 'insert_record'),
		'class' => null,
		'text' => locale('link_insert_record')
	];

	$href = '';
	$href .= parse('a', $href_records);
	$href .= parse('a', $href_search_records);
	$href .= (isAdministrator()) ? parse('a', $href_table_structure) : null;
	$href .= (getPermission('insert')) ? parse('a', $href_insert_record) : null;

	if (getPermission('update') && isset($_GET['id']))
	{
		$href_update_record = [
			'href' => $path . setKeyValue('view', 'update_record') . setKeyValue('id', $_GET['id']),
			'class' => null,
			'text' => locale('link_update_record')
		];

		$href .= parse('a', $href_update_record);
	}

	$content = parse('nav', ['data' => $heading . $href], true);

	return $content;
}

# return user permission for [filename] according to [action]
function getPermission($action, $filename = null) : bool
{
	if (isAdministrator())
	{
		return true;
	}

	$page_name = (is_null($filename)) ? Controller::$file_class : $filename;

	if (isset($_SESSION['authenticate']['permission'][$page_name][$action]))
	{
		$permission = ($_SESSION['authenticate']['permission'][$page_name][$action]) ? true : false;

		return $permission;
	}

	return false;
}

# return <table> with records and options according to user permission
function setRecordTable(array $th_array, array $td_array, array $result_array, array $options) : string
{
	$table_options = false;

	if (getPermission('update') || getPermission('delete'))
	{
		$table_options = true;

		if (empty($options))
		{
			$table_options = false;
		}
	}

	$thead = setTableHead($th_array, $table_options);
	$tbody = '';

	foreach ($result_array as $row)
	{
		$tdata = '';

		foreach ($td_array as $td)
		{
			if (boolval(preg_match('/\|/', $td)))
			{
				$td = preg_replace('/\|/', '', $td);

				$tdata .= parse('td', ['class' => 'center', 'data' => $row[$td]]);
			}
			else
			{
				$tdata .= parse('td', ['class' => null, 'data' => $row[$td]]);
			}
		}

		if ($table_options)
		{
			$new_options = [];

			if (getPermission('update'))
			{
				if (array_key_exists('option', $options))
				{
					$new_options['update']['option'] = $options['option'];
				}

				$new_options['update']['view'] = 'update_record';
				$new_options['update']['id'] = $row[$options['id']];

				$string = '';

				foreach ($new_options['update'] as $key => $value)
				{
					$string .= setKeyValue($key, $value);
				}

				$new_options['update'] = $string;
			}

			if (getPermission('delete'))
			{
				$new_options['delete'] = [
					'delete_option' => $options['commit_option'],
					'element_name' => str_replace('\'', '\\\'', $row[$options['element_name']]),
					'id' => $row[$options['id']]
				];
			}

			$tdata .= setOptions($new_options);
		}

		$tbody .= PHP_EOL . parse('tr_class', ['class' => null, 'data' => $tdata]);
	}

	$table = '';

	if (array_key_exists('pagination', $options))
	{
		$table .= PHP_EOL . trim($options['pagination']);
	}

	$table .= parse('table', ['id' => 'record', 'data' => $thead . $tbody]);

	return $table;
}

# return options for record <table>
function setOptions(array $array) : string
{
	$data = '';

	if (array_key_exists('update', $array))
	{
		$path = PATH_ADMIN_INTERFACE . Controller::$file_class . $array['update'];

		$href_class = ($_GET['view'] === 'search_records') ? '" target="_blank' : null;

		$href_update = [
			'href' => $path,
			'class' => $href_class,
			'text' => locale('table_update')
		];

		$data .= parse('a', $href_update);
	}

	if (array_key_exists('delete', $array))
	{
		$onclick_function = setJavaScriptFunction('admin.deleteRecord', array_values($array['delete']));

		$data .= parse('a_onclick', ['onclick' => $onclick_function, 'text' => locale('table_delete')]);
	}

	$tdata = parse('td', ['class' => 'right', 'data' => $data]);

	return $tdata;
}

# return <form> for search
function setSearchForm(array $array) : string
{
	$tbody = '';
	$elements = '';

	foreach ($array as $key => $value)
	{
		if (array_key_exists('maxlength', $value))
		{
			$input_array = [
				'name' => $key,
				'value' => null,
				'maxlength' => $value['maxlength']
			];
			$input = parse('input_text', $input_array);

			$tbody .= parse('tr_data', ['th' => setSearchLabel($key), 'td' => $input], true);
		}

		if (array_key_exists('select', $value))
		{
			$options = '';
			$options .= parse('option_selected', ['value' => '', 'text' => $value['select'][0]]);

			unset($value['select'][0]);

			foreach ($value['select'] as $_key => $_value)
			{
				$options .= PHP_EOL . parse('option', ['value' => $_key, 'text' => $_value]);
			}

			$select = parse('select', ['name' => $key, 'options' => $options]);

			$tbody .= parse('tr_data', ['th' => setSearchLabel($key), 'td' => $select], true);
		}
	}

	$table = parse('table', ['id' => null, 'data' => $tbody]);

	$elements .= $table;

	$submit = PHP_EOL . parse('input_button', ['name' => 'search_records', 'value' => locale('form_search')]);

	$elements .= $submit;

	$form_array = [
		'action' => null,
		'method' => 'post',
		'id' => 'interface',
		'elements' => $elements
	];
	$form = parse('form', $form_array) . PHP_EOL;

	return $form;
}

# return label for search <form>
function setSearchLabel($string) : string
{
	$array = explode('_', $string);

	foreach ($array as $key => &$value)
	{
		if ($value === 'id')
		{
			unset($array[$key]);
		}

		$value = ucfirst($value);
	}

	$label = implode(' ', $array);

	return $label;
}

# return query and bind value for model
function setSearchArguments()
{
	if (isset($_POST['search_records']))
	{
		unset($_POST['search_records']);

		foreach ($_POST as $key => $value)
		{
			$valid_input_value = preg_match('#^[' . SEARCH_PATTERN . ']+$#', $value);

			if (boolval($valid_input_value))
			{
				$_SESSION['search_records'][$key] = $value;
			}
		}

		setLocation(Controller::$page_url);
	}

	$query_where = 'WHERE' . PHP_EOL;

	if (isset($_SESSION['search_records']))
	{
		foreach ($_SESSION['search_records'] as $key => $value)
		{
			$placeholder = ':' . $key;

			$operator = (is_numeric($value)) ? ' = ' : ' LIKE ';
			$bind_value = (is_numeric($value)) ? intval($value) : '%' . $value . '%';

			$array['query'][] = '`' . $key . '`' . $operator . $placeholder;
			$array['bind_value'][$placeholder] = $bind_value;
		}

		unset($_SESSION['search_records']);

		return [
			'query' => $query_where . implode(' AND ', $array['query']),
			'bind_value' => $array['bind_value']
		];
	}

	return [
		'query' => $query_where . 'FALSE',
		'bind_value' => []
	];
}

# return <table> for MYSQL table structure
function setTableStructure(array $array) : string
{
	$th_array = ['Column', 'Type', 'Key', 'Null', 'Comment'];
	$td_array = ['column', 'type', '|key|', '|null|', '|comment|'];

	$thead = setTableHead($th_array, false);
	$tbody = '';

	foreach ($array as $row)
	{
		$tdata = '';

		foreach ($td_array as $td)
		{
			if (boolval(preg_match('/\|/', $td)))
			{
				$td = preg_replace('/\|/', '', $td);

				$tdata .= parse('td', ['class' => 'center', 'data' => $row[$td]]);
			}
			else
			{
				$tdata .= parse('td', ['class' => null, 'data' => $row[$td]]);
			}
		}

		$tbody .= PHP_EOL . parse('tr', ['data' => $tdata]);
	}

	$table = parse('table', ['id' => 'table-structure', 'data' => $thead . $tbody]);

	return $table;
}

# return integer id from url
function getInterfaceUpdateID() : ?int
{
	$id = (isset($_GET['id'])) ? intval($_GET['id']) : null;

	return $id;
}

# return [array] for <form> <select> without selected value
function setRemoveCurrentValue(array $array, $key, $string) : array
{
	if (is_null($string))
	{
		return $array;
	}
	else
	{
		$new_array = [];

		foreach ($array as $value)
		{
			if ($value[$key] !== $string)
			{
				$new_array[] = $value;
			}
		}

		return $new_array;
	}
}

# return <form> for interface files
function setInterfaceForm($is_update, $elements) : string
{
	$url['action'] = ($is_update) ? 'update' : 'insert';

	if (isset($_GET['option']))
	{
		$url['option'] = $_GET['option'];
	}

	if ($is_update)
	{
		$url['id'] = getInterfaceUpdateID();
	}

	$submit = PHP_EOL . parse('input_button', ['name' => null, 'value' => ($is_update) ? locale('form_update') : locale('form_insert')]);

	$elements .= $submit;

	$form_array = [
		'action' => setFormAction($url, false),
		'method' => null,
		'id' => 'interface',
		'elements' => $elements
	];
	$form = parse('form', $form_array);

	return $form;
}

# return error for date format <input> value
function setDatePosted()
{
	$error = false;
	$date_posted = $_POST['date_posted'];

	if (strlen($date_posted) === 10)
	{
		$allowed_years = range('2011', date('Y'));

		if (boolval(preg_match('/[0123]{1}[0-9]{1}-(01|02|03|04|05|06|07|08|09|10|11|12)-(' . implode('|', $allowed_years) . ')/', $date_posted)))
		{
			$day = substr($date_posted, 0, 2);
			$month = substr($date_posted, 3, 2);
			$year = substr($date_posted, 6, 4);
			$_31 = false;
			$_30 = false;
			$_28 = false;

			switch ($month)
			{
				case '01':
				case '03':
				case '05':
				case '07':
				case '08':
				case '10':
				case '12':
					$_31 = true;
				break;

				case '04':
				case '06':
				case '09':
				case '11':
					$_30 = true;
				break;

				case '02':
					$_28 = true;
				break;
			}

			$is_leap_year = (in_array($year, [2012, 2016, 2020])) ? true : false;

			if (intval($day) !== 0)
			{
				if ($is_leap_year)
				{
					if ($_28 && (intval($day) > 29))
					{
						$error = 'month is more than 29 days';
					}
				}
				else
				{
					if ($_28 && (intval($day) > 28))
					{
						$error = 'month is more than 28 days';
					}
				}

				if ($_30 && (intval($day) > 30))
				{
					$error = 'month is more than 30 days';
				}

				if ($_31 && (intval($day) > 31))
				{
					$error = 'month is more than 31 days';
				}
			}
			else
			{
				$error = 'not a valid day';
			}
		}
		else
		{
			$error = 'not a valid date';
		}
	}
	else
	{
		$error = 'date must be 10 symbols';
	}

	return $error;
}

# return values from errors in setDebugBacktrace function
function setDebug(array $array) : string
{
	$th_array = [
		'_30_|Syntax Error',
		'_20_|Key',
		'_10_|Line',
		'File'
	];
	$td_array = ['type', 'key', '|line|', 'file'];

	$thead = setTableHead($th_array, false);
	$tbody = '';

	foreach ($array as $row)
	{
		$tdata = '';

		foreach ($td_array as $td)
		{
			if (boolval(preg_match('/\|/', $td)))
			{
				$td = preg_replace('/\|/', '', $td);

				$tdata .= parse('td', ['class' => 'center', 'data' => $row[$td]]);
			}
			else
			{
				$tdata .= parse('td', ['class' => null, 'data' => $row[$td]]);
			}
		}

		$tbody .= PHP_EOL . parse('tr', ['data' => $tdata]);
	}

	$table = parse('table', ['id' => 'debug', 'data' => $thead . $tbody], true);

	return $table;
}