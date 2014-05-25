{% extends 'admin/layout/admin_one_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block content %}
	{% if product is defined and product is not null %}
		<div class="span12">
			<h1>{{ get_title()|striptags }}</h1>
			<h2>Такой товар возможно уже существует</h2>
			<h3>Ниже представлен список совпадений. Если представлены другие товары, отредактируйте SEO-название, чтобы оно отличалось</h3>
			<h4>(можно добавить "_", "-",...)</h4>
			{% if sameProducts is defined and sameProducts is not empty %}
				<ul>
					{% for product in sameProducts %}
						<li><a href="/admin/editproduct/{{ product._id }}/" target="_blank">{{ product.seo_name }}</a></li>
					{% endfor %}
				</ul>
			{% endif %}
			<form action="/admin/editseoname/{{ product._id }}/" method="POST">
				<fieldset>
					<legend>{{ product.seo_name }}</legend>
					<table class="table-bordered">
						<tr>
							<th>Тип</th>
							<td>{{ product.type }}</td>
						</tr>
						<tr>
							<th>Артикул</th>
							<td>{{ product.articul }}</td>
						</tr>
						<tr>
							<th>Модель</th>
							<td>{{ product.model }}</td>
						</tr>
						<tr>
							<th>Страна-производитель</th>
							<td>{{ product.country }}</td>
						</tr>
						<tr>
							<th>Бренд</th>
							<td>{{ product.brand }}</td>
						</tr>
						{% if product.main_curancy is 'uah' %}
							<tr>
								<th>Цена</th>
								<td>{{ product.price_uah }} грн.</td>
							</tr>
						{% elseif product.main_curancy is 'usd' %}
							<tr>
								<th>Цена</th>
								<td>{{ product.price_usd }} $</td>
							</tr>
						{% elseif product.main_curancy is 'eur' %}
							<tr>
								<th>Цена</th>
								<td>{{ product.price_eur }} евро</td>
							</tr>
						{% endif %}
					</table>
					{#--Если есть ошибки, выводим их--#}
					{% if errors is defined and errors is not empty %}
						{% for error in errors %}
							{% for message in error %}
								<p class="text-error">{{ message }}</p>
							{% endfor %}
						{% endfor %}
					{% endif %}
					{#----------#}
					<label for="seo-name">SEO-название</label>
					<input type="text" name="seo-name" id="seo-name" value="{{ product.seo_name }}"/>
					<input type="submit" class="btn btn-primary" value="Сохранить"/>
				</fieldset>
			</form>
		</div>
	{% else %}
		<h1>Нечего тут делать</h1>
	{% endif %}
{% endblock %}