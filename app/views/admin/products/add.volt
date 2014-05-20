{% extends 'admin/layout/admin_one_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block content %}
	<div class="span12">
		<h2>Добавление основных параметров товара</h2>
		{% if errors is defined and errors is not empty %}
			{% for error in errors %}
				{% for err in error %}
					<h4 class="text-error">{{ err }}</h4>
				{% endfor %}
			{% endfor %}
		{% endif %}
		<form action="/admin/addproduct" method="POST">
			<table class="table table-bordered table-hover">
				<tbody>
					<tr>
						<th><label for="type">Тип:</label></th>
						<td>
							<input type="text" name="type" value="{{ data['type'] }}" id="type" data-provide="typeahead" data-items="5" data-source='{{ types }}'/>
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
							<input type="text" name="country" value="{{ data['country'] }}" id="country" data-provide="typeahead" data-items="5" data-source='{{ countries }}'/>
						</td>
					</tr>
					<tr>
						<th><label for="brand">Бренд:</label></th>
						<td>
							<input type="text" name="brand" value="{{ data['brand'] }}" id="brand" data-provide="typeahead" data-items="5" data-source='{{ brands }}'/>
						</td>
					</tr>
					<tr>
						<td><label for="main_curancy">Основная валюта:</label></td>
						<td>
							<select name="main_curancy" id="main_curancy">
								<option value="eur">Евро</option>
								<option value="usd">Доллар США</option>
								<option value="uah">Гривна</option>
							</select>
						</td>
					</tr>
					<tr>
						<th><label for="price">Цена:</label></th>
						<td>
							<input type="text" name="price" value="{{ data['price'] }}" id="price"/>
						</td>
					</tr>
					<tr>
						<th><label for="short_desc">Короткое описание:</label></th>
						<td><textarea class="tinymce" name="short_desc" id="short_desc" rows="10"></textarea>{{ data['short_description'] }}</td>
					</tr>
					<tr>
						<th><label for="full_desc">Полное описание:</label></th>
						<td><textarea class="tinymce" name="full_desc" id="full_desc" rows="20">{{ data['full_description'] }}</textarea></td>
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