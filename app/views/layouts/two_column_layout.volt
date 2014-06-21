<!DOCTYPE html>
<html lang="ru-RU">
{% block head %}
	{{ partial('partials/head') }}
{% endblock %}

<body>
<div id="wrapper">
	{% block header %}
		{{ partial('partials/header') }}
	{% endblock %}

	{% block breadcrumbs %}{% endblock %}

	<div id="container">

		{% block left_sidebar %}
			<aside class="sidebar" role="complementary">
				{{ partial('partials/sidebar/nav') }}
				{{ partial('partials/sidebar/price_link') }}
				{{ partial('partials/sidebar/top_products') }}
				{{ partial('partials/sidebar/info_block') }}
			</aside><!--end sidebar -->
		{% endblock %}

		{% block content %}{% endblock %}

	</div><!-- end #container -->

	{#<button class="btn-ask" type="button"></button>#}

	<div class="leaders">
		Наша компания - одна из лидирующих в Европе. Добро пожаловать к нам!
	</div>
</div><!-- end #wrapper -->

{% block footer %}
	{{ partial('partials/footer') }}
{% endblock %}

<!--[if lte IE 7]><script>window.msie = 7;</script><![endif]-->
<!--[if IE 8]><script>window.msie = 8;</script><![endif]-->
<script data-main="/js/main" src="/js/require.js"></script>
</body>
</html>