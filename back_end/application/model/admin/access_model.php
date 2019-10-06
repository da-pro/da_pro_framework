<?php
final class Access_Model extends Model
{
	const REGEX_COMMIT_OPTION = '^[a-z_.]+$';
	const PROFILE_MODERATOR = 'moderator';
	const DEFAULT_ACCOUNT_PERMISSION = '0';

	# query for execute function
	public $query;

	# bind value for [query] in execute function
	public $bind_value = [];

	# referer for execute function
	private $referer;

	# insert [option] into database
	public function setInsert($option)
	{
		$this->setTableColumn($option);
		$this->trimPOST();

		switch ($option)
		{
			# INSERT `subdomain_file`
			case SUBDOMAIN_FILE_DATABASE_TABLE_NAME:
				$this->referer = ADMIN_ROUTE . '/review_files';

				$this->setValidateForm(['name', 'commit_option']);

				if (empty($this->errors))
				{
					if (empty($_POST['commit_option']))
					{
						$this->errors[] = locale('insert_commit_option');
					}
					else
					{
						$interface_files = getInterfaceFiles();

						if (in_array($_POST['name'], $interface_files))
						{
							if (strlen($_POST['name']) > $this->table_column[$option]['name'])
							{
								$this->errors[] = locale('filename_is_too_long');
							}
						}
						else
						{
							$this->errors[] = locale('invalid_file');
						}

						$this->setValidateField(self::REGEX_COMMIT_OPTION, 'commit_option', locale('invalid_symbols_for_commit_option'));

						if (strlen($_POST['commit_option']) > $this->table_column[$option]['commit_option'])
						{
							$this->errors[] = locale('commit_option_is_too_long');
						}

						if (empty($this->errors))
						{
							$query = '
							SELECT
								CASE
									WHEN `sub_query`.`name` IS NULL THEN 0
									ELSE 1
								END AS `name`,
								CASE
									WHEN `sub_query`.`commit_option` IS NULL THEN 0
									ELSE 1
								END AS `commit_option`
							FROM
								(
									SELECT
									(
										SELECT
											`s_f`.`name`
										FROM
											`admin`.`subdomain_file` AS `s_f`
										JOIN
											`admin`.`subdomain` AS `s` USING (`subdomain_id`)
										WHERE
											`s`.`name` = :subdomain_name AND `s_f`.`name` = :name
									) AS `name`,
									(
										SELECT
											`s_f`.`commit_option`
										FROM
											`admin`.`subdomain_file` AS `s_f`
										JOIN
											`admin`.`subdomain` AS `s` USING (`subdomain_id`)
										WHERE
											`s`.`name` = :subdomain_name AND `s_f`.`commit_option` = :commit_option
									) AS `commit_option`
								) AS `sub_query`
							';
							$bind_value = [
								':subdomain_name' => SUBDOMAIN_NAME,
								':name' => $_POST['name'],
								':commit_option' => $_POST['commit_option']
							];

							$row = $this->getRow($query, $bind_value);

							if (boolval($row['name']))
							{
								$this->errors[] = locale('file_already_exist');
							}

							if (boolval($row['commit_option']))
							{
								$this->errors[] = locale('commit_option_already_exist');
							}

							$this->query = self::insertQuery($option, ['subdomain_id', 'name', 'commit_option']);
							$this->bind_value = [
								1 => $row['subdomain_id'],
								2 => $_POST['name'],
								3 => $_POST['commit_option']
							];
						}
					}
				}
			break;

			# INSERT `account_permission`
			case ACCOUNT_PERMISSION_DATABASE_TABLE_NAME:
				$this->referer = ADMIN_ROUTE . '/permissions_of_users';

				$this->setValidateForm(['account_id', 'subdomain_file_id']);

				if (empty($this->errors))
				{
					$this->isPositiveInteger('account_id', '[account_id] ' . locale('have_to_be_integer_value'));
					$this->isPositiveInteger('subdomain_file_id', '[subdomain_file_id] ' . locale('have_to_be_integer_value'));

					if (empty($this->errors))
					{
						$query = '
						SELECT
							(
								SELECT
									`profile`
								FROM
									`admin`.`account`
								WHERE
									`account_id` = :account_id
							) AS `profile`,
							(
								SELECT
									`id`
								FROM
									`admin`.`account_permission`
								WHERE
									`account_id` = :account_id AND `subdomain_file_id` = :subdomain_file_id
							) AS `id`,
							(
								SELECT
									`s`.`name`
								FROM
									`admin`.`subdomain` AS `s`
								JOIN
									`admin`.`subdomain_file` AS `s_f` USING (`subdomain_id`)
								WHERE
									`s_f`.`subdomain_file_id` = :subdomain_file_id
							) AS `subdomain_name`
						';
						$bind_value = [
							':account_id' => $_POST['account_id'],
							':subdomain_file_id' => $_POST['subdomain_file_id']
						];

						$row = $this->getRow($query, $bind_value);

						if (!empty($row) && $row['profile'] === self::PROFILE_MODERATOR && is_null($row['id']) && $row['subdomain_name'] === SUBDOMAIN_NAME)
						{
							$_SESSION['account_permission'] = $_POST['account_id'];

							$this->query = self::insertQuery($option, ['account_id', 'subdomain_file_id', 'table_insert', 'table_update', 'table_delete']);
							$this->bind_value = [
								1 => $_POST['account_id'],
								2 => $_POST['subdomain_file_id'],
								3 => self::DEFAULT_ACCOUNT_PERMISSION,
								4 => self::DEFAULT_ACCOUNT_PERMISSION,
								5 => self::DEFAULT_ACCOUNT_PERMISSION
							];
						}
						else
						{
							$this->errors[] = locale('form_is_modified');
						}
					}
				}
			break;
		}
	}

