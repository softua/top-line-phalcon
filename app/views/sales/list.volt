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
							<img src="{{ item['img'] }}" alt="{{ item['name'] }}"/>
							<figcaption class="sales__item__img__caption">
								{{ item['name'] }}
							</figcaption>
						</figure>
						<div class="sales__item__text-wrapper">
							{{ item['short_description'] }}
							<a class="sales__item__more" href="{{ item['href'] }}" title="{{ item['name'] }}">Подробнее...</a>
						</div>
					</li>
				{% endfor %}
			</ul><!-- end sales -->

			{% if data.total_pages > 1 %}
				<ul class="pagination">
					{% if data.current != data.first %}
						<li class="pagination__item pagination__item--prev">
							<a class="pagination__item__link" href="{{ url('sales/?page=') }}{{ data.first }}">←</a>
						</li>
					{% endif %}

					{% for key, link in data.links %}
						{% if key == paginate.current %}
							<li class="pagination__item active">
								<a class="pagination__item__link" href="{{ link }}">{{ key + 1 }}</a>
							</li>
						{% else %}
							<li class="pagination__item">
								<a class="pagination__item__link" href="{{ link }}">{{ key + 1 }}</a>
							</li>
						{% endif %}
					{% endfor %}

					{% if data.current != data.last %}
						<li class="pagination__item pagination__item--next">
							<a class="pagination__item__link" href="{{ url('sales/?page=') }}{{ data.last }}">→</a>
						</li>
					{% endif %}

				</ul>
			{% endif %}
		{% else %}
			<h1>Извините, акционных предлжений нет.</h1>
		{% endif %}
	</main>
{% endblock %}