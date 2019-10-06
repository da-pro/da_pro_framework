<?php
return [
'doctype' => '
<!doctype html>
<html>
<head>
	<title>[text]</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex, nofollow">
	<meta name="author" content="' . AUTHOR . '">
	<link rel="stylesheet" media="screen" href="/css/reset.css">
	<link rel="stylesheet" media="screen" href="/css/admin.css">
</head>
<body>
[data]
</body>
</html>
',
'script_external' => '
<script src="[src]"></script>
',
'script_inline' => '
<script>
[data]
</script>
',
'a' => '
<a href="[href]" class="[class]">[text]</a>
',
'a_onclick' => '
<a onclick="[onclick]">[text]</a>
',
'span' => '
<span>[text]</span>
',
'h1' => '
<h1>[text]</h1>
',
'p' => '
<p>[text]</p>
',
'form' => '
<form action="[action]" method="[method]" id="[id]">
[elements]
</form>
',
'form_upload' => '
<form action="[action]" method="[method]" id="[id]" enctype="multipart/form-data">
[elements]
</form>
',
'label' => '
<label>[text]</label>
',
'label_for' => '
<label for="[for]">[text]</label>
',
'input_text' => '
<input type="text" name="[name]" value="[value]" maxlength="[maxlength]">
',
'input_password' => '
<input type="password" name="[name]" maxlength="[maxlength]">
',
'input_radio' => '
<input type="radio" name="[name]" value="[value]" id="[id]">
',
'input_radio_checked' => '
<input type="radio" name="[name]" value="[value]" id="[id]" checked>
',
'input_file' => '
<input type="file" name="[name]">
',
'input_hidden' => '
<input type="hidden" name="[name]" value="[value]">
',
'input_button' => '
<input type="button" name="[name]" value="[value]">
',
'textarea' => '
<textarea name="[name]" maxlength="[maxlength]" spellcheck="false">[text]</textarea>
',
'select' => '
<select name="[name]">
[options]
</select>
',
'optgroup' => '
<optgroup label="[label]">
[options]
</optgroup>
',
'option' => '
<option value="[value]">[text]</option>
',
'option_selected' => '
<option value="[value]" selected>[text]</option>
',
'img' => '
<img src="[src]" alt="[alt]" width="[width]" height="[height]">
',
'top_frame' => '
<div id="top-frame">
[data]
</div><!-- top-frame -->
<script src="/js/jquery.js"></script>
',
'bottom_frame' => '
<div id="bottom-frame">
[data]
</div><!-- bottom-frame -->
<script src="/js/admin.js"></script>
',
'section' => '
<section>
[data]
</section>
',
'div_class' => '
<div class="[class]">[data]</div>
',
'div_id' => '
<div id="[id]">
[data]
</div>
',
'nav' => '
<nav>
[data]
</nav>
',
'table' => '
<table id="[id]">
[data]
</table>
',
'tr' => '
<tr>
[data]
</tr>
',
'tr_class' => '
<tr class="[class]">[data]</tr>
',
'tr_data' => '
<tr>
	<th>[th]</th>
	<td>[td]</td>
</tr>
',
'th' => '
<th class="[class]">[text]</th>
',
'td' => '
<td class="[class]">[data]</td>
'
];