/**
 * Created by Ruslan Koloskov
 * Date: 23.03.2014
 * Time: 10:50
 */
$(document).ready(function() {

	var admin = {};

	$('body').on('click', '[data-action="open"]', function(e) {
		admin.openCategory(e);
	});

	// Загружаем и открываем категории
	admin.openCategory = function(e) {
		e.preventDefault();

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
							innerList.append('<li class="admin__categories__item" data-category-id="' + data[i]._id.$id + '"><a href="/" class="btn btn-mini btn-primary" data-action="open">+</a><a href="/admin/category/edit/' + data[i]._id.$id + '" title="Редактировать">' + data[i].name + ' (' + 'SEO - ' + data[i].seo + ', sort = ' + data[i].sort + ')' + '</a><ul class="admin__categories admin__categories--hidden"><li class="admin__categories__item"><a href="/admin/category/add/' + data[i]._id.$id + '" class="btn btn-success">Добавить категорию</a></li></ul></li>');
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

});