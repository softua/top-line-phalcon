/**
 * Created by Ruslan Koloskov
 * Date: 08.07.14
 * Time: 0:39
 */
define(['jquery', 'fancyBox'],
	function ($, fb) {
		$('.fancybox').fancybox({
			helpers : {
				overlay : {
					css : {
						'background' : 'rgba(30, 30, 30, 0.8)'
					}
				}
			}
		});
	}
);