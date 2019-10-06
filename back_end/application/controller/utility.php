<?php
final class Utility extends Controller
{
	public function __construct()
	{
		$this->data = [
			TITLE => 'Utility',
			BODY => [
				IMPORT => ['utility']
			]
		];
	}

	public function index()
	{
		$this->view();
	}
}