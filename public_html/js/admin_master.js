/**
 * Created by Ruslan Koloskov
 * Date: 23.03.2014
 * Time: 10:50
 */

tinymce.init({
	selector: '.tinymce',
	element_format: 'html',
	language: 'ru',
	menu : {
		file   : {title : 'File'  , items : 'newdocument | undo redo | cut copy paste pastetext | selectall | removeformat'},
		insert : {title : 'Insert', items : 'link media | template hr'},
		table  : {title : 'Table' , items : 'inserttable tableprops deletetable | cell row column'},
		tools  : {title : 'Tools' , items : 'spellchecker code'}
	}
});

$.datepicker.setDefaults({
	dayNames: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
	dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
	firstDay: 1,
	monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь']
});

$('[data-calendar="true"]').datepicker({
	dateFormat: 'yy-mm-dd'
});

var admin = {};

$('body').on('click', '[data-action="open"]', function(e) {
	e.preventDefault();

	if($(e.target).data('editing'))
		admin.openCategoryForEditing(e);
	else
		admin.openCategory(e);
})

// Событие - добавление категории товару
.on('click', '[data-addcategory]', function(e) {
	e.preventDefault();
	admin.addCategoryToProduct(e);
})

.on('change', '[data-select-choosen]', function(e) {
	// Получаем список категорий с указанным родителем
	admin.addSelectWithCategories(admin.categories, $(e.target).find(':selected').attr('value'));
})

// Удаление категории
.on('click', '[data-delete-category]', function(e) {
	e.preventDefault();
	admin.deleteProductCategory(e);
})

// Удаление параметра
.on('click', '[data-delete-param]', function(e) {
	e.preventDefault();
	admin.parameter.deleteParam(e);
})

// Добавление полей для внесения параметров
.on('click', '[data-add-param]', function(e) {
	e.preventDefault();
	admin.parameter.addFields(e);
})

// Сохранение нового параметра
.on('click', '[data-save-parameter]', function(e) {
	e.preventDefault();
	admin.parameter.save(e);
})

// Удаление фоток товара
.on('dblclick', '[data-delete-foto="true"]', function(e) {
	admin.fotos.deleteFoto(e);
})

// Удаление фоток категории
.on('dblclick', '[data-delete-category-foto="true"]', function(e) {
	admin.categoryFoto.deleteFoto(e);
})

// Удаление фоток статических страниц
.on('dblclick', '[data-delete-static-page-foto="true"]', function(e) {
	admin.pages.deleteFoto(e);
})

// Удаление файла
.on('click', '[data-delete-file="true"]', function(e) {
	e.preventDefault();
	admin.files.deleteFile(e);
})

// Добавление видео товару
.on('click', '[data-add-video="true"]', function(e) {
	e.preventDefault();
	$(e.target).replaceWith('<div class="video-inputs"><input type="text" name="name" placeholder="Название видео"/><input type="text" name="href" class="input-large" placeholder="Ссылка на видео"/><button data-video-add-save="true" class="btn btn-primary">Добавить</button></div>');
})

// Сохранение добавленного видео
.on('click', '[data-video-add-save="true"]', function(e) {
	e.preventDefault();
	var container = $($(e.target).parent());
	var name = $('input[name="name"]', container).val();
	var href = $('input[name="href"]', container).val();

	$.ajax({
		url: '/admin/addvideo',
		type: 'POST',
		data: {
			name: name,
			href: href,
			prodId: $('.videos').data('product-id')
		},
		success: function (data) {
			if (data != 'false')
			{
				var video = JSON.parse(data);
				var videos = $('.videos');
				videos.append('<li data-video-id="' + video.id + '" class="videos__item"><a data-video-delete="true" href="" class="btn btn-mini btn-danger">Удалить</a><a data-video-edit="true" href="" class="btn btn-mini">Редактировать</a><a href="' + video.href + '" title="Смотреть видео" target="_blank">' + video.name + '</a></li>');

				container.replaceWith('<a data-add-video="true" href="/admin/addvideo" class="btn">Добавить видео</a>');
			}
		}
	});
})

