<nav class="sidebar__block sidebar__block--nav">
	<h6 class="sidebar__block__title">Каталог</h6>
	<a class="sidebar__block__btn" href="" title="">Акционные предложения</a>
	{% if sidebar_categories is defined and sidebar_categories is not empty %}
		<ul class="sidebar__nav">
			{% for cat in sidebar_categories %}
				{% if cat['active'] is defined and cat['active'] is true %}
					<li class="sidebar__nav__item active">
						<a class="sidebar__nav__item__link" href="{{ cat['path'] }}" title="{{ cat['name'] }}">{{ cat['name'] }}</a>
					</li>
				{% else %}
					<li class="sidebar__nav__item">
						<a class="sidebar__nav__item__link" href="{{ cat['path'] }}" title="{{ cat['name'] }}">{{ cat['name'] }}</a>
					</li>
				{% endif %}
			{% endfor %}
		</ul>
	{% endif %}
</nav>