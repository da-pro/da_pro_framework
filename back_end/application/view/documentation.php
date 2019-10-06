<h1>Da Pro Framework - Documentation</h1>

<h2>Overview</h2>
<p>MVC framework with a front controller <span>index.php</span>.</p>
<p>Easy to implement <span>pagination</span>.</p>
<p>All possible URLs assigned in <span>routes</span>.</p>
<p>Developed with responsive design.</p>

<br id="model">
<h2>Model</h2>
<p><span>$this->model(</span>'Index_Model', 'index'<span>);</span> will load model/index_model.php, with <span>$this->index</span> being the object.</p>
<p><span>$this->getRow(</span>[arguments]<span>);</span> and <span>$this->getRows(</span>[arguments]<span>);</span> will get a result from the database with the provided query.</p>
<p><span>$this->set(</span>[arguments]<span>);</span> will update the database with the provided query.</p>

<br id="view">
<h2>View</h2>
<p>The view files will be included inside the body.</p>
<p>The document consist of template files for the <span>head</span> and <span>body</span>, including <span>header</span> and <span>footer</span>.</p>
<p>The <span>view/frames</span> holds files that are needed by multiple pages.</p>

<br id="controller">
<h2>Controller</h2>
<p>You add html title and files to be loaded in the body of the document with the 'data' array property like this:</p>
<pre>
$this->data = [
	TITLE => 'Color Palette',
	BODY => [
		CSS_CLASS => 'b-66bb66',
		IMPORT => ['color_palette']
	]
];
</pre>
<p>Additional keys can be added to the array, which will be extracted as variables when the <span>'view'</span> method is called.</p>
<p><span>'TITLE'</span> key will be printed in the <span>'title'</span> tag.</p>
<p><span>'BODY => CSS_CLASS'</span> is optional, its purpose is to be able to change the class of the <span>'body'</span> tag.</p>
<p><span>'BODY => IMPORT'</span> will include the files in the corresponding array.</p>
<p><span>'BODY => FRAMES'</span> will include the files that are commonly used as a layout in multiple pages, they need to be put in the <span>view/frames</span> folder.</p>
<p>Controller::<span>$page_url;</span> is the requested path without leading or trailing forward slashes.</p>
<p>Controller::<span>$file_class;</span> is the physical file that holds the controller of the same name and class.</p>
<p>Controller::<span>frames(</span>[arguments]<span>);</span> is a method to load models in the <span>view/frames</span> folder.</p>

<br id="storage">
<h2>Storage</h2>
<p>Storage::<span>XML(</span>[arguments]<span>);</span> is a method to handle XML files from <span>storage/xml</span> folder.</p>
<p>Storage::<span>openJSON(</span>[arguments]<span>);</span> is a method to open URL or local JSON files from <span>storage/json</span> folder.</p>
<p>Storage::<span>saveJSON(</span>[arguments]<span>);</span> is a method to save local JSON files in <span>storage/json</span> folder.</p>

<br id="routes">
<h2>Routes</h2>
<p><span>'method_arguments'</span> will be passed as arguments for controller methods, when not a valid <span>key-value</span> in the URL.</p>
<p><span>'required_arguments'</span> will alter the route key itself, removing the initial route key, for example adding key value pairs.</p>
<p><span>'optional_arguments'</span> will alter the route key itself, but preserving the initial route key, for example adding key value pairs.</p>
<pre>
DEFAULT_ROUTE => [
	'file_class' => 'index',
	'class_method' => 'index'
]

ERROR_ROUTE => [
	'file_class' => 'custom_error',
	'class_method' => 'index'
]

DEBUG_ROUTE => [
	'file_class' => 'debug',
	'class_method' => 'index'
]

'pagination_example' => [
	'file_class' => 'pagination_example',
	'class_method' => 'index',
	'optional_arguments' => ['pagination']
]

'rules_css' => [
	'file_class' => 'rules',
	'class_method' => 'css'
]

'rules_html' => [
	'file_class' => 'rules',
	'class_method' => 'html'
]

'guide/
(Legion/(The_Emerald_Nightmare|The_Nighthold))|
(Warlords_of_Draenor/(Hellfire_Citadel|Blackrock_Foundry|Highmaul|World_Bosses))|
(Mists_of_Pandaria/(Siege_of_Orgrimmar|Throne_of_Thunder|Terrace_of_Endless_Spring|Heart_of_Fear|Mogu_shan_Vaults|World_Bosses))|
(Cataclysm/(Dragon_Soul|Firelands|Throne_of_the_Four_Winds|The_Bastion_of_Twilight|Blackwing_Descent|Baradin_Hold))
' => [
	'file_class' => 'guide',
	'class_method' => 'index',
	'method_arguments' => null
]

ADMIN_ROUTE . '/settings/(account_summary|change_password|change_mail|change_locale)' => [
	'file_class' => 'settings',
	'class_method' => 'index',
	'method_arguments' => null
]

ADMIN_ROUTE . '/commit/action-insert/option-template.(customer|country)' => [
	'file_class' => 'commit',
	'class_method' => 'setInsert'
]

ADMIN_ROUTE . '/commit/action-update/option-template.(customer|country)' => [
	'file_class' => 'commit',
	'class_method' => 'setUpdate',
	'required_arguments' => ['id']
]

ADMIN_INTERFACE_ROUTE . 'template_customer/option-template.customer/view-(search_records|table_structure|insert_record)' => [
	'file_class' => 'template_customer',
	'class_method' => 'index'
]
</pre>

<br id="configuration">
<h2>Configuration</h2>
<p><span>ENABLE_DIRECTORY_STRUCTURE</span> [boolean] - enable this to print site structure and <span>PowerShell</span> commands to change file and folders dates.</p>
<p><span>ENABLE_REVISION_ROUTE</span> [boolean] - enable this if there are changes in the routes folder to update <span>storage/routes.php</span> file.</p>
<p><span>ENABLE_CYRILLIC_ROUTE</span> [boolean] - enable this if using cyrillic URLs in routes.</p>
<p><span>ENABLE_DEBUG</span> [boolean] - enable this to display set of data for the requested page and routes in <span>/debug</span> URL.</p>

<br id="administrator_panel">
<h2>Administrator Panel</h2>
<p><span>/admin</span> URL is the default path for the Administrator Panel, which will lead to a login page if not authenticated.</p>
<p>Authentication is done through <span>$_SESSION['authenticate']</span> on successful login, which is an associative array.</p>
<p><span>Profile</span> can be of <span>administrator</span> or <span>moderator</span> type, the latter has less privileges.</p>
<p><span>Locale</span> can be <span>english [en]</span> or <span>bulgarian [bg]</span>, the default is <span>[<?= ADMIN_LOCALE ?>]</span>.</p>
<p><span>/admin/interface</span> URL is where the interface URLs are displayed.</p>
<p><span>controller/admin</span> folder contains the controller files for the Administrator Panel.</p>
<p><span>controller/admin/interface</span> folder contains the controller files for the Administrator Panel interface, specific for the site.</p>
<p><span>model/admin</span> folder contains the model files for the Administrator Panel.</p>
<p><span>model/admin/interface</span> folder contains the model files for the Administrator Panel interface, specific for the site.</p>
