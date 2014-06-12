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
					<a class="nav__item__link" href="/" title="Главная страница">Главная</a>
				</li>
			{% else %}
				<li class="nav__item">
					<a class="nav__item__link" href="/" title="Главная страница">Главная</a>
				</li>
			{% endif %}

			{% if active_link is 'about' %}
				<li class="nav__item active">
					<a class="nav__item__link" href="/about" title="О компании">О компании</a>
					<ul class="nav2">
						<li class="nav2__item">
							<a class="nav2__item__link" href="" title="О нас">О нас</a>
						</li>
						<li class="nav2__item">
							<a class="nav2__item__link" href="" title="Новости">Новости</a>
						</li>
					</ul>
				</li>
			{% else %}
				<li class="nav__item">
					<a class="nav__item__link" href="/about" title="">О компании</a>
					<ul class="nav2">
						<li class="nav2__item">
							<a class="nav2__item__link" href="" title="">О нас</a>
						</li>
						<li class="nav2__item">
							<a class="nav2__item__link" href="" title="">Новости</a>
						</li>
					</ul>
				</li>
			{% endif %}

			{% if active_link is 'catalog' %}
				<li class="nav__item active">
					<a class="nav__item__link" href="/catalog" title="Каталог">Каталог</a>
				</li>
			{% else %}
				<li class="nav__item">
					<a class="nav__item__link" href="/catalog" title="Каталог">Каталог</a>
				</li>
			{% endif %}

			<li class="nav__item">
				<a class="nav__item__link" href="" title="">Сервис</a>
			</li>
			<li class="nav__item">
				<a class="nav__item__link" href="" title="">Проекты</a>
			</li>
			<li class="nav__item">
				<a class="nav__item__link" href="" title="">Видео</a>
			</li>
			<li class="nav__item">
				<a class="nav__item__link" href="" title="">Контакты</a>
			</li>
		</ul>
	</nav>
</header><!-- end header -->