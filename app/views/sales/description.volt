{% extends 'layouts/two_column_layout.volt' %}

{% block breadcrumbs %}
	<ul class="breadcrumbs" itemscope itemtype="http://schema.org/WebPage">
		<li class="breadcrumbs__item">
			<a class="breadcrumbs__item__link" href="{{ url() }}" title="Главная" itemprop="breadcrumb">Главная</a> -
		</li>
		<li class="breadcrumbs__item">
			<a class="breadcrumbs__item__link" href="{{ url('sales/') }}" title="Главная" itemprop="breadcrumb">Акционные предложения</a> -
		</li>
		<li class="breadcrumbs__item">
			<span class="breadcrumbs__item__current" itemprop="breadcrumb">{{ page.name }}</span>
		</li>
	</ul>
{% endblock %}

{% block content %}
	<main class="main main--aside" role="main">
		<article class="article article--background">
			<h1 class="title">{{ page.name }}</h1>
			<div class="cf">
				<figure class="article__img article__img--sales">
					{% if page.getMainImage() is not false %}
						<img src="{{ page.getMainImage().imgDescriptionPath }}" alt="{{ page.name }}"/>
					{% else %}
						<img src="{{ static_url('img/no_foto.png') }}" alt="{{ page.name }}"/>
					{% endif %}
					<figcaption class="article__img__caption">{{ page.name }}</figcaption>
				</figure>
			</div>
			{{ page.fullContent }}
			<div class="article__actions">
				<a class="main__back" href="{{ url('sales/') }}" title="Акционные предложения">Назад</a>
				<a class="btn-next" href="{{ url('contacts#form') }}" title="Контакты">Подать заявку</a>
			</div>
			{% if products.items is defined and products.items is not empty %}
				<ul class="products-list">

					{% for product in products.items %}
						<li class="products-list__item">
							<h2 class="products-list__title">
								<a href="{{ product.path }}" title="{{ product.name }}">{{ product.name }}</a>
							</h2>
							<figure class="products-list__img">
								<img src="{{ product.getMainImageForList() }}" alt="{{ product.name }}"/>
							</figure>
							<div class="products-list__code">Артикул: {{ product.articul }}</div>
							{{ product.short_description }}
						</li>
					{% endfor %}
				</ul><!-- end products-list -->
			{% endif %}

			{% if products.links is defined and products.links is not empty %}
				<ul class="pagination">
					{% for link in products.links %}
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

		</article>
	</main>
{% endblock %}