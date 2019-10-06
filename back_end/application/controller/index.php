<?php
final class Index extends Controller
{
	public function __construct()
	{
		$this->data = [
			TITLE => 'Home',
			BODY => [
				IMPORT => ['index']
			]
		];

		$this->model('Index_Model', 'index');
	}

	public function index()
	{
		$server_software = $_SERVER['SERVER_SOFTWARE'];

		$server_software_array = explode(' ', $server_software);

		$this->data['web_server'] = reset($server_software_array);

		$this->data['server_side_scripting_language'] = end($server_software_array);

		$this->data['database_management_system'] = $this->index->getDBMS();

		$this->data['accounts'] = $this->index->getAccounts();

		$this->view();
	}
}