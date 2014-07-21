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
			<form action="/admin/editproduct/{{ id }}/" method="POST" enctype="multipart/form-data">
				<table class="table table-bordered table-hover product-editing">
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
							<th style="max-width: 200px;">Картинки товара:</th>
							<td>
								<div data-upload-foto="true" data-product-id="{{ data['id'] }}" class="btn btn-primary" style="margin: 0 0 20px 0;">
									Загрузить фото
									<input type="file" name="fotos" multiple style="display: none;"/>
								</div>
								<div class="progress progress-striped active" data-progress-fotos="true" style="display: none;">
									<div class="bar" style="width: 0;">0%</div>
								</div>
								<ul data-uploaded-list="fotos" data-product-id="{{ data['id'] }}" class="thumbnails">
									{% if fotos is defined and fotos is not empty %}
										{% for foto in fotos %}
											<li data-uploaded-id="{{ foto.id }}" data-delete-foto="true">
												<img src="{{ foto.imgAdminPath }}" alt="" class="thumbnail"/>
											</li>
										{% endfor %}
									{% endif %}
								</ul>
							</td>
						</tr>
						<tr>
							<th style="max-width: 200px;">Видео</th>
							<td>
								<a data-add-video="true" href="/admin/addvideo" class="btn">Добавить видео</a>
								<ul class="videos" data-product-id="{{ data['id'] }}">
									{% if data['video'] is defined and data['video'] is not empty %}
										{% for video in data['video'] %}
											<li data-video-id="{{ video['id'] }}" class="videos__item">
												<a data-video-delete="true" href="" class="btn btn-mini btn-danger">Удалить</a>
												<a data-video-edit="true" href="" class="btn btn-mini">Редактировать</a>
												<a href="{{ video['href'] }}" title="Смотреть видео" target="_blank">{{ video['name'] }}</a>
											</li>
										{% endfor %}
									{% endif %}
								</ul>
							</td>
						</tr>
						<tr>
							<th style="max-width: 200px;">Файлы:</th>
							<td>
								<div data-upload-file="true" data-product-id="{{ data['id'] }}" class="btn btn-primary" style="margin: 0 0 20px 0;">
									Добавить файлы
									<input type="file" name="files" multiple style="display: none;"/>
								</div>
								<div class="progress progress-striped active" data-progress-files="true" style="display: none;">
									<div class="bar" style="width: 0;">0%</div>
								</div>
								<ul data-uploaded-list="files" data-product-id="{{ data['id'] }}" class="thumbnails">
									{% if files is defined and files is not empty %}
										{% for file in files %}
											<li data-uploaded-id="{{ file.id }}" data-delete-file="true">
												<a href="/admin/deletefile/{{ file.id }}/" class="btn btn-danger">Удалить файл</a>
												{{ file.name }}
											</li>
										{% endfor %}
									{% endif %}
								</ul>
							</td>
						</tr>
						<tr>
							<th>Акции</th>
							<td>
								<a href="{{ data['id'] }}" class="btn btn-primary" data-add-sale='{{ allSales }}'>Добавить акцию</a>
								<ul class="sales" data-product-id="{{ data['id'] }}">
									{% if sales is defined and sales is not empty %}
										{% for sale in sales %}
											<li>
												<a href="{{ sale.id }}" class="btn btn-danger" data-delete-sale="true">Удалить товар из акции</a>
												<a href="{{ sale.path }}" target="_blank">{{ sale.name }}</a>
											</li>
										{% endfor %}
									{% endif %}
								</ul>
							</td>
						</tr>
						<tr>
							<th><label for="public">Опубликовать</label></th>
							<td>
								{% if data['public'] is 1 %}
									<input type="checkbox" id="public" name="public" checked/>
								{% else %}
									<input type="checkbox" id="public" name="public"/>
								{% endif %}
							</td>
						</tr>
						<tr>
							<th><label for="top">Топ</label></th>
							<td>
								{% if data['top'] is 1 %}
									<input type="checkbox" id="top" name="top" checked/>
								{% else %}
									<input type="checkbox" id="top" name="top"/>
								{% endif %}
							</td>
						</tr>
						<tr>
							<th><label for="top">Новинка</label></th>
							<td>
								{% if data['novelty'] is 1 %}
									<input type="checkbox" id="novelty" name="novelty" checked/>
								{% else %}
									<input type="checkbox" id="novelty" name="novelty"/>
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