// Удаление видео
.on('click', '.videos__item [data-video-delete="true"]', function (e) {
	e.preventDefault();
	$.ajax({
		url: '/admin/deletevideo/' + $(e.target).parent().data('video-id'),
		type: 'post',
		success: function (data) {
			if (data == 'true')
			{
				$(e.target).parent().remove();
			}
		}
	});
})

// Редактирование видео
.on('click', '.videos__item [data-video-edit="true"]', function (e) {
	e.preventDefault();
	var name = $(e.target).next().text();
	var href = $(e.target).next().attr('href');
	var id = $(e.target).parent().data('video-id');
	$(e.target).parent().replaceWith('<div class="video-inputs" data-video-id="' + id + '"><input type="text" name="name" placeholder="Название видео" value="' + name + '"/><input type="text" name="href" class="input-large" placeholder="Ссылка на видео" value="' + href + '"/><button data-video-edited-save="true" class="btn btn-primary">Сохранить</button></div>');
})

// Сохранение отредактированного видео
.on('click', '[data-video-edited-save="true"]', function (e) {
	e.preventDefault();
	var container = $(e.target).parent();
	var name = $(e.target).siblings('input[name="name"]').val();
	var href = $(e.target).siblings('input[name="href"]').val();
	var id = $(e.target).parent().data('video-id');
		$.ajax({
			url: '/admin/editvideo/' + id,
			type: 'POST',
			data: {
				name: name,
				href: href
			},
			success: function (data) {
				if (data == 'true')
				{
					var li = $('<li data-video-id="' + id + '" class="videos__item"><a data-video-delete="true" href="" class="btn btn-mini btn-danger">Удалить</a><a data-video-edit="true" href="" class="btn btn-mini">Редактировать</a><a href="' + href + '" title="Смотреть видео" target="_blank">' + name + '</a></li>');
					container.replaceWith(li);
				}
			}
		});

});

// Сортировка параметров
$('[data-parameters]').sortable({
	axis: 'y', // перемещение только по вертикали
	opacity: 0.5,
	update: function(event, ui) { // событие окончания перетаскивания
		$.ajax({
			url: '/admin/sortparams',
			type: 'POST',
			data: 'ids=' + JSON.stringify($(event.target).sortable('toArray'))
		});
	}
});

// Сортировка фото товара
$('[data-uploaded-list="fotos"]').sortable({
	opacity: 0.5,
	update: function(event, ui) { // событие окончания перетаскивания
		var postData = [];
		$('li', '[data-uploaded-list="fotos"]').each(function(index, element) {
			postData.push($(element).data('uploaded-id'));
		});

		$.ajax({
			url: '/admin/sortfotos',
			type: 'POST',
			data: 'ids=' + JSON.stringify(postData)
		});
	}
});

// Сортировка фото статических страниц
$('[data-uploaded-list="pages-fotos"]').sortable({
	opacity: 0.5,
	update: function(event, ui) { // событие окончания перетаскивания
		var postData = [];
		$('li', '[data-uploaded-list="pages-fotos"]').each(function(index, element) {
			postData.push($(element).data('uploaded-id'));
		});

		$.ajax({
			url: '/admin/sortstaticpagesfotos',
			type: 'POST',
			data: 'ids=' + JSON.stringify(postData)
		});
	}
});

// Сортировка видео
$('.videos').sortable({
	opacity: 0.5,
	axis: 'y',
	update: function (event, ui) {
		var lis = $('li', '.videos');
		var ids = [];
		lis.each(function (index, element) {
			ids[index] = $(element).attr('data-video-id');
		});

		$.ajax({
			url: '/admin/sortvideo',
			type: 'POST',
			data: {
				ids: JSON.stringify(ids)
			}
		});
	}
});

