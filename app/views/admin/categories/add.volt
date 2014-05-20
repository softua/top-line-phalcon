{% extends 'admin/layout/admin_one_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block content %}
	<div class="span12">
		{% if fullParentCategory is not null %}
			<h3>{{ fullParentCategory }}</h3>
		{% endif %}
		{% if errors is defined and errors is not empty %}
			{% for error in errors %}
				{% for message in error %}
					<p class="text-error">{{ message }}</p>
				{% endfor %}
			{% endfor %}
		{% endif %}
		{% if parent is defined and parent is not null %}
			<form class="form-horizontal" action="/admin/category/add/{{ parent }}" method="post">
		{% else %}
			<form class="form-horizontal" action="/admin/category/add/0" method="post">
		{% endif %}
			<div class="control-group">
				<label class="control-label" for="name">Категория:</label>
				<div class="controls">
					<input type="text" name="name" id="name" value="">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="sort">Порядок:</label>
				<div class="controls">
					<input type="text" name="sort" id="sort" value="0">
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<input type="submit" class="btn btn-success" value="Сохранить"/>
					<a class="btn btn-danger" href="/admin/categories">Вернуться к списку</a>
				</div>
			</div>
		</form>
	</div>
{% endblock %}