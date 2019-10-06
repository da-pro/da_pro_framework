<?php
final class Country_Model extends Model
{
	# return pagination results and links from `template`.`country` | search countries from `template`.`country` | all countries from `template`.`country`
	public function getCountry($page = null, $output = null, $is_search = false)
	{
		$query = '
		SELECT
			`country_id`, `name`, `currency`
		FROM
			`template`.`country`
		';
		$query_order_by = '
		ORDER BY
			`name` ASC
		';

		if (is_null($page) && is_null($output))
		{
			if ($is_search)
			{
				$arguments = setSearchArguments();

				$query .= $arguments['query'];
				$bind_value = $arguments['bind_value'];

				$result = $this->getRows($query . $query_order_by, $bind_value);
			}
			else
			{
				$result = $this->getRows($query . $query_order_by);
			}
		}
		else
		{
			$result = $this->setPagination($query . $query_order_by, $page, $output);
		}

		return $result;
	}

	# return country data by id
	public function getCountryByID($id)
	{
		$query = '
		SELECT
			`name`, `currency`
		FROM
			`template`.`country`
		WHERE
			`country_id` = :id
		';
		$bind_value[':id'] = $id;

		$result = $this->getRow($query, $bind_value);

		return $result;
	}
}