{% extends 'layouts/two_column_layout.volt' %}

{% block breadcrumbs %}
	<ul class="breadcrumbs" itemscope itemtype="http://schema.org/WebPage">
		<li class="breadcrumbs__item">
			<a class="breadcrumbs__item__link" href="/" title="Главная" itemprop="breadcrumb">Главная</a> -
		</li>
		<li class="breadcrumbs__item">
			<span class="breadcrumbs__item__current" itemprop="breadcrumb">Контакты</span>
		</li>
	</ul>
{% endblock %}

{% block content %}
	<main class="main main--aside" role="main">
		<div class="contacts">
			<h1 class="title title--big">Наши контакты</h1>
			<div class="contacts__address">
				Компания ООО "Топ Линия"  02152, <br/>
				г. Киев, ул. <b>Серафимовича, д. 13</b>
			</div>
			<ul class="contacts__phones">
				<li class="contacts__phones__item">+38 044 <b>553-66-67</b></li>
				<li class="contacts__phones__item">+38 044 <b>553-26-52</b></li>
				<li class="contacts__phones__item">+38 044 <b>553-09-69</b></li>
			</ul>

			<div><a class="contacts__email" href="mailto:top@tip-topline.com" title="">top@tip-topline.com</a></div>

			<div class="contacts__map--outer-wrapper">
				<h2 class="title2">Карта проезда</h2>

				<div class="contacts__map">
					<iframe width="490" height="360" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
					        src="https://maps.google.com.ua/maps?gl=ua&amp;gbv=2&amp;q=%D0%9A%D0%B8%D0%B5%D0%B2,+%D1%83%D0%BB.+%D0%A1%D0%B5%D1%80%D0%B0%D1%84%D0%B8%D0%BC%D0%BE%D0%B2%D0%B8%D1%87%D0%B0,+%D0%B4.+13&amp;ie=UTF8&amp;hq=&amp;hnear=%D0%B2%D1%83%D0%BB.+%D0%A1%D0%B5%D1%80%D0%B0%D1%84%D0%B8%D0%BC%D0%BE%D0%B2%D0%B8%D1%87%D0%B0,+13,+%D0%9A%D0%B8%D1%97%D0%B2,+%D0%BC%D1%96%D1%81%D1%82%D0%BE+%D0%9A%D0%B8%D1%97%D0%B2&amp;ll=50.434242,30.604705&amp;spn=0.002221,0.005338&amp;t=m&amp;z=14&amp;output=embed"></iframe>
				</div>
			</div>

			<form id="form" class="form" action="action.php">
				<h2 class="title2">Заполните форму</h2>

				<label>
					<span class="form__label-name">Ваше имя</span>
					<input type="text"/>
				</label>
				<label>
					<span class="form__label-name">Ваш e-mail</span>
					<input type="text"/>
				</label>
				<div class="form__as-label">
					<span class="form__label-name">Ваш телефон</span>
					<input class="form__prefix" type="text"/> -
					<input class="form__phone" type="text"/>
				</div>
				<label>
					<span class="form__label-name">Ваше сообщение</span>
					<textarea></textarea>
				</label>

				<button class="form__submit" type="submit">Подать заявку</button>
			</form>

			<div class="contacts__tire-fitting">
				<h2 class="title2"><a href="" title="">У нас есть свой шиномонтаж:</a></h2>

				<p> г. Киев, ул. Березняковская, 25 <br/>
					+38 093 811-90-56<br/>
					Александр
				</p>

				<div class="title2">Как найти </div>
				<div class="contacts__map">
					<iframe width="490" height="360" frameborder="0" scrolling="no"
					        marginheight="0" marginwidth="0" src="https://maps.google.com.ua/maps?f=q&amp;source=s_q&amp;hl=uk&amp;geocode=&amp;q=%D0%B3.+%D0%9A%D0%B8%D0%B5%D0%B2,+%D1%83%D0%BB.+%D0%91%D0%B5%D1%80%D0%B5%D0%B7%D0%BD%D1%8F%D0%BA%D0%BE%D0%B2%D1%81%D0%BA%D0%B0%D1%8F,+25&amp;aq=&amp;sll=50.433923,30.604692&amp;sspn=0.002221,0.005338&amp;gl=ua&amp;ie=UTF8&amp;hq=&amp;hnear=%D0%B2%D1%83%D0%BB.+%D0%91%D0%B5%D1%80%D0%B5%D0%B7%D0%BD%D1%8F%D0%BA%D1%96%D0%B2%D1%81%D1%8C%D0%BA%D0%B0,+25,+%D0%9A%D0%B8%D1%97%D0%B2,+%D0%BC%D1%96%D1%81%D1%82%D0%BE+%D0%9A%D0%B8%D1%97%D0%B2&amp;ll=50.421174,30.600033&amp;spn=0.002222,0.005338&amp;t=m&amp;z=17&amp;output=embed"></iframe>
				</div>
			</div>
		</div>

	</main>
{% endblock %}