// Загрузка фотографий товара
$('[data-upload-foto="true"]').on('click', function(e) {
	$(e.target).children('input').click();
}).fileupload({
	url: '/admin/uploadfoto?prodId=' + $('[data-upload-foto="true"]').data('product-id'),
	sequentialUploads: true,
	formData: {script: true},
	add: function (e, data) {
		$('[data-upload-foto="true"]').append('<img src="/img/indicato.gif" width="30">');
		var ajax = data.submit();

		ajax.success(function (result, textStatus, jqXHR) {
			if (result !== 'false')
			{
				var imgData = JSON.parse(result);
				var ul = $('[data-uploaded-list="fotos"]');
				var li = $('<li data-uploaded-id="' + imgData.id + '" data-delete-foto="true"><img src="' + imgData.path + '" alt="/" class="thumbnail"/></li>');
				ul.append(li);
			}
		});

		ajax.complete(function() {
			$('img', '[data-upload-foto="true"]').remove();
		});
	},
	progressall: function(e, data) {
		var progress = parseInt(data.loaded / data.total * 100);

		if (progress < 100)
		{
			$('[data-progress-fotos="true"]')
				.fadeIn()
				.children('.bar')
				.css('width', progress + '%')
				.text(progress + ' %');
		} else {
			$('[data-progress-fotos="true"]')
				.children('.bar')
				.css('width', progress + '%')
				.text('Загрузка завершена');

			setTimeout(function() {
				$('[data-progress-fotos="true"]').fadeOut();
			}, 1000)
		}
	}
});

// Загрузка фотографий категорий
$('[data-upload-foto-category="true"]').on('click', function(e) {
	$(e.target).children('input').click();
}).fileupload({
	url: '/admin/uploadfotocategory?catId=' + $('[data-upload-foto-category="true"]').data('category-id'),
	sequentialUploads: true,
	formData: {script: true},
	add: function (e, data) {
		var ajax = data.submit();

		ajax.success(function (result, textStatus, jqXHR) {
			if (result !== 'false')
			{
				var imgData = JSON.parse(result);
				var ul = $('[data-uploaded-list="fotos-categories"]');
				var li = $('<li data-uploaded-id="' + imgData.id + '" data-delete-category-foto="true"><img src="' + imgData.path + '" alt="/" class="thumbnail"/></li>');
				ul.append(li);

			}
		});
	},
	progressall: function(e, data) {
		var progress = parseInt(data.loaded / data.total * 100);

		if (progress < 100)
		{
			$('[data-progress-fotos="true"]')
				.fadeIn()
				.children('.bar')
				.css('width', progress + '%')
				.text(progress + ' %');
		} else {
			$('[data-progress-fotos="true"]')
				.children('.bar')
				.css('width', progress + '%')
				.text('Загрузка завершена');

			setTimeout(function() {
				$('[data-progress-fotos="true"]').fadeOut();
			}, 1000)
		}
	}
});

// Загрузка фотографий статических страниц
$('[data-upload-static-page-foto="true"]').on('click', function(e) {
	$(e.target).children('input').click();
}).fileupload({
	url: '/admin/uploadstaticpagefoto/' + $('[data-upload-static-page-foto="true"]').data('page-id'),
	sequentialUploads: true,
	formData: {script: true},
	add: function (e, data) {
		$('[data-upload-static-page-foto="true"]').append('<img src="/img/indicato.gif" width="30">');
		var ajax = data.submit();

		ajax.success(function (result, textStatus, jqXHR) {
			if (result !== 'false')
			{
				var imgData = JSON.parse(result);
				var ul = $('[data-uploaded-list="pages-fotos"]');
				var li = $('<li data-uploaded-id="' + imgData.id + '" data-delete-static-page-foto="true"><img src="' + imgData.path + '" alt="/" class="thumbnail"/></li>');
				ul.append(li);
			}
		});

		ajax.complete(function() {
			$('img', '[data-upload-static-page-foto="true"]').remove();
		});
	},
	progressall: function(e, data) {
		var progress = parseInt(data.loaded / data.total * 100);

		if (progress < 100)
		{
			$('[data-progress-fotos="true"]')
				.fadeIn()
				.children('.bar')
				.css('width', progress + '%')
				.text(progress + ' %');
		} else {
			$('[data-progress-fotos="true"]')
				.children('.bar')
				.css('width', progress + '%')
				.text('Загрузка завершена');

			setTimeout(function() {
				$('[data-progress-fotos="true"]').fadeOut();
			}, 1000)
		}
	}
});

