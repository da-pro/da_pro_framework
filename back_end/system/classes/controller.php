<?php
class Controller
{
	# name of the requested url
	public static $page_url;

	# name of the controller class file
	public static $file_class;

	# check for admin panel url
	public static $is_admin_path = false;

	# set locale for the admin panel
	public static $admin_locale = ADMIN_LOCALE;

	# string data for the HTML output in the admin panel
	public static $admin_data;

	# import from application/storage/routes.php
	private static $routes = [];

	# populate with key value pairs extracted in the view
	protected $data = [];

	public function __construct()
	{
		self::$page_url = $this->getRequestURI();

		self::setDebug('page_url', self::$page_url);

		$this->setRoutes();
		$this->setQuery();

		$this->getController(self::$page_url);
	}

	public function __set($property, $value)
	{
		$this->$property = $value;
	}

	# get requested uri | remove and filter symbols
	private function getRequestURI()
	{
		if (ENABLE_CYRILLIC_ROUTE)
		{
			if (isset($_SERVER['REQUEST_URI']))
			{
				$_SERVER['REQUEST_URI'] = rawurldecode($_SERVER['REQUEST_URI']);
			}
		}

		$request_uri = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : FORWARD_SLASH;

		if ($request_uri === FORWARD_SLASH || boolval(preg_match('#^' . setKeyValue('page') . '$#', $request_uri)))
		{
			return $request_uri;
		}

		$counter = 0;

		# remove leading forward slash
		while ($request_uri[0] === FORWARD_SLASH)
		{
			++$counter;

			$request_uri = substr($request_uri, 1);
		}

		$leading_forward_slash = ($counter > 1) ? true : false;

		$counter = 0;

		# remove last forward slash
		while (substr($request_uri, -1) === FORWARD_SLASH)
		{
			++$counter;

			$request_uri = substr($request_uri, 0, -1);
		}

		$last_forward_slash = ($counter >= 1) ? true : false;

		if ($leading_forward_slash || $last_forward_slash)
		{
			setLocation($request_uri);
		}

		# check for valid url path
		$valid_path = boolval(preg_match('#^[' . (ENABLE_CYRILLIC_ROUTE ? BULGARIAN_CYRILLIC_PATTERN : null) . URL_PATTERN . ']+$#', $request_uri));

		if ($valid_path)
		{
			return $request_uri;
		}
		else
		{
			setLocation();
		}
	}

	# set routes from file | generate new routes and save to file
	private function setRoutes()
	{
		$routes_filename = realpath(PATH_STORAGE . 'routes' . PHP_FILE_EXTENSION);

		if (ENABLE_REVISION_ROUTE)
		{
			self::$routes = (new Route)->routes;

			if ($handle = fopen($routes_filename, 'r+'))
			{
				ftruncate($handle, 12);

				fseek($handle, 12);

				fwrite($handle, ' ' . var_export(self::$routes, true) . ';');

				fclose($handle);

				self::setErrorPage(201, 'routes file has been revised');
			}
		}
		else
		{
			self::$routes = require_once $routes_filename;
		}

		if (!ENABLE_DEBUG)
		{
			unset(self::$routes[DEBUG_ROUTE]);
		}

		self::setDebug('routes', self::$routes);
	}

	# populate [_GET]
	private function setQuery()
	{
		$array = explode(FORWARD_SLASH, self::$page_url);

		foreach ($array as $value)
		{
			if (boolval(preg_match('/' . KEY_VALUE_SEPARATOR . '/', $value)))
			{
				$key_value_array = explode(KEY_VALUE_SEPARATOR, $value, 2);

				$get_key = $key_value_array[0];
				$get_value = $key_value_array[1];

				$_GET[$get_key] = $get_value;
			}
		}
	}

