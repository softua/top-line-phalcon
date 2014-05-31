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
			$this->user = Models\User::find((int)trim($this->cookies->get('user')->getValue()))[0];

			$this->cookies->set('user', $this->user->id, time() + $this->config->cookie['lifetime']);
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

			$users = Models\User::query()
				->where('login = :login:')
				->bind(['login' => $login])
				->execute();

			if (count($users) == 1) {
				$bdPass = trim($this->crypt->decryptBase64($users[0]->password));

				if ($bdPass == $password) {

					$this->cookies->set('user', (string)$users[0]->id, time() + $this->config->cookie['lifetime']);

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

		$allUsers = Models\User::find();
		$roles = Models\Role::find();

		$this->view->users = $allUsers;
		$this->view->roles = $roles;

		echo $this->view->render('admin/users/list');
	}

	public function userAction()
	{
		$id = $this->dispatcher->getParams()[0];
		$this->tag->prependTitle('Редактирование');
		$user = Models\User::find($id)[0];
		$roles = Models\Role::find();

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
				$user->role_id = $role;

				$user->save();
			} else {
				$errors = $validation->getMessages();
				$this->view->errors = $errors;
			}
		}

		$this->view->user = $user;
		$this->view->roles = $roles;

		echo $this->view->render('admin/users/edit');
	}

	public function newUserAction()
	{
		$this->tag->prependTitle('Новый пользователь');

		// GET запрос
		if ($this->request->isGet()) {
			$this->view->roles = Models\Role::find();
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
				$user = new Models\User();

				$user->login = $login;
				$user->password = $this->crypt->encryptBase64($password);
				$user->name = $name;
				$user->email = $email;
				$user->role_id = $role;

				if ($user->save()) {
					$this->response->redirect('admin/users');
				}
			} else {
				$this->view->roles = Models\Role::find();
				$this->view->errors = $validation->getMessages();

				echo $this->view->render('admin/users/new');
			}
		}
	}

	public function deleteUserAction()
	{
		$id = $this->dispatcher->getParams()[0];

		$user = Models\User::find($id);
		$user->delete();

		if ($this->cookies->get('user')->getValue() == $id) {
			$this->logoutAction();
		}

		$this->response->redirect('admin/users');
	}

	public function categoriesAction()
	{
		$this->tag->prependTitle('Категории');

		$this->view->mainCategories = Models\Category::getMainCategories();

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

		$this->view->fullParentCategory = $fullParentCategory;
		$this->view->parent = $parent;

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
				$category->parent_id = $parent;
				$category->seo_name = \App\Translit::get_seo_keyword($name, true);
				$category->sort = (int)$sort;

				$category->save();

				return $this->response->redirect('admin/categories');
			} else {
				$errors = $validation->getMessages();
				$this->view->errors = $errors;
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

		$category = Models\Category::find($id)[0];
		$this->view->category = $category;
		$fullParentCategory = Models\Category::getFullCategoryName($category->parent_id);
		$this->view->fullParentCategory = $fullParentCategory;

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
				$category->seo_name = \App\Translit::get_seo_keyword($name, true);
				$category->sort = (int)$sort;
				$category->save();
			} else {
				$errors = $validation->getMessages();
				$this->view->errors = $errors;
			}
		}

		echo $this->view->render('admin/categories/edit');
	}

	public function deleteCategoryAction()
	{
		$id = $this->dispatcher->getParams()[0];
		if($id && $id != '0') {
			$category = Models\Category::find($id);
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
			$countProducts = Models\ProductCategory::query()
				->where('product_id = :id:')
				->bind(['id' => $product->id])
				->execute()->count();

			if (!$countProducts)
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
			if ($categoryId)
			{
				$products = Models\Product::getProducts($categoryId);

				if ($products && count($products))
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

		$types = Models\PossibleProductTypes::getAllTypesAsString();
		$countries = Models\Country::getAllTypesAsString();
		$brands = Models\PossibleBrands::getAllTypesAsString();

		$this->view->types = $types;
		$this->view->countries = $countries;
		$this->view->brands = $brands;

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
			$priceAlternative = $this->request->getPost('price_alternative', ['trim', 'striptags']);
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
				'Страна-производитель' => $country
			], 3, 20, false);

			$validation->isInRangeString([
				'Тип' => $type
			], 3, 50, false);

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
				'price_alternative' => $priceAlternative,
				'short_description' => $short_desc,
				'full_description' => $full_desc,
				'meta_keywords' => $meta_keywords,
				'meta_description' => $meta_description
			];

			// Если пройдена вся валидация
			if ($validation->validate())
			{
				// Добавляем возможные варианты стран, типов и брендов для автодополнения
				Models\Country::addCountry($country);
				Models\PossibleBrands::addBrand($brand);
				Models\PossibleProductTypes::addType($type);

				$product = new Models\Product();

				$product->name = $name;
				$product->type = $type;
				$product->articul = $articul;
				if ($model)
					$product->model = $model;

				$countryId = Models\Country::findFirst([
					'name = ?1',
					'bind' => [1 => $country]
				]);
				$product->country_id = $countryId->id;

				if ($brand)
					$product->brand = $brand;

				$product->main_curancy = $curancy;

				// Название цены на основании основной валюты
				$priceName = 'price_' . $curancy;

				$product->$priceName = floatval($price);
				$product->price_alternative = $priceAlternative;
				if ($short_desc)
					$product->short_description = $short_desc;

				if ($full_desc)
					$product->full_description = $full_desc;

				if ($meta_keywords)
					$product->meta_keywords = $meta_keywords;

				if ($meta_description)
					$product->meta_description = $meta_description;

				$product->seo_name = Models\Product::generateSeoName($product);
				$product->public = 0;

				$product->save();

				if (Models\Product::isUniqueSeoName($product->seo_name)) // Если SEO-название уникально, то перенаправляем на дальнейшее редактирование
				{
					return $this->response->redirect('admin/editproduct/' . $product->id . '/');

				} else // Иначе идем на страницу для редактирования SEO-названия
				{
					return $this->response->redirect('admin/editseoname/' . $product->id . '/');
				}


			} else // Иначе передаем ошибки в представление
			{
				$this->view->data = $inputs;
				$this->view->errors = $validation->getMessages();
			}


		}

		echo $this->view->render('admin/products/add');
	}

	public function editProductAction()
	{
		$id = $this->dispatcher->getParams()[0];
		$this->tag->prependTitle('Редактирование');

		if ($id && preg_match('/[0-9]+/', $id)) // Если ID есть, обрабатываем запрос
		{
			$product = Models\Product::getProductById($id);  // объект товара, с которым работаем

			$inputs['id'] = $product->id;
			$inputs['seo_name'] = $product->seo_name;
			$inputs['name'] = $product->name;
			$inputs['type'] = $product->type;
			$inputs['articul'] = $product->articul;
			$inputs['model'] = $product->model;
			$inputs['country'] = Models\Country::findFirst($product->country_id)->name;
			$inputs['brand'] = $product->brand;
			$inputs['main_curancy'] = $product->main_curancy;
			$inputs['price_alternative'] = $product->price_alternative;
			$inputs['short_description'] = $product->short_description;
			$inputs['full_description'] = $product->full_description;
			$inputs['meta_keywords'] = $product->meta_keywords;
			$inputs['meta_description'] = $product->meta_description;
			$inputs['public'] = $product->public;

			if ($inputs['main_curancy'] == 'eur')
				$inputs['price'] = $product->price_eur;
			elseif ($inputs['main_curancy'] == 'usd')
				$inputs['price'] = $product->price_usd;
			elseif ($inputs['main_curancy'] == 'uah')
				$inputs['price'] = $product->price_uah;

			$productCategoryObjects = Models\ProductCategory::find([
				'product_id = ?1',
				'bind' => [1 => $id],
				'order' => 'sort'
			]);

			if ($productCategoryObjects && count($productCategoryObjects) > 0)
			{
				$productCats = [];
				foreach ($productCategoryObjects as $productCategoryObject)
				{
					$productCats[] = Models\Category::getCategoryWithFullName($productCategoryObject->category_id);
				}
				$inputs['categories'] = $productCats;
			}

			if ($this->request->isPost()) // Если POST запрос
			{
				$inputs['seo_name'] = $this->request->getPost('seo-name', ['trim', 'striptags']);
				$inputs['name'] = $this->request->getPost('name', ['trim', 'striptags']);
				$inputs['type'] = $this->request->getPost('type', ['trim', 'striptags']);
				$inputs['articul'] = $this->request->getPost('articul', ['trim', 'striptags']);
				$inputs['model'] = $this->request->getPost('model', ['trim', 'striptags']);
				$inputs['country'] = $this->request->getPost('country', ['trim', 'striptags']);
				$inputs['brand'] = $this->request->getPost('brand', ['trim', 'striptags']);
				$inputs['price'] = $this->request->getPost('price', ['trim', 'striptags']);
				$inputs['main_curancy'] = $this->request->getPost('main_curancy', ['trim', 'striptags']);
				$inputs['price_alternative'] = $this->request->getPost('price_alternative', ['trim', 'striptags']);
				$inputs['short_description'] = $this->request->getPost('short_desc', ['trim', 'striptags']);
				$inputs['full_description'] = $this->request->getPost('full_desc', ['trim', 'striptags']);
				$inputs['meta_keywords'] = $this->request->getPost('keywords', ['trim', 'striptags']);
				$inputs['meta_description'] = $this->request->getPost('description', ['trim', 'striptags']);
				$inputs['public'] = $this->request->getPost('public', ['trim', 'striptags']);

				$validation = new \App\Validation();

				$validation->isNotEmpty([
					'Название' => $inputs['name'],
					'Тип' => $inputs['type'],
					'Страна-производитель' => $inputs['country'],
					'Цена' => $inputs['price'],
					'Основная валюта' => $inputs['main_curancy']
				], false);

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
					'Страна-производитель' => $inputs['country']
				], 3, 20, false);

				$validation->isInRangeString([
					'Тип' => $inputs['type']
				], 3, 50, false);

				if ($validation->isFloat(['Цена' => $inputs['price']], true))
					$inputs['price'] = preg_replace('/,/', '.', $inputs['price']); // меняем "," на "."

				if (!Models\Product::isUniqueSeoName($inputs['seo_name']))
					$validation->setMessageManual('SEO название', 'SEO название не уникально');

				// Если пройдена вся валидация
				if ($validation->validate())
				{
					// Добавляем возможные варианты стран, типов и брендов для автодополнения
					Models\Country::addCountry($inputs['country']);
					Models\PossibleBrands::addBrand($inputs['brand']);
					Models\PossibleProductTypes::addType($inputs['type']);

					$product->name = $inputs['name'];
					$product->type = $inputs['type'];
					$product->articul = $inputs['articul'];

					if ($inputs['model'])
						$product->model = $inputs['model'];

					$product->country_id = Models\Country::findFirst([
						'name = ?1',
						'bind' => [1 => $inputs['country']]
					])->id;

					if ($inputs['brand'])
						$product->brand = $inputs['brand'];

					$product->main_curancy = $inputs['main_curancy'];

					// Название цены на основании основной валюты
					$priceName = 'price_' . $inputs['main_curancy'];
					$product->$priceName = floatval($inputs['price']);

					$product->price_alternative = $inputs['price_alternative'];

					if ($inputs['short_description'])
						$product->short_description = $inputs['short_description'];

					if ($inputs['full_description'])
						$product->full_description = $inputs['full_description'];

					if ($inputs['meta_keywords'])
						$product->meta_keywords = $inputs['meta_keywords'];

					if ($inputs['meta_description'])
						$product->meta_description = $inputs['meta_description'];

					if ($inputs['public'] == 'on')
						$product->public = $inputs['public'] = 1;
					else
						$product->public = $inputs['public'] = 0;

					if ($product->save())
						$this->view->success = 'Данные успешно сохранены';
					else
						$this->view->alert = 'Что-то не так !';

				} else // Иначе передаем ошибки в представление
				{
					$this->view->errors = $validation->getMessages();
				}
			}

			$this->view->data = $inputs;
			$this->view->id = $id;
			$this->view->categories = json_encode(Models\Category::getAllCategories());
			$this->view->types = Models\PossibleProductTypes::getAllTypesAsString();
			$this->view->countries = Models\Country::getAllTypesAsString();
			$this->view->brands = Models\PossibleBrands::getAllTypesAsString();
			$this->view->parameters = Models\ProductParam::getParamsByProductId($id);

			echo $this->view->render('admin/products/edit');

		}
		else // иначе отправляемся ...
		{
			return $this->response->redirect('admin');
		}
	}

	public function deleteProductAction()
	{
		$id = $this->dispatcher->getParams()[0];

		if ($id && preg_match('//\d+', $id))
		{
			$product = Models\Product::findFirst($id);
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
			echo Models\PossibleParameters::getAllParameters(true);

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
			$paramName = strtolower(strip_tags(trim($this->request->getPost('key'))));
			$paramValue = strip_tags(trim($this->request->getPost('value')));

			if ($prodId && strlen($prodId) == 24 && $paramName && $paramValue)
			{
				Models\PossibleParameters::addParameter($paramName); // добавляем название параметра в ощий список
				Models\PossibleParameters::addParameter($paramValue); // добавляем значение параметра в ощий список

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
			$paramName = $this->request->getPost('param-name');

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