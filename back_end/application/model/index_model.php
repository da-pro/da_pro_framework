<?php
final class Index_Model extends Model
{
	# return all profiles from `admin`.`account`
	public function getAccounts()
	{
		$query = '
		SELECT
			`account_id`, `username`, `profile`, `e_mail`
		FROM
			`admin`.`account`
		ORDER BY
			`username` ASC
		';

		$result = $this->getRows($query);

		return $result;
	}
}