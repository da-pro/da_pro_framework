<?php
final class Custom_Error extends Controller
{
	public function __construct()
	{
		$this->data = [
			TITLE => 'Custom Error',
			BODY => [
				IMPORT => ['custom_error']
			]
		];
	}

	public function index($error_code = 500, $error_message = null)
	{
		if (isset($_GET['error']))
		{
			if (boolval(preg_match('/' . $_GET['error'] . '/', ERROR_ROUTE)))
			{
				$error_code = intval($_GET['error']);
			}
		}

		switch ($error_code)
		{
			case 400:
				$message = 'bad request';
			break;

			case 401:
				$message = 'authorization required';
			break;

			case 403:
				$message = 'access denied';
			break;

			case 404:
				$message = 'page not found';
			break;

			case 500:
				$message = 'internal server error';
			break;

			default:
				$message = 'error page';
		}

		$this->data['error_code'] = $error_code;

		$this->data['message'] = (is_null($error_message)) ? $message : $error_message;

		$this->view();
	}
}