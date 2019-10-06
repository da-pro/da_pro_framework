<?php
require_once 'constants.php';

# errors
ini_set('error_reporting', COMPILER_ERROR_REPORTING);
ini_set('display_errors', COMPILER_DISPLAY_ERRORS);

# sessions
ini_set('session.save_path', PATH_SESSION);
ini_set('session.sid_length', 40);
ini_set('session.sid_bits_per_character', 5);

require_once realpath(PATH_FUNCTIONS . 'common' . PHP_FILE_EXTENSION);

spl_autoload_register('getUndefinedClass');

if (ENABLE_DIRECTORY_STRUCTURE)
{
	new Directory_Structure;

	exit;
}

require_once realpath(PATH_FUNCTIONS . 'custom' . PHP_FILE_EXTENSION);