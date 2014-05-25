{% extends 'admin/layout/admin_one_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block content %}
	<div class="span12">
		<a class="btn btn-primary" href="/admin/products">Вернуться к списку</a>
		{% if product is defined and product is not null %}

			<h2>Редактирование товара</h2>
			{% if success is defined and success is not null %}
				<div class="alert alert-success">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<h4>Поздравляем!</h4>{{ success }}
				</div>
			{% endif %}

			{% if errors is defined and errors is not empty %}
				{% for error in errors %}
					{% for err in error %}
						<h4 class="text-error">{{ err }}</h4>
					{% endfor %}
				{% endfor %}
			{% endif %}

			<form action="/admin/editproduct/{{ id }}/" method="POST">
				<table class="table table-bordered table-hover">
					<tbody>
						<tr>
							<th><label for="seo-name">SEO название</label></th>
							<td>
								<input type="text" name="seo-name" id="seo-name" value="{{ product['seo_name'] }}"/>
							</td>
						</tr>
						<tr>
							<th><label for="name">Название</label></th>
							<td>
								<input type="text" name="name" id="name" value="{{ product['name'] }}"/>
							</td>
						</tr>
						<tr>
							<th><label for="type">Тип:</label></th>
							<td>
								<input type="text" name="type" value="{{ product['type'] }}" id="type" data-provide="typeahead" data-items="5" data-source='{{ types }}' autocomplete="off"/>
							</td>
						</tr>
						<tr>
							<th><label for="articul">Артикул:</label></th>
							<td>
								<input type="text" name="articul" value="{{ product['articul'] }}" id="articul"/>
							</td>
						</tr>
						<tr>
							<th><label for="model">Модель:</label></th>
							<td>
								<input type="text" name="model" value="{{ product['model'] }}" id="model"/>
							</td>
						</tr>
						<tr>
							<th><label for="country">Страна-производитель:</label></th>
							<td>
								<input type="text" name="country" value="{{ product['country'] }}" id="country" data-provide="typeahead" data-items="5" data-source='{{ countries }}' autocomplete="off"/>
							</td>
						</tr>
						<tr>
							<th><label for="brand">Бренд:</label></th>
							<td>
								<input type="text" name="brand" value="{{ product['brand'] }}" id="brand" data-provide="typeahead" data-items="5" data-source='{{ brands }}' autocomplete="off"/>
							</td>
						</tr>
						<tr>
							<th><label for="price">Цена:</label></th>
							<td>
								{% if product['main_curancy'] is 'eur' %}
									<input type="text" name="price" value="{{ product['price_eur'] }}" id="price"/>
								{% endif %}
								{% if product['main_curancy'] is 'usd' %}
									<input type="text" name="price" value="{{ product['price_usd'] }}" id="price"/>
								{% endif %}
								{% if product['main_curancy'] is 'uah' %}
									<input type="text" name="price" value="{{ product['price_uah'] }}" id="price"/>
								{% endif %}

								<select name="main_curancy">
									{% if product['main_curancy'] is 'eur' %}
										<option value="eur" selected>Евро</option>
									{% else %}
										<option value="eur">Евро</option>
									{% endif %}
									{% if product['main_curancy'] is 'usd' %}
										<option value="usd" selected>Доллар США</option>
									{% else %}
										<option value="usd">Доллар США</option>
									{% endif %}
									{% if product['main_curancy'] is 'uah' %}
										<option value="uah" selected>Гривна</option>
									{% else %}
										<option value="uah">Гривна</option>
									{% endif %}
								</select>
							</td>
						</tr>
						<tr>
							<th>Технические характеристики:</th>
							<td>
								<a href="#" class="btn" data-parameter-add="true">Добавить параметр</a>
							</td>
						</tr>
						<tr>
							<th>Категории</th>
							<td>
								<a href="{{ id }}" class="btn" data-addcategory data-categories-list='{{ categories }}'>Добавить категорию</a>
								<div data-categories>
									{% if productCats is defined and productCats is not empty %}
										{% for cat in productCats %}
											<br>
											<a data-delete-category href="/admin/deleteproductcategory/{{ cat['id'] }}/{{ id }}/" class="btn btn-danger">Удалить</a>
											<span>{{ cat['full_name'] }}</span><br>
										{% endfor %}
									{% endif %}
								</div>
							</td>
						</tr>
						<tr>
							<th><label for="short_desc">Короткое описание:</label></th>
							<td><textarea class="tinymce" name="short_desc" id="short_desc" rows="10">{{ product['short_description'] }}</textarea></td>
						</tr>
						<tr>
							<th><label for="full_desc">Полное описание:</label></th>
							<td><textarea class="tinymce" name="full_desc" id="full_desc" rows="20">{{ product['full_description'] }}</textarea></td>
						</tr>
						<tr>
							<th><label for="keywords">Meta keywords:</label></th>
							<td>
								<input type="text" name="keywords" value="{{ product['meta_keywords'] }}" id="keywords"/>
							</td>
						</tr>
						<tr>
							<th><label for="description">Meta description:</label></th>
							<td>
								<textarea name="description" id="description" rows="10">{{ product['meta_description'] }}</textarea>
							</td>
						</tr>
						<tr>
							<th><label for="public">public</label></th>
							<td>
								{% if product['public'] is 'on' %}
									<input type="checkbox" id="public" name="public" checked/>
								{% else %}
									<input type="checkbox" id="public" name="public"/>
								{% endif %}
							</td>
						</tr>
						<tr>
							<td><a href="/admin/deleteproduct/{{ product['_id'] }}/" class="btn btn-danger">Удалить СОВСЕМ</a></td>
							<td>
								<input class="btn btn-success" type="submit" value="Сохранить"/>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		{% endif %}
	</div>
{% endblock %}