<?php
abstract class Model
{
	const ENCODING = 'SET NAMES utf8';
	const PAGINATION = [
		'DEFAULT_OUTPUT' => 20,
		'FETCH_MAXIMUM_PAGES' => 100000,
		'MAXIMUM_LINKS' => 5,
		'FIRST_LINK_SYMBOL' => '[',
		'LAST_LINK_SYMBOL' => ']',
		'PREVIOUS_LINK_SYMBOL' => '&laquo;',
		'NEXT_LINK_SYMBOL' => '&raquo;',
		'ELLIPSIS_SYMBOL' => '&bullet;',
		'CURRENT_LINK_CLASS' => 'active'
	];

	# collect table column maximum length | enumeration
	public $table_column = [];

	# collect errors
	public $errors = [];

	# set location for redirect
	public $location;

	# database handle
	private static $pdo;

	# database object
	private $sql_object;

	# page number
	private $page;

	# url with pagination
	private $pagination_url;

	# last executed sql query string
	protected $query_string;

	public final function __construct()
	{
		if (is_null(self::$pdo))
		{
			try
			{
				self::$pdo = new PDO(DATABASE_ENGINE, DSN_USERNAME, DSN_PASSWORD, [PDO::MYSQL_ATTR_INIT_COMMAND => self::ENCODING]);
				self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
				self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				if (is_null(self::$pdo))
				{
					throw new Exception;
				}
			}

			catch (Exception $exception)
			{
				Controller::setErrorPage(500, 'database connection error');
			}
		}
	}

	# return an array for single row from database
	protected final function getRow($query, $bind_value = [])
	{
		$this->setSQL($query, $bind_value);

		$single_row = $this->sql_object->fetch();

		if (is_array($single_row))
		{
			return $single_row;
		}

		return [];
	}

	# return an array for multiple rows from database
	protected final function getRows($query, $bind_value = [])
	{
		$this->setSQL($query, $bind_value);

		$multiple_rows = $this->sql_object->fetchAll();

		if (is_array($multiple_rows))
		{
			return $multiple_rows;
		}

		return [];
	}

	# serve for INSERT | UPDATE | DELETE statement
	protected final function set($query, $bind_value)
	{
		$this->sql_object = self::$pdo->prepare($query);

		$this->setBindValue($bind_value);

		$this->setQueryString($bind_value);

		if ($this->sql_object->execute())
		{
			return true;
		}

		$this->errors[] = locale('query_error_occurred');
	}

	# call query function | call prepare and execute functions
	private function setSQL($query, $bind_value)
	{
		if (empty($bind_value))
		{
			$this->sql_object = self::$pdo->query($query);
		}
		else
		{
			$this->sql_object = self::$pdo->prepare($query);

			$this->setBindValue($bind_value);

			$this->sql_object->execute();
		}

		$this->setQueryString($bind_value);
	}

	# call bindValue function on [sql_object]
	private function setBindValue(array $bind_value)
	{
		foreach ($bind_value as $key => $value)
		{
			$pdo_param = PDO::PARAM_STR;

			if (is_null($value))
			{
				$pdo_param = PDO::PARAM_NULL;
			}

			if (is_integer($value))
			{
				$pdo_param = PDO::PARAM_INT;
			}

			$this->sql_object->bindValue($key, $value, $pdo_param);
		}
	}

	# add query string to debug
	private function setQueryString($bind_value)
	{
		foreach ($bind_value as &$value)
		{
			if (is_string($value))
			{
				$value = "'" . $value . "'";
			}
		}

		$this->query_string = str_replace(array_keys($bind_value), array_values($bind_value), preg_replace('/\t{2}/m', '', $this->sql_object->queryString));

		Controller::setDebug('queries', $this->query_string, []);
	}

	# return DBMS
	public final function getDBMS()
	{
		$query = '
		SELECT
			version() AS `version`
		';

		$result = $this->getRow($query)['version'];

		return $result;
	}

