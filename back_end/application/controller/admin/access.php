<?php
final class Access extends Controller
{
	private $message;

	public function __construct()
	{
		if (!isAJAXRequest())
		{
			setLocation();
		}

		$this->model('Access_Model', 'access');
	}

	private function proceed($is_post = true)
	{
		if (!isAdministrator())
		{
			$this->access->errors[] = locale('invalid_access');

			return false;
		}

		if ($is_post)
		{
			if (empty($_POST))
			{
				$this->access->errors[] = locale('all_fields_are_required');

				return false;
			}
		}

		return true;
	}

	public function setInsert()
	{
		if (isset($_POST['message']))
		{
			switch ($_GET['option'])
			{
				case SUBDOMAIN_FILE_DATABASE_TABLE_NAME:
					$this->message = locale('text_insert_commit_option');
				break;

				case ACCOUNT_PERMISSION_DATABASE_TABLE_NAME:
					$this->message = locale('text_insert_permission');
				break;
			}
		}
		else
		{
			if ($this->proceed())
			{
				$this->access->setInsert($_GET['option']);
			}
		}
	}

	public function setUpdate()
	{
		if (isset($_POST['message']))
		{
			switch ($_GET['option'])
			{
				case SUBDOMAIN_FILE_DATABASE_TABLE_NAME:
					$this->message = locale('text_update_commit_option');
				break;

				case ACCOUNT_PERMISSION_DATABASE_TABLE_NAME:
					$this->message = locale('text_update_permission');
				break;
			}
		}
		else
		{
			if ($this->proceed())
			{
				$this->access->setUpdate($_GET['option'], intval($_GET['id']));
			}
		}
	}

	public function setDelete()
	{
		if (isset($_POST['message']))
		{
			switch ($_GET['option'])
			{
				case ACCOUNT_DATABASE_TABLE_NAME:
					$this->message = locale('text_delete_user');
				break;

				case SUBDOMAIN_FILE_DATABASE_TABLE_NAME:
					$this->message = locale('text_delete_file');
				break;

				case ACCOUNT_PERMISSION_DATABASE_TABLE_NAME:
					$this->message = locale('text_delete_permission');
				break;
			}
		}
		else
		{
			if ($this->proceed(false))
			{
				$this->access->setDelete($_GET['option'], intval($_GET['id']));
			}
		}
	}

	public function __destruct()
	{
		if (isAJAXRequest())
		{
			if (is_null($this->message) && empty($this->access->errors))
			{
				if (!empty($this->access->query) && !empty($this->access->bind_value))
				{
					$this->access->execute();
				}
			}

			$response = [];

			if (!empty($this->message))
			{
				$response['message'] = $this->message;
			}

			if (!empty($this->access->errors))
			{
				$response['errors'] = $this->access->errors;
			}

			if (!empty($this->access->location))
			{
				$response['location'] = $this->access->location;
			}

			Output::json($response);
		}
	}
}