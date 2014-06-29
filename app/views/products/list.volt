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
		<h1 class="title title--dot">{{ name }}</h1>
		<div class="filter">
			<div class="filter__wrapper">
				<div class="filter__title">Сортировать по цене:</div>
				<div class="js-select">
					<div class="js-select__value">От больших к меньшим</div>
					<select data-filter="price" name="price">
						{% if sort is defined and sort is not empty %}
							{% if sort is 'ASC' %}
								<option value="DESC">От больших к меньшим</option>
								<option value="ASC" selected>От меньших к большим </option>
							{% else %}
								<option value="DESC" selected>От больших к меньшим</option>
								<option value="ASC">От меньших к большим </option>
							{% endif %}
						{% else %}
							<option value="DESC" selected>От больших к меньшим</option>
							<option value="ASC">От меньших к большим </option>
						{% endif %}
					</select>
				</div>
			</div>
		</div>

		{% if products.items is defined and products.items is not empty %}
			<div class="products-list--outer-wrapper">
				<ul class="products-list">
					{% for product in products.items %}
						<li class="products-list__item">
							<h2 class="products-list__title">
								<a href="{{ product['path'] }}" title="{{ product['name'] }}">{{ product['name'] }}</a>
							</h2>
							<figure class="products-list__img">
								<img src="{{ product['img'] }}" alt="{{ product['name'] }}"/>
							</figure>
							<div class="products-list__code">Артикул: {{ product['articul'] }}</div>
							{{ product['short_desc'] }}
						</li>
					{% endfor %}
				</ul><!-- end products-list -->

				{% if products.total_pages > 1 %}
					<ul class="pagination">
						{% if products.current is not products.first %}
							<li class="pagination__item pagination__item--prev">
								<a class="pagination__item__link" href="{{ products.links[0] }}" title="Первая">←</a>
							</li>
						{% endif %}

						{% for key, link in products.links %}
							{% if key + 1 is products.current %}
								<li class="pagination__item active">
									<a class="pagination__item__link" href="{{ link }}" title="{{ key + 1 }}">{{ key + 1 }}</a>
								</li>
							{% else %}
								<li class="pagination__item">
									<a class="pagination__item__link" href="{{ link }}" title="{{ key + 1 }}">{{ key + 1 }}</a>
								</li>
							{% endif %}
						{% endfor %}

						{% if products.current is not products.last %}
							<li class="pagination__item pagination__item--next">
								<a class="pagination__item__link" href="{{ products.links[products.last - 1] }}" title="Последняя">→</a>
							</li>
						{% endif %}
					</ul>
				{% endif %}
			</div>
		{% else %}
			В данной категории пока нет товаров
		{% endif %}
	</main>
{% endblock %}