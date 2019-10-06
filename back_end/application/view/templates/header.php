<header>
	<nav>
		<li></li>
		<li></li>
		<li></li>
	</nav>
	<nav>
		<li><a href="/"<?= setActive([FORWARD_SLASH]) ?>>home</a></li>
		<li>
			<a href="/documentation"<?= setActive(['documentation'], 'left') ?>>documentation</a>
			<ul class="left">
				<a href="/documentation#model">model</a>
				<a href="/documentation#view">view</a>
				<a href="/documentation#controller">controller</a>
				<a href="/documentation#storage">storage</a>
				<a href="/documentation#routes">routes</a>
				<a href="/documentation#configuration">configuration</a>
				<a href="/documentation#administrator_panel">administrator panel</a>
			</ul>
		</li>
		<li><a href="/form_elements"<?= setActive(['form_elements']) ?>>form elements</a></li>
		<li><a href="/utility"<?= setActive(['utility']) ?>>utility</a></li>
		<li><a href="/pagination_example"<?= setActive(['pagination_example']) ?>>pagination example</a></li>
		<li><a href="/rules_css"<?= setActive(['rules_css']) ?>>rules css</a></li>
		<li><a href="/rules_html"<?= setActive(['rules_html']) ?>>rules html</a></li>
		<li><a href="/color_palette"<?= setActive(['color_palette']) ?>>color palette</a></li>
		<li>
			<a href="/admin"<?= setActive([], 'right') ?> target="_blank">admin</a>
			<ul class="right">
				<a href="/admin/system_properties" target="_blank">system properties</a>
				<a href="/admin/settings/account_summary" target="_blank">account summary</a>
				<a href="/admin/logout">logout</a>
			</ul>
		</li>
	</nav>
</header>
<script src="/js/jquery.js?<?= ${REVISION} ?>"></script>
<script src="/js/common.js?<?= ${REVISION} ?>"></script>
