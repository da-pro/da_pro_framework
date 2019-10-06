<?php
final class Commit extends Controller
{
	private $message;

	public function __construct()
	{
		if (!isAJAXRequest())
		{
			setLocation();
		}

		$this->model('Commit_Model', 'commit');
	}

	private function proceed($is_post = true)
	{
		if (!isAdministrator())
		{
			$access_denied = true;

			foreach ($_SESSION['authenticate']['permission'] as $value)
			{
				if (in_array($_GET['option'], $value['commit']))
				{
					if ($value[$_GET['action']])
					{
						$access_denied = false;
					}

					break;
				}
			}

			if ($access_denied)
			{
				setLocation(ADMIN_ROUTE . '/system_properties');
			}
		}

		if ($is_post)
		{
			if (empty($_POST))
			{
				$this->commit->errors[] = locale('all_fields_are_required');

				return false;
			}
		}

		return true;
	}

	public function setInsert()
	{
		if ($this->proceed())
		{
			$this->commit->setInsert($_GET['option']);
		}
	}

	public function setUpdate()
	{
		if ($this->proceed())
		{
			$this->commit->setUpdate($_GET['option'], intval($_GET['id']));
		}
	}

	public function setDelete()
	{
		if (isset($_POST['message']))
		{
			$this->message = locale('text_delete_record');
		}
		else
		{
			if ($this->proceed(false))
			{
				$this->commit->setDelete($_GET['option'], intval($_GET['id']));
			}
		}
	}

	public function __destruct()
	{
		if (is_null($this->message) && empty($this->commit->errors))
		{
			if (!empty($this->commit->query) && !empty($this->commit->bind_value))
			{
				$this->commit->execute();
			}
		}

		$response = [];

		if (!empty($this->message))
		{
			$response['message'] = $this->message;
		}

		if (!empty($this->commit->errors))
		{
			$response['errors'] = $this->commit->errors;
		}

		if (!empty($this->commit->location))
		{
			$response['location'] = $this->commit->location;
		}

		Output::json($response);
	}
}