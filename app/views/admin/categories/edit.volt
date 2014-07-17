{% extends 'admin/layout/admin_one_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block content %}
	<div class="span12">
		{% if fullParentCategory is defined and fullParentCategory is not null %}
			<h3>{{ fullParentCategory }}</h3>
		{% else %}
			<h3>Корневая категория</h3>
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
			<div data-upload-foto-category="true" data-category-id="{{ category.id }}" class="btn btn-primary" style="margin: 0 0 20px 0;">
				Загрузить фото
				<input type="file" name="fotos" style="display: none;"/>
			</div>
			<div class="progress progress-striped active" data-progress-fotos="true" style="display: none;">
				<div class="bar" style="width: 0;">0%</div>
			</div>
			<ul data-uploaded-list="fotos-categories" data-category-id="{{ category.id }}" class="thumbnails">
				{% if fotos is defined and fotos is not empty %}
					{% for foto in fotos %}
						<li data-uploaded-id="{{ foto.id }}" data-delete-category-foto="true">
							<img src="{{ foto.imgPath }}" alt="{{ foto.name }}" class="thumbnail"/>
						</li>
					{% endfor %}
				{% endif %}
			</ul>
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