	# match url to a key from routes
	private function getController($request_uri)
	{
		if (array_key_exists($request_uri, self::$routes))
		{
			$this->getAdministratorPanel($request_uri, self::$routes[$request_uri]['file_class']);

			self::setController(self::$routes[$request_uri]);

			return;
		}

		$parameters = [];

		foreach (self::$routes as $key => $value)
		{
			if (boolval(preg_match('#^' . $key . '$#', $request_uri, $match)))
			{
				if (array_key_exists('method_arguments', $value))
				{
					foreach ($match as $_key => &$_value)
					{
						if (!boolval($_key))
						{
							continue;
						}

						if (empty($_value))
						{
							unset($match[$_key]);
						}
						else
						{
							if (boolval(preg_match('#' . FORWARD_SLASH . '#', $_value)))
							{
								$array = explode(FORWARD_SLASH, $_value);

								$_value = $array[0];
							}
						}
					}

					array_shift($match);

					$array = [];

					foreach ($match as $argument)
					{
						if (!in_array($argument, array_values($_GET)))
						{
							$array[] = $argument;
						}
					}

					$value['method_arguments'] = $array;
				}

				$parameters = $value;

				break;
			}
		}

		if (empty($parameters))
		{
			self::setErrorPage(404);
		}
		else
		{
			$this->getAdministratorPanel($request_uri, $parameters['file_class']);

			self::setController($parameters);
		}
	}

	# check for admin panel | set session | interface files permission
	private function getAdministratorPanel($path, $file_class)
	{
		# check for valid admin url path
		$valid_path = boolval(preg_match('#^' . ADMIN_ROUTE . '.*#', $path));

		if ($valid_path)
		{
			if (session_status() === PHP_SESSION_NONE)
			{
				session_start();
			}

			self::$is_admin_path = true;

			require_once realpath(PATH_FUNCTIONS . 'admin' . PHP_FILE_EXTENSION);

			if (self::$page_url === ADMIN_ROUTE . '/account/authenticateUser')
			{
				return;
			}

			if (isset($_SESSION['authenticate']['username']))
			{
				if (self::$page_url !== ADMIN_ROUTE . '/logout')
				{
					if ($_SESSION['authenticate']['timeout'] < UNIX_TIME)
					{
						setLocation(ADMIN_ROUTE . '/logout');
					}
				}

				# check authenticate permission to access interface files
				if (!isAdministrator())
				{
					$interface_files = getInterfaceFiles();

					if (in_array($file_class, $interface_files))
					{
						if (!array_key_exists($file_class, $_SESSION['authenticate']['permission']))
						{
							setLocation(ADMIN_ROUTE . '/system_properties');
						}
					}
				}

				self::$admin_locale = $_SESSION['authenticate']['locale'];

				self::$admin_data = setTopFrame();
			}
			else
			{
				$valid_login_path = boolval(preg_match('#^' . ADMIN_ROUTE . '(' . setKeyValue('locale', LOCALE_PATTERN) . ')?$#', self::$page_url));

				if (!$valid_login_path)
				{
					setLocation(ADMIN_ROUTE);
				}
			}
		}
	}

	# set error page
	public final static function setErrorPage($code, $message = null)
	{
		self::$is_admin_path = false;

		$parameters = self::$routes[ERROR_ROUTE];
		$parameters['method_arguments'] = [$code, $message];

		self::setController($parameters);

		exit;
	}

	# set controller file class according to the route key
	private static function setController($parameters)
	{
		self::$file_class = $parameters['file_class'];

		if (self::$is_admin_path)
		{
			$path = boolval(preg_match('#^' . ADMIN_INTERFACE_ROUTE . '#', self::$page_url)) ? PATH_CONTROLLER_ADMIN_INTERFACE : PATH_CONTROLLER_ADMIN;
		}
		else
		{
			$path = PATH_CONTROLLER;
		}

		$controller_file = realpath($path . $parameters['file_class'] . PHP_FILE_EXTENSION);

		self::setDebug('controller_file', $controller_file);

		if (file_exists($controller_file))
		{
			require_once $controller_file;

			if (class_exists($parameters['file_class']))
			{
				$controller = new $parameters['file_class'];

				if (method_exists($parameters['file_class'], $parameters['class_method']))
				{
					if (isset($parameters['method_arguments']))
					{
						call_user_func_array([$controller, $parameters['class_method']], $parameters['method_arguments']);
					}
					else
					{
						call_user_func([$controller, $parameters['class_method']]);
					}
				}
				else
				{
					self::setErrorPage(501, 'class method [' . $parameters['class_method'] . '] does not exist');
				}
			}
			else
			{
				self::setErrorPage(501, 'controller class [' . $parameters['file_class'] . '] does not exist');
			}
		}
		else
		{
			self::setErrorPage(404, 'controller file [' . $parameters['file_class'] . '] does not exist');
		}
	}

