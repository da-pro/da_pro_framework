<?php
final class Customer_Model extends Model
{
	# return pagination results and links from `template`.`customer` | search customers from `template`.`customer`
	public function getCustomer($page = null, $output = null)
	{
		$query = '
		SELECT
			`cu`.`customer_id`, CONCAT_WS(" ", `cu`.`first_name`, `cu`.`last_name`) AS `name`, `cu`.`city`,
			`co`.`name` AS `country`
		FROM
			`template`.`customer` AS `cu`
		LEFT JOIN
			`template`.`country` AS `co` USING (`country_id`)
		';
		$query_order_by = '
		ORDER BY
			`customer_id` DESC
		';

		if (is_null($page) && is_null($output))
		{
			$arguments = setSearchArguments();

			$query .= $arguments['query'];
			$bind_value = $arguments['bind_value'];

			$result = $this->getRows($query . $query_order_by, $bind_value);
		}
		else
		{
			$result = $this->setPagination($query . $query_order_by, $page, $output);
		}

		return $result;
	}

	# return customer data by id
	public function getCustomerByID($id)
	{
		$query = '
		SELECT
			`cu`.`customer_id`, `cu`.`country_id`, `cu`.`first_name`, `cu`.`last_name`, `cu`.`city`, `cu`.`address`,
			`co`.`name` AS `country`
		FROM
			`template`.`customer` AS `cu`
		LEFT JOIN
			`template`.`country` AS `co` USING (`country_id`)
		WHERE
			`customer_id` = :id
		';
		$bind_value[':id'] = $id;

		$result = $this->getRow($query, $bind_value);

		return $result;
	}
}