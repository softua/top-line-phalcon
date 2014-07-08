{% extends 'admin/layout/admin_one_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block content %}
	<a href="/admin/addpage" class="btn btn-primary">Добавить статическую страницу</a>
	{% if pages is defined and pages is not empty %}
		{% for type, page in pages %}
			{% if page is defined and page is not empty %}
				<h3>Страницы типа "{{ type }}"</h3>
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<th>#</th>
							<th>Название</th>
							<th>Добавлено</th>
							{% if type is 'Видео' %}
								<th>Ссылка</th>
							{% endif %}
							<th>Доступность</th>
							<th>Действия</th>
						</tr>
					</thead>
					<tbody>
						{% for item in page %}
							<tr>
								<td>{{ loop.index }}</td>
								<td>{{ item['name'] }}</td>
								<td>{{ item['time'] }}</td>
								{% if type is 'Видео' %}
									<td>{{ url('video/#') }}{{ item['seo_name'] }}</td>
								{% endif %}
								{% if item['public'] is 1 %}
									<td>Показывать</td>
								{% else %}
									<td>Не показывать</td>
								{% endif %}
								<td>
									<a href="{{ url('admin/deletepage/') }}{{ item['seo_name'] }}" class="btn btn-danger">Удалить</a>
									<a href="{{ url('admin/editpage/') }}{{ item['seo_name'] }}" class="btn">Редактировать</a>
								</td>
							</tr>
						{% endfor %}
					</tbody>
				</table>
			{% endif %}
		{% endfor %}
	{% else %}
		Еще нет страниц
	{% endif %}
	<a href="/admin/addpage" class="btn btn-primary">Добавить статическую страницу</a>
{% endblock %}