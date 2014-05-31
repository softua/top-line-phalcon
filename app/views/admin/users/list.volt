{% extends 'admin/layout/admin_two_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block sidebar %}
<nav>
	<a class="btn" href="/admin/newUser">Новый пользователь</a>
</nav>
{% endblock %}

{% block content %}
	{% if users is not empty %}
	<table class="table table-hover">
		<caption>Users</caption>
		<thead>
			<tr>
				<th>№</th>
				<th>Логин</th>
				<th>Имя</th>
				<th>Права</th>
				<th>Действия</th>
			</tr>
		</thead>
		<tbody>
			{% for user in users %}
				<tr>
					<td>{{ loop.index }}</td>
					<td>{{ user.login }}</td>
					<td>{{ user.name }}</td>
					<td>
						{% if user.role_id %}
							{% for role in roles %}
								{% if user.role_id is role.id %}
									{{ role.description }}
								{% endif %}
							{% endfor %}
						{% endif %}
					</td>
					<td>
						<a class="btn" href="/admin/user/{{ user.id }}/" title="Редактировать пользователя">Редактировать</a>
						<a class="btn btn-danger" href="/admin/deleteUser/{{ user.id }}/" title="Удалить пользователя">Удалить</a>
					</td>
				</tr>
			{% endfor %}
		</tbody>
	</table>
	{% endif %}
{% endblock %}