<?php
final class Index_Model extends Model
{
	# return all profiles from `admin`.`account`
	public function getAccounts()
	{
		$query = '
		SELECT
			`account_id`, `username`, `profile`, `e_mail`, `last_login`, `origin_id`
		FROM
			`admin`.`account`
		ORDER BY
			`account_id` ASC
		';

		$result = $this->getRows($query);

		return $result;
	}

	# return interface files from `admin`.`subdomain_file`
	public function getInterfaceFiles()
	{
		$query = '
		SELECT
			`s_f`.`subdomain_file_id`, `s_f`.`name`, `s_f`.`commit_option`
		FROM
			`admin`.`subdomain_file` AS `s_f`
		JOIN
			`admin`.`subdomain` AS `s` USING (`subdomain_id`)
		WHERE
			`s`.`name` = :name
		ORDER BY
			`s_f`.`subdomain_file_id` ASC
		';
		$bind_value[':name'] = SUBDOMAIN_NAME;

		$result = $this->getRows($query, $bind_value);

		return $result;
	}

	# return all account permissions from `admin`.`account_permission`
	public function getAccountPermissions()
	{
		$query = '
		SELECT
			`a_p`.`id`, `a_p`.`account_id`, `a_p`.`table_insert`, `a_p`.`table_update`, `a_p`.`table_delete`,
			`s_f`.`name`
		FROM
			`admin`.`account_permission` AS `a_p`
		JOIN
			`admin`.`subdomain_file` AS `s_f` USING (`subdomain_file_id`)
		JOIN
			`admin`.`subdomain` AS `s` USING (`subdomain_id`)
		WHERE
			`s`.`name` = :name
		';
		$bind_value[':name'] = SUBDOMAIN_NAME;

		$result = $this->getRows($query, $bind_value);

		return $result;
	}

	# return all moderator profiles from `admin`.`account`
	public function getModerators()
	{
		$query = '
		SELECT
			`account_id`, `username`
		FROM
			`admin`.`account`
		WHERE
			`profile` = :profile
		';
		$bind_value[':profile'] = 'moderator';

		$result = $this->getRows($query, $bind_value);

		return $result;
	}
}