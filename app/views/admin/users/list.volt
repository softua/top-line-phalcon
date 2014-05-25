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
						{% for role in roles %}
							{% if role._id == user.role %}
								{{ role.description }}
							{% endif %}
						{% endfor %}
					</td>
					<td>
						<a class="btn" href="/admin/user/{{ user._id }}/" title="Редактировать пользователя">Редактировать</a>
						<a class="btn btn-danger" href="/admin/deleteUser/{{ user._id }}/" title="Удалить пользователя">Удалить</a>
					</td>
				</tr>
			{% endfor %}
		</tbody>
	</table>
	{% endif %}
{% endblock %}