	# update [option] from database by [id]
	public function setUpdate($option, $id)
	{
		$this->setTableColumn($option);
		$this->trimPOST();

		switch ($option)
		{
			# UPDATE `subdomain_file`
			case SUBDOMAIN_FILE_DATABASE_TABLE_NAME:
				$this->referer = ADMIN_ROUTE . '/review_files';

				$this->setValidateForm(['name', 'commit_option']);

				if (empty($this->errors))
				{
					if (empty($_POST['commit_option']))
					{
						$this->errors[] = locale('insert_commit_option');
					}
					else
					{
						$interface_files = getInterfaceFiles();

						if (in_array($_POST['name'], $interface_files))
						{
							if (strlen($_POST['name']) > $this->table_column[$option]['name'])
							{
								$this->errors[] = locale('filename_is_too_long');
							}
						}
						else
						{
							$this->errors[] = locale('invalid_file');
						}

						$this->setValidateField(self::REGEX_COMMIT_OPTION, 'commit_option', locale('invalid_symbols_for_commit_option'));

						if (strlen($_POST['commit_option']) > $this->table_column[$option]['commit_option'])
						{
							$this->errors[] = locale('commit_option_is_too_long');
						}

						if (empty($this->errors))
						{
							$query = '
							SELECT
								CASE
									WHEN `sub_query`.`commit_option` IS NULL THEN 0
									ELSE 1
								END AS `commit_option`
							FROM
								(
									SELECT
									(
										SELECT
											`s_f`.`commit_option`
										FROM
											`admin`.`subdomain_file` AS `s_f`
										JOIN
											`admin`.`subdomain` AS `s` USING (`subdomain_id`)
										WHERE
											`s`.`name` = :subdomain_name AND `s_f`.`commit_option` = :commit_option
									) AS `commit_option`
								) AS `sub_query`
							';
							$bind_value = [
								':subdomain_name' => SUBDOMAIN_NAME,
								':commit_option' => $_POST['commit_option']
							];

							$row = $this->getRow($query, $bind_value);

							if (boolval($row['commit_option']))
							{
								$this->errors[] = locale('commit_option_already_exist');
							}

							$this->query = self::updateQuery($option, ['commit_option'], 'subdomain_file_id');
							$this->bind_value = [
								':subdomain_file_id' => $id,
								':commit_option' => $_POST['commit_option']
							];
						}
					}
				}
			break;

			# UPDATE `account_permission`
			case ACCOUNT_PERMISSION_DATABASE_TABLE_NAME:
				$this->referer = ADMIN_ROUTE . '/permissions_of_users';

				$this->setValidateForm(['table_insert', 'table_update', 'table_delete']);

				if (empty($this->errors))
				{
					$query = '
					SELECT
						`account_id`
					FROM
						`admin`.`account_permission`
					WHERE
						`id` = :id
					';
					$bind_value[':id'] = $id;

					$row = $this->getRow($query, $bind_value);

					$valid_table_insert = in_array($_POST['table_insert'], $this->table_column[$option]['table_insert']) ? true : false;
					$valid_table_update = in_array($_POST['table_update'], $this->table_column[$option]['table_update']) ? true : false;
					$valid_table_delete = in_array($_POST['table_delete'], $this->table_column[$option]['table_delete']) ? true : false;

					if (!empty($row) && $valid_table_insert && $valid_table_update && $valid_table_delete)
					{
						$_SESSION['account_permission'] = $row['account_id'];

						$this->query = self::updateQuery($option, ['table_insert', 'table_update', 'table_delete'], 'id');
						$this->bind_value = [
							':id' => $id,
							':table_insert' => $_POST['table_insert'],
							':table_update' => $_POST['table_update'],
							':table_delete' => $_POST['table_delete']
						];
					}
					else
					{
						$this->errors[] = locale('form_is_modified');
					}
				}
			break;
		}
	}

