{% extends 'admin/layout/admin_one_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block content %}
	<div class="span12">
		<h2>Настройки</h2>
		{{ partial('admin/partials/errors') }}
		<form action="/admin/settings" method="post">
			{% if data is defined %}
				<h3>Курсы валют:</h3>
				<table class="table table-bordered">
					<tbody>
						<tr>
							<th><label for="curancy_eur">Курс евро:</label></th>
							<td><input type="text" name="curancy_eur" id="curancy_eur" value="{{ data['curancy_eur'] }}" class="input-mini"/> грн.</td>
						</tr>
						<tr>
							<th><label for="curancy_usd">Курс usd:</label></th>
							<td><input type="text" name="curancy_usd" id="curancy_usd" value="{{ data['curancy_usd'] }}" class="input-mini"/> грн.</td>
						</tr>
						<tr>
							<th></th>
							<td><input type="submit" class="btn btn-success" value="Сохранить"/></td>
						</tr>
					</tbody>
				</table>
			{% endif %}
		</form>
	</div>
{% endblock %}