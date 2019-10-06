<?php
final class Debug extends Controller
{
	public function __construct()
	{
		if (!isset($_SESSION['debug']))
		{
			setLocation();
		}

		$this->data = [
			TITLE => 'Debug[' . $_SESSION['debug']['page_url'] . ']',
			BODY => [
				IMPORT => ['debug']
			]
		];
	}

	public function index()
	{
		$this->data['page_url'] = $_SESSION['debug']['page_url'];
		$this->data['controller_file'] = $_SESSION['debug']['controller_file'];
		$this->data['model_files'] = (isset($_SESSION['debug']['model_files'])) ? printArray($_SESSION['debug']['model_files']) : null;
		$this->data['queries'] = (isset($_SESSION['debug']['queries'])) ? printArray($_SESSION['debug']['queries']) : null;
		$this->data['view_files'] = (isset($_SESSION['debug']['view_files'])) ? printArray($_SESSION['debug']['view_files']) : null;
		$this->data['routes'] = printArray($_SESSION['debug']['routes']);

		$this->view();
	}
}