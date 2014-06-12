{% extends 'layouts/two_column_layout.volt' %}

{% block breadcrumbs %}
	{% if breadcrumbs is defined and breadcrumbs is not empty %}
		<ul class="breadcrumbs" itemscope itemtype="http://schema.org/WebPage">
			<li class="breadcrumbs__item">
				<a class="breadcrumbs__item__link" href="/" title="Главная" itemprop="breadcrumb">Главная</a> -
			</li>
			<li class="breadcrumbs__item">
				<a class="breadcrumbs__item__link" href="/catalog" title="Каталог" itemprop="breadcrumb">Каталог</a> -
			</li>
		{% for breadcrumb in breadcrumbs %}
			{% if loop.last %}
				<li class="breadcrumbs__item">
					<span class="breadcrumbs__item__current" itemprop="breadcrumb">{{ breadcrumb['name'] }}</span>
				</li>
			{% else %}
				<li class="breadcrumbs__item">
					<a class="breadcrumbs__item__link" href="{{ breadcrumb['path'] }}" title="{{ breadcrumb['name'] }}" itemprop="breadcrumb">{{ breadcrumb['name'] }}</a> -
				</li>
			{% endif %}
		{% endfor %}
		</ul>
	{% else %}
		<ul class="breadcrumbs" itemscope itemtype="http://schema.org/WebPage">
			<li class="breadcrumbs__item">
				<a class="breadcrumbs__item__link" href="/" title="Главная" itemprop="breadcrumb">Главная</a> -
			</li>
			<li class="breadcrumbs__item">
				<span class="breadcrumbs__item__current" itemprop="breadcrumb">Каталог</span>
			</li>
		</ul>
	{% endif %}
{% endblock %}

{% block content %}
	<main class="main main--aside" role="main">
		{% if categories is defined and categories is not empty %}
			<ul class="catalog">
				{% for category in categories %}
					<li class="catalog__item">
						<a class="catalog__item__link" href="{{ category['path'] }}" title="{{ category['name'] }}">
							<figure class="catalog__item__img">
								<img src="{{ category['img'] }}" alt="{{ category['name'] }}"/>
							</figure>
							{{ category['name'] }}
						</a>
					</li>
				{% endfor %}
			</ul><!-- end catalog -->
		{% endif %}
	</main>
{% endblock %}