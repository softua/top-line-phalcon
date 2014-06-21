{% extends 'admin/layout/admin_one_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block content %}
	<a href="/admin/addpage" class="btn btn-primary">Добавить статическую страницу</a>
	<h3>Страницы Компании</h3>
	{% if company_pages is defined and company_pages is not empty %}
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>#</th>
					<th>Название</th>
					<th>Тип</th>
					<th>Доступность</th>
					<th>Действия</th>
				</tr>
			</thead>
			<tbody>
				{% for page in company_pages %}
					<tr>
						<td>{{ loop.index }}</td>
						<td>{{ page['name'] }}</td>
						<td>{{ page['type'] }}</td>
						{% if page['public'] is 1 %}
							<td>Показывать</td>
						{% else %}
							<td>Не показывать</td>
						{% endif %}
						<td>
							<a href="/admin/deletepage/{{ page['seo_name'] }}" class="btn btn-danger">Удалить</a>
							<a href="/admin/editpage/{{ page['seo_name'] }}" class="btn">Редактировать</a>
						</td>
					</tr>
				{% endfor %}
			</tbody>
		</table>
	{% else %}
		Еще нет страниц Компании
	{% endif %}

	<h3>Страницы Проектов</h3>
	{% if project_pages is defined and project_pages is not empty %}
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>#</th>
					<th>Название</th>
					<th>Тип</th>
					<th>Доступность</th>
					<th>Действия</th>
				</tr>
			</thead>
			<tbody>
				{% for page in project_pages %}
					<tr>
						<td>{{ loop.index }}</td>
						<td>{{ page['name'] }}</td>
						<td>{{ page['type'] }}</td>
						{% if page['public'] is 1 %}
							<td>Показывать</td>
						{% else %}
							<td>Не показывать</td>
						{% endif %}
						<td>
							<a href="/admin/deletepage/{{ page['seo_name'] }}" class="btn btn-danger">Удалить</a>
							<a href="/admin/editpage/{{ page['seo_name'] }}" class="btn">Редактировать</a>
						</td>
					</tr>
				{% endfor %}
			</tbody>
		</table>
	{% else %}
		Еще нет Проектов
	{% endif %}
{% endblock %}