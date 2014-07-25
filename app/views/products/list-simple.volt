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
				<table class="products-list--table">
					<thead>
						<tr>
							<th>№ по каталогу</th>
							<th>Наименование</th>
							<th>Шт. в упак.</th>
							<th>Цена за единицу</th>
							<th>Цена за упаковку</th>
						</tr>
					</thead>
					<tbody>
						{% for product in products %}
							<tr>
								<td>{{ product.articul }}</td>

								<td class="products-list--table__centered">{{ product.name }}</td>

								{% set packageCount = product.getParamByName('Шт. в упак.') %}
								{% if packageCount %}
									<td class="products-list--table__centered">{{ packageCount.value }}</td>
								{% else %}
									<td class="products-list--table__centered">-</td>
								{% endif %}

								{% if product.main_curancy is 'eur' %}
									<td class="products-list--table__right">{{ product.price_eur }} евро</td>
								{% endif %}
								{% if product.main_curancy is 'usd' %}
									<td class="products-list--table__right">{{ product.price_usd }} $</td>
								{% endif %}
								{% if product.main_curancy is 'uah' %}
									<td class="products-list--table__right">{{ product.price_uah }} грн.</td>
								{% endif %}

								{% set packagePrice = product.getParamByName('Цена за упаковку') %}
								{% if packagePrice %}
									<td class="products-list--table__right">{{ packagePrice.value }}</td>
								{% else %}
									<td class="products-list--table__right">-</td>
								{% endif %}
							</tr>
						{% endfor %}
					</tbody>
				</table>
			{% else %}
				В данной категории пока нет товаров
			{% endif %}
		</div>
	</main>
{% endblock %}