<?php
# return CSS class
function setActive(array $array, string $dropdown = null) : string
{
	$css_class = [];

	foreach ($array as $value)
	{
		if (boolval(preg_match('#^' . $value . '.*#', Controller::$page_url)))
		{
			$css_class[] = 'active';

			break;
		}
	}

	if (!is_null($dropdown))
	{
		$css_class[] = $dropdown;
	}

	if (empty($css_class))
	{
		return '';
	}
	else
	{
		return ' class="' . implode(' ', $css_class) . '"';
	}
}