	# delete [option] from database by [id]
	public function setDelete($option, $id)
	{
		switch ($option)
		{
			# DELETE `account`
			case ACCOUNT_DATABASE_TABLE_NAME:
				$this->referer = ADMIN_ROUTE . '/all_users';

				$query = '
				SELECT
					`account_id`, `profile`, `origin_id`, `session_id`
				FROM
					`admin`.`account`
				ORDER BY
					`account_id` ASC
				';

				$result = $this->getRows($query);

				if (!empty($result))
				{
					foreach ($result as $value)
					{
						$origin_id_array[$value['account_id']] = [
							'origin' => intval($value['origin_id']),
							'profile' => $value['profile'],
							'session_id' => $value['session_id']
						];
					}

					$delete_origin_id_array = setOriginIteration($origin_id_array);

					if (in_array($id, $delete_origin_id_array))
					{
						$session_id = $origin_id_array[$id]['session_id'];

						if (!empty($session_id))
						{
							if (is_file(PATH_SAVE_SESSION . $session_id))
							{
								unlink(PATH_SAVE_SESSION . $session_id);
							}
						}

						$query = '
						UPDATE
							`admin`.`account`
						SET
							`origin_id` = :update_origin_id
						WHERE
							`origin_id` = :origin_id
						';
						$bind_value = [
							':update_origin_id' => $origin_id_array[$id]['origin'],
							':origin_id' => $id
						];

						if ($this->set($query, $bind_value))
						{
							$this->query = self::deleteQuery($option, 'account_id');
							$this->bind_value[':account_id'] = $id;
						}
					}
					else
					{
						$this->errors[] = locale('invalid_access');
					}
				}
			break;

			# DELETE `subdomain_file`
			case SUBDOMAIN_FILE_DATABASE_TABLE_NAME:
				$this->referer = ADMIN_ROUTE . '/review_files';

				$query = '
				SELECT
					`s_f`.`subdomain_file_id`, `s_f`.`name`
				FROM
					`admin`.`subdomain_file` AS `s_f`
				JOIN
					`admin`.`subdomain` AS `s` USING (`subdomain_id`)
				WHERE
					`s`.`name` = :name
				ORDER BY
					`s_f`.`subdomain_file_id`
				';
				$bind_value[':name'] = SUBDOMAIN_NAME;

				$result = $this->getRows($query, $bind_value);

				$compatible_array = [];
				$interface_files = getInterfaceFiles();

				if (!empty($result))
				{
					foreach ($result as $value)
					{
						$compatible_array[] = $value['name'];
					}

					$missing_interface_file_array = array_diff($compatible_array, $interface_files);
					$missing_file_array = [];

					foreach ($result as $value)
					{
						if (in_array($value['name'], $missing_interface_file_array))
						{
							$missing_file_array[] = $value['subdomain_file_id'];
						}
					}

					if (in_array($id, $missing_file_array))
					{
						$this->query = self::deleteQuery($option, 'subdomain_file_id');
						$this->bind_value[':subdomain_file_id'] = $id;
					}
					else
					{
						$this->errors[] = locale('invalid_file');
					}
				}
			break;

			# DELETE `account_permission`
			case ACCOUNT_PERMISSION_DATABASE_TABLE_NAME:
				$this->referer = ADMIN_ROUTE . '/permissions_of_users';

				$query = '
				SELECT
					`account_id`
				FROM
					`admin`.`account_permission`
				WHERE
					`id` = :id
				';
				$bind_value[':id'] = $id;

				$row = $this->getRow($query, $bind_value);

				if (empty($row))
				{
					$this->errors[] = locale('form_is_modified');
				}
				else
				{
					$_SESSION['account_permission'] = $row['account_id'];

					$this->query = self::deleteQuery($option, 'id');
					$this->bind_value[':id'] = $id;
				}
			break;
		}
	}

	# execute query on database
	public function execute()
	{
		if ($this->set($this->query, $this->bind_value))
		{
			$_SESSION['alert_box'] = locale('successful_query_operation');

			$this->location = getLocation($this->referer);
		}
	}
}