	# return schema_name with table_name as `schema_name`.`table_name` notation
	protected final static function setSchemaTableNotation($schema_name_with_table_name)
	{
		$notation = '`' . implode('`.`', explode('.', $schema_name_with_table_name)) . '`';

		return $notation;
	}

	# return INSERT query
	protected final static function insertQuery($table, array $array)
	{
		foreach ($array as $value)
		{
			$insert[] = '`' . $value . '`';
		}

		$insert_into_string = implode(', ', $insert);
		$question_mark_array = array_fill(0, count($array), '?');
		$question_mark_string = implode(', ', $question_mark_array);

		$sql_table = self::setSchemaTableNotation($table);

		$query = '
		INSERT INTO
			' . $sql_table . ' (' . $insert_into_string . ')
		VALUES
			(' . $question_mark_string . ')
		';

		return $query;
	}

	# return UPDATE query
	protected final static function updateQuery($table, array $array, $where)
	{
		foreach ($array as $value)
		{
			$update[] = '`' . $value . '` = :' . $value;
		}

		$set_string = implode(', ', $update);

		$sql_table = self::setSchemaTableNotation($table);

		$query = '
		UPDATE
			' . $sql_table . '
		SET
			' . $set_string . '
		WHERE
			`' . $where . '` = :' . $where . '
		';

		return $query;
	}

	# return DELETE query
	protected final static function deleteQuery($table, $where)
	{
		$sql_table = self::setSchemaTableNotation($table);

		$query = '
		DELETE FROM
			' . $sql_table . '
		WHERE
			`' . $where . '` = :' . $where . '
		LIMIT 1
		';

		return $query;
	}

	# call trim function on values of [_POST]
	protected final function trimPOST()
	{
		foreach ($_POST as &$value)
		{
			$value = trim($value);
		}
	}

	# check for valid form with post method
	protected final function setValidateForm(array $form_fields)
	{
		foreach ($form_fields as $value)
		{
			if (!array_key_exists($value, $_POST))
			{
				$this->errors[] = locale('form_is_modified');

				break;
			}
		}
	}

	# check value from [_POST] against pattern
	protected final function setValidateField($regex, $key, $message, $is_unicode = false)
	{
		$pattern = '#' . $regex . '#';

		if ($is_unicode)
		{
			$pattern .= 'u';
		}

		if (!boolval(preg_match($pattern, $_POST[$key])))
		{
			if (array_key_exists($key, $this->errors))
			{
				$key .= '~' . count($this->errors);
			}

			$this->errors[$key] = $message;
		}
	}

	# check if value from [_POST] is positive integer
	protected final function isPositiveInteger($key, $message)
	{
		$regex = '#^' . POSITIVE_INTEGER_PATTERN . '$#';

		if (!boolval(preg_match($regex, $_POST[$key])))
		{
			if (array_key_exists($key, $this->errors))
			{
				$key .= count($this->errors);
			}

			$this->errors[$key] = $message;
		}
	}

	# set [table_column] maximum length | enumeration
	public final function setTableColumn($table)
	{
		$query = '
		SELECT
			`COLUMN_NAME` AS `column`,
			`DATA_TYPE` AS `type`,
			`CHARACTER_MAXIMUM_LENGTH` AS `length`,
			`COLUMN_TYPE` AS `value`
		FROM
			`INFORMATION_SCHEMA`.`COLUMNS`
		WHERE
			CONCAT_WS(".", `TABLE_SCHEMA`, `TABLE_NAME`) = :table
		';
		$bind_value[':table'] = $table;

		$result = $this->getRows($query, $bind_value);

		$this->table_column[$table] = [];

		foreach ($result as $value)
		{
			if (in_array($value['type'], ['char', 'varchar']))
			{
				$this->table_column[$table][$value['column']] = intval($value['length']);
			}

			if (in_array($value['type'], ['enum', 'set']))
			{
				$values = preg_replace('(enum|set|\(|\)|\')', '', $value['value']);

				$enumeration = explode(',', $values);

				$this->table_column[$table][$value['column']] = $enumeration;
			}
		}
	}

