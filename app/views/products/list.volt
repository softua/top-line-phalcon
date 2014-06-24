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
						<option value="1" selected>От больших к меньшим</option>
						<option value="2">От меньших к большим </option>
					</select>
				</div>
			</div>
		</div>

		{% if products is defined and products is not empty %}
			<div class="products-list--outer-wrapper">
				<ul class="products-list">
					{% for product in products %}
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

				{#{{ partial('partials/pagination') }}#}
			</div>
		{% else %}
			В данной категории пока нет товаров
		{% endif %}
	</main>
{% endblock %}