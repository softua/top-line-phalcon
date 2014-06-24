{% extends 'layouts/two_column_layout.volt' %}

{% block breadcrumbs %}
	<ul class="breadcrumbs" itemscope itemtype="http://schema.org/WebPage">
		<li class="breadcrumbs__item">
			<a class="breadcrumbs__item__link" href="/" title="Главная" itemprop="breadcrumb">Главная</a> -
		</li>
		<li class="breadcrumbs__item">
			<span class="breadcrumbs__item__current" itemprop="breadcrumb">Новости</span>
		</li>
	</ul>
{% endblock %}

{% block content %}
	<main class="main main--aside" role="main">
		<h1 class="title title--big">Новости</h1>
		<div class="article">
			<div class="cf">
				<figure class="article__img article__img--skew">
					<img src="{{ static_url('img/dummy/skew.jpg') }}" alt=""/>
					<figcaption class="article__img__caption">Новейшее шиномонтажное оборудование</figcaption>
				</figure>
			</div>
			<h2 class="title"><span class="title__wrapper">Главные новости</span></h2>
			{% if paginate.items is defined and paginate.items is not empty %}
				<ul class="news-list">
					{% for item in paginate.items %}
						{% if loop.index is even %}
							<li class="news-list__item news-list__item--even">
						{% else %}
							<li class="news-list__item">
						{% endif %}
							<h3 class="news-list__item__title">{{ item['name'] }}</h3>
							<figure class="news-list__item__img">
								<img src="{{ item['img'] }}" alt="{{ item['name'] }}"/>
							</figure>
							{{ item['short_content'] }}
							<a href="{{ url('company/news/') }}{{ item['seo_name'] }}">Подробнее ...</a>
						</li>
					{% endfor %}
				</ul>
			{% endif %}

			{% if paginate.total_pages > 1 %}
				<ul class="pagination">
					{% if paginate.current != paginate.first %}
						<li class="pagination__item pagination__item--prev">
							<a class="pagination__item__link" href="{{ url('company/news/?page=') }}{{ paginate.first }}">←</a>
						</li>
					{% endif %}

					{% for link in paginate.links %}
						{% if link['page'] is paginate.current %}
							<li class="pagination__item active">
								<a class="pagination__item__link" href="{{ link['href'] }}">{{ link['page'] }}</a>
							</li>
						{% else %}
							<li class="pagination__item">
								<a class="pagination__item__link" href="{{ link['href'] }}">{{ link['page'] }}</a>
							</li>
						{% endif %}
					{% endfor %}

					{% if paginate.current != paginate.last %}
						<li class="pagination__item pagination__item--next">
							<a class="pagination__item__link" href="{{ url('company/news/?page=') }}{{ paginate.last }}">→</a>
						</li>
					{% endif %}

				</ul>
			{% endif %}
			<div class="article__actions">
				<div class="article__actions__social">
					<div class="pluso" data-background="transparent" data-options="small,square,line,horizontal,nocounter,theme=08" data-services="yandex,vkontakte,facebook,twitter,livejournal,google,blogger"></div>
				</div>
			</div>
		</div>
	</main>
{% endblock %}