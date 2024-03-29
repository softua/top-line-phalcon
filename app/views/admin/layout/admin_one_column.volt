<!DOCTYPE html>
<html>
{{ partial('admin/partials/head') }}
<body>
	<section class="container-fluid admin">
		<div class="row-fluid" style="margin-top: 5px">
			{% block menu %}{% endblock %}
		</div>
		<div class="row-fluid">
			<!-- Content -->
			{% block content %}{% endblock %}
		</div>
	</section>
	<script src="/js/vendor/bootstrap.min.js"></script>
	<script src="/js/admin_master.js"></script>
</body>
</html>