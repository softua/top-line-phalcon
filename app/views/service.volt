{% extends 'layouts/two_column_layout.volt' %}

{% block breadcrumbs %}
	<ul class="breadcrumbs" itemscope itemtype="http://schema.org/WebPage">
		<li class="breadcrumbs__item">
			<a class="breadcrumbs__item__link" href="/" title="Главная" itemprop="breadcrumb">Главная</a> -
		</li>
		<li class="breadcrumbs__item">
			<span class="breadcrumbs__item__current" itemprop="breadcrumb">Сервис</span>
		</li>
	</ul>
{% endblock %}

{% block content %}
	<main class="main main--aside" role="main">
		<h1 class="title title--big">Сервис</h1>

		<div class="article">
			<div class="cf">
				<figure class="article__img article__img--skew">
					<img src="/img/dummy/services.jpg" alt=""/>
					<figcaption class="article__img__caption article__img__caption--black">Заказ услуг и запасных частей</figcaption>
				</figure>
			</div>

			<h2 class="title">Наши услуги</h2>

			<ul class="services">
				<li class="services__item">
					Установка оборудования и монтаж
					<div class="services__item__actions">
						<a class="services__item__file-link" href="" title="">Перечень услуг (PDF)</a>
						<a class="btn-next" href="{{ url('contacts#form') }}" title="">Заказать услугу</a>
					</div>
				</li>
				<li class="services__item">
					Техническое и послегалантерейное  обслуживание
					<div class="services__item__actions">
						<a class="btn-next" href="{{ url('contacts#form') }}" title="">Заказать услугу</a>
					</div>
				</li>
				<li class="services__item">
					Запасные части
					<div class="services__item__actions">
						<a class="btn-next" href="{{ url('contacts#form') }}" title="">Заказать услугу</a>
					</div>
				</li>
			</ul>

			<h2 class="title">Общее описание возможностей нашей службы</h2>

			<figure class="article__img article__img--float-left">
				<img src="/img/dummy/services/1.jpg" alt=""/>
			</figure>
			<p><strong>Laccumsan rhoncus. Proin Fringilla tincidunt эрос</strong>
				<br/> Амет faucibus. Quisque Lectus Масса, ultricies placerat Semper справка, venenatis Eget велит. Vivamus porttitor Орси биография turpis vulputate, сед лациния Sed f Ipsum переменного тока диаметр aliquet, viverra sodales элит convallis. Донец Мариуса Dolor переносные, не feugiat, scelerisque ут фелис. Quisque malesuada, пзиз Новичок hendrerit sodales, урна Метусe. Что-то надо сделать к делу. Что угодно!
			</p>

			<p>Lorem Ipsum Dolor сидеть Амет, consectetur adipiscing Элит. В imperdiet Метус анте, ес Предполагаемое posuere в. Proin Varius Метус Ipsum, imperdiet Justo blandit переменного тока. Nulla Eget язычок Массе sodales hendrerit. DUIs pellentesque Porta convallis. Nullam ЕС consectetur эрос. Quisque Quis neque suscipit neque Semper consectetur в тоШз tortor. Nunc Eget feugiat Purus, не placerat Массе. Suspendisse Justo урна, sagittis лациния анте Varius, rutrum Cursusnibh. Sed Quis Nunc auctor, placerat либеро переменного тока,
				<a href="" title="">Bibendum пзиз</a>. Преддверия condimentum Одио сидеть Амет Мариуса feugiat, СЭД Porta Элит pulvinar. Morbi aliquet Arcu volutpat lobortis. Proin dignissim Ipsum переменного тока posuere Elementum. Меценат Lectus Мариуса, luctus Eget pellentesque ЕС, venenatis переменного Nunc. Curabitur blandit велит Nunc, Quis sagittis эрос Varius Eget. Vivamus dapibus разводе ID augue accumsan, ID auctor Лев.
				<br/> Sed лациния vehicula Arcu без Porta. Aenean ЕС Лев, не фелис ​​Маттис pellentesque ID, не Sed laoreet gravida Мариуса, auctor миль consectetur Quis. Меценат feugiat aliquam
			</p>

			<figure class="article__img article__img--float-left">
				<img src="/img/dummy/services/2.jpg" alt=""/>
			</figure>
			<p><strong>Laccumsan rhoncus. Proin Fringilla tincidunt эрос</strong>
				<br/> Амет faucibus. Quisque Lectus Масса, ultricies placerat Semper справка, venenatis Eget велит. Vivamus porttitor Орси биография turpis vulputate, сед лациния Sed f Ipsum переменного тока диаметр aliquet, viverra sodales элит convallis. Донец Мариуса Dolor переносные, не feugiat, scelerisque ут фелис. Quisque malesuada, пзиз Новичок hendrerit sodales, урна Метусe. Что-то надо сделать к делу. Что угодно!
			</p>

			<p>Lorem Ipsum Dolor сидеть Амет, consectetur adipiscing Элит. В imperdiet Метус анте, ес Предполагаемое posuere в. Proin Varius Метус Ipsum, imperdiet Justo blandit переменного тока. Nulla Eget язычок Массе sodales hendrerit. DUIs pellentesque Porta convallis. Nullam ЕС consectetur эрос. Quisque Quis neque suscipit neque Semper consectetur в тоШз tortor. Nunc Eget feugiat Purus, не placerat Массе. Suspendisse Justo урна, sagittis лациния анте Varius, rutrum Cursusnibh. Sed Quis Nunc auctor, placerat либеро переменного тока,
				<a href="" title="">Bibendum пзиз</a>. Преддверия condimentum Одио сидеть Амет Мариуса feugiat, СЭД Porta Элит pulvinar. Morbi aliquet Arcu volutpat lobortis. Proin dignissim Ipsum переменного тока posuere Elementum. Меценат Lectus Мариуса, luctus Eget pellentesque ЕС, venenatis переменного Nunc. Curabitur blandit велит Nunc, Quis sagittis эрос Varius Eget. Vivamus dapibus разводе ID augue accumsan, ID auctor Лев.
				<br/> Sed лациния vehicula Arcu без Porta. Aenean ЕС Лев, не фелис ​​Маттис pellentesque ID, не Sed laoreet gravida Мариуса, auctor миль consectetur Quis. Меценат feugiat aliquam
			</p>

			<div class="clear"></div>

			<div class="article__actions">
				<div class="article__actions__social">
					<div class="pluso" data-background="transparent" data-options="small,square,line,horizontal,nocounter,theme=08" data-services="yandex,vkontakte,facebook,twitter,livejournal,google,blogger"></div>
				</div>
				<a href="{{ url('contacts#form') }}" title="">Подписаться на новости сервиса</a>
				<a class="main__back" href="{{ url('company/news/') }}" title="">Все новости </a>
			</div>
		</div>
	</main>
{% endblock %}