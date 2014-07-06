{% extends 'admin/layout/admin_one_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block content %}
	<div class="span12">
		<h2>Добавление основных параметров товара</h2>
		{{ partial('admin/partials/errors') }}
		<form action="/admin/addproduct" method="POST">
			<table class="table table-bordered table-hover">
				<tbody>
					<tr>
						<th><label for="name">Название:</label></th>
						<td><input type="text" name="name" value="{{ data['name'] }}" id="name" /></td>
					</tr>
					<tr>
						<th><label for="type">Тип:</label></th>
						<td>
							<input type="text" name="type" value="{{ data['type'] }}" id="type" data-provide="typeahead" data-items="5" data-source='{{ types }}' autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<th><label for="articul">Артикул:</label></th>
						<td>
							<input type="text" name="articul" value="{{ data['articul'] }}" id="articul"/>
						</td>
					</tr>
					<tr>
						<th><label for="model">Модель:</label></th>
						<td>
							<input type="text" name="model" value="{{ data['model'] }}" id="model"/>
						</td>
					</tr>
					<tr>
						<th><label for="country">Страна-производитель:</label></th>
						<td>
							<input type="text" name="country" value="{{ data['country'] }}" id="country" data-provide="typeahead" data-items="5" data-source='{{ countries }}' autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<th><label for="brand">Бренд:</label></th>
						<td>
							<input type="text" name="brand" value="{{ data['brand'] }}" id="brand" data-provide="typeahead" data-items="5" data-source='{{ brands }}' autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<th><label for="price">Цена:</label></th>
						<td>
							<input type="text" name="price" value="{{ data['price'] }}" id="price"/>
							<select name="main_curancy" id="main_curancy">
								{% if data['main_curancy'] is 'eur' %}
									<option value="eur" selected>Евро</option>
								{% else %}
									<option value="eur">Евро</option>
								{% endif %}
								{% if data['main_curancy'] is 'usd' %}
									<option value="usd" selected>Доллар США</option>
								{% else %}
									<option value="usd">Доллар США</option>
								{% endif %}
								{% if data['main_curancy'] is 'uah' %}
									<option value="uah" selected>Гривна</option>
								{% else %}
									<option value="uah">Гривна</option>
								{% endif %}
							</select>
							<select name="price_alternative">
								{% if data['price_alternative'] %}
									<option value="" disabled>Если цена = 0</option>
								{% else %}
									<option value="" disabled selected>Если цена = 0</option>
								{% endif %}
								{% if data['price_alternative'] and data['price_alternative'] is 'Нет в наличии' %}
									<option value="Нет в наличии" selected>Нет в наличии</option>
								{% else %}
									<option value="Нет в наличии">Нет в наличии</option>
								{% endif %}
								{% if data['price_alternative'] and data['price_alternative'] is 'Под заказ' %}
									<option value="Под заказ" selected>Под заказ</option>
								{% else %}
									<option value="Под заказ">Под заказ</option>
								{% endif %}
								{% if data['price_alternative'] and data['price_alternative'] is 'Еще какой-то вариант' %}
									<option value="Еще какой-то вариант" selected>Еще какой-то вариант</option>
								{% else %}
									<option value="Еще какой-то вариант">Еще какой-то вариант</option>
								{% endif %}
							</select>
						</td>
					</tr>
					<tr>
						<th><label for="short_desc">Короткое описание:</label></th>
						<td><textarea class="editor" name="short_desc" id="short_desc" rows="10">{{ data['short_description'] }}</textarea></td>
					</tr>
					<tr>
						<th><label for="full_desc">Полное описание:</label></th>
						<td><textarea class="editor" name="full_desc" id="full_desc" rows="20">{{ data['full_description'] }}</textarea></td>
					</tr>
					<tr>
						<th><label for="keywords">Meta keywords:</label></th>
						<td>
							<input type="text" name="keywords" value="{{ data['meta_keywords'] }}" id="keywords"/>
						</td>
					</tr>
					<tr>
						<th><label for="description">Meta description:</label></th>
						<td>
							<textarea name="description" id="description" rows="10">{{ data['meta_description'] }}</textarea>
						</td>
					</tr>
					<tr>
						<td><a class="btn btn-primary" href="/admin/products">Вернуться к списку</a></td>
						<td><input class="btn btn-success" type="submit" value="Создать"/></td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
{% endblock %}