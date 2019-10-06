<?php
return [
	DEFAULT_ROUTE => [
		'file_class' => 'index',
		'class_method' => 'index'
	],
	ERROR_ROUTE => [
		'file_class' => 'custom_error',
		'class_method' => 'index'
	],
	DEBUG_ROUTE => [
		'file_class' => 'debug',
		'class_method' => 'index'
	],
	# CUSTOM
	'documentation' => [
		'file_class' => 'documentation',
		'class_method' => 'index'
	],
	'form_elements' => [
		'file_class' => 'form_elements',
		'class_method' => 'index'
	],
	'utility' => [
		'file_class' => 'utility',
		'class_method' => 'index'
	],
	'pagination_example' => [
		'file_class' => 'pagination_example',
		'class_method' => 'index',
		'optional_arguments' => ['pagination']
	],
	'rules_css' => [
		'file_class' => 'rules',
		'class_method' => 'css'
	],
	'rules_html' => [
		'file_class' => 'rules',
		'class_method' => 'html'
	],
	'color_palette' => [
		'file_class' => 'color_palette',
		'class_method' => 'index'
	]
];