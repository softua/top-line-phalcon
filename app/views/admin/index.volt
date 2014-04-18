{% extends 'admin/layout/admin_one_column.volt' %}
{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}
{% block content %}
	<h1 style="text-align: center">
		Добро пожаловать, {{ user.name }}.
	</h1>
	<h3 style="text-align: center">
		Для продолжения работы выберите раздел в верхнем меню
	</h3>
{% endblock %}