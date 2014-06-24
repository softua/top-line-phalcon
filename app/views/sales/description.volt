{% extends 'layouts/two_column_layout.volt' %}

{% block breadcrumbs %}
	<ul class="breadcrumbs" itemscope itemtype="http://schema.org/WebPage">
		<li class="breadcrumbs__item">
			<a class="breadcrumbs__item__link" href="{{ url() }}" title="Главная" itemprop="breadcrumb">Главная</a> -
		</li>
		<li class="breadcrumbs__item">
			<a class="breadcrumbs__item__link" href="{{ url('sales/') }}" title="Главная" itemprop="breadcrumb">Акционные предложения</a> -
		</li>
		<li class="breadcrumbs__item">
			<span class="breadcrumbs__item__current" itemprop="breadcrumb">{{ data['name'] }}</span>
		</li>
	</ul>
{% endblock %}

{% block content %}
	<main class="main main--aside" role="main">
		<article class="article article--background">
			<h1 class="title">{{ data['name'] }}</h1>
			<div class="cf">
				<figure class="article__img article__img--sales">
					<img src="{{ data['img'] }}" alt="{{ data['name'] }}"/>
					<figcaption class="article__img__caption">{{ data['name'] }}</figcaption>
				</figure>
			</div>
			{{ data['full_content'] }}
			<div class="article__actions">
				<a class="main__back" href="{{ url('sales/') }}" title="Акционные предложения">Назад</a>
				<a class="btn-next" href="{{ url('contacts#form') }}" title="Контакты">Подать заявку</a>
			</div>
		</article>
	</main>
{% endblock %}