<h1>Da Pro Framework - Utility</h1>

<h2>Generate Password</h2>
<div class="table">
	<div class="row">
		<div class="_6 _3_">
			<label class="checkbox"><input type="checkbox" name="letter" checked><span></span>Letters</label>
			<label class="radio"><input type="radio" name="letter_case" value="Aa" checked><span></span>All</label>
			<label class="radio"><input type="radio" name="letter_case" value="A"><span></span>Upper Case</label>
			<label class="radio"><input type="radio" name="letter_case" value="a"><span></span>Lower Case</label>
			<label class="checkbox"><input type="checkbox" name="number" checked><span></span>Numbers</label>
		</div>
		<div class="_6 _3_">
			<label class="checkbox"><input type="checkbox" name="mathematical_symbol"><span></span>Mathematical Symbols <b>+-*/</b></label>
			<label class="checkbox"><input type="checkbox" name="bracket_symbol"><span></span>Bracket Symbols <b>()[]{}</b></label>
			<label class="checkbox"><input type="checkbox" name="sentence_symbol"><span></span>Sentence Symbols <b>,.!?</b></label>
			<label class="checkbox"><input type="checkbox" name="special_symbol"><span></span>Special Symbols <b>@#$%^&amp;</b></label>
			<label class="checkbox"><input type="checkbox" name="additional_symbol"><span></span>Additional Symbols <b>;:_=&lt;&gt;</b></label>
		</div>
	</div>

	<div class="row">
		<div class="_12 _6_">
			<input type="number" name="password_length" min="5" max="50" placeholder="password length">
		</div>
	</div>

	<div class="row">
		<div class="_12 _6_">
			<input type="button" value="generate password">
		</div>
	</div>

	<div class="row">
		<div class="_2 _1_"></div>
		<div class="_8 _4_">
			<h5></h5>
			<h6></h6>
		</div>
		<div class="_2 _1_"></div>
	</div>
</div>
<script>
var generate_password = {
	letter : {
		'Aa' : 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
		'A' : 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
		'a' : 'abcdefghijklmnopqrstuvwxyz'
	},
	number : '0123456789',
	mathematical_symbol : '+-*/',
	bracket_symbol : '()[]{}',
	sentence_symbol : ',.!?',
	special_symbol : '@#$%^&',
	additional_symbol : ';:_=<>',
	minimum_length : 5,
	maximum_length : 50,
	construct : function()
	{
		$('input[type=button]').on('click', function()
		{
			$('h5, h6').text('').hide();

			var i,
				error = [],
				available_symbols = '',
				password = '';

			var password_length = parseInt($('input[name=password_length]').val().trim(), 10),
				symbols = {
					letter : ($('input[name=letter]').is(':checked')) ? $('input[name=letter_case]:checked').val().trim() : false,
					number : $('input[name=number]').is(':checked'),
					mathematical_symbol : $('input[name=mathematical_symbol]').is(':checked'),
					bracket_symbol : $('input[name=bracket_symbol]').is(':checked'),
					sentence_symbol : $('input[name=sentence_symbol]').is(':checked'),
					special_symbol : $('input[name=special_symbol]').is(':checked'),
					additional_symbol : $('input[name=additional_symbol]').is(':checked')
				};

			for (i in symbols)
			{
				if (symbols.hasOwnProperty(i))
				{
					if (symbols[i] !== false)
					{
						if (typeof symbols[i] === 'boolean')
						{
							available_symbols += generate_password[i];
						}
						else
						{
							available_symbols += generate_password.letter[symbols[i]];
						}
					}
				}
			}

			var available_symbols_length = available_symbols.length;

			if (available_symbols_length === 0)
			{
				error.push('choose at least one type of symbols');
			}

			if (isNaN(password_length))
			{
				error.push('insert password length');
			}
			else
			{
				if (password_length < generate_password.minimum_length || password_length > generate_password.maximum_length)
				{
					error.push('password length must be between '+generate_password.minimum_length+' and '+generate_password.maximum_length);
				}
			}

			if (error.length)
			{
				$('h5').text(error.join('\n')).show();
			}
			else
			{
				for (i = 0; i < password_length; ++i)
				{
					password += available_symbols[Math.floor(Math.random() * available_symbols_length)];
				}

				$('h6').text(password).show();
			}
		});
	},
	keydown : function()
	{
		$(document).on('keydown', function(event)
		{
			if (event.which === 13)
			{
				$('input[type=button]').trigger('click').focus();

				return false;
			}
		});
	}()
};
generate_password.construct();
</script>
