{% extends 'admin/layout/admin_one_column.volt' %}

{% block content %}
	<h3 style="text-align: center">Создание пользователя:</h3>
	{{ partial('admin/partials/errors') }}
	<div class="span1 offset5">
		{{ form('admin/newuser', 'style': 'text-align: center') }}
		<fieldset>
			{{ text_field('login', 'placeholder': 'Логин') }}
			{{ password_field('password', 'placeholder': 'Пароль') }}
			{{ text_field('name', 'placeholder': 'Имя') }}
			{{ email_field('email', 'placeholder': 'E-mail') }}
			<select name="role" id="role">
				<option value="">Выберите права...</option>
				{% for role in roles %}
					<option value="{{ role.id }}">{{ role.description }}</option>
				{% endfor %}
			</select>
			{{ submit_button('Создать', 'class': 'btn btn-success') }}
		</fieldset>
		{{ end_form }}
	</div>
{% endblock %}