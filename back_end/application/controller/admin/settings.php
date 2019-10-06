<?php
final class Settings extends Controller
{
	public function __construct(){}

	public function index($option)
	{
		if (!in_array($option, ['account_summary']))
		{
			$this->model('Account_Model', 'account');

			$this->account->setTableColumn(ACCOUNT_DATABASE_TABLE_NAME);
		}

		$content = '';

		switch ($option)
		{
			case 'account_summary':
				$data = '';
				$data .= parse('tr_data', ['th' => locale('text_name'), 'td' => $_SESSION['authenticate']['username']], true);
				$data .= parse('tr_data', ['th' => locale('text_profile'), 'td' => locale('text_' . $_SESSION['authenticate']['profile'])], true);
				$data .= parse('tr_data', ['th' => locale('text_mail'), 'td' => $_SESSION['authenticate']['e_mail']]);

				$content .= parse('table', ['id' => 'summary', 'data' => $data]);
			break;

			case 'change_password':
				$elements = '';

				if (isset($_SESSION['change_password']))
				{
					$elements .= parse('p', ['text' => locale('text_change_password')], true);

					unset($_SESSION['change_password']);
				}

				$label_old_password = parse('label', ['text' => locale('label_old_password')]);

				$input_old_password_array = [
					'name' => 'old_password',
					'maxlength' => Account_Model::LENGTH['MAXIMUM_PASSWORD']
				];
				$input_old_password = parse('input_password', $input_old_password_array, true);

				$elements .= $label_old_password . $input_old_password;

				$label_new_password = parse('label', ['text' => locale('label_new_password')]);

				$input_new_password_array = [
					'name' => 'new_password',
					'maxlength' => Account_Model::LENGTH['MAXIMUM_PASSWORD']
				];
				$input_new_password = parse('input_password', $input_new_password_array, true);

				$elements .= $label_new_password . $input_new_password;

				$label_confirm_new_password = parse('label', ['text' => locale('label_confirm_new_password')]);

				$input_confirm_new_password_array = [
					'name' => 'confirm_new_password',
					'maxlength' => Account_Model::LENGTH['MAXIMUM_PASSWORD']
				];
				$input_confirm_new_password = parse('input_password', $input_confirm_new_password_array, true);

				$elements .= $label_confirm_new_password . $input_confirm_new_password;

				$submit = parse('input_button', ['name' => 'submit_change_password', 'value' => locale('submit_confirm')]);

				$elements .= $submit;

				$form_array = [
					'action' => null,
					'method' => null,
					'id' => 'account',
					'elements' => $elements
				];
				$form = parse('form', $form_array);

				$content .= $form;
			break;

			case 'change_mail':
				$elements = '';

				if (isset($_SESSION['change_mail']))
				{
					$elements .= parse('p', ['text' => locale('text_change_mail')], true);

					unset($_SESSION['change_mail']);
				}

				$label_password = parse('label', ['text' => locale('label_password')]);

				$input_password_array = [
					'name' => 'password',
					'maxlength' => Account_Model::LENGTH['MAXIMUM_PASSWORD']
				];
				$input_password = parse('input_password', $input_password_array, true);

				$elements .= $label_password . $input_password;

				$label_new_mail = parse('label', ['text' => locale('label_new_mail')]);

				$input_new_mail_array = [
					'name' => 'new_e_mail',
					'value' => null,
					'maxlength' => $this->account->table_column[ACCOUNT_DATABASE_TABLE_NAME]['e_mail']
				];
				$input_new_mail = parse('input_text', $input_new_mail_array, true);

				$elements .= $label_new_mail . $input_new_mail;

				$submit = parse('input_button', ['name' => 'submit_change_mail', 'value' => locale('submit_confirm')]);

				$elements .= $submit;

				$form_array = [
					'action' => null,
					'method' => null,
					'id' => 'account',
					'elements' => $elements
				];
				$form = parse('form', $form_array);

				$content .= $form;
			break;

			case 'change_locale':
				$elements = '';

				if (isset($_SESSION['change_locale']))
				{
					$elements .= parse('p', ['text' => locale('text_change_locale')], true);

					unset($_SESSION['change_locale']);
				}

				$label_new_locale = parse('label', ['text' => locale('label_new_locale')]);

				$options = '';
				$options .= parse('option_selected', ['value' => '', 'text' => locale('select')]);
				$options .= PHP_EOL . parse('option', ['value' => 'en', 'text' => 'english']);
				$options .= PHP_EOL . parse('option', ['value' => 'bg', 'text' => 'български']);

				$select_new_locale = parse('select', ['name' => 'new_locale', 'options' => $options], true);

				$elements .= $label_new_locale . $select_new_locale;

				$submit = parse('input_button', ['name' => 'submit_change_locale', 'value' => locale('submit_confirm')]);

				$elements .= $submit;

				$form_array = [
					'action' => null,
					'method' => null,
					'id' => 'account',
					'elements' => $elements
				];
				$form = parse('form', $form_array);

				$content .= $form;
			break;
		}

		$path = FORWARD_SLASH . ADMIN_ROUTE . '/settings/';

		$href_account_summary = [
			'href' => $path . 'account_summary',
			'class' => null,
			'text' => locale('link_account_summary')
		];
		$href_change_password = [
			'href' => $path . 'change_password',
			'class' => null,
			'text' => locale('link_change_password')
		];
		$href_change_mail = [
			'href' => $path . 'change_mail',
			'class' => null,
			'text' => locale('link_change_mail')
		];
		$href_change_locale = [
			'href' => $path . 'change_locale',
			'class' => null,
			'text' => locale('link_change_locale')
		];

		$href = '';
		$href .= parse('a', $href_account_summary, true);
		$href .= parse('a', $href_change_password, true);
		$href .= parse('a', $href_change_mail, true);
		$href .= parse('a', $href_change_locale);

		$nav = parse('nav', ['data' => $href], true);
		$section = parse('section', ['data' => $content]);

		$bottom_frame = parse('bottom_frame', ['data' => $nav . $section]);

		parent::$admin_data .= $bottom_frame;

		Output::html(locale('document_title_settings'));
	}
}