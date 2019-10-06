<h1>Da Pro Framework - Home</h1>

<div class="table">
	<div class="row">
		<div class="_4 _2_">
			<h2><a href="/documentation#model">Model</a></h2>
		</div>
		<div class="_4 _2_">
			<h2><a href="/documentation#view">View</a></h2>
		</div>
		<div class="_4 _2_">
			<h2><a href="/documentation#controller">Controller</a></h2>
		</div>
	</div>

	<div class="row">
		<div class="_4 _2_">
			<h3>Web Server</h3>
			<span><?= $web_server ?></span>
		</div>
		<div class="_4 _2_">
			<h3>Server Side Scripting Language</h3>
			<span><?= $server_side_scripting_language ?></span>
		</div>
		<div class="_4 _2_">
			<h3>Database Management System</h3>
			<span><?= $database_management_system ?></span>
		</div>
	</div>

	<div class="row">
		<div class="_3 _1_"></div>
		<div class="_6 _4_">
			<h2><a href="/phpmyadmin" target="_blank">phpMyAdmin</a></h2>
		</div>
		<div class="_3 _1_"></div>
	</div>
</div>

<table>
	<caption>Accounts</caption>
	<tr>
		<th>#</th><th>Username</th><th>Profile</th><th>E-mail</th>
	</tr>
<?php foreach ($accounts as $value): ?>
	<tr>
		<td class="center bold"><?= $value['account_id'] ?></td>
		<td><?= $value['username'] ?></td>
		<td><?= $value['profile'] ?></td>
		<td><?= $value['e_mail'] ?></td>
	</tr>
<?php endforeach; ?>
</table>
