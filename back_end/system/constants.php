<?php
define('COMPILER_ERROR_REPORTING', E_ALL);
define('COMPILER_DISPLAY_ERRORS', 1);

define('ENABLE_DIRECTORY_STRUCTURE', false);
define('ENABLE_REVISION_ROUTE', false);
define('ENABLE_CYRILLIC_ROUTE', false);
define('ENABLE_DEBUG', false);

define('DATABASE_ENGINE', 'mysql:');
define('DSN_USERNAME', '');
define('DSN_PASSWORD', '');

define('ACCOUNT_DATABASE_TABLE_NAME', 'admin.account');
define('SUBDOMAIN_FILE_DATABASE_TABLE_NAME', 'admin.subdomain_file');
define('ACCOUNT_PERMISSION_DATABASE_TABLE_NAME', 'admin.account_permission');

define('FORWARD_SLASH', '/');
define('PATH_SYSTEM', __DIR__ . FORWARD_SLASH);
define('PATH_CLASSES', PATH_SYSTEM . 'classes/');
define('PATH_FUNCTIONS', PATH_SYSTEM . 'functions/');
define('PATH_ROUTES', PATH_SYSTEM . 'routes/');
define('PATH_APPLICATION', dirname(PATH_SYSTEM) . '/application/');
define('PATH_CONTROLLER', PATH_APPLICATION . 'controller/');
define('PATH_CONTROLLER_ADMIN', PATH_CONTROLLER . 'admin/');
define('PATH_CONTROLLER_ADMIN_INTERFACE', PATH_CONTROLLER_ADMIN . 'interface/');
define('PATH_MODEL', PATH_APPLICATION . 'model/');
define('PATH_MODEL_ADMIN', PATH_MODEL . 'admin/');
define('PATH_MODEL_ADMIN_INTERFACE', PATH_MODEL_ADMIN . 'interface/');
define('PATH_VIEW', PATH_APPLICATION . 'view/');
define('PATH_VIEW_TEMPLATES', PATH_VIEW . 'templates/');
define('PATH_VIEW_FRAMES', PATH_VIEW . 'frames/');
define('PATH_STORAGE', PATH_APPLICATION . 'storage/');
define('PATH_XML', PATH_STORAGE . 'xml/');
define('PATH_JSON', PATH_STORAGE . 'json/');
define('PATH_SESSION', '');
define('PATH_SAVE_SESSION', PATH_SESSION . '/sess_');
define('CURRENT_DIRECTORY', '.');
define('PARENT_DIRECTORY', '..');

define('PHP_FILE_EXTENSION', '.php');
define('XML_FILE_EXTENSION', '.xml');
define('JSON_FILE_EXTENSION', '.json');

define('KEY_VALUE_SEPARATOR', '-');

define('POSITIVE_INTEGER_PATTERN', '([1-9]+[0-9]*)');
define('URL_PATTERN', 'A-Za-z0-9-_.' . FORWARD_SLASH);
define('BULGARIAN_CYRILLIC_PATTERN', 'АБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЬЮЯабвгдежзийклмнопрстуфхцчшщъьюя');
define('SEARCH_PATTERN', 'A-Za-z0-9 ');
define('LOCALE_PATTERN', '(bg|en)');

define('DEFAULT_ROUTE', FORWARD_SLASH);
define('ERROR_ROUTE', 'error' . KEY_VALUE_SEPARATOR . '(400|401|403|404|500)');
define('DEBUG_ROUTE', 'debug');
define('ADMIN_ROUTE', 'admin');
define('ADMIN_INTERFACE_ROUTE', ADMIN_ROUTE . '/interface/');
define('PATH_ADMIN_INTERFACE', FORWARD_SLASH . ADMIN_INTERFACE_ROUTE);

define('REVISION', '_' . md5('revision'));
define('TITLE', '_' . md5('title'));
define('BODY', '_' . md5('body'));
define('CSS_CLASS', 'css_class');
define('IMPORT', 'import');
define('FRAMES', 'frames');
define('VALID_FILE_ARRAY', 'valid_file_array');
define('INVALID_FILE_ARRAY', 'invalid_file_array');

define('ADMIN_LOCALE', 'en');
define('UNIX_TIME', time());
# CUSTOM
define('SUBDOMAIN_NAME', 'localhost');
define('SITE_NAME', 'Da Pro Framework');
define('DESCRIPTION', 'MVC framework builded for small size websites');
define('KEYWORDS', 'Da Pro Framework, Darin Prodanov, Дарин Проданов');
define('AUTHOR', 'Darin Prodanov, Дарин Проданов');
define('COPYRIGHT', 'Da Pro &copy;2016-' . date('Y'));