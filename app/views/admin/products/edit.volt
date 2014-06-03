{% extends 'admin/layout/admin_one_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block content %}
	<div class="span12">
		<a class="btn btn-primary" href="/admin/products">Вернуться к списку</a>
		{% if data is defined and data is not null %}

			<h2>Редактирование товара</h2>
			{{ partial('admin/partials/errors') }}
			<form action="/admin/editproduct/{{ id }}/" method="POST">
				<table class="table table-bordered table-hover">
					<tbody>
						<tr>
							<th><label for="seo-name">SEO название</label></th>
							<td>
								<input type="text" name="seo-name" id="seo-name" value="{{ data['seo_name'] }}"/>
							</td>
						</tr>
						<tr>
							<th><label for="name">Название</label></th>
							<td>
								<input type="text" name="name" id="name" value="{{ data['name'] }}"/>
							</td>
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
								<select name="main_curancy">
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
							<th>Технические характеристики:</th>
							<td>
								<a href="{{ data['id'] }}" class="btn" data-add-param="true">Добавить параметр</a>
								<ul class="parameters" data-parameters>
									{% if parameters is defined and parameters is not empty %}
										{% for param in parameters %}
											<li id="{{ param.id }}" class="parameters__item">
												<a href="/admin/deleteparam/{{ param.id }}/" data-delete-param="true" data-param-name="{{ param.name }}" class="btn btn-mini btn-danger">Удалить параметр</a>
												<a href="/admin/editparam/{{ param.id }}/" data-param-name="{{ param.name }}" class="btn btn-mini">Редактировать</a>
												<span>{{ param.name }}</span>
												<span> - </span>
												<span>{{ param.value }}</span>
											</li>
										{% endfor %}
									{% endif %}
								</ul>
							</td>
						</tr>
						<tr>
							<th>Категории</th>
							<td>
								<a href="{{ id }}" class="btn" data-addcategory data-categories-list='{{ categories }}'>Добавить категорию</a>
								<div data-categories>
									{% if data['categories'] is defined and data['categories'] is not empty %}
										{% for cat in data['categories'] %}
											<br>
											<a data-delete-category href="/admin/deleteproductcategory/{{ cat['id'] }}/{{ id }}/" class="btn btn-mini btn-danger">Удалить категорию</a>
											<span>{{ cat['full_name'] }}</span><br>
										{% endfor %}
									{% endif %}
								</div>
							</td>
						</tr>
						<tr>
							<th><label for="short_desc">Короткое описание:</label></th>
							<td><textarea class="tinymce" name="short_desc" id="short_desc" rows="10">{{ data['short_description'] }}</textarea></td>
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
							<th><label for="public">public</label></th>
							<td>
								{% if data['public'] is 1 %}
									<input type="checkbox" id="public" name="public" checked/>
								{% else %}
									<input type="checkbox" id="public" name="public"/>
								{% endif %}
							</td>
						</tr>
						<tr>
							<td><a href="/admin/deleteproduct/{{ data['id'] }}/" class="btn btn-danger">Удалить СОВСЕМ</a></td>
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