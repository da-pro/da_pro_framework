<?php
final class Commit_Model extends Model
{
	# query for execute function
	public $query;

	# bind value for [query] in execute function
	public $bind_value = [];

	# referer for execute function
	private $referer;

	# insert [option] into database
	public function setInsert($option)
	{
		$this->referer = $_SERVER['HTTP_REFERER'];

		$this->setTableColumn($option);
		$this->trimPOST();

		switch ($option)
		{
			# INSERT `customer`
			case 'template.customer':
				$post_array = ['country_id', 'first_name', 'last_name', 'city', 'address'];

				$this->setValidateForm($post_array);

				if (empty($this->errors))
				{
					$this->query = self::insertQuery($option, $post_array);
					$this->bind_value = [
						1 => $_POST['country_id'],
						2 => $_POST['first_name'],
						3 => $_POST['last_name'],
						4 => $_POST['city'],
						5 => $_POST['address']
					];

					$this->setValidateField('^[A-Za-z]{1,' . $this->table_column[$option]['first_name'] . '}$', 'first_name', 'invalid first name');
					$this->setValidateField('^[A-Za-z]{1,' . $this->table_column[$option]['last_name'] . '}$', 'last_name', 'invalid last name');
					$this->isPositiveInteger('country_id', 'invalid country');
					$this->setValidateField('^[\p{L} ]{1,' . $this->table_column[$option]['city'] . '}$', 'city', 'invalid city', true);
					$this->setValidateField('^[\p{L}0-9-, \'\.]{1,' . $this->table_column[$option]['address'] . '}$', 'address', 'invalid address', true);

					if (empty($this->errors))
					{
						$query = '
						SELECT
							*
						FROM
							`template`.`country`
						WHERE
							`country_id` = :country_id
						';
						$bind_value[':country_id'] = $_POST['country_id'];

						$result = $this->getRow($query, $bind_value);

						if (empty($result))
						{
							$this->errors['country_id'] = 'invalid country';
						}
					}
				}
			break;

			# INSERT `country`
			case 'template.country':
				$post_array = ['name', 'currency'];

				$this->setValidateForm($post_array);

				if (empty($this->errors))
				{
					$this->query = self::insertQuery($option, $post_array);
					$this->bind_value = [
						1 => $_POST['name'],
						2 => $_POST['currency']
					];

					$this->setValidateField('^[A-Za-z ]{1,' . $this->table_column[$option]['name'] . '}$', 'name', 'invalid country');
					$this->setValidateField('^[A-Z]{' . $this->table_column[$option]['currency'] . '}$', 'currency', 'invalid currency');

					if (empty($this->errors))
					{
						$query = '
						SELECT
							*
						FROM
							`template`.`country`
						WHERE
							`name` = :name
						';
						$bind_value[':name'] = $_POST['name'];

						$result = $this->getRow($query, $bind_value);

						if (!empty($result))
						{
							$this->errors['name'] = 'country already exist';
						}
					}
				}
			break;
		}
	}

	# update [option] from database by [id]
	public function setUpdate($option, $id)
	{
		$this->referer = $_SERVER['HTTP_REFERER'];

		$this->setTableColumn($option);
		$this->trimPOST();

		switch ($option)
		{
			# UPDATE `customer`
			case 'template.customer':
				$post_array = ['country_id', 'first_name', 'last_name', 'city', 'address'];

				$this->setValidateForm($post_array);

				if (empty($this->errors))
				{
					$this->query = self::updateQuery($option, $post_array, 'customer_id');
					$this->bind_value = [
						':customer_id' => $id,
						':country_id' => $_POST['country_id'],
						':first_name' => $_POST['first_name'],
						':last_name' => $_POST['last_name'],
						':city' => $_POST['city'],
						':address' => $_POST['address']
					];

					$this->setValidateField('^[A-Za-z]{1,' . $this->table_column[$option]['first_name'] . '}$', 'first_name', 'invalid first name');
					$this->setValidateField('^[A-Za-z]{1,' . $this->table_column[$option]['last_name'] . '}$', 'last_name', 'invalid last name');
					$this->isPositiveInteger('country_id', 'invalid country');
					$this->setValidateField('^[\p{L} ]{1,' . $this->table_column[$option]['city'] . '}$', 'city', 'invalid city', true);
					$this->setValidateField('^[\p{L}0-9-, \'\.]{1,' . $this->table_column[$option]['address'] . '}$', 'address', 'invalid address', true);

					if (empty($this->errors))
					{
						$query = '
						SELECT
							*
						FROM
							`template`.`country`
						WHERE
							`country_id` = :country_id
						';
						$bind_value[':country_id'] = $_POST['country_id'];

						$result = $this->getRow($query, $bind_value);

						if (empty($result))
						{
							$this->errors['country_id'] = 'invalid country';
						}
					}
				}
			break;

			# UPDATE `country`
			case 'template.country':
				$post_array = ['name', 'currency'];

				$this->setValidateForm($post_array);

				if (empty($this->errors))
				{
					$this->query = self::updateQuery($option, $post_array, 'country_id');
					$this->bind_value = [
						':country_id' => $id,
						':name' => $_POST['name'],
						':currency' => $_POST['currency']
					];

					$this->setValidateField('^[A-Za-z ]{1,' . $this->table_column[$option]['name'] . '}$', 'name', 'invalid country');
					$this->setValidateField('^[A-Z]{' . $this->table_column[$option]['currency'] . '}$', 'currency', 'invalid currency');

					if (empty($this->errors))
					{
						$query = '
						SELECT
							*
						FROM
							`template`.`country`
						WHERE
							`country_id` != :country_id AND `name` = :name
						';
						$bind_value = [
							':country_id' => $id,
							':name' => $_POST['name']
						];

						$result = $this->getRow($query, $bind_value);

						if (!empty($result))
						{
							$this->errors['name'] = 'country already exist';
						}
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
			# DELETE `customer`
			case 'template.customer':
				$this->referer = ADMIN_INTERFACE_ROUTE . 'template_customer' . setKeyValue('option', $option) . setKeyValue('view', 'records');

				$this->query = self::deleteQuery($option, 'customer_id');
				$this->bind_value[':customer_id'] = $id;
			break;

			# DELETE `country`
			case 'template.country':
				$this->referer = ADMIN_INTERFACE_ROUTE . 'template_customer' . setKeyValue('option', $option) . setKeyValue('view', 'records');

				$this->query = self::deleteQuery($option, 'country_id');
				$this->bind_value[':country_id'] = $id;
			break;
		}
	}

	# execute query on database
	public function execute()
	{
		if ($this->set($this->query, $this->bind_value))
		{
			$_SESSION['alert_box'] = locale('successful_query_operation');

			$absolute_path = (boolval(preg_match('#^http://.*$#', $this->referer))) ? true : false;

			$this->location = getLocation($this->referer, $absolute_path);
		}
	}
}