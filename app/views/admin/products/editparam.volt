{% extends 'admin/layout/admin_one_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block content %}
	<div class="span12">
		{{ partial('admin/partials/errors') }}
		<form class="form-horizontal" action="/admin/editparam/{{ data['id'] }}/" method="post">
			<div class="control-group">
				<label class="control-label" for="name">Название параметра:</label>
				<div class="controls">
					<input type="text" name="name" id="name" value="{{ data['name'] }}" data-provide="typeahead" data-items="5" data-source="{{ possibleValues|e }}" autocomplete="off">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="value">Значение параметра:</label>
				<div class="controls">
					<input type="text" name="value" id="value" value="{{ data['value'] }}" data-provide="typeahead" data-items="5" data-source="{{ possibleValues|e }}" autocomplete="off">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="sort">Порядок:</label>
				<div class="controls">
					<input type="text" name="sort" id="sort" value="{{ data['sort'] }}">
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<input type="submit" class="btn btn-success" value="Сохранить"/>
					<a class="btn btn-danger" href="/admin/editproduct/{{ data['product_id'] }}/">Вернуться к товару</a>
				</div>
			</div>
		</form>
	</div>
{% endblock %}