<?php
final class Documentation extends Controller
{
	public function __construct()
	{
		$this->data = [
			TITLE => 'Documentation',
			BODY => [
				IMPORT => ['documentation']
			]
		];
	}

	public function index()
	{
		$this->view();
	}
}