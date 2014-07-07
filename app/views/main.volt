{% extends 'layouts/one_column_layout.volt' %}

{% block content %}
	<main class="main" role="main">
		<div class="slider--outer-wrapper">
			<div class="bounding-box">
				<ul class="slider">
					<li class="slider__item">
						<a class="slider__link" href="{{ url('catalog/show/produktsiya_rema_tip_-_top/') }}">
							<img class="slider__img" src="{{ static_url('img/dummy/slider/1.jpg') }}" alt=""/>
						</a>
						<h3 class="slider__title">
							Инструменты и материалы
							<br/>
							для сервиса и ремонта
							<br/>
							шин
						</h3>
						<a href="{{ url('catalog/show/produktsiya_rema_tip_-_top/') }}">
							<img class="slider__link__button" src="{{ static_url('img/dummy/slider/button.png') }}" alt=""/>
						</a>
					</li>
					<li class="slider__item">
						<a class="slider__link" href="{{ url('catalog/show/shinomontajnoe_oborudovanie/') }}">
							<img class="slider__img" src="{{ static_url('img/dummy/slider/2.jpg') }}" alt=""/>
						</a>
						<h3 class="slider__title">
							Лучшие предложения
							<br/>
							по оборудованию
							<br/>
							для шиномонтажа
						</h3>
						<a href="{{ url('catalog/show/shinomontajnoe_oborudovanie/') }}">
							<img class="slider__link__button" src="{{ static_url('img/dummy/slider/button.png') }}" alt=""/>
						</a>
					</li>
					<li class="slider__item">
						<a class="slider__link" href="{{ url('catalog/') }}">
							<img class="slider__img" src="{{ static_url('img/dummy/slider/3.jpg') }}" alt=""/>
						</a>
						<h3 class="slider__title">
							Профессиональное
							<br/>
							оборудование для СТО
							<br/>
							и автосервиса
						</h3>
						<a href="{{ url('catalog/') }}">
							<img class="slider__link__button" src="{{ static_url('img/dummy/slider/button.png') }}" alt=""/>
						</a>
					</li>
					<li class="slider__item">
						<a class="slider__link" href="{{ url('projects/') }}">
							<img class="slider__img" src="{{ static_url('img/dummy/slider/4.jpg') }}" alt=""/>
						</a>
						<h3 class="slider__title">
							Комплексные решения
							<br/>
							для Вашего бизнеса
						</h3>
						<a href="{{ url('projects/') }}">
							<img class="slider__link__button" src="{{ static_url('img/dummy/slider/button.png') }}" alt=""/>
						</a>
					</li>
				</ul>
			</div>
		</div><!-- end slider -->

		{% if news is defined and news is not empty %}
			<div class="news-main-list--outer-wrapper">
				<h2 class="title title--big">Новости</h2>
				<ul class="news-main-list">
					{% for item in news %}
						<li class="news-main-list__item">
							<h3 class="news-main-list__title">
								<a href="{{ item['href'] }}" title="{{ item['name'] }}">{{ item['name'] }}</a>
							</h3>
							<time class="news-main-list__date" datetime="{{ item['date-2'] }}">{{ item['date'] }}</time>
							{{ item['short_content'] }}
						</li>
					{% endfor %}
				</ul>
			</div><!-- end news-main-list -->
		{% endif %}

		{% if sales is defined and sales is not empty %}
			<div class="slider-sales--outer-wrapper js-slider">
				<h2 class="slider-sales--outer-wrapper__title">Акционные предложения</h2>
				<div class="bounding-box">
					<ul class="slider-sales">
						{% for sale in sales %}
							<li class="slider-sales__item">
								<a class="slider-sales__link" href="{{ sale.path }}" title="{{ sale.name }}">
									{#<div class="slider-sales__link__wrapper">
										<h3 class="slider-sales__title">{{ sale.name }}</h3>
										{{ sale.shortContent }}
									</div>#}
									<figure class="slider-sales__img">
										{% if sale.hasImages() %}
											<img src="<?= $sale->getImages()[0]->pageListPath ?>" alt="{{ sale.name }}"/>
										{% else %}
											<img src="{{ static_url('img/no_foto.png') }}" alt="{{ sale.name }}"/>
										{% endif %}
									</figure>
									{% if sale.expiration is not null %}
										<div class="slider-sales__remain">Акция действует до: <?= date('d.m.Y', strtotime($sale->expiration)) ?></div>
									{% else %}
										<div class="slider-sales__remain">Спешите, пока не поздно!</div>
									{% endif %}
								</a>
							</li>
						{% endfor %}
					</ul>
				</div>
			</div><!-- end slider-sales -->
		{% endif %}


		{% if novelties is defined and novelties is not empty %}
			<div class="new-products--outer-wrapper js-slider">
				<h2 class="new-products--outer-wrapper__title">Новинки</h2>

				<div class="bounding-box">
					<ul class="new-products">
						{% for novelty in novelties %}
							<li class="new-products__item">
								<a class="new-products__link" href="{{ novelty.path }}" title="">
									<figure class="new-products__img">
										{% if novelty.getMainImageForList() %}
											<img src="{{ novelty.getMainImageForList() }}" alt="{{ novelty.name }}"/>
										{% else %}
											<img src="{{ static_url('img/no_foto.png') }}" alt="{{ novelty.name }}"/>
										{% endif %}
									</figure>

									<p>
										{{ novelty.name }}
									</p>

									<span class="new-products__more">Подробнее</span>
								</a>
							</li>
						{% endfor %}
					</ul>
				</div>
			</div><!-- end new-products -->
		{% endif %}

		<div class="partners--outer-wrapper js-slider">
			<h2 class="partners--outer-wrapper__title">
				<span class="partners--outer-wrapper__title__wrapper">Наши партнеры</span></h2>

			<div class="bounding-box">
				<ul class="partners">
					<li class="partners__item">
						<a class="partners__link" href="" title="">
							<img src="img/dummy/partners/pasquin.png" alt=""/>
						</a>
					</li>
					<li class="partners__item">
						<a class="partners__link" href="" title="">
							<img src="img/dummy/partners/rema.png" alt=""/>
						</a>
					</li>
					<li class="partners__item">
						<a class="partners__link" href="" title="">
							<img src="img/dummy/partners/fasep.png" alt=""/>
						</a>
					</li>
					<li class="partners__item">
						<a class="partners__link" href="" title="">
							<img src="img/dummy/partners/aircast.png" alt=""/>
						</a>
					</li>
					<li class="partners__item">
						<a class="partners__link" href="" title="">
							<img src="img/dummy/partners/pasquin.png" alt=""/>
						</a>
					</li>
					<li class="partners__item">
						<a class="partners__link" href="" title="">
							<img src="img/dummy/partners/rema.png" alt=""/>
						</a>
					</li>
					<li class="partners__item">
						<a class="partners__link" href="" title="">
							<img src="img/dummy/partners/fasep.png" alt=""/>
						</a>
					</li>
					<li class="partners__item">
						<a class="partners__link" href="" title="">
							<img src="img/dummy/partners/aircast.png" alt=""/>
						</a>
					</li>
				</ul>
			</div>

			<p>Наши партнеры - крупнейшие производители расходных материалов и оборудования для ремонта шин:
				<br/> REMA TIP-TOP, DELTA, WERTER, UNI-TROL, TIPTOPOL, KART, etc.
			</p>
		</div><!-- end partners -->
	</main>
{% endblock %}

{% block footer %}
	{{ partial('partials/footer') }}
{% endblock %}