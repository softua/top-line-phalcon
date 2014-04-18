{% extends 'admin/layout/admin_one_column.volt' %}
{% block content %}
	<h3 class="text-error" style="text-align: center">{{ error }}</h3>
	<div class="span2 offset5">
		{{ form('admin/login', 'style': 'text-align: center') }}
		<fieldset>
			<legend>Вход:</legend>
			{{ text_field('login', 'placeholder': 'Логин') }}
			{{ password_field('password', 'placeholder': 'Пароль') }}
			{{ submit_button('Войти', 'class': 'btn') }}
		</fieldset>
		{{ end_form }}
	</div>
{% endblock %}