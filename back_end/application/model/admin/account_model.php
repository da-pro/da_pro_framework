<?php
final class Account_Model extends Model
{
	const REGEX = [
		'USERNAME' => '^[A-Za-z]+$',
		'PASSWORD' => '^[A-Za-z0-9]+$',
		'E_MAIL' => '^\w+([.-]\w+)*@\w+([.-]\w+)*\.\w{2,4}$'
	];
	const LENGTH = [
		'MINIMUM_USERNAME' => 4,
		'MINIMUM_PASSWORD' => 4,
		'MAXIMUM_PASSWORD' => 10
	];
	const PROFILE = [
		'ADMINISTRATOR' => 'administrator',
		'MODERATOR' => 'moderator'
	];
	const TIMEOUT = [
		'ADMINISTRATOR' => 240,
		'MODERATOR' => 120
	];
	const LOGIN = [
		'LOCKED_UNTIL' => 60,
		'FAILED_ATTEMPT' => 5
	];

	# input values for login <form>
	private $input_authenticate_user = ['username', 'password', 'locale'];

	# input values for register <form>
	private $input_create_user = ['profile', 'username', 'password', 'confirm_password', 'locale', 'e_mail'];

	# input values for change password <form>
	private $input_change_password = ['old_password', 'new_password', 'confirm_new_password'];

	# input value for change mail <form>
	private $input_change_mail = ['password', 'new_e_mail'];

	# input value for change locale <form>
	private $input_change_locale = ['new_locale'];

	# login user | create session
	public function authenticateUser()
	{
		$this->setValidateForm($this->input_authenticate_user);

		if (empty($this->errors))
		{
			if (boolval(preg_match('#^' . LOCALE_PATTERN . '$#', $_POST['locale'])))
			{
				Controller::$admin_locale = $_POST['locale'];
			}

			foreach ($this->input_authenticate_user as $value)
			{
				if ($_POST[$value] === '')
				{
					$this->errors[] = locale('all_fields_are_required');

					return;
				}
			}

			$this->setValidateField(self::REGEX['USERNAME'], 'username', locale('letters_allowed_for_username'));

			if (strlen($_POST['username']) < self::LENGTH['MINIMUM_USERNAME'])
			{
				$this->setErrors('username', locale('username_is_too_short'));
			}

			if (strlen($_POST['username']) > $this->table_column[ACCOUNT_DATABASE_TABLE_NAME]['username'])
			{
				$this->setErrors('username', locale('username_is_too_long'));
			}

			$this->setValidateField(self::REGEX['PASSWORD'], 'password', locale('letters_and_digits_allowed_for_password'));

			if (strlen($_POST['password']) < self::LENGTH['MINIMUM_PASSWORD'])
			{
				$this->setErrors('password', locale('password_is_too_short'));
			}

			if (strlen($_POST['password']) > self::LENGTH['MAXIMUM_PASSWORD'])
			{
				$this->setErrors('password', locale('password_is_too_long'));
			}

			if (empty($this->errors))
			{
				$query = '
				SELECT
					`account_id`, `username`, `password`, `profile`, `locale`, `e_mail`, `date_joined`, `failed_login_attempt`, `locked_login_until`, `session_id`
				FROM
					`admin`.`account`
				WHERE
					`username` = :username
				';
				$bind_value[':username'] = $_POST['username'];

				$row = $this->getRow($query, $bind_value);

				if (empty($row))
				{
					$this->errors['username'] = locale('wrong_username');
				}
				else
				{
					if (intval($row['locked_login_until']) > UNIX_TIME)
					{
						$this->errors[] = locale('account_is_locked_for_time_interval_because_of_too_many_login_attempts');

						return;
					}

					$unique = md5($row['date_joined']);

					if (!password_verify($unique . $_POST['password'], $row['password']))
					{
						$query = '
						UPDATE
							`admin`.`account`
						SET
							`failed_login_attempt` = (
								CASE
									WHEN `failed_login_attempt` < :failed_login_attempt THEN (`failed_login_attempt` + 1)
									ELSE 1
								END
							),
							`locked_login_until` = (
								CASE
									WHEN `failed_login_attempt` = :failed_login_attempt THEN :locked_login_until
									ELSE NULL
								END
							)
						WHERE
							`username` = :username
						';
						$bind_value = [
							':failed_login_attempt' => self::LOGIN['FAILED_ATTEMPT'],
							':locked_login_until' => UNIX_TIME + (self::LOGIN['LOCKED_UNTIL'] * 60),
							':username' => $_POST['username']
						];

						if ($this->set($query, $bind_value))
						{
							$this->errors['password'] = locale('wrong_password');
						}
					}
					else
					{
						$session_id = session_id();

						if (!empty($row['session_id']))
						{
							if ($row['session_id'] !== $session_id)
							{
								if (is_file(PATH_SAVE_SESSION . $row['session_id']))
								{
									unlink(PATH_SAVE_SESSION . $row['session_id']);
								}
							}
						}

						$query = self::updateQuery(ACCOUNT_DATABASE_TABLE_NAME, ['last_login', 'failed_login_attempt', 'locked_login_until'], 'username');
						$bind_value = [
							':last_login' => UNIX_TIME,
							':failed_login_attempt' => 0,
							':locked_login_until' => null,
							':username' => $_POST['username']
						];

						if ($row['session_id'] !== $session_id)
						{
							$query = self::updateQuery(ACCOUNT_DATABASE_TABLE_NAME, ['last_login', 'failed_login_attempt', 'locked_login_until', 'session_id'], 'username');
							$bind_value[':session_id'] = $session_id;
						}

						if ($this->set($query, $bind_value))
						{
							$timeout = ($row['profile'] === self::PROFILE['ADMINISTRATOR']) ? self::TIMEOUT['ADMINISTRATOR'] : self::TIMEOUT['MODERATOR'];

							$_SESSION['authenticate'] = [
								'username' => $row['username'],
								'profile' => $row['profile'],
								'locale' => $row['locale'],
								'e_mail' => $row['e_mail'],
								'id' => intval($row['account_id']),
								'timeout' => UNIX_TIME + ($timeout * 60)
							];

							if ($row['profile'] === self::PROFILE['MODERATOR'])
							{
								$query = '
								SELECT
									`a_p`.`table_insert`, `a_p`.`table_update`, `a_p`.`table_delete`,
									`s_f`.`name`, `s_f`.`commit_option`
								FROM
									`admin`.`account_permission` AS `a_p`
								JOIN
									`admin`.`account` AS `a` USING (`account_id`)
								JOIN
									`admin`.`subdomain_file` AS `s_f` USING (`subdomain_file_id`)
								JOIN
									`admin`.`subdomain` AS `s` USING (`subdomain_id`)
								WHERE
									`a`.`username` = :username AND `s`.`name` = :name
								';
								$bind_value = [
									':username' => $_POST['username'],
									':name' => SUBDOMAIN_NAME
								];

								$result = $this->getRows($query, $bind_value);

								$permission = [];

								foreach ($result as $value)
								{
									$permission[$value['name']] = [
										'insert' => boolval($value['table_insert']),
										'update' => boolval($value['table_update']),
										'delete' => boolval($value['table_delete']),
										'commit' => $value['commit_option']
									];
								}

								$_SESSION['authenticate']['permission'] = $permission;
							}

							$this->location = getLocation(ADMIN_ROUTE . '/system_properties');
						}
					}
				}
			}
		}
	}

