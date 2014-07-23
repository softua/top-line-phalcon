{% extends 'layouts/two_column_layout.volt' %}
{% block breadcrumbs %}
	{% if page is defined and page is not empty %}
		<ul class="breadcrumbs" itemscope itemtype="http://schema.org/WebPage">
			<li class="breadcrumbs__item">
				<a class="breadcrumbs__item__link" href="/" title="Главная" itemprop="breadcrumb">Главная</a> -
			</li>
			<li class="breadcrumbs__item">
				<span class="breadcrumbs__item__current" itemprop="breadcrumb">{{ page.name }}</span>
			</li>
		</ul>
	{% else %}
		<ul class="breadcrumbs" itemscope itemtype="http://schema.org/WebPage">
			<li class="breadcrumbs__item">
				<a class="breadcrumbs__item__link" href="/" title="Главная" itemprop="breadcrumb">Главная</a> -
			</li>
			<li class="breadcrumbs__item">
				<span class="breadcrumbs__item__current" itemprop="breadcrumb">Ошибка</span>
			</li>
		</ul>
	{% endif %}
{% endblock %}

{% block content %}
	{% if page is defined and page is not empty %}
		<main class="main main--aside" role="main">
			<h1 class="title title--big">{{ page.name }}</h1>
			<div class="article">
				{{ page.full_content }}
				<div class="article__actions">
					<div class="article__actions__social">
						<div class="pluso" data-background="transparent" data-options="small,square,line,horizontal,nocounter,theme=08" data-services="yandex,vkontakte,facebook,twitter,livejournal,google,blogger"></div>
					</div>
					<a class="main__back" href="{{ url('company/news/') }}" title="">Все новости </a>
				</div>
			</div>
		</main>
	{% else %}
		<main class="main main--aside" role="main">
			<h1 class="title title--big">Страница не найдена</h1>
		</main>
	{% endif %}
{% endblock %}