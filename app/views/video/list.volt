{% extends 'layouts/two_column_layout.volt' %}

{% block breadcrumbs %}
	<ul class="breadcrumbs" itemscope itemtype="http://schema.org/WebPage">
		<li class="breadcrumbs__item">
			<a class="breadcrumbs__item__link" href="/" title="Главная" itemprop="breadcrumb">Главная</a> -
		</li>
		<li class="breadcrumbs__item">
			<span class="breadcrumbs__item__current" itemprop="breadcrumb">Видео</span>
		</li>
	</ul>
{% endblock %}

{% block content %}
	<main class="main main--aside" role="main">
	{% if videos is defined and videos is not empty %}
		<ul class="video">
			{% for video in videos %}
				<li id="{{ video['seo_name'] }}" class="video__item">
					<figure class="video__item__container">
						<div class="video__item__wrapper">
							{{ video['video_content'] }}
						</div>
						<figcaption class="video__item__caption">
							<h2 class="video__item__title">
								{{ video['name'] }}
							</h2>
							<p>
								{{ video['short_content'] }}
							</p>
						</figcaption>
					</figure>
				</li>
			{% endfor %}
		</ul><!-- end video -->
	{% else %}
		<h3>Нет видео</h3>
	{% endif %}
	</main>
{% endblock %}