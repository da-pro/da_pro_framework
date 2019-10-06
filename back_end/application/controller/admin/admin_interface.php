<?php
final class Admin_Interface extends Controller
{
	public function __construct(){}

	public function index()
	{
		$base_path = PATH_ADMIN_INTERFACE;
		$data = '';

		if (getInterfaceFileClass('template_customer'))
		{
			$path = $base_path . 'template_customer' . setKeyValue('option', 'template.customer');

			$title = PHP_EOL . parse('h1', ['text' => 'Customer'], true);

			$href_records = [
				'href' => $path . setKeyValue('view', 'records'),
				'class' => null,
				'text' => locale('link_records')
			];
			$href_search_records = [
				'href' => $path . setKeyValue('view', 'search_records'),
				'class' => null,
				'text' => locale('link_search_records')
			];
			$href_table_structure = [
				'href' => $path . setKeyValue('view', 'table_structure'),
				'class' => null,
				'text' => locale('link_table_structure')
			];
			$href_insert_record = [
				'href' => $path . setKeyValue('view', 'insert_record'),
				'class' => null,
				'text' => locale('link_insert_record')
			];

			$records = parse('a', $href_records, true);
			$search_records = parse('a', $href_search_records, true);
			$table_structure = (isAdministrator()) ? parse('a', $href_table_structure, true) : null;
			$insert_record = (getPermission('insert', 'template_customer')) ? parse('a', $href_insert_record) : null;

			$href = parse('nav', ['data' => $records . $search_records . $table_structure . $insert_record], true);

			$interface_class = parse('div_class', ['class' => 'interface', 'data' => $title . $href], true);

			$data .= $interface_class;
		}

		if (getInterfaceFileClass('template_country'))
		{
			$path = $base_path . 'template_country' . setKeyValue('option', 'template.country');

			$title = PHP_EOL . parse('h1', ['text' => 'Country'], true);

			$href_records = [
				'href' => $path . setKeyValue('view', 'records'),
				'class' => null,
				'text' => locale('link_records')
			];
			$href_search_records = [
				'href' => $path . setKeyValue('view', 'search_records'),
				'class' => null,
				'text' => locale('link_search_records')
			];
			$href_table_structure = [
				'href' => $path . setKeyValue('view', 'table_structure'),
				'class' => null,
				'text' => locale('link_table_structure')
			];
			$href_insert_record = [
				'href' => $path . setKeyValue('view', 'insert_record'),
				'class' => null,
				'text' => locale('link_insert_record')
			];

			$records = parse('a', $href_records, true);
			$search_records = parse('a', $href_search_records, true);
			$table_structure = (isAdministrator()) ? parse('a', $href_table_structure, true) : null;
			$insert_record = (getPermission('insert', 'template_country')) ? parse('a', $href_insert_record) : null;

			$href = parse('nav', ['data' => $records . $search_records . $table_structure . $insert_record], true);

			$interface_class = parse('div_class', ['class' => 'interface', 'data' => $title . $href]);

			$data .= $interface_class;
		}

		if (empty($data))
		{
			$data .= parse('p', ['text' => locale('no_permissions_available')]);
		}

		$section = parse('section', ['data' => $data]);

		$bottom_frame = parse('bottom_frame', ['data' => $section]);

		parent::$admin_data .= $bottom_frame;

		Output::html(locale('document_title_interface'));
	}
}