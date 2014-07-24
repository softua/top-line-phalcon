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
			{% if products is defined and products is not empty %}
				<ul class="products-list">
					{% for product in products %}
						<li class="products-list__item products-list__item--simple">
							<h2 class="products-list__title">
								{{ product.name }}
							</h2>
							<div class="products-list__code">Артикул: {{ product.articul }}</div>
							{% set param = product.getParams() %}
							{% if param %}
								<div class="products-list__code">{{ param[0] }}</div>
							{% endif %}
							{% if product.main_curancy is 'eur' %}
								<h2 class="products-list__price">
									Цена: {{ product.price_eur }} евро
								</h2>
							{% endif %}
							{% if product.main_curancy is 'usd' %}
								<h2 class="products-list__price">
									Цена: ${{ product.price_usd }}
								</h2>
							{% endif %}
							{% if product.main_curancy is 'uah' %}
								<h2 class="products-list__price">
									Цена: {{ product.price_uah }} грн
								</h2>
							{% endif %}
						</li>
					{% endfor %}
				</ul><!-- end products-list -->
			{% else %}
				В данной категории пока нет товаров
			{% endif %}
		</div>
	</main>
{% endblock %}