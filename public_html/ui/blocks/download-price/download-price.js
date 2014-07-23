/**
 * Created by Ruslan Koloskov
 * Date: 22.07.14
 * Time: 11:51
 */
define(['jquery', 'fancyBox'], function ($, fb) {
	$('.download-price').fancybox({
		maxWidth	: 800,
		minWidth    : 615,
		maxHeight	: 600,
		minHeight   : 170,
		fitToView	: false,
		width		: '70%',
		height		: '70%',
		autoSize	: true,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none',
		helpers : {
			overlay : {
				css : {
					'background' : 'rgba(30, 30, 30, 0.8)'
				}
			}
		}
	});
});