	# create user
	public function createUser()
	{
		$this->setValidateForm($this->input_create_user);

		if (empty($this->errors))
		{
			foreach ($this->input_create_user as $value)
			{
				if ($_POST[$value] === '')
				{
					$this->errors[] = locale('all_fields_are_required');

					return;
				}
			}

			if (!in_array($_POST['profile'], $this->table_column[ACCOUNT_DATABASE_TABLE_NAME]['profile']))
			{
				$this->errors['profile'] = locale('profile_is_altered');
			}

			$this->setValidateField(self::REGEX['USERNAME'], 'username', locale('letters_allowed_for_username'));

			if (strlen($_POST['username']) < self::LENGTH['MINIMUM_USERNAME'])
			{
				$this->setErrors('username', locale('username_is_too_short'));
			}

			if (strlen($_POST['username']) > $this->table_column[ACCOUNT_DATABASE_TABLE_NAME]['username'])
			{
				$this->setErrors('username', locale('username_is_too_long'));
			}

			if ($_POST['password'] === $_POST['confirm_password'])
			{
				$this->setValidateField(self::REGEX['PASSWORD'], 'password', locale('letters_and_digits_allowed_for_password'));

				if (array_key_exists('password', $this->errors))
				{
					$this->errors['password|confirm_password'] = $this->errors['password'];

					unset($this->errors['password']);
				}

				if (strlen($_POST['password']) < self::LENGTH['MINIMUM_PASSWORD'])
				{
					$this->setErrors('password|confirm_password', locale('passwords_are_too_short'));
				}

				if (strlen($_POST['password']) > self::LENGTH['MAXIMUM_PASSWORD'])
				{
					$this->setErrors('password|confirm_password', locale('passwords_are_too_long'));
				}
			}
			else
			{
				$this->errors['password|confirm_password'] = locale('passwords_do_not_match');
			}

			if (!in_array($_POST['locale'], $this->table_column[ACCOUNT_DATABASE_TABLE_NAME]['locale']))
			{
				$this->errors['locale'] = locale('locale_is_altered');
			}

			$this->setValidateField(self::REGEX['E_MAIL'], 'e_mail', locale('mail_is_wrong'));

			if (strlen($_POST['e_mail']) > $this->table_column[ACCOUNT_DATABASE_TABLE_NAME]['e_mail'])
			{
				$this->setErrors('e_mail', locale('mail_is_too_long'));
			}

			if (empty($this->errors))
			{
				$query = '
				SELECT
					(
						SELECT
							`username`
						FROM
							`admin`.`account`
						WHERE
							`username` = :username
					) AS `username`,
					(
						SELECT
							`e_mail`
						FROM
							`admin`.`account`
						WHERE
							`e_mail` = :e_mail
					) AS `e_mail`
				';
				$bind_value = [
					':username' => $_POST['username'],
					':e_mail' => $_POST['e_mail']
				];

				$row = $this->getRow($query, $bind_value);

				if (!empty($row['username']))
				{
					$this->errors['username'] = locale('username_already_exist');
				}

				if (!empty($row['e_mail']))
				{
					$this->errors['e_mail'] = locale('mail_already_exist');
				}

				if (empty($this->errors))
				{
					$date_joined = UNIX_TIME;
					$unique = md5($date_joined);
					$password = password_hash($unique . $_POST['password'], PASSWORD_BCRYPT);

					$query = self::insertQuery(ACCOUNT_DATABASE_TABLE_NAME, ['username', 'password', 'profile', 'locale', 'e_mail', 'date_joined', 'origin_id']);
					$bind_value = [
						1 => $_POST['username'],
						2 => $password,
						3 => $_POST['profile'],
						4 => $_POST['locale'],
						5 => $_POST['e_mail'],
						6 => $date_joined,
						7 => $_SESSION['authenticate']['id']
					];

					if ($this->set($query, $bind_value))
					{
						$_SESSION['create_user'] = locale('text_' . $_POST['profile']) . ' [' . $_POST['username'] . '] ' . locale('text_created');

						$this->location = getLocation(ADMIN_ROUTE . '/create_user');
					}
				}
			}
		}
	}

