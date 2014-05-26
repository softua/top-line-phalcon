<?

namespace App\Controllers;

use App\Models;

class AdminController extends BaseAdminController
{
	public function beforeExecuteRoute()
	{
		if (!$this->cookies->has('user')) {

			if ($this->dispatcher->getActionName() != 'login') {

				$this->dispatcher->forward([
					'action' => 'login'
				]);
			}

		} else {
			$this->user = \App\Models\Users::findById(trim($this->cookies->get('user')->getValue()));

			$this->cookies->set('user', $this->user->_id, time() + $this->config->cookie_lifetime);
			$this->cookies->send();

			$this->view->setVars([
				'name' => $this->config->name,
				'url' => $this->request->getURI(),
				'user' => $this->user
			]);
		}
	}

	public function notFoundAction()
	{
		$this->tag->prependTitle('Ошибка');

		$this->response->setStatusCode(404, 'Страница не найдена');

		$this->dispatcher->forward([
			'controller' => 'admin',
			'action' => 'index'
		]);
	}

	public function indexAction()
	{
		$this->tag->prependTitle('Админ. панель');

		echo $this->view->render('admin/index');
	}

	public function loginAction()
	{
		if ($this->cookies->has('user')) {
			return $this->response->redirect('admin');
		}

		if ($this->request->isGet()) {
			$this->tag->prependTitle('Вход');

			echo $this->view->render('admin/auth/login');
		} else {
			$login = $this->request->getPost('login', ['striptags', 'trim']);
			$password = $this->request->getPost('password', ['striptags', 'trim']);

			$users = Models\Users::find([
				'conditions' => ['login' => $login]
			]);

			if (count($users) == 1) {
				$bdPass = trim($this->crypt->decryptBase64($users[0]->password));

				if ($bdPass == $password) {

					$this->cookies->set('user', (string)$users[0]->_id, time() + $this->config->cookie_lifetime);

					$this->response->redirect('admin');
				} else {
					$this->tag->prependTitle('Вход');
					$this->view->setVar('error', 'Неверный логин или пароль');
					echo $this->view->render('admin/auth/login');
				}
			} else {
				$this->tag->prependTitle('Вход');
				$this->view->setVar('error', 'Неверный логин или пароль');
				echo $this->view->render('admin/auth/login');
			}
		}
	}

	public function logoutAction()
	{
		$this->cookies->set('user', null, time() - 60);

		return $this->response->redirect('admin');
	}

	public function usersAction()
	{
		$this->tag->prependTitle('Пользователи');

		$allUsers = Models\Users::find();
		$roles = Models\Roles::find();

		$this->view->setVars([
			'users' => $allUsers,
			'roles' => $roles
		]);

		echo $this->view->render('admin/users/list');
	}

	public function userAction()
	{
		$id = $this->dispatcher->getParams()[0];
		$this->tag->prependTitle('Редактирование');
		$user = Models\Users::findById($id);
		$roles = Models\Roles::find();

		// POST запрос
		if ($this->request->isPost()) {

			$name = $this->request->getPost('name', ['striptags', 'trim']);
			$email = $this->request->getPost('email', ['email', 'trim']);
			$role = $this->request->getPost('role', ['alphanum', 'trim']);

			$validation = new \App\Validation();

			$validation->isNotEmpty([
				'Имя' => $name,
				'Права' => $role
			], false);

			if ($validation->validate()) {
				$user->name = $name;
				$user->email = $email;
				$user->role = $role;

				$user->save();
			} else {
				$errors = $validation->getMessages();
				$this->view->setVar('errors', $errors);
			}
		}

		$this->view->setVars([
			'user' => $user,
			'roles' => $roles
		]);

		echo $this->view->render('admin/users/edit');
	}

