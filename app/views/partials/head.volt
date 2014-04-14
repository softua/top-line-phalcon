<head>
	{{ get_title() }}
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
	<!--[if lt IE 9]>
    {{ javascript_include('http://html5shiv.googlecode.com/svn/trunk/html5.js', false) }}
	<![endif]-->
	{% if env != 'production' %}
        {{ stylesheet_link('master.css') }}
        {{ javascript_include(['js/vendor/require-2.9.1.js', 'data-main': '/js/master']) }}
    {% else %}
        {{ stylesheet_link('master.min.css') }}
        {{ javascript_include('js/master.min.js') }}
	{% endif %}
</head>