<!DOCTYPE html>
<html>
{{ partial('admin/partials/head') }}
<body>
	<section class="container-fluid admin">
		<div class="row-fluid" style="margin-top: 5px">
			{% block menu %}{% endblock %}
		</div>
		<div class="row-fluid">
			<div class="span3">
				<!-- Sidebar -->
                {% block sidebar %}{% endblock %}
			</div>
			<div class="span9">
				<!-- Content -->
                {% block content %}{% endblock %}
			</div>
		</div>
	</section>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    {{ javascript_include('js/vendor/jquery-1.11.0.min.js') }}
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    {{ javascript_include('js/vendor/bootstrap.min.js') }}
</body>
</html>