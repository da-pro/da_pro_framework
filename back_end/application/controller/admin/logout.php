<?php
final class Logout extends Controller
{
	public function __construct(){}

	public function index()
	{
		if (!empty($_SESSION))
		{
			$session_keys = array_keys($_SESSION);

			foreach ($session_keys as $value)
			{
				unset($_SESSION[$value]);
			}

			setLocation(ADMIN_ROUTE);
		}
	}
}