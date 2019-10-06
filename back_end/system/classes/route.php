<?php
final class Route
{
	# generate routes from application/system/routes files
	public $routes = [];

	public function __construct()
	{
		$client_side = require_once realpath(PATH_ROUTES . 'client_side' . PHP_FILE_EXTENSION);
		$server_side = require_once realpath(PATH_ROUTES . 'server_side' . PHP_FILE_EXTENSION);

		$routes = array_merge($client_side, $server_side);

		foreach ($routes as $key => $value)
		{
			if (array_key_exists('required_arguments', $value))
			{
				$this->setRequiredArguments($routes, $key, $value);

				continue;
			}

			if (array_key_exists('optional_arguments', $value))
			{
				$this->setOptionalArguments($routes, $key, $value);
			}
		}

		$this->routes = $routes;
	}

	# alter the route key to add values
	private function setRequiredArguments(&$array, $key, $value)
	{
		$route = $key;

		foreach ($value['required_arguments'] as $_key => $_value)
		{
			if ($_value === 'pagination')
			{
				$route .= setKeyValue('page');

				continue;
			}

			if (is_integer($_key))
			{
				$route .= setKeyValue($_value);
			}
			else
			{
				$route .= setKeyValue($_key, $_value);
			}
		}

		unset($array[$key], $value['required_arguments']);

		$array[$route] = $value;

		if (array_key_exists('optional_arguments', $value))
		{
			$this->setOptionalArguments($array, $route, $value);
		}
	}

	# alter and preserve the route key to add values
	private function setOptionalArguments(&$array, $key, $value)
	{
		$route = ($key === DEFAULT_ROUTE) ? null : $key;

		$parameters_array = [];

		foreach ($value['optional_arguments'] as $_key => $_value)
		{
			if ($_value === 'pagination')
			{
				$parameters_array[] = $route . setKeyValue('page');

				continue;
			}

			if (is_integer($_key))
			{
				$parameters_array[] = $route . setKeyValue($_value);
			}
			else
			{
				$parameters_array[] = $route . setKeyValue($_key, $_value);
			}
		}

		unset($array[$key]['optional_arguments'], $value['optional_arguments']);

		foreach ($parameters_array as $_value)
		{
			$array[$_value] = $value;
		}
	}
}