<?php
final class Pagination_Example_Model extends Model
{
	# return pagination results and links from `template`.`customer`
	public function getCustomer($page, $output)
	{
		$query = '
		SELECT
			`cu`.`customer_id`, CONCAT_WS(" ", `cu`.`first_name`, `cu`.`last_name`) AS `customer_name`, `cu`.`city`, `cu`.`address`,
			`co`.`name` AS `country`
		FROM
			`template`.`customer` AS `cu`
		JOIN
			`template`.`country` AS `co` USING (`country_id`)
		ORDER BY
			`customer_id` DESC
		';

		$result = $this->setPagination($query, $page, $output);

		return $result;
	}
}