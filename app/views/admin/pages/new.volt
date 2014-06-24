{% extends 'admin/layout/admin_one_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block content %}
	<a href="/admin/pages" class="btn btn-primary">Вернуться к списку</a>
	<h2>Новая страница</h2>
	{{ partial('admin/partials/errors') }}
	<form action="/admin/addpage" method="post">
		<table class="table table-bordered">
			<tbody>
				<tr>
					<th>Название</th>
					<td>
						{% if page['name'] is defined %}
							<input type="text" name="name" placeholder="Название" value="{{ page['name'] }}"/>
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
					<th>Короткое описание</th>
					<td>
						{% if page['short_content'] is defined %}
							<textarea name="short-content" cols="30" rows="10" class="tinymce">{{ page['short_content'] }}</textarea>
						{% else %}
							<textarea name="short-content" cols="30" rows="10" class="tinymce"></textarea>
						{% endif %}
					</td>
				</tr>
				<tr>
					<th>Контент</th>
					<td>
						{% if page['short_content'] is defined %}
							<textarea name="full-content" cols="30" rows="50" class="tinymce">{{ page['full_content'] }}</textarea>
						{% else %}
							<textarea name="full-content" cols="30" rows="50" class="tinymce"></textarea>
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
						<a href="/admin/pages" class="btn btn-primary">Вернуться к списку</a>
					</td>
					<td>
						<input type="submit" value="Создать страницу" class="btn btn-success"/>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
{% endblock %}