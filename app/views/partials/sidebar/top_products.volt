{% if top_products is defined and top_products is not empty %}
	<div class="sidebar__block">
		<h6 class="sidebar__block__title">Лидеры продаж</h6>
		<ul class="sidebar__top">
			{% for top_product in top_products %}
				{% set img = top_product.getMainImage() %}
				<li class="sidebar__top__item">
					<a class="sidebar__top__item__img" href="{{ top_product.path }}" title="{{ top_product.name }}">
						<img src="{{ img.imgTopPath }}" alt="{{ top_product.name }}"/>
					</a>
					<a class="sidebar__top__item__link" href="{{ top_product.path }}" title="{{ top_product.name }}">
						{{ top_product.name }}
					</a>
				</li>
			{% endfor %}
		</ul><!-- end sidebar__top -->
	</div>
{% endif %}