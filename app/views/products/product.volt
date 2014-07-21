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
		{% if product is defined and product is not empty %}
			<div class="product">
				<h1 class="title">{{ product['name'] }}</h1>
				{% if product['articul'] is defined %}
					<div class="product__code">Артикул: {{ product['articul'] }}</div>
				{% endif %}
				<div class="product__info">
					{% if product['images'] is defined and product['images'] is not empty %}
						<div class="product__gallery">
							{% set images = product['images'] %}
							{% set mainImage = images[0] %}
							{% if product['sales'] %}
								<figure class="product__gallery__full product__gallery__full--sale">
									<a class="fancybox" href="{{ mainImage.imgOriginWPath }}">
										<img class="product__gallery__full__img" src="{{ mainImage.imgDescriptionPath }}" alt=""/>
									</a>
								</figure>
							{% else %}
								<figure class="product__gallery__full product__gallery__full">
									<a class="fancybox" href="{{ mainImage.imgOriginWPath }}">
										<img class="product__gallery__full__img" src="{{ mainImage.imgDescriptionPath }}" alt=""/>
									</a>
								</figure>
							{% endif %}
							<ul class="product__gallery__thumbs">
								{% for image in product['images'] %}
									<li class="product__gallery__thumbs__item">
										<a class="product__gallery__thumbs__link" href="{{ image.imgDescriptionPath }}" title="" data-big-img-path="{{ image.imgOriginWPath }}">
											<img src="{{ image.imgThumbPath }}" alt=""/>
										</a>
									</li>
								{% endfor %}
							</ul>
						</div>
					{% else %}
						<div class="product__gallery">
							<figure class="product__gallery__full">
								<img class="product__gallery__full__img" src="/img/no_foto.png" width="290" alt=""/>
							</figure>
						</div>
					{% endif %}
					<div class="product__info__wrapper">

						{% if product['sales'] %}
							<a href="{{ url('sales/product/') }}{{ product['seo_name'] }}" title="Перейкти к акциям с этим товаром">
								<img class="product__info__sale-btn" src="/public_html/img/sales/btn.png" alt="Скидки"/>
							</a>
						{% endif %}

						{% if product['novelty'] %}
							<img class="product__info__novelty" src="{{ static_url('img/novelty/novelty-btn.png') }}" alt="Новинка"/>
						{% endif %}

						{% if product['alt_price'] is defined %}
							<div class="product__info__instock product__info__instock--null">{{ product['alt_price'] }}</div>
							<div class="product__info__price">Стоимость уточняйте</div>
						{% else %}
							{% if product['main_curancy'] is 'eur' %}
								<div class="product__info__instock">Есть в наличии</div>
								<div class="product__info__price"><b>{{ product['price'] }}</b> евро </div>
							{% endif %}
							{% if product['main_curancy'] is 'usd' %}
								<div class="product__info__instock">Есть в наличии</div>
								<div class="product__info__price"><b>{{ product['price'] }}</b> $ </div>
							{% endif %}
							{% if product['main_curancy'] is 'uah' %}
								<div class="product__info__instock">Есть в наличии</div>
								<div class="product__info__price"><b>{{ product['price'] }}</b> грн </div>
							{% endif %}
						{% endif %}
						<div class="product__info__producer"><b>Производитель:</b>  {{ product['country'] }}</div>
						{% if product['brand'] is defined and product['brand'] is not empty %}
							<div class="product__info__brand"><b>Бренд:</b>  {{ product['brand'] }}</div>
						{% endif %}
						{% if product['short_desc'] is defined and product['short_desc'] is not empty %}
							<h2 class="product__title">Краткое описание:</h2>
							{{ product['short_desc'] }}
						{% endif %}
					</div>
				</div>
				{% if product['parameters'] is defined and product['parameters'] is not empty %}
					<h2 class="product__title">Технические характеристики:</h2>
					<ul class="product__specs">
						{% for key, value in product['parameters'] %}
							<li><b>{{ key }}</b> {{ value }}</li>
						{% endfor %}
					</ul>
				{% endif %}
				{% if product['full_desc'] is defined and product['full_desc'] is not empty %}
					<h2 class="product__title">Описание товара</h2>
					{{ product['full_desc'] }}
				{% endif %}


				<div class="product__actions">
					{% if product['video'] is defined and product['video'] is not empty %}
						{% for video in product['video'] %}
							<a class="product__actions__video" href="{{ video.href }}" title="Смотреть видео" target="_blank">
								{% if video.name %}
									{{ video.name }}
								{% else %}
									{{ video.href }}
								{% endif %}
							</a>
						{% endfor %}
					{% endif %}
					{% if product['files'] is defined and product['files'] is not empty %}
						{% for file in product['files'] %}
							<a class="product__actions__pdf" href="{{ static_url(file.path) }}" title="Открыть файл" target="_blank">{{ file.name }}</a>
						{% endfor %}
					{% endif %}
				</div>

			</div><!-- end product -->
		{% endif %}
	</main>
{% endblock %}