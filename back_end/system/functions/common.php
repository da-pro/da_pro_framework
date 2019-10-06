<?php
# register autoloader for spl_autoload_register function
function getUndefinedClass($file_class)
{
	require_once realpath(PATH_CLASSES . $file_class . PHP_FILE_EXTENSION);
}

# return [path]
function getLocation($path = null, $absolute_url = false)
{
	$location = ($absolute_url) ? $path : FORWARD_SLASH . $path;

	return $location;
}

# redirect to [path]
function setLocation($path = null, $absolute_url = false)
{
	$location = getLocation($path, $absolute_url);

	header('location: ' . $location);

	exit;
}

# create /[key]-[value] pair for url
function setKeyValue($key, $value = null) : string
{
	$string = FORWARD_SLASH . $key . KEY_VALUE_SEPARATOR;
	$string .= (is_null($value)) ? POSITIVE_INTEGER_PATTERN : $value;

	return $string;
}

# return HTML tag
function parse($key, $array = [], $end_of_line = false)
{
	static $templates;

	if (is_null($templates))
	{
		$templates = require_once realpath(PATH_SYSTEM . 'templates' . PHP_FILE_EXTENSION);
	}

	if (array_key_exists($key, $templates))
	{
		$string = $templates[$key];

		if (is_string($string) && !empty($string))
		{
			foreach ($array as $key => $value)
			{
				if (!boolval(preg_match('/\[' . $key . '\]/', $string)))
				{
					setDebugBacktrace('FALSE PARSE KEY', $key);
				}

				$search = '[' . $key . ']';
				$replace = $value;

				if (is_null($value))
				{
					if (boolval(preg_match('/ ' . $key . '="\[' . $key . '\]"/', $string)))
					{
						$search = ' ' . $key . '="[' . $key . ']"';
					}
					else
					{
						$search = '[' . $key . ']';
					}

					$replace = '';
				}

				$string = str_replace($search, $replace, $string);
			}

			return ($end_of_line) ? trim($string) . PHP_EOL : trim($string);
		}
		else
		{
			setDebugBacktrace('INVALID PARSE STRING IN TEMPLATES FILE', $key);
		}
	}
	else
	{
		setDebugBacktrace('FALSE PARSE NAME', $key);
	}
}

# return locale string in admin panel
function locale($key)
{
	static $languages;

	if (is_null($languages))
	{
		$languages = require_once realpath(PATH_SYSTEM . 'languages' . PHP_FILE_EXTENSION);
	}

	if (array_key_exists($key, $languages))
	{
		$array = $languages[$key];

		if (is_array($array) && array_key_exists(Controller::$admin_locale, $array))
		{
			return $array[Controller::$admin_locale];
		}
		else
		{
			setDebugBacktrace('INVALID LOCALE ARRAY IN LANGUAGES FILE', $key);
		}
	}
	else
	{
		setDebugBacktrace('FALSE LOCALE NAME', $key);
	}
}

# create global variable if an error occurs
function setDebugBacktrace($type, $key)
{
	$debug = debug_backtrace();

	$line = $debug[1]['line'];
	$file = $debug[1]['file'];

	$GLOBALS['debug'][] = [
		'type' => $type,
		'key' => $key,
		'line' => $line,
		'file' => $file
	];
}

# return array appropriate for debug
function printArray(array $array)
{
	return trim(preg_replace('/(Array\n\(|\n\))/', '', print_r($array, true)), PHP_EOL);
}

# return whether request is AJAX
function isAJAXRequest() : bool
{
	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
	{
		return true;
	}

	return false;
}

# return debug link if [ENABLE_DEBUG]
function getDebug() : string
{
	$debug = '';

	if (ENABLE_DEBUG)
	{
		if (Controller::$page_url !== DEBUG_ROUTE)
		{
			$href_debug = [
				'href' => FORWARD_SLASH . DEBUG_ROUTE . '" target="_blank',
				'class' => 'debug',
				'text' => 'debug'
			];

			$debug = parse('a', $href_debug, true);
		}
	}

	return $debug;
}