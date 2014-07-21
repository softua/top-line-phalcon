{% extends 'layouts/two_column_layout.volt' %}

{% block breadcrumbs %}
	<ul class="breadcrumbs" itemscope itemtype="http://schema.org/WebPage">
		<li class="breadcrumbs__item">
			<a class="breadcrumbs__item__link" href="/" title="Главная" itemprop="breadcrumb">Главная</a> -
		</li>
		<li class="breadcrumbs__item">
			<a class="breadcrumbs__item__link" href="/projects/" title="Проекты" itemprop="breadcrumb">Проекты</a> -
		</li>
		<li class="breadcrumbs__item">
			<span class="breadcrumbs__item__current" itemprop="breadcrumb">{{ project.name }}</span>
		</li>
	</ul>
{% endblock %}

{% block content %}
	<main class="main main--aside" role="main">
		<a class="main__back" href="{{ url('projects/') }}" title="">Назад к проектам</a>
		<article class="article">
			<h1 class="title">{{ project.name }}</h1>
			{% set image = project.getMainImage() %}
			{% if image %}
				<figure class="article__main-img">
					<img src="{{ image.imgDescriptionPath }}" alt="{{ project.name }}"/>
				</figure>
			{% endif %}
			{{ project.full_content }}
			<div class="clear"></div>
		</article>
	</main>
{% endblock %}