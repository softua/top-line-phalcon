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

	// Загружаем и открываем категории
	admin.openCategory = function(e) {
		var href = $(e.target);
		var innerList = href.siblings('.admin__categories');
		var parent = href.parent();
		var parentId = parent.attr('data-category-id');

		if(innerList.hasClass('admin__categories--hidden')) {
			$.ajax({
				url: '/admin/getcategories/' + parentId,
				method: 'GET',
				dataType: 'JSON',
				success: function(data) {
					if(data != null) {
						innerList.empty();
						for(var i = 0; i < data.length; i++) {
							innerList.append('<li class="admin__categories__item" data-category-id="' + data[i]._id.$id + '"><a href="/admin/getproducts/' + data[i]._id.$id + '" data-action="open" data-editing="false">' + data[i].name + '</a><ul class="admin__categories admin__categories--hidden"></ul></li>');
						}
						innerList.removeClass('admin__categories--hidden');
					} else {
						var urlArray = this.url.split('/');
						admin.getProductsByCategoryId(urlArray[urlArray.length - 1]);
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
				url: '/admin/getcategories/' + parentId,
				method: 'GET',
				dataType: 'JSON',
				success: function(data) {
					if(data != null) {
						innerList.empty();
						for(var i = 0; i < data.length; i++) {
							innerList.append('<li class="admin__categories__item" data-category-id="' + data[i]._id.$id + '"><a href="/" class="btn btn-mini btn-primary" data-action="open" data-editing="true">+</a><a href="/admin/category/edit/' + data[i]._id.$id + '" title="Редактировать">' + data[i].name + ' (' + 'SEO - ' + data[i].seo + ', sort = ' + data[i].sort + ')' + '</a><ul class="admin__categories admin__categories--hidden"><li class="admin__categories__item"><a href="/admin/category/add/' + data[i]._id.$id + '" class="btn btn-success">Добавить категорию</a></li></ul></li>');
						}

						innerList.append('<li class="admin__categories__item"><a href="/admin/category/add/' + parentId + '" class="btn btn-success">Добавить категорию</a></li>');
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

	admin.getProductsByCategoryId = function(id) {
		if(id.length > 0) {
			alert('Выводим список товаров с категорией: ' + id);
			// TODO: Ajax запрос на получение товаров данной категории.
		}
	}

});