{% extends 'admin/layout/admin_one_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block content %}
	<div class="span12">
		{% if errors is defined and errors is not empty %}
			{% for error in errors %}
				{% for message in error %}
					<p class="text-error">{{ message }}</p>
				{% endfor %}
			{% endfor %}
		{% endif %}
		<form class="form-horizontal" action="/admin/user/{{ user._id }}" method="post">
			{% for key, value in user %}
				{% if key is 'login' %}
					<h2>{{ value }}</h2>
				{% elseif key is 'name' %}
					<div class="control-group">
						<label class="control-label" for="{{ key }}">Имя:</label>
						<div class="controls">
							<input type="text" name="{{ key }}" id="{{ key }}" value="{{ value }}">
						</div>
					</div>
				{% elseif key is 'password' or key is '_id' %}
					{% continue %}
				{% elseif key is 'email' %}
					<div class="control-group">
						<label class="control-label" for="{{ key }}">E-mail:</label>
						<div class="controls">
							<input type="text" name="{{ key }}" id="{{ key }}" value="{{ value }}">
						</div>
					</div>
				{% elseif key is 'role' %}
					<div class="control-group">
						<label class="control-label" for="{{ key }}">Права:</label>
						<div class="controls">
							<select name="{{ key }}" id="{{ key }}">
								{% for role in roles %}
									{% if role._id == value %}
										<option selected value="{{ role._id }}">{{ role.description }}</option>
									{% else %}
										<option value="{{ role._id }}">{{ role.description }}</option>
									{% endif %}
								{% endfor %}
							</select>
						</div>
					</div>
				{% endif %}
			{% endfor %}
			<div class="control-group">
				<div class="controls">
					<input type="submit" class="btn btn-success" value="Сохранить"/>
					<a class="btn btn-danger" href="/admin/users">Вернуться к списку</a>
				</div>
			</div>
		</form>
	</div>
{% endblock %}