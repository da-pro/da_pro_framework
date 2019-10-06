<?php
final class Template_Country extends Controller
{
	public function __construct()
	{
		$this->model('Country_Model', 'country');
	}

	public function index()
	{
		$option = $_GET['option'];
		$view = setInterfaceView();

		$content = '';
		$content .= setInterfaceLinks('Country', $this->country->getCountTableRows($option), $option);

		$th = [
			'_50_|Name',
			'_20_|Currency'
		];
		$td = ['name', '|currency|'];
		$country_options = [
			'option' => $option,
			'id' => 'country_id',
			'commit_option' => $option,
			'element_name' => 'name'
		];

		switch ($view)
		{
			case 'records':
				$page = (isset($_GET['page'])) ? intval($_GET['page']) : 1;

				$data = $this->country->getCountry($page, 25);

				$country_options['pagination'] = $data['links'];

				$content .= setRecordTable($th, $td, $data['results'], $country_options);
			break;

			case 'search_records':
				$results = $this->country->getCountry(null, null, true);

				$content .= setSearchForm($this->country->getTableColumns($option));
				$content .= setRecordTable($th, $td, $results, $country_options);
			break;

			case 'table_structure':
				$content .= setTableStructure($this->country->getTableStructure($option));
			break;

			case 'insert_record':
			case 'update_record':
				$is_update = false;

				$id = getInterfaceUpdateID();

				if (is_integer($id))
				{
					$row = $this->country->getCountryByID($id);

					if (empty($row))
					{
						setLocation(ADMIN_INTERFACE_ROUTE . Controller::$file_class . setKeyValue('option', $option) . setKeyValue('view', 'records'));
					}
					else
					{
						$is_update = true;
					}
				}

				$this->country->setTableColumn($option);

				$tbody = '';

				$input_name_array = [
					'name' => 'name',
					'value' => ($is_update) ? $row['name'] : null,
					'maxlength' => $this->country->table_column[$option]['name']
				];
				$input_name = parse('input_text', $input_name_array);

				$tbody .= parse('tr_data', ['th' => 'Name', 'td' => $input_name], true);

				$input_currency_array = [
					'name' => 'currency',
					'value' => ($is_update) ? $row['currency'] : null,
					'maxlength' => $this->country->table_column[$option]['currency']
				];
				$input_currency = parse('input_text', $input_currency_array);

				$tbody .= parse('tr_data', ['th' => 'Currency', 'td' => $input_currency]);

				$table = parse('table', ['id' => null, 'data' => $tbody]);

				$content .= setInterfaceForm($is_update, $table);
			break;
		}

		$section = parse('section', ['data' => $content]);

		$bottom_frame = parse('bottom_frame', ['data' => $section]);

		parent::$admin_data .= $bottom_frame;

		Output::html('Template Country');
	}
}