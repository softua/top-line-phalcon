/**
 * Created by Ruslan Koloskov
 * Date: 23.06.14
 * Time: 0:19
 */
define(['jquery'], function (jquery) {

	var $filter = $('[data-filter="price"]');
	var prodList = $('.products-list');
	var oldValue = $filter.val();
	$('body').on('change', $filter, function(e) {
		if (oldValue != $(e.target).val()) {
			var paths = location.pathname.split('/');
			prodList.empty().css({
				minHeight: 100,
				background: 'url(/img/indicato.gif) no-repeat center'
			});
			$.ajax({
				url: '/products/sort',
				type: 'post',
				data: {
					category: paths[paths.length - 2],
					sort: ($(e.target).val() == 1) ? 'DESC' : 'ASC'
				},
				success: function (data) {
					if (data != 'false') {
						var prods = JSON.parse(data);
						for (var i = 0; i < prods.length; i++) {
							var $li = $('<li class="products-list__item"><h2 class="products-list__title"><a href="' + prods[i].path + '" title="' + prods[i].name + '">' + prods[i].name + '</a></h2><figure class="products-list__img"><img src="' + prods[i].img + '" alt="' + prods[i].name + '"/></figure><div class="products-list__code">Артикул: ' + prods[i].articul + '</div>' + prods[i].short_desc + '</li>');
							prodList.append($li);
						}
					}
				},
				complete: function () {
					prodList.css({
						minHeight: 0,
						background: 'none'
					})
				}
			})
		}
		oldValue = $(e.target).val();
	});
});