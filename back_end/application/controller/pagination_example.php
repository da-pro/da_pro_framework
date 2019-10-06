<?php
final class Pagination_Example extends Controller
{
	public function __construct()
	{
		$this->data = [
			TITLE => 'Pagination Example',
			BODY => [
				IMPORT => ['pagination_example']
			]
		];

		$this->model('Pagination_Example_Model', 'pagination_example');
	}

	public function index()
	{
		$page = (isset($_GET['page'])) ? $_GET['page'] : 1;

		$output = 5;

		$this->data['pagination'] = $this->pagination_example->getCustomer($page, $output);

		$this->view();
	}
}