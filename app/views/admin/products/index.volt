{% extends 'admin/layout/admin_two_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block sidebar %}
	{% if mainCategories is defined and mainCategories is not null %}
		<ul class="admin__catogories">
			{% for category in mainCategories %}
				<li class="admin__categories__item" data-category-id="{{ category._id }}">
					<a href="/admin/getproducts/{{ category._id }}" data-action="open" data-editing="false">{{ category.name }}</a>
					<ul class="admin__categories admin__categories--hidden"></ul>
				</li>
			{% endfor %}
		</ul>
	{% endif %}
{% endblock %}

{% block content %}
	<a class="btn btn-primary" href="/admin/addproduct">Добавить новый товар</a>
	<div class="products">
		{% if products is defined and products is not null and products is iterable %}
			<h3>В БД - {{ products|length }} товар (ов, а)</h3>
			<h3>Выберите категорию слева для отображения списка товаров</h3>
		{% else %}
			<h3>Нет товаров</h3>
		{% endif %}
	</div>
{% endblock %}