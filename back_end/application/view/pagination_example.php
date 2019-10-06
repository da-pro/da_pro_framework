<h1>Da Pro Framework - Pagination Example</h1>

<section>
<?= $pagination['links'] ?>
</section>
<table>
	<caption>Customers</caption>
	<tr>
		<th>#</th><th>Name</th><th>Country</th><th>City</th><th>Address</th>
	</tr>
<?php foreach ($pagination['results'] as $value): ?>
	<tr>
		<td class="center bold"><?= $value['customer_id'] ?></td>
		<td><?= $value['customer_name'] ?></td>
		<td><?= $value['country'] ?></td>
		<td class="right"><?= $value['city'] ?></td>
		<td><?= $value['address'] ?></td>
	</tr>
<?php endforeach; ?>
</table>
<section>
<?= $pagination['links'] ?>
</section>