	public function newUserAction()
	{
		$this->tag->prependTitle('Новый пользователь');

		// GET запрос
		if ($this->request->isGet()) {
			$this->view->setVars([
				'roles' => Models\Roles::find()
			]);
			echo $this->view->render('admin/users/new');
		}

		// POST запрос
		if ($this->request->isPost()) {
			$validation = new \App\Validation();

			$login = $this->request->getPost('login', ['striptags', 'trim', 'lower']);
			$password = $this->request->getPost('password', 'trim');
			$name = $this->request->getPost('name', ['striptags', 'trim']);
			$email = $this->request->getPost('email', ['striptags', 'trim', 'email']);
			$role = $this->request->getPost('role', ['striptags', 'trim', 'alphanum']);

			$validation->isNotEmpty([
				'login' => $login,
				'password' => $password,
				'name' => $name,
				'role' => $role
			], false);

			$validation->isUniqueUser($login, false);

			if ($validation->validate()) { // Создаем юзера и переходим к их списку
				$user = new Models\Users();

				$user->login = $login;
				$user->password = $this->crypt->encryptBase64($password);
				$user->name = $name;
				$user->email = $email;
				$user->role = $role;

				if ($user->save()) {
					$this->response->redirect('admin/users');
				}
			} else {
				$this->view->setVars([
					'roles' => Models\Roles::find(),
					'errors' => $validation->getMessages()
				]);
				echo $this->view->render('admin/users/new');
			}
		}
	}

	public function deleteUserAction()
	{
		$id = $this->dispatcher->getParams()[0];

		$user = Models\Users::findById($id);
		$user->delete();

		if ($this->user == $id) {
			$this->logoutAction();
		}

		$this->response->redirect('admin/users');
	}

	public function categoriesAction()
	{
		$this->tag->prependTitle('Категории');

		$this->view->setVar('mainCategories', Models\Category::getMainCategories());

		echo $this->view->render('admin/categories/categories');
	}

	public function getCategoriesAction()
	{
		$parent = $this->dispatcher->getParams()[0];
		if($this->request->isAjax() && $parent != null) {
			$categories = Models\Category::getCategories($parent);

			if($categories)
				echo json_encode($categories);
			else
				echo json_encode(null);

		} else {
			$this->response->redirect('admin/categories');
		}
	}

	public function addCategoryAction()
	{
		$parent = $this->dispatcher->getParams()[0];
		$this->tag->prependTitle('Редактирование');

		$fullParentCategory = Models\Category::getFullCategoryName($parent);

		$this->view->setVars([
			'fullParentCategory', $fullParentCategory,
			'parent' => $parent
		]);

		// POST запрос
		if ($this->request->isPost()) {

			$category = new Models\Category();

			$name = $this->request->getPost('name', ['striptags', 'trim']);
			$sort = $this->request->getPost('sort', ['striptags', 'trim', 'int']);

			$validation = new \App\Validation();

			$validation->isNotEmpty([
				'Категория' => $name,
				'Порядок' => $sort
			], false);

			if ($validation->validate()) {
				$category->name = $name;
				$category->parent = $parent;
				$category->seo = \App\Translit::get_seo_keyword($name, true);
				$category->sort = (int)$sort;

				$category->save();

				return $this->response->redirect('admin/categories');
			} else {
				$errors = $validation->getMessages();
				$this->view->setVar('errors', $errors);
			}
		}

		echo $this->view->render('admin/categories/add');
	}

	public function editCategoryAction()
	{
		$id = $this->dispatcher->getParams()[0];
		if($id == null)
			return $this->response->redirect('admin/categories');

		$this->tag->prependTitle('Редактирование');

		$category = Models\Category::findById($id);
		$this->view->setVar('category', $category);
		$fullParentCategory = Models\Category::getFullCategoryName($category->parent);
		$this->view->setVar('fullParentCategory', $fullParentCategory);

		// POST
		if($this->request->isPost()) {
			$name = $this->request->getPost('name', ['striptags', 'trim']);
			$sort = $this->request->getPost('sort', ['striptags', 'trim', 'int']);

			$validation = new \App\Validation();
			$validation->isNotEmpty([
				'Категория' => $name,
				'Порядок' => $sort
			], false);
			if ($validation->validate()) {
				$category->name = $name;
				$category->seo = \App\Translit::get_seo_keyword($name, true);
				$category->sort = (int)$sort;
				$category->save();
			} else {
				$errors = $validation->getMessages();
				$this->view->setVar('errors', $errors);
			}
		}

		echo $this->view->render('admin/categories/edit');
	}

	public function deleteCategoryAction()
	{
		$id = $this->dispatcher->getParams()[0];
		if($id && $id != '0') {
			$category = Models\Category::findById($id);
			$children = Models\Category::getCategories($id);
			if($children === null)
				$category->delete();

			return $this->response->redirect('admin/categories');
		}
	}

