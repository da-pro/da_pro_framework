var common = {
	menu : function()
	{
		var toggle = true;

		$('header nav:first-child').on('click', function()
		{
			if (toggle)
			{
				toggle = false;

				$('header nav:first-child li:nth-child(2)').animate({'opacity':0}, 200, function()
				{
					$('header nav:first-child li:nth-child(1)').css('transform', 'translateY(16px) rotate(45deg)');
					$('header nav:first-child li:nth-child(3)').css('transform', 'translateY(-16px) rotate(-45deg)');
				});
				$('header nav:last-child').css({'opacity':0, 'display':'block'}).animate({'opacity':1}, 400);
			}
			else
			{
				toggle = true;

				$('header nav:first-child li:nth-child(1)').css('transform', 'translateY(0) rotate(0)');
				$('header nav:first-child li:nth-child(3)').css('transform', 'translateY(0) rotate(0)');
				$('header nav:first-child li:nth-child(2)').animate({'opacity':1}, 200);
				$('header nav:last-child').fadeOut(400);
			}
		});
	}()
};