	# return number of rows in a table
	public final function getCountTableRows($table)
	{
		$sql_table = self::setSchemaTableNotation($table);

		$query = '
		SELECT
			COUNT(*) AS `count`
		FROM
			' . $sql_table;

		$result = $this->getRow($query)['count'];

		return $result;
	}

	# return column data from a table for searching
	public final function getTableColumns($table)
	{
		$query = '
		SELECT
			`COLUMN_NAME` AS `column`,
			`DATA_TYPE` AS `type`,
			`CHARACTER_MAXIMUM_LENGTH` AS `length`,
			`COLUMN_TYPE` AS `value`,
			`COLUMN_KEY` AS `key`,
			`COLUMN_COMMENT` AS `comment`
		FROM
			`INFORMATION_SCHEMA`.`COLUMNS`
		WHERE
			CONCAT_WS(".", `TABLE_SCHEMA`, `TABLE_NAME`) = :table
		';
		$bind_value[':table'] = $table;

		$result = $this->getRows($query, $bind_value);

		$array = [];
		$accepted_data_type = [
			'input' => ['char', 'varchar'],
			'select' => ['enum', 'set']
		];

		foreach ($result as $value)
		{
			$column = $value['column'];

			if ($value['key'] === 'MUL' && !empty($value['comment']))
			{
				$reference = explode('.', $value['comment']);

				$database_name = $reference[0];
				$table_name = $reference[1];
				$column_name = $reference[2];

				$sql_table = self::setSchemaTableNotation($database_name . '.' . $table_name);

				$query = '
				SELECT
					`' . $column . '`, `' . $column_name . '`
				FROM
					' . $sql_table . '
				ORDER BY
					`' . $column_name . '` ASC
				';

				$_array = $this->getRows($query);

				$key_value_array[] = locale('select');

				foreach ($_array as $_value)
				{
					$key_value_array[$_value[$column]] = $_value[$column_name];
				}

				$array[$column]['select'] = $key_value_array;
			}
			else
			{
				if (in_array($value['type'], $accepted_data_type['input']))
				{
					$array[$column]['maxlength'] = $value['length'];
				}

				if (in_array($value['type'], $accepted_data_type['select']))
				{
					$values = preg_replace('(enum|set|\(|\)|\')', '', $value['value']);

					$enumeration = explode(',', $values);

					$enumeration_array = array_combine($enumeration, $enumeration);

					array_unshift($enumeration_array, locale('select'));

					$array[$column]['select'] = $enumeration_array;
				}
			}
		}

		return $array;
	}

	# return column data for a table
	public final function getTableStructure($table)
	{
		$query = '
		SELECT
			`COLUMN_NAME` AS `column`,
			`COLUMN_TYPE` AS `type`,
			`COLUMN_KEY` AS `key`,
			`IS_NULLABLE` AS `null`,
			`COLUMN_COMMENT` AS `comment`
		FROM
			`INFORMATION_SCHEMA`.`COLUMNS`
		WHERE
			CONCAT_WS(".", `TABLE_SCHEMA`, `TABLE_NAME`) = :table
		';
		$bind_value[':table'] = $table;

		$result = $this->getRows($query, $bind_value);

		return $result;
	}

