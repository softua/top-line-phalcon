{% extends 'layouts/two_column_layout.volt' %}

{% block breadcrumbs %}
	<ul class="breadcrumbs" itemscope itemtype="http://schema.org/WebPage">
		<li class="breadcrumbs__item">
			<a class="breadcrumbs__item__link" href="/" title="Главная" itemprop="breadcrumb">Главная</a> -
		</li>
		<li class="breadcrumbs__item">
			<a class="breadcrumbs__item__link" href="/projects/" title="Проекты" itemprop="breadcrumb">О компании</a> -
		</li>
		<li class="breadcrumbs__item">
			<span class="breadcrumbs__item__current" itemprop="breadcrumb">{{ current_page['name'] }}</span>
		</li>
	</ul>
{% endblock %}

{% block content %}
	<main class="main main--aside" role="main">
		<article class="article">
			<h1 class="title">{{ current_page['name'] }}</h1>
			{{ current_page['full_content'] }}
			<div class="clear"></div>
		</article>
	</main>
{% endblock %}