{% extends 'admin/layout/admin_one_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block content %}
	<a href="/admin/addpage" class="btn btn-primary">Добавить статическую страницу</a>
	<h3>Страницы</h3>
	{% if pages is defined and pages is not empty %}
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>#</th>
					<th>Название</th>
					<th>Тип</th>
					<th>Добавлено</th>
					<th>Ссылка</th>
					<th>Доступность</th>
					<th>Действия</th>
				</tr>
			</thead>
			<tbody>
				{% for page in pages %}
					<tr>
						<td>{{ loop.index }}</td>
						<td>{{ page['name'] }}</td>
						<td>{{ page['type'] }}</td>
						<td>{{ page['datetime'] }}</td>
						<td>{{ page['link'] }}</td>
						{% if page['public'] is 1 %}
							<td>Показывать</td>
						{% else %}
							<td>Не показывать</td>
						{% endif %}
						<td>
							<a href="{{ url('admin/deletepage/') }}{{ page['seo_name'] }}" class="btn btn-danger">Удалить</a>
							<a href="{{ url('admin/editpage/') }}{{ page['seo_name'] }}" class="btn">Редактировать</a>
						</td>
					</tr>
				{% endfor %}
			</tbody>
		</table>
	{% else %}
		Еще нет страниц
	{% endif %}
{% endblock %}