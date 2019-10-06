<?php
final class Form_Elements extends Controller
{
	public function __construct()
	{
		$this->data = [
			TITLE => 'Form Elements',
			BODY => [
				IMPORT => ['form_elements']
			]
		];
	}

	public function index()
	{
		$this->view();
	}
}