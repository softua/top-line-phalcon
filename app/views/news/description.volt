{% extends 'layouts/two_column_layout.volt' %}

{% block breadcrumbs %}
	<ul class="breadcrumbs" itemscope itemtype="http://schema.org/WebPage">
		<li class="breadcrumbs__item">
			<a class="breadcrumbs__item__link" href="/" title="Главная" itemprop="breadcrumb">Главная</a> -
		</li>
		<li class="breadcrumbs__item">
			<a class="breadcrumbs__item__link" href="{{ url('company/news/') }}" title="Главная" itemprop="breadcrumb">Новости</a> -
		</li>
		<li class="breadcrumbs__item">
			<span class="breadcrumbs__item__current" itemprop="breadcrumb">{{ news['name'] }}</span>
		</li>
	</ul>
{% endblock %}

{% block content %}
	<main class="main main--aside" role="main">
		<a class="main__back" href="{{ url('company/news/') }}">Назад к новостям</a>
		<article class="article">
			<h1 class="title">{{ news['name'] }}</h1>
			<time class="article__date" datetime="{{ news['time'] }}">{{ news['time'] }}</time>
			<figure class="article__main-img">
				<img src="{{ news['img'] }}" alt="{{ news['name'] }}"/>
			</figure>
			{{ news['full_content'] }}
			<div class="clear"></div>

			<div class="article__actions">
				<div class="article__actions__social">
					<div class="pluso" data-background="transparent" data-options="small,square,line,horizontal,nocounter,theme=08" data-services="yandex,vkontakte,facebook,twitter,livejournal,google,blogger"></div>
				</div>
				<a class="main__back" href="{{ url('company/news/') }}">Назад к новостям</a>
			</div>
		</article>
	</main>
{% endblock %}