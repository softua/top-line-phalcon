<header class="header">
	<a class="header__logo" href="/" title="Перейти на главную страницу сайта">
		Надежный поставщик
		шиноремонтных материалов и
		оборудования для СТО
	</a>

	<div class="header__contacts">
		<div class="header__contacts__phone">
			<span class="header__contacts__phone__prefix">044</span> 553-66-67</div>
		<div class="header__contacts__phone">
			<span class="header__contacts__phone__prefix">044</span> 553-26-52</div>
		<div class="header__contacts__email">E-mail
			<a href="mailto:top@tip-topline.com" title="">top@tip-topline.com</a></div>
	</div>

	<form class="header__search" action="action.php">
		Поиск по сайту

		<div class="header__search__wrapper">
			<input type="text" placeholder="Что ищем?"/>
			<button class="header__search__submit" type="submit"></button>
		</div>
	</form>

	<nav class="nav--outer-wrapper">
		<ul class="nav">
			{% if active_link is 'main' %}
				<li class="nav__item active">
					<a class="nav__item__link" href="/">Главная</a>
				</li>
			{% else %}
				<li class="nav__item">
					<a class="nav__item__link" href="/">Главная</a>
				</li>
			{% endif %}

			{% if active_link is 'company' %}
				<li class="nav__item active">
					<a class="nav__item__link" href="/company/">О компании</a>
					<ul class="nav2">
						<li class="nav2__item">
							<a class="nav2__item__link" href="">О нас</a>
						</li>
						<li class="nav2__item">
							<a class="nav2__item__link" href="">Новости</a>
						</li>
					</ul>
				</li>
			{% else %}
				<li class="nav__item">
					<a class="nav__item__link" href="/company/">О компании</a>
					<ul class="nav2">
						<li class="nav2__item">
							<a class="nav2__item__link" href="">О нас</a>
						</li>
						<li class="nav2__item">
							<a class="nav2__item__link" href="">Новости</a>
						</li>
					</ul>
				</li>
			{% endif %}

			{% if active_link is 'catalog' %}
				<li class="nav__item active">
					<a class="nav__item__link" href="/catalog/">Каталог</a>
				</li>
			{% else %}
				<li class="nav__item">
					<a class="nav__item__link" href="/catalog/">Каталог</a>
				</li>
			{% endif %}

			{% if active_link is 'service' %}
				<li class="nav__item active">
					<a class="nav__item__link" href="/service">Сервис</a>
				</li>
			{% else %}
				<li class="nav__item">
					<a class="nav__item__link" href="/service">Сервис</a>
				</li>
			{% endif %}

			{% if active_link is 'projects' %}
				<li class="nav__item active">
					<a class="nav__item__link" href="/projects/">Проекты</a>
				</li>
			{% else %}
				<li class="nav__item">
					<a class="nav__item__link" href="/projects/">Проекты</a>
				</li>
			{% endif %}

			{% if active_link is 'video' %}
				<li class="nav__item active">
					<a class="nav__item__link" href="/video/">Видео</a>
				</li>
			{% else %}
				<li class="nav__item">
					<a class="nav__item__link" href="/video/">Видео</a>
				</li>
			{% endif %}

			{% if active_link is 'contacts' %}
				<li class="nav__item active">
					<a class="nav__item__link" href="/contacts">Контакты</a>
				</li>
			{% else %}
				<li class="nav__item">
					<a class="nav__item__link" href="/contacts">Контакты</a>
				</li>
			{% endif %}
		</ul>
	</nav>
</header><!-- end header -->