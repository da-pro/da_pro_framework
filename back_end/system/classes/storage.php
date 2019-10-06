<?php
final class Storage
{
	# return array for XML object and path
	public static function XML($filename, $is_path = true, $options = 0)
	{
		$xml_file = realpath(PATH_XML . $filename . XML_FILE_EXTENSION);

		if (file_exists($xml_file))
		{
			return [
				'object' => new SimpleXMLElement($xml_file, $options, $is_path),
				'file_path' => $xml_file
			];
		}
	}

	# return array from JSON
	public static function openJSON($source_path, $is_url = true)
	{
		if ($is_url)
		{
			$json = file_get_contents($source_path);
		}
		else
		{
			$json_file = realpath(PATH_JSON . $source_path . JSON_FILE_EXTENSION);

			if (file_exists($json_file))
			{
				$json = file_get_contents($json_file);
			}
		}

		return (isset($json)) ? json_decode($json, true) : [];
	}

	# save JSON file
	public static function saveJSON($filename, $json_object)
	{
		$json_file = realpath(PATH_JSON . $filename . JSON_FILE_EXTENSION);

		if (file_exists($json_file))
		{
			file_put_contents($json_file, $json_object);
		}
	}
}