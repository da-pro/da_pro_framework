<body class="<?= ${BODY}[CSS_CLASS] ?>">
<div id="outer-frame">
<?php require 'header.php'; ?>
<div id="inner-frame">
<?php
foreach (${BODY}[VALID_FILE_ARRAY] as $value)
{
	require $value;
}
?>
</div><!-- inner-frame -->
<?php require 'footer.php'; ?>
</div><!-- outer-frame -->
<?= getDebug() ?>
</body>
</html>