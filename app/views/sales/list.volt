{% extends 'layouts/two_column_layout.volt' %}

{% block breadcrumbs %}
	<ul class="breadcrumbs" itemscope itemtype="http://schema.org/WebPage">
		<li class="breadcrumbs__item">
			<a class="breadcrumbs__item__link" href="{{ url() }}" title="Главная" itemprop="breadcrumb">Главная</a> -
		</li>
		<li class="breadcrumbs__item">
			<span class="breadcrumbs__item__current" itemprop="breadcrumb">Акционные предложения</span>
		</li>
	</ul>
{% endblock %}

{% block content %}
	<main class="main main--aside" role="main">
		{% if data is defined and data is not empty %}
			<h1 class="title title--dot">Акционные предложения!</h1>
			<ul class="sales">
				{% for item in data.items %}
					<li class="sales__item sales__item--label-sales">
						<figure class="sales__item__img">
							{% set image = item.getImages() %}
							{% if image is defined and image is not empty %}
								<img src="{{ image[0].imgListPath }}" alt="{{ item.name }}"/>
							{% else %}
								<img src="{{ static_url('img/no_foto.png') }}" alt="{{ item.name }}"/>
							{% endif %}
							<figcaption class="sales__item__img__caption">
								{{ item.name }}
							</figcaption>
						</figure>
						<div class="sales__item__text-wrapper">
							{{ item.shortContent }}
							<a class="sales__item__more" href="{{ item.path }}" title="{{ item.name }}">Подробнее...</a>
						</div>
					</li>
				{% endfor %}
			</ul><!-- end sales -->

			{% if data.links is defined and data.links is not empty %}
				<ul class="pagination">
					{% for link in data.links %}
						{% if link.active is true %}
							<li class="pagination__item active">
								<a class="pagination__item__link" href="{{ link.href }}">{{ link.name }}</a>
							</li>
						{% else %}
							<li class="pagination__item">
								<a class="pagination__item__link" href="{{ link.href }}">{{ link.name }}</a>
							</li>
						{% endif %}
					{% endfor %}
				</ul>
			{% endif %}
		{% else %}
			<h1>Извините, акционных предложений нет.</h1>
		{% endif %}
	</main>
{% endblock %}