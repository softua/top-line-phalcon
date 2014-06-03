{% extends 'admin/layout/admin_one_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block content %}
	<div class="span12">
		{% if fullParentCategory is defined and fullParentCategory is not null %}
			<h3>{{ fullParentCategory }}</h3>
		{% endif %}
		{{ partial('admin/partials/errors') }}
		<form class="form-horizontal" action="/admin/editcategory/{{ category.id }}/" method="post">
			<div class="control-group">
				<label class="control-label" for="name">Категория:</label>
				<div class="controls">
					<input type="text" name="name" id="name" value="{{ category.name }}">
					{% if category.seo_name is not null %}
						<span class="help-block">СЕО название: {{ category.seo_name }}</span>
					{% endif %}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="sort">Порядок:</label>
				<div class="controls">
					<input type="text" name="sort" id="sort" value="{{ category.sort }}">
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<input type="submit" class="btn btn-success" value="Сохранить"/>
					<a class="btn btn-primary" href="/admin/categories">Вернуться к списку</a>
					<a class="btn btn-danger" href="/admin/deletecategory/{{ category.id }}/">Удалить категорию</a>
				</div>
			</div>
		</form>
	</div>
{% endblock %}