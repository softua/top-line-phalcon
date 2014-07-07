{% if infoPages is defined and infoPages is not empty %}
	<div class="sidebar__block sidebar__block--info">
		<h6 class="sidebar__block__title">Информация</h6>
		<ul class="sidebar__info">
			{% for infoPage in infoPages %}
				<li class="sidebar__info__item">
					<a class="sidebar__info__link" href="{{ infoPage.path }}" title="Перейти к странице '{{ infoPage.name }}'">{{ infoPage.name }}</a>
				</li>
			{% endfor %}
		</ul>
	</div>
{% endif %}