	# create query | validate page number
	protected final function setPagination($query, $page, $output = self::PAGINATION['DEFAULT_OUTPUT'])
	{
		$page = intval($page);

		if ($page < 1 || $page > self::PAGINATION['FETCH_MAXIMUM_PAGES'])
		{
			$page = 1;
		}

		$this->page = $page;

		$output = (intval($output) > 0) ? intval($output) : self::PAGINATION['DEFAULT_OUTPUT'];

		if (substr($query, 0, 26) !== 'SELECT SQL_CALC_FOUND_ROWS')
		{
			$query = substr_replace(trim($query), PHP_EOL . 'SELECT SQL_CALC_FOUND_ROWS', 0, 6);
		}

		if ($page === 1)
		{
			$query .= ' LIMIT 0, ' . $output;
		}
		else
		{
			$query .= ' LIMIT ' . (($page - 1) * $output) . ', ' . $output;
		}

		$results = $this->getRows($query);

		$query = '
		SELECT
			FOUND_ROWS() AS `found_rows`
		';

		$found_rows = intval($this->getRow($query)['found_rows']);

		if (!Controller::$is_admin_path)
		{
			if (empty($found_rows))
			{
				Controller::setErrorPage(500);
			}

			if (empty($results))
			{
				Controller::setErrorPage(400);
			}
		}

		$total_pages = intval(ceil($found_rows / $output));

		if ($total_pages > self::PAGINATION['FETCH_MAXIMUM_PAGES'])
		{
			$total_pages = self::PAGINATION['FETCH_MAXIMUM_PAGES'];
		}

		$this->pagination_url = FORWARD_SLASH . Controller::$page_url;

		if (!isset($_GET['page']))
		{
			$this->pagination_url .= setKeyValue('page', $page);
		}

		if ($total_pages > self::PAGINATION['MAXIMUM_LINKS'])
		{
			$start = $page - 2;
			$start_range = ($start < 1) ? 1 : $start;

			$end = $page + 2;
			$end_range = ($end > $total_pages) ? $total_pages : $end;

			if (($end_range - $start_range) !== (self::PAGINATION['MAXIMUM_LINKS'] - 1))
			{
				if ($end_range === $total_pages)
				{
					$start_range = $total_pages - self::PAGINATION['MAXIMUM_LINKS'] + 1;
				}

				if ($start_range === 1)
				{
					$end_range = self::PAGINATION['MAXIMUM_LINKS'];
				}
			}
		}
		else
		{
			$start_range = 1;
			$end_range = $total_pages;
		}

		$links = '';

		if ($start_range > 1)
		{
			$links .= $this->getPaginationLink(1, self::PAGINATION['FIRST_LINK_SYMBOL']);
			$links .= parse('span', ['text' => str_repeat(self::PAGINATION['ELLIPSIS_SYMBOL'], 3)], true);
		}

		if ($page > 1)
		{
			$links .= $this->getPaginationLink($page - 1, self::PAGINATION['PREVIOUS_LINK_SYMBOL']);
		}

		$links .= $this->setPaginationRange($start_range, $end_range);

		if ($page < $total_pages)
		{
			$links .= $this->getPaginationLink($page + 1, self::PAGINATION['NEXT_LINK_SYMBOL']);
		}

		if ($end_range < $total_pages)
		{
			$links .= parse('span', ['text' => str_repeat(self::PAGINATION['ELLIPSIS_SYMBOL'], 3)], true);
			$links .= $this->getPaginationLink($total_pages, self::PAGINATION['LAST_LINK_SYMBOL']);
		}

		return [
			'results' => $results,
			'links' => parse('nav', ['data' => trim($links)], true)
		];
	}

	# create HTML page link
	private function getPaginationLink($page, $symbol = null)
	{
		$symbol = (is_null($symbol)) ? $page : $symbol;

		$page_url = preg_replace('#' . setKeyValue('page') . '#', setKeyValue('page', $page), $this->pagination_url);

		$href_pagination = [
			'href' => $page_url,
			'class' => ($page === $this->page) ? self::PAGINATION['CURRENT_LINK_CLASS'] : null,
			'text' => $symbol
		];

		$link = parse('a', $href_pagination, true);

		return $link;
	}

	# create all HTML page links
	private function setPaginationRange($start_range, $end_range)
	{
		$data = '';

		while ($start_range <= $end_range)
		{
			$data .= $this->getPaginationLink($start_range);

			++$start_range;
		}

		return $data;
	}
}