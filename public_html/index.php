<?

try {
	// Отключаем PHP сессии
	ini_set('session.auto_start', '0');
	ini_set('session.use_cookies', '0');

	define('BASE_URL', '../app/');

	// Создание DI
	$di = new \Phalcon\DI\FactoryDefault();

	// URI
	$di->setShared('url', function() {
		$url = new \Phalcon\Mvc\Url();
		$url->setBaseUri('http://' . $_SERVER['HTTP_HOST'] .'/');
		$url->setStaticBaseUri('http://' . $_SERVER['HTTP_HOST'] . '/public_html/');
		return $url;
	});

    // Регистрация автозагрузчика
	$loader = new \Phalcon\Loader();
	$loader->registerNamespaces([
		'App' => BASE_URL . 'classes/',
		'App\Controllers' => BASE_URL . 'controllers/',
		'App\Models' => BASE_URL . 'models/'
	])->register();

	// Подключаем конфигурацию как сервис
	$di->setShared('config', function() {
		return new \App\Config('development');
	});

    // Сервис для работы с БД
	$di->setShared('db', function() use($di)
	{
		return new \Phalcon\Db\Adapter\Pdo\Mysql([
			'host' => $di['config']->db['host'],
			'username' => $di['config']->db['username'],
			'password' => $di['config']->db['password'],
			'dbname' => $di['config']->db['dbname'],
			'options' => [
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
			]
		]);
	});

	// Collection Manager
	$di->set('collectionManager', function(){
		return new \Phalcon\Mvc\Collection\Manager();
	}, true);

	// Шаблонизатор Volt в DI
	$di->setShared('voltService', function($view, $di) {

		$volt = new Phalcon\Mvc\View\Engine\Volt($view, $di);

		$volt->setOptions(array(
			"compiledExtension" => ".compiled"
		));

		return $volt;
	});

    // Настраиваем компонент View/Simple с шаблонизатором
    $di->setShared('view', function(){
        $view = new \Phalcon\Mvc\View\Simple();
        $view->setViewsDir('../app/views/');
	    $view->registerEngines([
		    '.volt' => 'voltService'
	    ]);
        return $view;
    });

	// Шифрование
	$di->setShared('crypt', function() use($di) {
		$crypt = new \Phalcon\Crypt();
		$crypt->setKey($di['config']->secret);
		$crypt->setMode('ecb');
		return $crypt;
	});

	// Куки
	$di->setShared('cookies', function() {
		$cookies = new \Phalcon\Http\Response\Cookies();
		$cookies->useEncryption(true);
		return $cookies;
	});

	// Роуты
	require BASE_URL . 'classes/Routes.php';

	//----- Модели -----
	$di->setShared('CategoryModel', function () {
		return new \App\Models\CategoryModel();
	});
	$di->setShared('CategoryImageModel', function() {
		return new \App\Models\CategoryImageModel();
	});
	$di->setShared('CountryModel', function() {
		return new \App\Models\CountryModel();
	});
	$di->setShared('ExchangeModel', function() {
		return new \App\Models\ExchangeModel();
	});
	$di->setShared('PageModel', function() {
		return new \App\Models\PageModel();
	});
	$di->setShared('PageImageModel', function() {
		return new \App\Models\PageImageModel();
	});
	$di->setShared('PageTypeModel', function() {
		return new \App\Models\PageTypeModel();
	});
	$di->setShared('PossibleBrandsModel', function() {
		return new \App\Models\PossibleBrandsModel();
	});
	$di->setShared('PossibleParametersModel', function() {
		return new \App\Models\PossibleParametersModel();
	});
	$di->setShared('PossibleProductTypesModel', function() {
		return new \App\Models\PossibleProductTypesModel();
	});
	$di->setShared('ProductModel', function() {
		return new \App\Models\ProductModel();
	});
	$di->setShared('ProductCategoryModel', function() {
		return new \App\Models\ProductCategoryModel();
	});
	$di->setShared('ProductFileModel', function() {
		return new \App\Models\ProductFileModel();
	});
	$di->setShared('ProductImageModel', function() {
		return new \App\Models\ProductImageModel();
	});
	$di->setShared('ProductParamModel', function() {
		return new \App\Models\ProductParamModel();
	});
	$di->setShared('ProductSaleModel', function() {
		return new \App\Models\ProductSaleModel();
	});
	$di->setShared('ProductVideoModel', function() {
		return new \App\Models\ProductVideoModel();
	});
	$di->setShared('RoleModel', function() {
		return new \App\Models\RoleModel();
	});
	$di->setShared('UserModel', function() {
		return new \App\Models\UserModel();
	});
	//------------------

    // Обработка запроса
    $application = new \Phalcon\Mvc\Application($di);

	$application->useImplicitView(false);

    echo $application->handle()->getContent();

} catch(\Phalcon\Exception $e) {
     echo "PhalconException: ", $e->getMessage();
}