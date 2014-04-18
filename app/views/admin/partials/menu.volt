<div class="navbar">
	<div class="navbar-inner">
		<a class="brand" href="/" title="Показать сайт" target="_blank">{{ name }}</a>
		<ul class="nav">
			<li class="divider-vertical"></li>

			{% if url is '/admin' %}
				<li class="active"><a href="/admin">На главную</a></li>
			{% else %}
				<li><a href="/admin">На главную</a></li>
			{% endif %}

			<li class="divider-vertical"></li>

			{% if url is '/products' %}
				<li class="active"><a href="/products" title="Редактирование продуктов">Товары</a></li>
			{% else %}
				<li><a href="/products" title="Редактирование продуктов">Товары</a></li>
			{% endif %}

			<li class="divider-vertical"></li>

			{% if url is '/news' %}
				<li class="active"><a href="/news">Новости</a></li>
			{% else %}
				<li><a href="/news">Новости</a></li>
			{% endif %}

			<li class="divider-vertical"></li>

			{% if url is '/admin/users' %}
				<li class="active"><a href="/admin/users">Пользователи</a></li>
			{% else %}
				<li><a href="/admin/users">Пользователи</a></li>
			{% endif %}

			<li class="divider-vertical"></li>

			{% if url is '/pages' %}
				<li class="active"><a href="/pages">Статические страницы</a></li>
			{% else %}
				<li><a href="/pages">Статические страницы</a></li>
			{% endif %}

			<li class="divider-vertical"></li>

			{% if user != NULL %}
				<li><a href="/admin/logout">Выход</a></li>
			{% endif %}
		</ul>
	</div>
</div>