	public function productsAction()
	{
		$this->tag->prependTitle('Товары');

		$mainCats = Models\Category::getMainCategories();
		$products = Models\Product::getProducts();

		$productsWithoutCategories = [];
		$allProducts = Models\Product::find();
		foreach ($allProducts as $product)
		{
			if (!$product->categories)
				$productsWithoutCategories[] = $product;
		}

		$this->view->mainCategories = $mainCats;
		$this->view->products = $products;
		$this->view->productsWithoutCategories = $productsWithoutCategories;

		echo $this->view->render('admin/products/index');
	}

	public function getProductsAction()
	{
		$categoryId = $this->dispatcher->getParams()[0];
		if ($this->request->isAjax()) // Если AJAX запрос, обрабатываем его
		{
			if ($categoryId && strlen($categoryId) == 24)
			{
				$products = Models\Product::getProducts($categoryId);

				if ($products)
					echo json_encode($products);
				else
					echo json_encode(null);

			} else
				echo json_encode(null);

		} else // иначе перенаправляем ...
		{
			return $this->response->redirect('admin/products');
		}
	}

	public function addProductAction()
	{
		$this->tag->prependTitle('Добавление товара');

		$types = Models\ProductType::getAllTypesAsString();
		$countries = Models\Country::getAllTypesAsString();
		$brands = Models\ProductBrands::getAllTypesAsString();

		$this->view->setVars([
			'types' => $types,
			'countries' => $countries,
			'brands' => $brands
		]);

		// POST запрос
		if($this->request->isPost())
		{
			$name = $this->request->getPost('name', ['trim', 'striptags']);
			$type = $this->request->getPost('type', ['trim', 'striptags']);
			$articul = $this->request->getPost('articul', ['trim', 'striptags']);
			$model = $this->request->getPost('model', ['trim', 'striptags']);
			$country = $this->request->getPost('country', ['trim', 'striptags']);
			$brand = $this->request->getPost('brand', ['trim', 'striptags']);
			$curancy = $this->request->getPost('main_curancy', ['trim', 'striptags']);
			$price = $this->request->getPost('price', ['trim', 'striptags']);
			$short_desc = $this->request->getPost('short_desc', ['trim']);
			$full_desc = $this->request->getPost('full_desc', ['trim']);
			$meta_keywords = $this->request->getPost('keywords', ['trim', 'striptags']);
			$meta_description = $this->request->getPost('description', ['trim', 'striptags']);

			$validation = new \App\Validation();

			$validation->isNotEmpty([
				'Название' => $name,
				'Тип' => $type,
				'Страна-производитель' => $country,
				'Основная валюта' => $curancy,
				'Цена' => $price
			], false);

			if (!$articul)
			{
				// Если артикул не указан, пытаемся взять его значение из Модели
				if ($validation->isNotEmpty(['Модель' => $model], true))
				{
					$articul = $model;

				} else // Если Модель тоже не указана, генерируем ошибку артикула
				{
					$validation->isNotEmpty([
						'Артикул' => $articul
					], false);
				}
			}

			$validation->isInRangeString([
				'Тип' => $type,
				'Страна-производитель' => $country
			], 3, 20, false);

			if ($validation->isFloat(['Цена' => $price], true))
				$price = preg_replace('/,/', '.', $price); // меняем "," на "."

			// Внесенные значения
			$inputs = [
				'name' => $name,
				'type' => $type,
				'articul' => $articul,
				'model' => $model,
				'country' => $country,
				'brand' => $brand,
				'price' => $price,
				'short_description' => $short_desc,
				'full_description' => $full_desc,
				'meta_keywords' => $meta_keywords,
				'meta_description' => $meta_description
			];

			// Если пройдена вся валидация
			if ($validation->validate())
			{
				$product = new Models\Product();

				$product->name = $name;
				$product->type = $type;
				$product->articul = $articul;
				if ($model)
					$product->model = $model;
				$product->country = $country;
				if ($brand)
					$product->brand = $brand;
				$product->main_curancy = $curancy;

				// Название цены на основании основной валюты
				$priceName = 'price_' . $curancy;

				$product->$priceName = floatval($price);
				if ($short_desc)
					$product->short_description = $short_desc;

				if ($full_desc)
					$product->full_description = $full_desc;

				if ($meta_keywords)
					$product->meta_keywords = $meta_keywords;

				if ($meta_description)
					$product->meta_description = $meta_description;

				// Добавляем возможные варианты стран, типов и брендов для автодополнения
				Models\Country::addCountry($product->country);
				Models\ProductBrands::addBrand($product->brand);
				Models\ProductType::addType($product->type);

				$product->seo_name = Models\Product::generateSeoName($product);
				$product->public = false;

				$product->save();

				if (Models\Product::isUniqueSeoName($product->seo_name)) // Если SEO-название уникально, то перенаправляем на дальнейшее редактирование
				{
					return $this->response->redirect('admin/editproduct/' . $product->_id . '/');

				} else // Иначе идем на страницу для редактирования SEO-названия
				{
					return $this->response->redirect('admin/editseoname/' . $product->_id . '/');
				}


			} else // Иначе передаем ошибки в представление
			{
				$this->view->setVars([
					'data' => $inputs,
					'errors' => $validation->getMessages()
				]);
			}


		}

		echo $this->view->render('admin/products/add');
	}