	# set model object
	protected final function model($class_model, $alias = null)
	{
		if (self::$is_admin_path)
		{
			$path = boolval(preg_match('#^' . ADMIN_INTERFACE_ROUTE . '#', self::$page_url)) ? PATH_MODEL_ADMIN_INTERFACE : PATH_MODEL_ADMIN;
		}
		else
		{
			$path = PATH_MODEL;
		}

		$model_file = realpath($path . strtolower($class_model) . PHP_FILE_EXTENSION);

		self::setDebug('model_files', $model_file, $class_model);

		if (file_exists($model_file))
		{
			require_once $model_file;

			if (class_exists($class_model))
			{
				$model_object = (is_null($alias)) ? strtolower($class_model) : $alias;

				$this->$model_object = new $class_model;
			}
			else
			{
				self::setErrorPage(501, 'model class [' . $class_model . '] does not exist');
			}
		}
		else
		{
			self::setErrorPage(404, 'model file [' . strtolower($class_model) . '] does not exist');
		}
	}

	# set static model object
	public final static function frames($class_model)
	{
		$model_file = realpath(PATH_MODEL . strtolower($class_model) . PHP_FILE_EXTENSION);

		self::setDebug('model_files', $model_file, $class_model);

		if (file_exists($model_file))
		{
			require_once $model_file;

			if (class_exists($class_model))
			{
				return new $class_model;
			}
		}
	}

	# set HTML template files to generate a web page
	protected final function view()
	{
		$this->data[REVISION] = require_once realpath(PATH_STORAGE . 'revision' . PHP_FILE_EXTENSION);

		extract($this->data);

		if (array_key_exists(TITLE, $this->data))
		{
			${TITLE} = ${TITLE} . ' | ' . SITE_NAME;
		}
		else
		{
			${TITLE} = SITE_NAME;
		}

		if (array_key_exists(BODY, $this->data))
		{
			${BODY}[CSS_CLASS] = array_key_exists(CSS_CLASS, ${BODY}) ? ${BODY}[CSS_CLASS] : '';
			${BODY}[VALID_FILE_ARRAY] = [];
			${BODY}[INVALID_FILE_ARRAY] = [];

			foreach (${BODY} as $key => $files)
			{
				if (in_array($key, [CSS_CLASS, VALID_FILE_ARRAY, INVALID_FILE_ARRAY]))
				{
					continue;
				}

				$path = ($key === IMPORT) ? PATH_VIEW : PATH_VIEW_FRAMES;

				foreach ($files as $value)
				{
					$file = realpath($path . $value . PHP_FILE_EXTENSION);

					if (file_exists($file))
					{
						${BODY}[VALID_FILE_ARRAY][] = $file;

						self::setDebug('view_files', $file, $key . ' : ' . $value);
					}
					else
					{
						${BODY}[INVALID_FILE_ARRAY][] = basename($path) . FORWARD_SLASH . $value . PHP_FILE_EXTENSION;
					}
				}
			}

			if (empty(${BODY}[INVALID_FILE_ARRAY]))
			{
				require realpath(PATH_VIEW_TEMPLATES . 'html_head' . PHP_FILE_EXTENSION);
				require realpath(PATH_VIEW_TEMPLATES . 'html_body' . PHP_FILE_EXTENSION);
			}
			else
			{
				self::setErrorPage(404, implode(',', ${BODY}[INVALID_FILE_ARRAY]) . ' does not exist');
			}
		}
	}

	# populate [_SESSION['debug']]
	public final static function setDebug($key, $value, $sub_key = null)
	{
		if (ENABLE_DEBUG)
		{
			if (session_status() === PHP_SESSION_NONE)
			{
				session_start();
			}

			if (self::$page_url === DEBUG_ROUTE)
			{
				return;
			}

			if ($key === 'page_url')
			{
				if (isset($_SESSION['debug']))
				{
					unset($_SESSION['debug']);
				}
			}

			if (is_null($sub_key))
			{
				$_SESSION['debug'][$key] = $value;
			}
			else
			{
				if (is_array($sub_key))
				{
					$_SESSION['debug'][$key][] = $value;
				}
				else
				{
					$_SESSION['debug'][$key][$sub_key] = $value;
				}
			}
		}
	}
}