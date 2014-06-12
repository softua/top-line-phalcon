<!DOCTYPE html>
<html lang="ru-RU">
{% block head %}
	{{ partial('partials/head') }}
{% endblock %}

<body id="main-page">
	<div id="wrapper">
		{% block header %}
			{{ partial('partials/header') }}
		{% endblock %}

		<div id="container">
			{% block content %}{% endblock %}
		</div><!-- end #container -->

		<!--<button class="btn-ask" type="button"></button>-->
	</div><!-- end #wrapper -->

	{% block footer %}{% endblock %}

<!--[if lte IE 7]><script>window.msie = 7;</script><![endif]-->
<!--[if IE 8]><script>window.msie = 8;</script><![endif]-->
<script data-main="js/main" src="js/require.js"></script>
</body>
</html>