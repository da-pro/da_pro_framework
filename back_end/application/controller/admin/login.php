<?php
final class Login extends Controller
{
	public function __construct()
	{
		if (isset($_SESSION['authenticate']['username']))
		{
			setLocation(ADMIN_ROUTE . '/system_properties');
		}

		$this->model('Account_Model', 'account');

		$this->account->setTableColumn(ACCOUNT_DATABASE_TABLE_NAME);
	}

	public function index()
	{
		if (isset($_GET['locale']))
		{
			parent::$admin_locale = $_GET['locale'];
		}

		$path = FORWARD_SLASH . ADMIN_ROUTE . FORWARD_SLASH;

		$href_locale_bg = [
			'href' => $path . 'locale-bg',
			'class' => null,
			'text' => 'бъл'
		];
		$href_locale_en = [
			'href' => $path . 'locale-en',
			'class' => null,
			'text' => 'eng'
		];

		$href = '';
		$href .= parse('a', $href_locale_bg, true);
		$href .= parse('a', $href_locale_en);

		$nav = parse('nav', ['data' => $href]);

		$top_frame = parse('top_frame', ['data' => $nav], true);

		$json['locale'] = parent::$admin_locale;

		$object_literal = parse('script_inline', ['data' => setObjectLiteral($json, 'set')], true);

		$top_frame .= $object_literal;

		parent::$admin_data = $top_frame;

		$elements = '';

		$title = parse('h1', ['text' => locale('form_title')], true);

		$elements .= $title;

		$label_username = parse('label', ['text' => locale('label_username')]);

		$input_username_array = [
			'name' => 'username',
			'value' => null,
			'maxlength' => $this->account->table_column[ACCOUNT_DATABASE_TABLE_NAME]['username']
		];
		$input_username = parse('input_text', $input_username_array, true);

		$elements .= $label_username . $input_username;

		$label_password = parse('label', ['text' => locale('label_password')]);

		$input_password_array = [
			'name' => 'password',
			'maxlength' => Account_Model::LENGTH['MAXIMUM_PASSWORD']
		];
		$input_password = parse('input_password', $input_password_array, true);

		$elements .= $label_password . $input_password;

		$submit = parse('input_button', ['name' => 'submit_authenticate_user', 'value' => locale('submit_authenticate')]);

		$elements .= $submit;

		$form_array = [
			'action' => null,
			'method' => null,
			'id' => 'account',
			'elements' => $elements
		];
		$form = parse('form', $form_array);

		$bottom_frame = parse('bottom_frame', ['data' => $form]);

		parent::$admin_data .= $bottom_frame;

		Output::html(locale('document_title_login'));
	}
}