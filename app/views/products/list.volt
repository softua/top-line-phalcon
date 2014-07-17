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
						<span class="breadcrumbs__item__current" itemprop="breadcrumb">{{ breadcrumb.name }}</span>
					</li>
				{% else %}
					<li class="breadcrumbs__item">
						<a class="breadcrumbs__item__link" href="{{ breadcrumb.link }}" title="{{ breadcrumb.name }}" itemprop="breadcrumb">{{ breadcrumb.name }}</a> -
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

		<div class="products-list--outer-wrapper">
			{% if data.items is defined and data.items is not empty %}
				<ul class="products-list">
					{% for product in data.items %}
						<li class="products-list__item">
							<h2 class="products-list__title">
								<a href="{{ product.path }}" title="{{ product.name }}">{{ product.name }}</a>
							</h2>
							<figure class="products-list__img">
								{% if product.hasImages() %}
									<img src="{{ product.getMainImage().imgListPath }}" alt="{{ product.name }}"/>
								{% else %}
									<img src="{{ static_url('img/no_foto.png') }}" alt="{{ product.name }}"/>
								{% endif %}

								{% if product.hasSales() %}
									<img class="products-list__img--sale" src="{{ static_url('img/sales/list.png') }}" alt="Акция"/>
								{% endif %}
								{% if product.novelty %}
									<img class="products-list__img--novelty" src="{{ static_url('img/novelty/list-1.png') }}" alt="Акция"/>
								{% endif %}
							</figure>
							<div class="products-list__code">Артикул: {{ product.articul }}</div>
							{{ product.short_description }}
						</li>
					{% endfor %}
				</ul><!-- end products-list -->
			{% else %}
				В данной категории пока нет товаров
			{% endif %}

			{% if data.links is defined and data.links is not empty %}
				<ul class="pagination">
					{% for link in data.links %}
						{% if link.name is data.first %}
							<li class="pagination__item pagination__item--prev">
								<a class="pagination__item__link" href="{{ link.href }}" title="Первая">←</a>
							</li>
						{% elseif link.name is data.last %}
							<li class="pagination__item pagination__item--next">
								<a class="pagination__item__link" href="{{ link.href }}" title="Последняя">→</a>
							</li>
						{% elseif link.active is true %}
							<li class="pagination__item active">
								<a class="pagination__item__link" href="{{ link.href }}" title="{{ link.name }}">{{ link.name }}</a>
							</li>
						{% else %}
							<li class="pagination__item">
								<a class="pagination__item__link" href="{{ link.href }}" title="{{ link.name }}">{{ link.name }}</a>
							</li>
						{% endif %}
					{% endfor %}
				</ul>
			{% endif %}
		</div>
	</main>
{% endblock %}