	# change password of logged user
	public function changePassword()
	{
		$this->setValidateForm($this->input_change_password);

		if (empty($this->errors))
		{
			foreach ($this->input_change_password as $value)
			{
				if ($_POST[$value] === '')
				{
					$this->errors[] = locale('all_fields_are_required');

					return;
				}
			}

			$this->setValidateField(self::REGEX['PASSWORD'], 'old_password', locale('letters_and_digits_allowed_for_password'));

			if (strlen($_POST['old_password']) < self::LENGTH['MINIMUM_PASSWORD'])
			{
				$this->setErrors('old_password', locale('old_password_is_too_short'));
			}

			if (strlen($_POST['old_password']) > self::LENGTH['MAXIMUM_PASSWORD'])
			{
				$this->setErrors('old_password', locale('old_password_is_too_long'));
			}

			if ($_POST['new_password'] === $_POST['confirm_new_password'])
			{
				$this->setValidateField(self::REGEX['PASSWORD'], 'new_password', locale('letters_and_digits_allowed_for_password'));

				if (array_key_exists('new_password', $this->errors))
				{
					$this->errors['new_password|confirm_new_password'] = $this->errors['new_password'];

					unset($this->errors['new_password']);
				}

				if (strlen($_POST['new_password']) < self::LENGTH['MINIMUM_PASSWORD'])
				{
					$this->setErrors('new_password|confirm_new_password', locale('new_passwords_are_too_short'));
				}

				if (strlen($_POST['new_password']) > self::LENGTH['MAXIMUM_PASSWORD'])
				{
					$this->setErrors('new_password|confirm_new_password', locale('new_passwords_are_too_long'));
				}
			}
			else
			{
				$this->errors['new_password|confirm_new_password'] = locale('new_passwords_do_not_match');
			}

			if (empty($this->errors))
			{
				$query = '
				SELECT
					`password`, `date_joined`
				FROM
					`admin`.`account`
				WHERE
					`username` = :username
				';
				$bind_value[':username'] = $_SESSION['authenticate']['username'];

				$row = $this->getRow($query, $bind_value);

				$unique = md5($row['date_joined']);

				if (!password_verify($unique . $_POST['old_password'], $row['password']))
				{
					$this->errors['old_password'] = locale('wrong_old_password');
				}
				else
				{
					$new_password = password_hash($unique . $_POST['new_password'], PASSWORD_BCRYPT);

					$query = self::updateQuery(ACCOUNT_DATABASE_TABLE_NAME, ['password'], 'username');
					$bind_value = [
						':password' => $new_password,
						':username' => $_SESSION['authenticate']['username']
					];

					if ($this->set($query, $bind_value))
					{
						$_SESSION['change_password'] = true;

						$this->location = getLocation(ADMIN_ROUTE . '/settings/change_password');
					}
				}
			}
		}
	}

