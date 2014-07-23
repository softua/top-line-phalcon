/**
 * Created by Ruslan Koloskov
 * Date: 23.06.14
 * Time: 0:19
 */
define(['jquery'], function ($) {

	var $filter = $('[data-filter="price"]');
	var oldValue = $filter.val();
	$('body').on('change', $filter, function(e) {
		if (oldValue != $(e.target).val()) {
			location.assign(location.pathname + '?sort=' + $filter.val());
		}
	});
});