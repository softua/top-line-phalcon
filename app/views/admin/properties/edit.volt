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
		<form class="form-horizontal" action="/admin/property/edit/{{ property._id }}" method="post">
			<div class="control-group">
				<label class="control-label" for="name">Свойство:</label>
				<div class="controls">
					<input type="text" name="name" id="name" value="{{ property.name }}">
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<input type="submit" class="btn btn-success" value="Сохранить"/>
					<a class="btn btn-primary" href="/admin/properties">Вернуться к списку</a>
				</div>
			</div>
		</form>
	</div>
{% endblock %}