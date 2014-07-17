{% extends 'admin/layout/admin_one_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block content %}
	<a href="{{ url('admin/pages/') }}" class="btn btn-primary">Вернуться к списку</a>
	<h2>Редактирование страницы</h2>
	{{ partial('admin/partials/errors') }}
	<form action="/admin/editpage/{{ page['seo_name'] }}" method="post">
		<table class="table table-bordered">
			<tbody>
				<tr>
					<th>Название</th>
					<td>
						{% if page['name'] is defined %}
							<input type="text" name="name" placeholder="Название" value="{{ page['name']|escape }}"/>
						{% else %}
							<input type="text" name="name" placeholder="Название"/>
						{% endif %}
					</td>
				</tr>
				<tr>
					<th title="Это поле не трогаем. Изменяем его только в случае, если валидация не пройдена и такое название уже существует.">СЕО название</th>
					<td>
						{% if page['seo_name'] is defined %}
							<input type="text" name="seo-name" placeholder="НЕ ТРОГАТЬ" value="{{ page['seo_name'] }}"/>
						{% else %}
							<input type="text" name="seo-name" placeholder="НЕ ТРОГАТЬ"/>
						{% endif %}

					</td>
				</tr>
				<tr>
					<th title="Принадлежность страницы к группе">Тип</th>
					<td>
						{% if page['types'] is defined and page['types'] is not empty %}
							<select name="type-id">
								{% for type in page['types'] %}
									{% if type['active'] is true %}
										<option value="{{ type['id'] }}" selected>{{ type['name'] }}</option>
									{% else %}
										<option value="{{ type['id'] }}">{{ type['name'] }}</option>
									{% endif %}
								{% endfor %}
							</select>
						{% endif %}
					</td>
				</tr>
				<tr>
					<th title="Дата окончания действия акции">Окончание акции</th>
					<td>
						{% if page['expiration'] is defined %}
							<input data-calendar="true" type="text" name="expiration" value="{{ page['expiration'] }}"/>
						{% else %}
							<input data-calendar="true" type="text" name="expiration"/>
						{% endif %}
					</td>
				</tr>
				<tr>
					<th>Короткое описание</th>
					<td>
						{% if page['short_content'] is defined %}
							<textarea name="short-content" cols="30" rows="10" class="editor">{{ page['short_content'] }}</textarea>
						{% else %}
							<textarea name="short-content" cols="30" rows="10" class="editor"></textarea>
						{% endif %}
					</td>
				</tr>
				<tr>
					<th>Контент</th>
					<td>
						{% if page['short_content'] is defined %}
							<textarea name="full-content" cols="30" rows="50" class="editor">{{ page['full_content'] }}</textarea>
						{% else %}
							<textarea name="full-content" cols="30" rows="50" class="editor"></textarea>
						{% endif %}
					</td>
				</tr>
				<tr>
					<th>Видео контент</th>
					<td>
						{% if page['video_content'] is defined %}
							<textarea name="video-content" cols="30" rows="10" placeholder="Код вставки видео">{{ page['video_content'] }}</textarea>
						{% else %}
							<textarea name="video-content" cols="30" rows="10" placeholder="Код вставки видео"></textarea>
						{% endif %}
					</td>
				</tr>
				<tr>
					<th>Meta keywords</th>
					<td>
						{% if page['meta_keywords'] is defined %}
							<input type="text" name="meta-keywords" placeholder="keywords" value="{{ page['meta_keywords'] }}"/>
						{% else %}
							<input type="text" name="meta-keywords" placeholder="keywords"/>
						{% endif %}
					</td>
				</tr>
				<tr>
					<th>Meta description</th>
					<td>
						{% if page['meta_description'] is defined %}
							<textarea name="meta-description" cols="30" rows="5" placeholder="description">{{ page['meta_description'] }}</textarea>
						{% else %}
							<textarea name="meta-description" cols="30" rows="5" placeholder="description"></textarea>
						{% endif %}
					</td>
				</tr>
				<tr>
					<th style="max-width: 200px;">Картинки страницы:</th>
					<td>
						<div data-upload-static-page-foto="true" data-page-id="{{ page['id'] }}" class="btn btn-primary" style="margin: 0 0 20px 0;">
							Загрузить фото
							<input type="file" name="fotos" multiple style="display: none;"/>
						</div>
						<div class="progress progress-striped active" data-progress-fotos="true" style="display: none;">
							<div class="bar" style="width: 0;">0%</div>
						</div>
						<ul data-uploaded-list="pages-fotos" data-page-id="{{ page['id'] }}" class="thumbnails">
							{% if page['fotos'] is defined and page['fotos'] is not empty %}
								{% for foto in page['fotos'] %}
									<li data-uploaded-id="{{ foto.id }}" data-delete-static-page-foto="true">
										<img src="{{ foto.imgAdminPath }}" alt="" class="thumbnail"/>
									</li>
								{% endfor %}
							{% endif %}
						</ul>
					</td>
				</tr>
				<tr>
					<th>Сортировка</th>
					<td>
						{% if page['sort'] is defined %}
							<input type="text" name="sort" placeholder="Индекс сортировки" value="{{ page['sort'] }}"/>
						{% else %}
							<input type="text" name="sort" placeholder="Индекс сортировки"/>
						{% endif %}
					</td>
				</tr>
				<tr>
					<th>Доступность</th>
					<td>
						{% if page['public'] is 'on' %}
							<input type="checkbox" name="public" checked/>
						{% else %}
							<input type="checkbox" name="public"/>
						{% endif %}
					</td>
				</tr>
				<tr>
					<td>
						<a href="{{ url('admin/pages/') }}" class="btn btn-primary">Вернуться к списку</a>
						<a href="/admin/deletepage/{{ page['seo_name'] }}" class="btn btn-danger">Удалить СОВСЕМ</a>
					</td>
					<td>
						<input type="submit" value="Сохранить" class="btn btn-success"/>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
{% endblock %}