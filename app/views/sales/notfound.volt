{% extends 'layouts/two_column_layout.volt' %}

{% block breadcrumbs %}
	<ul class="breadcrumbs" itemscope itemtype="http://schema.org/WebPage">
		<li class="breadcrumbs__item">
			<a class="breadcrumbs__item__link" href="{{ url() }}" title="Главная" itemprop="breadcrumb">Главная</a> -
		</li>
		<li class="breadcrumbs__item">
			<a class="breadcrumbs__item__link" href="{{ url('sales/') }}" title="Акционные предложения" itemprop="breadcrumb">Акционные предложения</a> -
		</li>
		<li class="breadcrumbs__item">
			<span class="breadcrumbs__item__current" itemprop="breadcrumb">Ошибка 404</span>
		</li>
	</ul>
{% endblock %}

{% block content %}
	<main class="main main--aside" role="main">
		<h1>Ошибка 404</h1>
		<h2>Акция не найдена</h2>
	</main>
{% endblock %}