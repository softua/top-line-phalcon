{% extends 'admin/layout/admin_one_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block content %}
	{% if properties is not null %}
		<a class="btn btn-primary" href="/admin/property/add" title="Добавить параметр">Добавить параметр</a>
		<table class="table table-hover">
			<thead>
				<tr>
					<th>№</th>
					<th>Описание</th>
					<th>Название</th>
					<th>Действия</th>
				</tr>
			</thead>
			<tbody>
				{% for prop in properties %}
					<tr>
						<td>{{ loop.index }}</td>
						<td>{{ prop.desc }}</td>
						<td>{{ prop.name }}</td>
						<td><a class="btn" href="/admin/property/edit{{ prop._id }}">Редактировать</a></td>
					</tr>
				{% endfor %}
			</tbody>
		</table>
	{% else %}
		<h2>Пока что нет параметров</h2>
		<a class="btn btn-primary" href="/admin/property/add" title="Добавить параметр">Добавить параметр</a>
	{% endif %}
{% endblock %}