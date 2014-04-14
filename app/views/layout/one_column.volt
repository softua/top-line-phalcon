{{ get_doctype() }}
<html>
{{ partial('partials/head') }}
<body>
	<div class="main">
		{{ partial('partials/header') }}
        <section class="content">
            {% block content %}{% endblock %}
        </section>
            {{ partial('partials/footer') }}
	</div>
</body>
</html>