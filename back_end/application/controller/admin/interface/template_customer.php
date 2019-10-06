<?php
final class Template_Customer extends Controller
{
	public function __construct()
	{
		$this->model('Customer_Model', 'customer');
		$this->model('Country_Model', 'country');
	}

	public function index()
	{
		$option = $_GET['option'];
		$view = setInterfaceView();

		$content = '';
		$content .= setInterfaceLinks('Customer', $this->customer->getCountTableRows($option), $option);

		$th = [
			'_30_|Name',
			'_25_|Country',
			'_25_|City'
		];
		$td = ['name', 'country', 'city'];
		$customer_options = [
			'option' => $option,
			'id' => 'customer_id',
			'commit_option' => $option,
			'element_name' => 'name'
		];

		switch ($view)
		{
			case 'records':
				$page = (isset($_GET['page'])) ? intval($_GET['page']) : 1;

				$data = $this->customer->getCustomer($page, 25);

				$customer_options['pagination'] = $data['links'];

				$content .= setRecordTable($th, $td, $data['results'], $customer_options);
			break;

			case 'search_records':
				$data = $this->customer->getCustomer();

				$content .= setSearchForm($this->customer->getTableColumns($option));
				$content .= setRecordTable($th, $td, $data, $customer_options);
			break;

			case 'table_structure':
				$content .= setTableStructure($this->customer->getTableStructure($option));
			break;

			case 'insert_record':
			case 'update_record':
				$is_update = false;

				$id = getInterfaceUpdateID();

				if (is_integer($id))
				{
					$row = $this->customer->getCustomerByID($id);

					if (empty($row))
					{
						setLocation(ADMIN_INTERFACE_ROUTE . Controller::$file_class . setKeyValue('option', $option) . setKeyValue('view', 'records'));
					}
					else
					{
						$is_update = true;
					}
				}

				$this->customer->setTableColumn($option);

				$tbody = '';

				$input_first_name_array = [
					'name' => 'first_name',
					'value' => ($is_update) ? $row['first_name'] : null,
					'maxlength' => $this->customer->table_column[$option]['first_name']
				];
				$input_first_name = parse('input_text', $input_first_name_array);

				$tbody .= parse('tr_data', ['th' => 'First Name', 'td' => $input_first_name], true);

				$input_last_name_array = [
					'name' => 'last_name',
					'value' => ($is_update) ? $row['last_name'] : null,
					'maxlength' => $this->customer->table_column[$option]['last_name']
				];
				$input_last_name = parse('input_text', $input_last_name_array);

				$tbody .= parse('tr_data', ['th' => 'Last Name', 'td' => $input_last_name], true);

				$value_selected = ($is_update) ? $row['country_id'] : '';
				$name_selected = ($is_update) ? $row['country'] : locale('select');

				$options = '';
				$options .= parse('option_selected', ['value' => $value_selected, 'text' => $name_selected]);

				$country_array = $this->country->getCountry();

				$array = setRemoveCurrentValue($country_array, 'country_id', ($is_update) ? $row['country_id'] : null);

				foreach ($array as $value)
				{
					$options .= PHP_EOL . parse('option', ['value' => $value['country_id'], 'text' => $value['name']]);
				}

				$select_country_id = parse('select', ['name' => 'country_id', 'options' => $options]);

				$tbody .= parse('tr_data', ['th' => 'Country', 'td' => $select_country_id], true);

				$input_city_array = [
					'name' => 'city',
					'value' => ($is_update) ? $row['city'] : null,
					'maxlength' => $this->customer->table_column[$option]['city']
				];
				$input_city = parse('input_text', $input_city_array);

				$tbody .= parse('tr_data', ['th' => 'City', 'td' => $input_city], true);

				$textarea_address_array = [
					'name' => 'address',
					'maxlength' => $this->customer->table_column[$option]['address'],
					'text' => ($is_update) ? $row['address'] : null
				];
				$textarea_address = parse('textarea', $textarea_address_array);

				$tbody .= parse('tr_data', ['th' => 'Address', 'td' => $textarea_address]);

				$table = parse('table', ['id' => null, 'data' => $tbody]);

				$content .= setInterfaceForm($is_update, $table);
			break;
		}

		$section = parse('section', ['data' => $content]);

		$bottom_frame = parse('bottom_frame', ['data' => $section]);

		parent::$admin_data .= $bottom_frame;

		Output::html('Template Customer');
	}
}