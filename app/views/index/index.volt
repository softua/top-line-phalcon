{{ get_doctype() }}
<html>
<head>
	{{ get_title() }}
</head>
<body>
	<h1>Привет, пользователь</h1>

	{{ link_to('signup', 'Регистрируйся!') }}
    {{ link_to('signup', 'Регистрируйся еще раз!') }}
</body>
</html>