// Загрузка файлов
$('[data-upload-file="true"]').on('click', function(e) {
	$(e.target).children('input').click();
}).fileupload({
	url: '/admin/uploadfile?prodId=' + $('[data-upload-file="true"]').data('product-id'),
	sequentialUploads: true,
	formData: {script: true},
	add: function (e, data) {
		var ajax = data.submit();

		ajax.success(function (result, textStatus, jqXHR) {
			console.log(result);
			if (result !== 'false')
			{
				var fileData = JSON.parse(result);
				var ul = $('[data-uploaded-list="files"]');
				var li = $('<li data-uploaded-id="' + fileData.id + '" data-delete-file="true"><a href="/admin/deletefile/' + fileData.id + '/" class="btn btn-danger">Удалить файл</a>' + fileData.name + '</li>');
				ul.append(li);
			}
		});
	},
	progressall: function(e, data) {
		var progress = parseInt(data.loaded / data.total * 100);

		if (progress < 100)
		{
			$('[data-progress-files="true"]')
				.fadeIn()
				.children('.bar')
				.css('width', progress + '%')
				.text(progress + ' %');
		} else {
			$('[data-progress-files="true"]')
				.children('.bar')
				.css('width', progress + '%')
				.text('Загрузка завершена');

			setTimeout(function() {
				$('[data-progress-files="true"]').fadeOut();
			}, 1000)
		}
	}
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
						innerList.append('<li class="admin__categories__item" data-category-id="' + data[i].id + '"><a href="/admin/getproducts/' + data[i].id + '/" data-action="open" data-editing="false">' + data[i].name + '</a><ul class="admin__categories admin__categories--hidden"></ul></li>');
					}
					innerList.removeClass('admin__categories--hidden');
					$('.products').empty();
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
	var parentId = parent.data('category-id');

	if(innerList.hasClass('admin__categories--hidden')) {
		$.ajax({
			url: '/admin/getcategories/' + parentId + '/',
			method: 'GET',
			dataType: 'JSON',
			success: function(data) {
				if(data != null) {
					innerList.empty();
					for(var i = 0; i < data.length; i++) {
						innerList.append('<li class="admin__categories__item" data-category-id="' + data[i].id + '"><a href="/" class="btn btn-mini btn-primary" data-action="open" data-editing="true">+</a><a href="/admin/editcategory/' + data[i].id + '/" title="Редактировать">' + data[i].name + ' (' + 'SEO - ' + data[i].seo_name + ', sort = ' + data[i].sort + ')' + '</a><ul class="admin__categories admin__categories--hidden"><li class="admin__categories__item"><a href="/admin/addcategory/' + data[i].id + '/" class="btn btn-success">Добавить категорию</a></li></ul></li>');
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

	if (id)
	{
		$.ajax({
			url: '/admin/getproducts/' + id + '/',
			type: 'POST',
			dataType: 'JSON',
			success: function(prods)
			{
				console.warn(prods);
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

						var editBtn = '<a href="/admin/editproduct/' + prods[i].id + '/" class="btn">Редактировать</a>';

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
		if (cats[i].parent_id == 0) {
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
		var option = $('<option value="' + mainCats[i].id + '">' + mainCats[i].name + '</option>');
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
		if (cats[i].parent_id == parentId) {
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
			var option = $('<option value="' + result[i].id + '">' + result[i].name + '</option>');
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
				$('[data-categories]').append('<br><a data-delete-category href="/admin/deleteproductcategory/' + category.id + '/' + productId + '/" class="btn btn-mini btn-danger">Удалить</a> <span>' + category.full_name + '</span><br>');
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

// Добавление полей для нового параметра
admin.parameter.addFields = function(e)
{
	var prodId = $(e.target).attr('href');

	$('[data-fields-for-adding-parameters]').remove(); // удалить предыдущие поля

	$.ajax({
		url: '/admin/getjsonparameters',
		type: 'POST',
		success: function(parameters) {
			var container = $('<div class="parameters-fields" data-fields-for-adding-parameters="true"></div>');
			var key = $('<input type="text" name="param_key" data-provide="typeahead" data-items="5" data-source=\'' + parameters + '\' autocomplete="off"/>');
			var value = $('<input type="text" name="param_value" data-provide="typeahead" data-items="5" data-source=\'' + parameters + '\' autocomplete="off"/>');
			var btn = $('<a class="btn btn-success" href="/admin/addparam/' + prodId + '/" data-save-parameter="true">Сохранить</a>');

			container.append(key, value, btn);
			$('[data-parameters]').prepend(container);
		}
	});
}

// Сохранение нового параметра
admin.parameter.save = function(e)
{
	var target = $(e.target);
	var url = target.attr('href');
	var paramName = $('[name="param_key"]', '[data-fields-for-adding-parameters]').val();
	var paramValue = $('[name="param_value"]', '[data-fields-for-adding-parameters]').val();
	var paramsList = $('[data-parameters]');
	var span = $('<span> - </span>');
	var spanKey = $('<span>' + paramName + '</span>');
	var spanValue = $('<span>' + paramValue + '</span>');

	$.ajax({
		url: url,
		type: 'POST',
		data: 'key=' + paramName + '&value=' + paramValue,
		success: function(data) {
			console.log(data);
			if (data > 0)
			{
				var paramsItem = $('<li id="' + data + '" class="parameters__item"></li>');
				var deletingAncor = $('<a href="/admin/deleteparam/' + data + '/" data-delete-param="true" data-param-name="' + paramName + '" class="btn btn-mini btn-danger">Удалить параметр</a>');

				var editBtn = $('<a href="/admin/editparam/' + data + '/" data-param-name="' + paramName + '" class="btn btn-mini">Редактировать</a>');

				paramsItem.append(deletingAncor, editBtn, spanKey, span, spanValue);
				paramsList.append(paramsItem);
			}
		},
		complete: function() {
			$('[data-fields-for-adding-parameters]').remove();
		}
	});
}

// Удаление параметра
admin.parameter.deleteParam = function(e)
{
		var $target = $(e.target); // кнопка удаления
		var href = $target.attr('href'); // ссылка
		var parent = $target.parent(); // родитель - <li>, который нужно убрать из списка

		// запрос на удаление параметра
		$.ajax({
			url: href,
			type: 'POST',
			success: function(data)
			{
				if (data)
				{
					parent.remove();
				}
			}
		});
	}

admin.fotos = {}; // Объект для работы с картинками

// Удаление картинок
admin.fotos.deleteFoto = function(event) {
	$.ajax({
		url: '/admin/deleteproductfoto/',
		type: 'post',
		data: {
			id: $(event.target).parent('li').data('uploaded-id')
		},
		success: function(data) {
			if (data === 'true')
			{
				$(event.target).parent('li').remove();
			}
		}
	});
}

// Работа с файлами продукта
admin.files = {};

// Удаление файлов
admin.files.deleteFile = function(event) {
	$.ajax({
		url: $(event.target).attr('href'),
		type: 'post',
		success: function(data) {
			if (data === 'true')
			{
				$(event.target).parent('li').remove();
			}
		}
	});
};

admin.categoryFoto = {};

admin.categoryFoto.deleteFoto = function(event) {
	$.ajax({
		url: '/admin/deletecategoryfoto',
		type: 'post',
		data: {
			id: $(event.target).parent('li').data('uploaded-id')
		},
		success: function(data) {
			if (data === 'true')
			{
				$(event.target).parent('li').remove();
			}
		}
	});
};

admin.pages = {};

admin.pages.deleteFoto = function(event) {
	$.ajax({
		url: '/admin/deletestaticpagefoto',
		type: 'post',
		data: {
			id: $(event.target).parent('li').data('uploaded-id')
		},
		success: function(data) {
			if (data === 'true')
			{
				$(event.target).parent('li').remove();
			}
		}
	});
}
