{% extends 'admin/layout/admin_one_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block content %}
	{% if data is defined and data is not null %}
		<div class="span12">
			<h1>{{ get_title()|striptags }}</h1>
			<h2>Такой товар возможно уже существует</h2>
			<h3>Ниже представлен список совпадений. Если представлены другие товары, отредактируйте SEO-название, чтобы оно отличалось</h3>
			<h4>(можно добавить "_", "-",...)</h4>
			{% if sameProducts is defined and sameProducts is not empty %}
				<ul>
					{% for product in sameProducts %}
						<li><a href="/admin/editproduct/{{ product.id }}/" target="_blank">{{ product.seo_name }}</a></li>
					{% endfor %}
				</ul>
			{% endif %}
			<form action="/admin/editseoname/{{ data['id'] }}/" method="POST">
				<fieldset>
					<legend>{{ data['seo_name'] }}</legend>
					<table class="table-bordered">
						<tr>
							<th>Тип</th>
							<td>{{ data['type'] }}</td>
						</tr>
						<tr>
							<th>Артикул</th>
							<td>{{ data['articul'] }}</td>
						</tr>
						<tr>
							<th>Модель</th>
							<td>{{ data['model'] }}</td>
						</tr>
						<tr>
							<th>Страна-производитель</th>
							<td>{{ data['country'] }}</td>
						</tr>
						<tr>
							<th>Бренд</th>
							<td>{{ data['brand'] }}</td>
						</tr>
						{% if data['main_curancy'] is 'uah' %}
							<tr>
								<th>Цена</th>
								<td>{{ data['price_uah'] }} грн.</td>
							</tr>
						{% elseif data['main_curancy'] is 'usd' %}
							<tr>
								<th>Цена</th>
								<td>{{ data['price_usd'] }} $</td>
							</tr>
						{% elseif data['main_curancy'] is 'eur' %}
							<tr>
								<th>Цена</th>
								<td>{{ data['price_eur'] }} евро</td>
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
					<input type="text" name="seo-name" id="seo-name" value="{{ data['seo_name'] }}"/>
					<input type="submit" class="btn btn-primary" value="Сохранить"/>
				</fieldset>
			</form>
		</div>
	{% else %}
		<h1>Нечего тут делать</h1>
	{% endif %}
{% endblock %}