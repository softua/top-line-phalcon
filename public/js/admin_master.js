/**
 * Created by Ruslan Koloskov
 * Date: 23.03.2014
 * Time: 10:50
 */
$(document).ready(function() {

	tinymce.init({
		selector: '.tinymce'
	});

	var admin = {};

	$('body').on('click', '[data-action="open"]', function(e) {
		e.preventDefault();

		if($(e.target).attr('data-editing') == 'true')
			admin.openCategoryForEditing(e);
		else
			admin.openCategory(e);
	});

	// Событие - добавление категории товару
	$('body').on('click', '[data-addcategory]', function(e) {
		e.preventDefault();
		admin.addCategoryToProduct(e);
	});

	$('body').on('change', '[data-select-choosen]', function(e) {
		// Получаем список категорий с указанным родителем
		admin.addSelectWithCategories(admin.categories, $(e.target).find(':selected').attr('value'));
	});

	// Удаление категории
	$('body').on('click', '[data-delete-category]', function(e) {
		e.preventDefault();
		admin.deleteProductCategory(e);
	});

	// Загружаем и открываем категории
	admin.openCategory = function(e) {
		var href = $(e.target);
		var innerList = href.siblings('.admin__categories');
		var parent = href.parent();
		var parentId = parent.attr('data-category-id');

		if(innerList.hasClass('admin__categories--hidden')) {
			$.ajax({
				url: '/admin/getcategories/' + parentId + '/',
				method: 'GET',
				dataType: 'JSON',
				success: function(data) {
					if(data != null) {
						innerList.empty();
						for(var i = 0; i < data.length; i++) {
							innerList.append('<li class="admin__categories__item" data-category-id="' + data[i]._id.$id + '"><a href="/admin/getproducts/' + data[i]._id.$id + '/" data-action="open" data-editing="false">' + data[i].name + '</a><ul class="admin__categories admin__categories--hidden"></ul></li>');
						}
						innerList.removeClass('admin__categories--hidden');
					} else {
						var urlArray = this.url.split('/');
						admin.showProductsInCategory(urlArray[urlArray.length - 2]);
					}
				}
			});

		} else {
			innerList.addClass('admin__categories--hidden');
		}
	};

	// Загружаем и открываем категории для редактирования
	admin.openCategoryForEditing = function(e) {
		var href = $(e.target);
		var innerList = href.siblings('.admin__categories');
		var parent = href.parent();
		var parentId = parent.attr('data-category-id');

		if(innerList.hasClass('admin__categories--hidden')) {
			$.ajax({
				url: '/admin/getcategories/' + parentId + '/',
				method: 'GET',
				dataType: 'JSON',
				success: function(data) {
					if(data != null) {
						innerList.empty();
						for(var i = 0; i < data.length; i++) {
							innerList.append('<li class="admin__categories__item" data-category-id="' + data[i]._id.$id + '"><a href="/" class="btn btn-mini btn-primary" data-action="open" data-editing="true">+</a><a href="/admin/editcategory/' + data[i]._id.$id + '/" title="Редактировать">' + data[i].name + ' (' + 'SEO - ' + data[i].seo + ', sort = ' + data[i].sort + ')' + '</a><ul class="admin__categories admin__categories--hidden"><li class="admin__categories__item"><a href="/admin/addcategory/' + data[i]._id.$id + '/" class="btn btn-success">Добавить категорию</a></li></ul></li>');
						}

						innerList.append('<li class="admin__categories__item"><a href="/admin/addcategory/' + parentId + '/" class="btn btn-success">Добавить категорию</a></li>');
						innerList.removeClass('admin__categories--hidden');
					} else {
						innerList.removeClass('admin__categories--hidden');
					}
				}
			});

		} else {
			innerList.addClass('admin__categories--hidden');
		}
	};

	// Показывает товары соответствующей категории
	admin.showProductsInCategory = function(id) {

		if (id.length == 24)
		{
			$.ajax({
				url: '/admin/getproducts/' + id + '/',
				type: 'POST',
				dataType: 'JSON',
				success: function(prods)
				{
					if (prods)
					{
						$('.products').empty();

						var table = $('<table class="table table-bordered"></table>');
						var thead = $('<thead></thead>');
						var tbody = $('<tbody></tbody>');

						$('.products').append(table);
						$('table', '.products').append(thead, tbody);
						$('table thead', '.products').append('<tr><th>#</th><th>Тип</th><th>Артикул</th><th>Модель</th><th>Бренд</th><th>Цена</th><th>Public</th><th>Действия</th></tr>');

						for (var i = 0; i < prods.length; i++)
						{
							var type = prods[i].type;
							var articul = prods[i].articul ? prods[i].articul : '---';
							var model = prods[i].model ? prods[i].model : '---';
							var brand = prods[i].brand ? prods[i].brand : '---';

							if (prods[i].main_curancy == 'eur')
								var price = prods[i].price_eur + ' евро';
							else if (prods[i].main_curancy == 'usd')
								var price = prods[i].price_usd + ' $';
							else if (prods[i].main_curancy == 'uah')
								var price = prods[i].price_uah + ' грн.';

							if (prods[i].public == true)
								var pub = 'Показывать';
							else
								var pub = 'Не показывать';

							var editBtn = '<a href="/admin/editproduct/' + prods[i]._id.$id + '/" class="btn">Редактировать</a>';

								$('table tbody', '.products').append('<tr><td>' + i + '</td><td>' + type + '</td><td>' + articul + '</td><td>' + model + '</td><td>' + brand + '</td><td>' + price + '</td><td>' + pub + '</td><td>' + editBtn + '</td></tr>');
						}
					} else
					{
						$('.products').empty().append('<h3>Нет товаров в этой категории</h3>');
					}
				}
			});
		} else
		{
			$('.products').empty().append('<h3>Нет товаров в этой категории</h3>');
		}
	};

	admin.getMainCategories = function(cats)
	{
		var array = [];

		for (var i = 0, l = cats.length; i < l; i++)
		{
			if (cats[i].parent == 0) {
				array.push(cats[i]);
			}
		}

		return array;
	}

	admin.addSelectWithMainCategories = function(categories)
	{
		var mainCats = admin.getMainCategories(categories);
		var select = $('<select data-select-choosen></select>');

		for (var i = 0; i < mainCats.length; i++)
		{
			var option = $('<option value="' + mainCats[i]._id.$id + '">' + mainCats[i].name + '</option>');
			select.append(option);
		}

		select.prepend('<option selected>Выбирайте категорию</option>');

		$('[data-temp-selects]').append(select);
	}

	admin.getCategoryByParentId = function(cats, parentId)
	{
		var array = [];

		for (var i = 0, l = cats.length; i < l; i++)
		{
			if (cats[i].parent == parentId) {
				array.push(cats[i]);
			}
		}

		return array;
	}

	admin.addSelectWithCategories = function(cats, parentId)
	{
		var result = admin.getCategoryByParentId(cats, parentId);

		if (result.length > 0)
		{
			var select = $('<select data-select-choosen></select>');

			for (var i = 0; i < result.length; i++)
			{
				var option = $('<option value="' + result[i]._id.$id + '">' + result[i].name + '</option>');
				select.append(option);
			}

			select.prepend('<option selected>Выбирайте категорию</option>');

			$('[data-temp-selects]').append(select);

		} else
		{
			$('[data-temp-selects]').remove();

			var productId = $('[data-addcategory]').attr('href');

			$.ajax({
				url: '/admin/addcategorytoproduct/' + parentId + '/' + productId + '/',
				type: 'POST',
				dataType: 'JSON',
				success: function(category) {
					$('[data-categories]').append('<br><a data-delete-category href="/admin/deleteproductcategory/' + category.id + '/' + productId + '/" class="btn btn-danger">Удалить</a> <span>' + category.full_name + '</span><br>');
				}
			});
		}
	}

	admin.addCategoryToProduct = function(e)
	{
		$('[data-temp-selects]').remove();
		admin.categories = $(e.target).data('categories-list');

		$(e.target).parent().prepend('<div data-temp-selects></div>');

		// Добавляем select с главными категориями
		admin.addSelectWithMainCategories(admin.categories);
	}

	admin.deleteProductCategory = function(e)
	{
		var target = $(e.target);
		var href = target.attr('href');
		var siblingSpan = target.next('span');
		var lastBr = siblingSpan.next('br');
		var prevBr = target.prev('br');

		$.ajax({
			url: href,
			type: 'POST',
			dataType: 'JSON',
			success: function (data) {
				if (data == true)
				{
					target.remove();
					siblingSpan.remove();
					lastBr.remove();
					prevBr.remove();
				}
			}
		});
	}

	admin.parameter = {}; // Объект для работы с дополнительными параметрами

	// Обработка события клика на кнопку "Добавить параметр"
	admin.parameter.add = function()
	{
		/*$.ajax({
			url:
		});*/
	}
});