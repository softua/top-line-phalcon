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
	<ol>
		{% for user in users %}
			<li>
				<a href="/admin/user/{{ user._id }}" title="Редактировать пользователя">{{ user.login }}</a> - <span>{{ user.name }}</span>
				{% for role in roles %}
					{% if role._id == user.role %}
						<p>Права: {{ role.description }}</p>
					{% endif %}
				{% endfor %}
				<a class="btn btn-danger" href="/admin/delete/user/{{ user._id }}">Удалить пользователя</a>
			</li>
		{% endfor %}
	</ol>
	{% endif %}
{% endblock %}