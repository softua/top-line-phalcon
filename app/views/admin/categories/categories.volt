{% extends 'admin/layout/admin_one_column.volt' %}

{% block menu %}
	{{ partial('admin/partials/menu') }}
{% endblock %}

{% block content %}
	{% if mainCategories is not null %}
		<ul class="admin__catogories">
			{% for category in mainCategories %}
				<li class="admin__categories__item" data-category-id="{{ category._id }}">
					<a href="/" class="btn btn-mini btn-primary" data-action="open" data-editing="true">+</a>
					<a href="/admin/editcategory/{{ category._id }}/" title="Редактировать">{{ category.name }} (SEO - {{ category.seo }}, sort = {{ category.sort }})</a>
					<ul class="admin__categories admin__categories--hidden">
						<li class="admin__categories__item">
							<a href="/admin/addcategory/{{ category._id }}/" class="btn btn-success">Добавить категорию</a>
						</li>
					</ul>
				</li>
				{% if loop.last %}
					<li class="admin__categories__item">
						<a href="/admin/addcategory/0/" class="btn btn-success">Добавить категорию</a>
					</li>
				{% endif %}
			{% endfor %}
		</ul>
	{% else %}
		<ul class="admin__catogories">
			<li class="admin__categories__item">
				<a href="/admin/addcategory/0/" class="btn btn-success">Добавить категорию</a>
			</li>
		</ul>
	{% endif %}
{% endblock %}