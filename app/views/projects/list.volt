{% extends 'layouts/two_column_layout.volt' %}

{% block breadcrumbs %}
	<ul class="breadcrumbs" itemscope itemtype="http://schema.org/WebPage">
		<li class="breadcrumbs__item">
			<a class="breadcrumbs__item__link" href="/" title="Главная" itemprop="breadcrumb">Главная</a> -
		</li>
		<li class="breadcrumbs__item">
			<span class="breadcrumbs__item__current" itemprop="breadcrumb">Проекты</span>
		</li>
	</ul>
{% endblock %}

{% block content %}
	<main class="main main--aside" role="main">
		<h1 class="title">Проектные решения</h1>
		<p class="s-font-size-big">
			Этот текст нужно ЗАМЕНИТЬ.
		</p>
		<ul class="catalog catalog--projects">
			{% if projects is defined and projects is not empty %}
				{% for project in projects %}
					<li class="catalog__item">
						<a class="catalog__item__link" href="{{ project.path }}">
							{% set image = project.getMainImage() %}
							{% if image is not false %}
								<figure class="catalog__item__img">
									<img src="{{ image.imgDescriptionPath }}" alt="{{ project.name }}"/>
								</figure>
							{% else %}
								<figure class="catalog__item__img">
									<img src="{{ static_url('img/no_foto.png') }}" alt="{{ project.name }}"/>
								</figure>
							{% endif %}
							{{ project.name }}
						</a>
						{{ project.short_content }}
					</li>
				{% endfor %}
			{% endif %}
		</ul><!-- end catalog -->
	</main>
{% endblock %}