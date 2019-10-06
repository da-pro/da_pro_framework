<?php
final class Account extends Controller
{
	public function __construct()
	{
		if (!isAJAXRequest())
		{
			setLocation();
		}

		$this->model('Account_Model', 'account');
	}

	public function index($option)
	{
		$proceed = true;

		if ($option === 'createUser')
		{
			if (!isAdministrator())
			{
				$proceed = false;
			}
		}

		if ($proceed && method_exists($this->account, $option))
		{
			$this->account->setTableColumn(ACCOUNT_DATABASE_TABLE_NAME);

			$this->account->{$option}();
		}
		else
		{
			$this->account->errors[] = locale('invalid_access');
		}
	}

	public function __destruct()
	{
		$response = [];

		if (!empty($this->account->errors))
		{
			$response['errors'] = $this->account->errors;
		}

		if (!empty($this->account->location))
		{
			$response['location'] = $this->account->location;
		}

		Output::json($response);
	}
}