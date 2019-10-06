<?php
return [
	ADMIN_ROUTE => [
		'file_class' => 'login',
		'class_method' => 'index',
		'optional_arguments' => ['locale' => LOCALE_PATTERN]
	],
	ADMIN_ROUTE . '/(system_properties|all_users|review_files|permissions_of_users|clear_cache|create_user)' => [
		'file_class' => 'index',
		'class_method' => 'index',
		'method_arguments' => null
	],
	ADMIN_ROUTE . '/settings/(account_summary|change_password|change_mail|change_locale)' => [
		'file_class' => 'settings',
		'class_method' => 'index',
		'method_arguments' => null
	],
	ADMIN_ROUTE . '/account/(authenticateUser|createUser|changePassword|changeMail|changeLocale)' => [
		'file_class' => 'account',
		'class_method' => 'index',
		'method_arguments' => null
	],
	ADMIN_ROUTE . '/logout' => [
		'file_class' => 'logout',
		'class_method' => 'index'
	],
	ADMIN_ROUTE . '/access/action-insert/option-admin.(subdomain_file|account_permission)' => [
		'file_class' => 'access',
		'class_method' => 'setInsert'
	],
	ADMIN_ROUTE . '/access/action-update/option-admin.(subdomain_file|account_permission)' => [
		'file_class' => 'access',
		'class_method' => 'setUpdate',
		'required_arguments' => ['id']
	],
	ADMIN_ROUTE . '/access/action-delete/option-admin.(account|subdomain_file|account_permission)' => [
		'file_class' => 'access',
		'class_method' => 'setDelete',
		'required_arguments' => ['id']
	],
	substr(ADMIN_INTERFACE_ROUTE, 0, -1) => [
		'file_class' => 'admin_interface',
		'class_method' => 'index'
	],
	# CUSTOM
	ADMIN_ROUTE . '/commit/action-insert/option-template.(customer|country)' => [
		'file_class' => 'commit',
		'class_method' => 'setInsert'
	],
	ADMIN_ROUTE . '/commit/action-update/option-template.(customer|country)' => [
		'file_class' => 'commit',
		'class_method' => 'setUpdate',
		'required_arguments' => ['id']
	],
	ADMIN_ROUTE . '/commit/action-delete/option-template.(customer|country)' => [
		'file_class' => 'commit',
		'class_method' => 'setDelete',
		'required_arguments' => ['id']
	],
	ADMIN_INTERFACE_ROUTE . 'template_customer/option-template.customer/view-records' => [
		'file_class' => 'template_customer',
		'class_method' => 'index',
		'optional_arguments' => ['pagination']
	],
	ADMIN_INTERFACE_ROUTE . 'template_customer/option-template.customer/view-(search_records|table_structure|insert_record)' => [
		'file_class' => 'template_customer',
		'class_method' => 'index'
	],
	ADMIN_INTERFACE_ROUTE . 'template_customer/option-template.customer/view-update_record' => [
		'file_class' => 'template_customer',
		'class_method' => 'index',
		'required_arguments' => ['id']
	],
	ADMIN_INTERFACE_ROUTE . 'template_country/option-template.country/view-records' => [
		'file_class' => 'template_country',
		'class_method' => 'index',
		'optional_arguments' => ['pagination']
	],
	ADMIN_INTERFACE_ROUTE . 'template_country/option-template.country/view-(search_records|table_structure|insert_record)' => [
		'file_class' => 'template_country',
		'class_method' => 'index'
	],
	ADMIN_INTERFACE_ROUTE . 'template_country/option-template.country/view-update_record' => [
		'file_class' => 'template_country',
		'class_method' => 'index',
		'required_arguments' => ['id']
	]
];