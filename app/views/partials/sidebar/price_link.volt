<div class="sidebar__price-link">
	Здесь Вы можете загрузить <br/>  или просмотреть наш <br/>  прайс - лист
	<a class="btn-red download-price" href="#popup-form">Загрузить / Посмотреть</a>
</div>
<div id="popup-form" class="popup-form">
	<h2>Получение прайс-листа</h2>
	<form id="price" name="getprice" action="{{ url('price') }}" method="post">
		<input type="email" name="email" placeholder="Ваш E-mail" class="popup-form__email">
		<input type="tel" name="phone" placeholder="Ваш телефон" class="popup-form__phone"/>
		<input type="submit" value="Запросить прайс" class="popup-form__submit"/>
	</form>
</div>