	public function editProductAction()
	{
		$id = $this->dispatcher->getParams()[0];
		$this->tag->prependTitle('Редактирование');

		if ($id && strlen($id) == 24) // Если ID есть, обрабатываем запрос
		{
			$productObj = Models\Product::getProductById($id);  // объект товара, с которым работаем
			$product = $productObj->toArray();                  // этот же объект в виде массива для представления
		}
		else // иначе отправляемся ...
		{
			return $this->response->redirect('admin');
		}

		if ($productObj->categories && count($productObj->categories) > 0)
		{
			$productCats = [];
			foreach ($productObj->categories as $catId)
			{
				$productCats[] = Models\Category::getCategoryWithFullName($catId);
			}
			$this->view->productCats = $productCats;
		}

		if ($this->request->isPost()) // Если POST запрос
		{
			$inputs = [
				'seo_name' => $this->request->getPost('seo-name', ['trim', 'striptags']),
				'name' => $this->request->getPost('name', ['trim', 'striptags']),
				'type' => $this->request->getPost('type', ['trim', 'striptags']),
				'articul' => $this->request->getPost('articul', ['trim', 'striptags']),
				'model' => $this->request->getPost('model', ['trim', 'striptags']),
				'country' => $this->request->getPost('country', ['trim', 'striptags']),
				'brand' => $this->request->getPost('brand', ['trim', 'striptags']),
				'main_curancy' => $this->request->getPost('main_curancy', ['trim', 'striptags'])
			];

			// Устанавливаем соответствующую цену
			if ($inputs['main_curancy'] == 'eur')
				$inputs['price_eur'] = $this->request->getPost('price', ['trim', 'striptags']);
			elseif ($inputs['main_curancy'] == 'usd')
				$inputs['price_usd'] = $this->request->getPost('price', ['trim', 'striptags']);
			elseif ($inputs['main_curancy'] == 'uah')
				$inputs['price_uah'] = $this->request->getPost('price', ['trim', 'striptags']);

			$inputs['short_description'] = $this->request->getPost('short_desc', ['trim']);
			$inputs['full_description'] = $this->request->getPost('full_desc', ['trim']);
			$inputs['meta_keywords'] = $this->request->getPost('keywords', ['trim', 'striptags']);
			$inputs['meta_description'] = $this->request->getPost('description', ['trim', 'striptags']);
			$inputs['public'] = $this->request->getPost('public', ['trim', 'striptags']);

			$validation = new \App\Validation();

			$validation->isNotEmpty([
				'Название' => $inputs['name'],
				'Тип' => $inputs['type'],
				'Страна-производитель' => $inputs['country'],
				'Основная валюта' => $inputs['main_curancy']
			], false);

			if ($inputs['main_curancy'] == 'eur')
				$validation->isNotEmpty(['Цена', $inputs['price_eur']], false);
			elseif ($inputs['main_curancy'] == 'usd')
				$validation->isNotEmpty(['Цена', $inputs['price_usd']], false);
			elseif ($inputs['main_curancy'] == 'uah')
				$validation->isNotEmpty(['Цена', $inputs['price_uah']], false);

			if (!$inputs['articul'])
			{
				// Если артикул не указан, пытаемся взять его значение из Модели
				if ($validation->isNotEmpty(['Модель' => $inputs['model']], true))
				{
					$inputs['articul'] = $inputs['model'];

				} else // Если Модель тоже не указана, генерируем ошибку артикула
				{
					$validation->isNotEmpty([
						'Артикул' => $inputs['articul']
					], false);
				}
			}

			$validation->isInRangeString([
				'Тип' => $inputs['type'],
				'Страна-производитель' => $inputs['country']
			], 3, 20, false);

			if ($inputs['main_curancy'] == 'eur')
			{
				if ($validation->isFloat(['Цена' => $inputs['price_eur']], true))
					$inputs['price_eur'] = preg_replace('/,/', '.', $inputs['price_eur']); // меняем "," на "."
			}
			elseif ($inputs['main_curancy'] == 'usd')
			{
				if ($validation->isFloat(['Цена' => $inputs['price_usd']], true))
					$inputs['price_usd'] = preg_replace('/,/', '.', $inputs['price_usd']); // меняем "," на "."
			}
			elseif ($inputs['main_curancy'] == 'uah')
			{
				if ($validation->isFloat(['Цена' => $inputs['price_uah']], true))
					$inputs['price_usd'] = preg_replace('/,/', '.', $inputs['price_uah']); // меняем "," на "."
			}

			if (!Models\Product::isUniqueSeoName($inputs['seo_name']))
				$validation->setMessageManual('SEO название', 'SEO название не уникально');

			// Если пройдена вся валидация
			if ($validation->validate())
			{
				$productObj->name = $inputs['name'];
				$productObj->type = $inputs['type'];
				$productObj->articul = $inputs['articul'];
				if ($inputs['model'])
					$productObj->model = $inputs['model'];
				$productObj->country = $inputs['country'];
				if ($inputs['brand'])
					$productObj->brand = $inputs['brand'];
				$productObj->main_curancy = $inputs['main_curancy'];

				// Название цены на основании основной валюты
				$priceName = 'price_' . $inputs['main_curancy'];

				$productObj->$priceName = floatval($inputs[$priceName]);
				if ($inputs['short_description'])
					$productObj->short_description = $inputs['short_description'];

				if ($inputs['full_description'])
					$productObj->full_description = $inputs['full_description'];

				if ($inputs['meta_keywords'])
					$productObj->meta_keywords = $inputs['meta_keywords'];

				if ($inputs['meta_description'])
					$productObj->meta_description = $inputs['meta_description'];

				$productObj->public = ($inputs['public'] == 'on') ? true : false;

				// Добавляем возможные варианты стран, типов и брендов для автодополнения
				Models\Country::addCountry($productObj->country);
				Models\ProductBrands::addBrand($productObj->brand);
				Models\ProductType::addType($productObj->type);

				$productObj->save();

				$this->view->success = 'Данные успешно сохранены';

			} else // Иначе передаем ошибки в представление
			{
				$this->view->errors = $validation->getMessages();
			}

			$this->view->product = $inputs;

		} elseif ($this->request->isGet())
		{
			$this->view->product = $product;
		}

		$this->view->id = $id;
		$this->view->categories = json_encode(Models\Category::getAllCategories());
		$this->view->types = Models\ProductType::getAllTypesAsString();
		$this->view->countries = Models\Country::getAllTypesAsString();
		$this->view->brands = Models\ProductBrands::getAllTypesAsString();
		$this->view->parameters = $productObj->parameters;

		echo $this->view->render('admin/products/edit');
	}

