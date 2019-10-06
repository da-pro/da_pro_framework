<?php
final class Output
{
	const APPLICATION = [
		'HTML' => 'text/html',
		'JSON' => 'application/json',
		'XML' => 'application/xml'
	];

	# use string data to generate HTML web page
	public static function html($string)
	{
		header('content-type: ' . self::APPLICATION['HTML']);

		$data = (empty($GLOBALS['debug'])) ? Controller::$admin_data : setDebug($GLOBALS['debug']);

		echo parse('doctype', ['text' => locale('document_title_panel') . $string, 'data' => $data]);
	}

	# use array data to output JSON data
	public static function json($array)
	{
		header('content-type: ' . self::APPLICATION['JSON']);

		echo json_encode($array);
	}

	# use string data to output XML data
	public static function xml($string)
	{
		header('content-type: ' . self::APPLICATION['XML']);

		echo $string;
	}
}