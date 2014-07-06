<footer class="footer--outer-wrapper">
	<div class="footer">
		<ul class="footer__nav">

			{% if active_link is 'main' %}
				<li class="footer__nav__item active">
					<a class="footer__nav__item__link" href="{{ url() }}" title="Перейти на главную страницу">Главная</a>
				</li>
			{% else %}
				<li class="footer__nav__item">
					<a class="footer__nav__item__link" href="{{ url() }}" title="Перейти на главную страницу">Главная</a>
				</li>
			{% endif %}

			{% if active_link is 'company' %}
				<li class="footer__nav__item active">
					<a class="footer__nav__item__link" href="{{ url('company/aboutus') }}" title="Перейти на страницу 'О нас'">О компании</a>
				</li>
			{% else %}
				<li class="footer__nav__item">
					<a class="footer__nav__item__link" href="{{ url('company/aboutus') }}" title="Перейти на страницу 'О нас'">О компании</a>
				</li>
			{% endif %}

			{% if active_link is 'catalog' %}
				<li class="footer__nav__item active">
					<a class="footer__nav__item__link" href="{{ url('catalog/') }}" title="Перейти в каталог">Каталог</a>
				</li>
			{% else %}
				<li class="footer__nav__item">
					<a class="footer__nav__item__link" href="{{ url('catalog/') }}" title="Перейти в каталог">Каталог</a>
				</li>
			{% endif %}

			{% if active_link is 'service' %}
				<li class="footer__nav__item active">
					<a class="footer__nav__item__link" href="{{ url('service') }}" title="Перейти на страницу 'Сервис'">Сервис</a>
				</li>
			{% else %}
				<li class="footer__nav__item">
					<a class="footer__nav__item__link" href="{{ url('service') }}" title="Перейти на страницу 'Сервис'">Сервис</a>
				</li>
			{% endif %}

			{% if active_link is 'projects' %}
				<li class="footer__nav__item active">
					<a class="footer__nav__item__link" href="{{ url('projects/') }}" title="Перейти к проектам">Проекты</a>
				</li>
			{% else %}
				<li class="footer__nav__item">
					<a class="footer__nav__item__link" href="{{ url('projects/') }}" title="Перейти к проектам">Проекты</a>
				</li>
			{% endif %}

			{% if active_link is 'video' %}
				<li class="footer__nav__item active">
					<a class="footer__nav__item__link" href="{{ url('video/') }}" title="Перейти к видео">Видео</a>
				</li>
			{% else %}
				<li class="footer__nav__item">
					<a class="footer__nav__item__link" href="{{ url('video/') }}" title="Перейти к видео">Видео</a>
				</li>
			{% endif %}

			{% if active_link is 'contacts' %}
				<li class="footer__nav__item active">
					<a class="footer__nav__item__link" href="{{ url('contacts') }}" title="Перейти к контактам">Контакты</a>
				</li>
			{% else %}
				<li class="footer__nav__item">
					<a class="footer__nav__item__link" href="{{ url('contacts') }}" title="Перейти к контактам">Контакты</a>
				</li>
			{% endif %}
		</ul>

		<div class="footer__phones">
			<span class="footer__phones__prefix">044</span> 553-66-67
			<span class="footer__phones__prefix">044</span> 553-26-52
		</div>

		<div class="footer__social">
			<div class="pluso" data-background="transparent" data-options="small,square,line,horizontal,nocounter,theme=08" data-services="yandex,vkontakte,facebook,twitter,livejournal,google,blogger"></div>
		</div>

		<div class="footer__copyright">Copyright © 2014 Топ Линия</div>
	</div>
</footer><!-- end footer -->