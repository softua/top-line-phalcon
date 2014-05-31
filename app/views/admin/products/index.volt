{% extends 'admin/layout/admin_two_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block sidebar %}
	{% if mainCategories is defined and mainCategories is not null %}
		<ul class="admin__catogories">
			{% for category in mainCategories %}
				<li class="admin__categories__item" data-category-id="{{ category.id }}">
					<a href="/admin/getproducts/{{ category.id }}/" data-action="open" data-editing="false">{{ category.name }}</a>
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
	{% if productsWithoutCategories is defined and productsWithoutCategories is not empty %}
		<h1>Товары без категорий:</h1>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>#</th>
					<th>Тип</th>
					<th>Артикул</th>
					<th>Модель</th>
					<th>Бренд</th>
					<th>Цена</th>
					<th>Public</th>
					<th>Действия</th>
				</tr>
			</thead>
			<tbody>
				{% for product in productsWithoutCategories %}
					<tr>
						<td>{{ loop.index }}</td>
						<td>{{ product.type }}</td>
						<td>{{ product.articul }}</td>
						<td>{{ product.model }}</td>
						<td>{{ product.brand }}</td>
						{% if product.main_curancy is 'eur' %}
							<td>{{ product.price_eur }} евро</td>
						{% endif %}
						{% if product.main_curancy is 'usd' %}
							<td>{{ product.price_usd }} $</td>
						{% endif %}
						{% if product.main_curancy is 'uah' %}
							<td>{{ product.price_usd }} грн.</td>
						{% endif %}
						{% if product.public is true %}
							<td>Показывать</td>
						{% else %}
							<td>Не показывать</td>
						{% endif %}
						<td><a href="/admin/editproduct/{{ product.id }}/" class="btn">Редактировать</a></td>
					</tr>
				{% endfor %}
			</tbody>
		</table>
	{% endif %}
{% endblock %}