{% if errors is defined and errors is not empty %}
	{% for key, messages in errors %}
		{% if key is 'success' %}
			<div class="alert alert-block alert-success">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4>Отлично!</h4>
				{% for message in messages %}
					<p>{{ message }}</p>
				{% endfor %}
			</div>
		{% else %}
			<div class="alert alert-block alert-danger">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4>Ошибки в "{{ key }}!"</h4>
				{% for message in messages %}
					<p>{{ message }}</p>
				{% endfor %}
			</div>
		{% endif %}
	{% endfor %}
{% endif %}