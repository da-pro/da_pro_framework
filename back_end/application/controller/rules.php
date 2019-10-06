<?php
final class Rules extends Controller
{
	public function __construct(){}

	public function css()
	{
		$this->data = [
			TITLE => 'Rules CSS',
			BODY => [
				IMPORT => ['rules_css']
			]
		];

		$this->view();
	}

	public function html()
	{
		$this->data = [
			TITLE => 'Rules HTML',
			BODY => [
				IMPORT => ['rules_html']
			]
		];

		$this->view();
	}
}