	public function deleteProductAction()
	{
		$id = $this->dispatcher->getParams()[0];

		if ($id)
		{
			$product = Models\Product::findById($id);
			$product->delete();

			$this->response->redirect('admin/products');

		} else
		{
			$this->response->redirect('admin/products');
		}
	}

	public function editSeoNameAction()
	{
		$id = $this->dispatcher->getParams()[0];
		$this->tag->prependTitle('SEO-название');

		if ($id)
		{
			$product = Models\Product::getProductById($id);

			// Товары с таким же SEO-названием
			$sameProducts = Models\Product::find([
				'conditions' => ['seo_name' => $product->seo_name]
			]);

			if (count($sameProducts) > 1)
			{
				$this->view->setVar('sameProducts', $sameProducts);
			}

			$this->view->setVar('product', $product);

			// POST запрос
			if ($this->request->isPost())
			{
				$validation = new \App\Validation();
				$seo_name = $this->request->getPost('seo-name', ['trim', 'striptags']);

				$validation->isNotEmpty(['Сео-название' => $seo_name], false);

				// Если валидация пройдена
				if ($validation->validate())
				{
					$product->seo_name = \App\Translit::get_seo_keyword($seo_name, true);
					$product->save();

					// Товары с таким же SEO-названием
					$sameProducts = Models\Product::find([
						'conditions' => ['seo_name' => $product->seo_name]
					]);

					// Если повторы все-равно есть
					if (count($sameProducts) > 1)
					{
						$this->view->setVar('sameProducts', $sameProducts);

					} else // иначе перенаправляемся к дальнейшему редактированию
					{
						return $this->response->redirect('admin/editproduct/' . $product->_id);
					}

				} else // иначе отправляем ошибки в представление
				{
					$this->view->setVar('errors', $validation->getMessages());
				}
			}
		}

		echo $this->view->render('admin/products/editseo');
	}

