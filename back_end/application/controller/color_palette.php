<?php
final class Color_Palette extends Controller
{
	public function __construct()
	{
		$this->data = [
			TITLE => 'Color Palette',
			BODY => [
				CSS_CLASS => 'b-66bb66',
				IMPORT => ['color_palette']
			]
		];
	}

	public function index()
	{
		$this->view();
	}
}