<?php
return array (
  '/' => 
  array (
    'file_class' => 'index',
    'class_method' => 'index',
  ),
  'error-(400|401|403|404|500)' => 
  array (
    'file_class' => 'custom_error',
    'class_method' => 'index',
  ),
  'debug' => 
  array (
    'file_class' => 'debug',
    'class_method' => 'index',
  ),
  'documentation' => 
  array (
    'file_class' => 'documentation',
    'class_method' => 'index',
  ),
  'form_elements' => 
  array (
    'file_class' => 'form_elements',
    'class_method' => 'index',
  ),
  'utility' => 
  array (
    'file_class' => 'utility',
    'class_method' => 'index',
  ),
  'pagination_example' => 
  array (
    'file_class' => 'pagination_example',
    'class_method' => 'index',
  ),
  'rules_css' => 
  array (
    'file_class' => 'rules',
    'class_method' => 'css',
  ),
  'rules_html' => 
  array (
    'file_class' => 'rules',
    'class_method' => 'html',
  ),
  'color_palette' => 
  array (
    'file_class' => 'color_palette',
    'class_method' => 'index',
  ),
  'admin' => 
  array (
    'file_class' => 'login',
    'class_method' => 'index',
  ),
  'admin/(system_properties|all_users|review_files|permissions_of_users|clear_cache|create_user)' => 
  array (
    'file_class' => 'index',
    'class_method' => 'index',
    'method_arguments' => NULL,
  ),
  'admin/settings/(account_summary|change_password|change_mail|change_locale)' => 
  array (
    'file_class' => 'settings',
    'class_method' => 'index',
    'method_arguments' => NULL,
  ),
  'admin/account/(authenticateUser|createUser|changePassword|changeMail|changeLocale)' => 
  array (
    'file_class' => 'account',
    'class_method' => 'index',
    'method_arguments' => NULL,
  ),
  'admin/logout' => 
  array (
    'file_class' => 'logout',
    'class_method' => 'index',
  ),
  'admin/access/action-insert/option-admin.(subdomain_file|account_permission)' => 
  array (
    'file_class' => 'access',
    'class_method' => 'setInsert',
  ),
  'admin/interface' => 
  array (
    'file_class' => 'admin_interface',
    'class_method' => 'index',
  ),
  'admin/commit/action-insert/option-template.(customer|country)' => 
  array (
    'file_class' => 'commit',
    'class_method' => 'setInsert',
  ),
  'admin/interface/template_customer/option-template.customer/view-records' => 
  array (
    'file_class' => 'template_customer',
    'class_method' => 'index',
  ),
  'admin/interface/template_customer/option-template.customer/view-(search_records|table_structure|insert_record)' => 
  array (
    'file_class' => 'template_customer',
    'class_method' => 'index',
  ),
  'admin/interface/template_country/option-template.country/view-records' => 
  array (
    'file_class' => 'template_country',
    'class_method' => 'index',
  ),
  'admin/interface/template_country/option-template.country/view-(search_records|table_structure|insert_record)' => 
  array (
    'file_class' => 'template_country',
    'class_method' => 'index',
  ),
  'pagination_example/page-([1-9]+[0-9]*)' => 
  array (
    'file_class' => 'pagination_example',
    'class_method' => 'index',
  ),
  'admin/locale-(bg|en)' => 
  array (
    'file_class' => 'login',
    'class_method' => 'index',
  ),
  'admin/access/action-update/option-admin.(subdomain_file|account_permission)/id-([1-9]+[0-9]*)' => 
  array (
    'file_class' => 'access',
    'class_method' => 'setUpdate',
  ),
  'admin/access/action-delete/option-admin.(account|subdomain_file|account_permission)/id-([1-9]+[0-9]*)' => 
  array (
    'file_class' => 'access',
    'class_method' => 'setDelete',
  ),
  'admin/commit/action-update/option-template.(customer|country)/id-([1-9]+[0-9]*)' => 
  array (
    'file_class' => 'commit',
    'class_method' => 'setUpdate',
  ),
  'admin/commit/action-delete/option-template.(customer|country)/id-([1-9]+[0-9]*)' => 
  array (
    'file_class' => 'commit',
    'class_method' => 'setDelete',
  ),
  'admin/interface/template_customer/option-template.customer/view-records/page-([1-9]+[0-9]*)' => 
  array (
    'file_class' => 'template_customer',
    'class_method' => 'index',
  ),
  'admin/interface/template_customer/option-template.customer/view-update_record/id-([1-9]+[0-9]*)' => 
  array (
    'file_class' => 'template_customer',
    'class_method' => 'index',
  ),
  'admin/interface/template_country/option-template.country/view-records/page-([1-9]+[0-9]*)' => 
  array (
    'file_class' => 'template_country',
    'class_method' => 'index',
  ),
  'admin/interface/template_country/option-template.country/view-update_record/id-([1-9]+[0-9]*)' => 
  array (
    'file_class' => 'template_country',
    'class_method' => 'index',
  ),
);