	public function addCategoryToProductAction()
	{
		$categoryId = $this->dispatcher->getParams()[0];
		$productId = $this->dispatcher->getParams()[1];
		if ($this->request->isAjax() && $categoryId && $productId && strlen($categoryId) == 24 && strlen($productId) == 24)
		{
			$product = Models\Product::findById($productId);

			$product->categories[] = $categoryId;

			if ($product->save())
			{
				echo json_encode(Models\Category::getCategoryWithFullName($categoryId));
			}
			else
				echo null;
		} else
		{
			return $this->response->redirect('admin');
		}
	}

	public function deleteProductCategoryAction()
	{
		$catId = $this->dispatcher->getParams()[0];
		$productId = $this->dispatcher->getParams()[1];

		if ($this->request->isAjax())
		{
			if ($catId && strlen($catId) == 24 && $productId && strlen($productId) == 24)
			{
				$product = Models\Product::getProductById($productId);

				if ($product && count($product) > 0)
				{
					foreach ($product->categories as $key => $value)
					{
						if ($value == $catId)
						{
							unset($product->categories[$key]);
						}
					}

					$product->save();
					echo json_encode(true);
				} else
				{
					echo json_encode(false);
				}
			} else
			{
				echo json_encode(false);
			}
		} else
		{
			echo json_encode(false);
			return $this->response->redirect('admin');
		}
	}

	public function getJsonParametersAction()
	{
		if ($this->request->isAjax())
		{
			echo Models\ProductParameter::getAllParameters(true);

		} else
		{
			$this->response->redirect('admin');
		}
	}

	public function addParamAction()
	{
		if ($this->request->isAjax())
		{
			$prodId = $this->dispatcher->getParams()[0];
			$paramName = strtolower(strip_tags(trim($this->dispatcher->getParams()[1])));
			$paramValue = strip_tags(trim($this->dispatcher->getParams()[2]));

			if ($prodId && strlen($prodId) == 24 && $paramName && $paramValue)
			{
				Models\ProductParameter::addParameter($paramName); // добавляем название параметра в ощий список

				if ($product = Models\Product::getProductById($prodId))
				{
					$isParameterThere = false;
					foreach ($product->parameters as $key => $value)
					{
						if ($key == $paramName)
						{
							$isParameterThere = true;
							break;
						}
					}

					if ($isParameterThere)
					{
						echo json_encode(false);
					} else
					{
						$product->parameters[$paramName] = $paramValue;
						$product->save();
						echo json_encode(true);
					}
				}

			} else
			{
				echo json_encode(false);
			}
		}
	}

	public function deleteParamAction()
	{
		if ($this->request->isAjax())
		{
			$prodId = $this->dispatcher->getParams()[0];
			$paramName = $this->dispatcher->getParams()[1];

			if ($prodId && strlen($prodId) == 24 && $paramName)
			{
				if ($product = Models\Product::getProductById($prodId))
				{
					foreach ($product->parameters as $key => $value)
					{
						if ($key == $paramName)
						{
							unset($product->parameters[$key]);
							break;
						}
					}

					if ($product->save())
					{
						echo json_encode(true);
					} else
					{
						echo json_encode(false);
					}

				} else
				{
					echo json_encode(false);
				}

			} else
			{
				echo json_encode(false);
			}
		} else
		{
			$this->response->redirect('admin');
		}


	}
}