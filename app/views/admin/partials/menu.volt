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

			{% if url is '/admin/products' or url is '/admin/addproduct' %}
				<li class="active"><a href="/admin/products" title="Редактирование товаров">Товары</a></li>
			{% else %}
				<li><a href="/admin/products" title="Редактирование товаров">Товары</a></li>
			{% endif %}

			<li class="divider-vertical"></li>

			{% if url is '/admin/categories' %}
				<li class="active"><a href="/admin/categories" title="Редактирование категорий">Категории</a></li>
			{% else %}
				<li><a href="/admin/categories" title="Редактирование категорий">Категории</a></li>
			{% endif %}

			<li class="divider-vertical"></li>

			{% if url is '/admin/news' %}
				<li class="active"><a href="/admin/news">Новости</a></li>
			{% else %}
				<li><a href="/admin/news">Новости</a></li>
			{% endif %}

			<li class="divider-vertical"></li>

			{% if url is '/admin/users' %}
				<li class="active"><a href="/admin/users">Пользователи</a></li>
			{% else %}
				<li><a href="/admin/users">Пользователи</a></li>
			{% endif %}

			<li class="divider-vertical"></li>

			{% if url is '/admin/pages/' %}
				<li class="active"><a href="/admin/pages/">Статические страницы</a></li>
			{% else %}
				<li><a href="/admin/pages/">Статические страницы</a></li>
			{% endif %}

			<li class="divider-vertical"></li>

			{% if url is '/admin/settings' %}
				<li class="active"><a href="/admin/settings">Настройки</a></li>
			{% else %}
				<li><a href="/admin/settings">Настройки</a></li>
			{% endif %}

			<li class="divider-vertical"></li>

			{% if user is not null %}
				<li><a href="/admin/logout">Выход</a></li>
			{% endif %}
		</ul>
	</div>
</div>