	# change mail of logged user
	public function changeMail()
	{
		$this->setValidateForm($this->input_change_mail);

		if (empty($this->errors))
		{
			foreach ($this->input_change_mail as $value)
			{
				if ($_POST[$value] === '')
				{
					$this->errors[] = locale('all_fields_are_required');

					return;
				}
			}

			$this->setValidateField(self::REGEX['PASSWORD'], 'password', locale('letters_and_digits_allowed_for_password'));

			if (strlen($_POST['password']) < self::LENGTH['MINIMUM_PASSWORD'])
			{
				$this->setErrors('password', locale('password_is_too_short'));
			}

			if (strlen($_POST['password']) > self::LENGTH['MAXIMUM_PASSWORD'])
			{
				$this->setErrors('password', locale('password_is_too_long'));
			}

			$this->setValidateField(self::REGEX['E_MAIL'], 'new_e_mail', locale('mail_is_wrong'));

			if (strlen($_POST['new_e_mail']) > $this->table_column[ACCOUNT_DATABASE_TABLE_NAME]['e_mail'])
			{
				$this->setErrors('new_e_mail', locale('mail_is_too_long'));
			}

			if (empty($this->errors))
			{
				$query = '
				SELECT
					`password`, `date_joined`,
					(
						SELECT
							`e_mail`
						FROM
							`admin`.`account`
						WHERE
							`e_mail` = :e_mail
					) AS `e_mail`
				FROM
					`admin`.`account`
				WHERE
					`username` = :username
				';
				$bind_value = [
					':e_mail' => $_POST['new_e_mail'],
					':username' => $_SESSION['authenticate']['username']
				];

				$row = $this->getRow($query, $bind_value);

				$unique = md5($row['date_joined']);

				if (!password_verify($unique . $_POST['password'], $row['password']))
				{
					$this->errors['password'] = locale('wrong_password');
				}
				else
				{
					if (!empty($row['e_mail']))
					{
						$this->errors['new_e_mail'] = locale('mail_already_exist');
					}
					else
					{
						$query = self::updateQuery(ACCOUNT_DATABASE_TABLE_NAME, ['e_mail'], 'username');

						if ($this->set($query, $bind_value))
						{
							$_SESSION['change_mail'] = true;
							$_SESSION['authenticate']['e_mail'] = $_POST['new_e_mail'];

							$this->location = getLocation(ADMIN_ROUTE . '/settings/change_mail');
						}
					}
				}
			}
		}
	}

	# change locale of logged user
	public function changeLocale()
	{
		$this->setValidateForm($this->input_change_locale);

		if (empty($this->errors))
		{
			foreach ($this->input_change_locale as $value)
			{
				if ($_POST[$value] === '')
				{
					$this->errors[] = locale('select_locale');

					return;
				}
			}

			if (!in_array($_POST['new_locale'], $this->table_column[ACCOUNT_DATABASE_TABLE_NAME]['locale']))
			{
				$this->errors['new_locale'] = locale('locale_is_altered');
			}

			if (empty($this->errors))
			{
				$query = self::updateQuery(ACCOUNT_DATABASE_TABLE_NAME, ['locale'], 'username');
				$bind_value = [
					':locale' => $_POST['new_locale'],
					':username' => $_SESSION['authenticate']['username']
				];

				if ($this->set($query, $bind_value))
				{
					$_SESSION['change_locale'] = true;
					$_SESSION['authenticate']['locale'] = $_POST['new_locale'];

					$this->location = getLocation(ADMIN_ROUTE . '/settings/change_locale');
				}
			}
		}
	}

	private function setErrors($key, $message)
	{
		if (array_key_exists($key, $this->errors))
		{
			$key .= '~' . count($this->errors);
		}

		$this->errors[$key] = $message;
	}
}