<?

namespace App\Controllers;

use App,
	App\Models,
	App\Translit,
	App\Validation,
	App\Upload;

class AdminController extends BaseAdminController
{
	public function beforeExecuteRoute()
	{
		if (!$this->cookies->has('user'))
		{
			if ($this->dispatcher->getActionName() != 'login')
			{
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
				$this->view->errors = ['Вход' => ['Неверный логин или пароль']];
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
		if ($this->request->isPost())
		{
			$name = $this->request->getPost('name', ['striptags', 'trim']);
			$email = $this->request->getPost('email', ['email', 'trim']);
			$role = $this->request->getPost('role_id', ['alphanum', 'trim']);

			$validation = new \App\Validation();

			$validation->isNotEmpty([
				'Имя' => $name,
				'Права' => $role
			], false);

			if ($validation->validate())
			{
				$user->name = $name;
				$user->email = $email;
				$user->role_id = $role;

				if ($user->save())
				{
					$this->view->errors = ['success' => ['Данные успешно сохранены']];
				} else {
					$this->view->errors = ['БД' => $user->getMessages()];
				}
			} else {
				$this->view->errors = $validation->getMessages();
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

				if ($user->save())
				{
					$this->response->redirect('admin/users');
				} else {
					$this->view->errors = ['БД' => $user->getMessages()];
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

		$this->view->mainCategories = Models\Category::getMainCategories($this->di);

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
		if ($this->request->isPost())
		{
			$category = new Models\Category();

			$name = $this->request->getPost('name', ['striptags', 'trim']);
			$sort = $this->request->getPost('sort', ['striptags', 'trim', 'int']);

			$validation = new \App\Validation();

			$validation->isNotEmpty([
				'Категория' => $name,
				'Порядок' => $sort
			], false);

			if ($validation->validate())
			{
				$category->name = $name;
				$category->parent_id = $parent;
				if ($parent == '0')
				{
					$category->seo_name = Translit::get_seo_keyword($name, true);

				} else {
					$category->seo_name = Models\Category::getCategory($parent)->seo_name . '__' . Translit::get_seo_keyword($name, true);
				}
				$category->sort = (int)$sort;

				if ($category->dbSave())
				{
					return $this->response->redirect('admin/categories');

				} else {
					$this->view->errors = ['БД' => $category->getMessages()];
				}
			} else {
				$this->view->errors = $validation->getMessages();
			}
		}

		echo $this->view->render('admin/categories/add');
	}

	public function editCategoryAction()
	{
		$id = $this->dispatcher->getParams()[0];
		if ($id == null) return $this->response->redirect('admin/categories');

		$this->tag->prependTitle('Редактирование');

		$category = Models\Category::findFirst($id);
		$this->view->category = $category;
		$fullParentCategory = Models\Category::getCategoryWithFullName($category->parent_id)['full_name'];
		$this->view->fullParentCategory = $fullParentCategory;

		// POST
		if($this->request->isPost())
		{
			$name = $this->request->getPost('name', ['striptags', 'trim']);
			$sort = $this->request->getPost('sort', ['striptags', 'trim', 'int']);

			$validation = new Validation();
			$validation->isNotEmpty([
				'Категория' => $name,
				'Порядок' => $sort
			], false);
			if ($validation->validate())
			{
				$category->name = $name;
				if (!$category->parent_id)
				{
					$category->seo_name = Translit::get_seo_keyword($name, true);

				} else {
					$category->seo_name = Models\Category::findFirst($category->parent_id)->seo_name . '__' . Translit::get_seo_keyword($name, true);

				}
				$category->sort = (int)$sort;

				if ($category->save())
				{
					$this->view->errors = ['success' => ['Данные успешно сохранены']];
				} else {
					$this->view->errors = ['БД' => $category->getMessages()];
				}
			} else {
				$this->view->errors = $validation->getMessages();
			}
		}

		/** @var Models\ImageCategory[] $fotos */
		$fotos = Models\ImageCategory::query()
			->where('belongs = \'category\'')
			->andWhere('belongs_id = ?1', [1 => $id])
			->orderBy('sort')
			->execute()->filter(function(Models\ImageCategory $img) {
				$img->setPaths();
				return $img;
			});

		$this->view->fotos = $fotos;

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

		$types = Models\PossibleProductType::getAllTypesAsString();
		$countries = Models\Country::getAllTypesAsString();
		$brands = Models\PossibleBrand::getAllTypesAsString();

		$this->view->types = $types;
		$this->view->countries = $countries;
		$this->view->brands = $brands;

		$inputs = [];

		// POST запрос
		if($this->request->isPost())
		{
			$inputs['name'] = $this->request->getPost('name', ['trim', 'striptags']);
			$inputs['type'] = $this->request->getPost('type', ['trim', 'striptags']);
			$inputs['articul'] = $this->request->getPost('articul', ['trim', 'striptags']);
			$inputs['model'] = $this->request->getPost('model', ['trim', 'striptags']);
			$inputs['country'] = $this->request->getPost('country', ['trim', 'striptags']);
			$inputs['brand'] = $this->request->getPost('brand', ['trim', 'striptags']);
			$inputs['main_curancy'] = $this->request->getPost('main_curancy', ['trim', 'striptags']);
			$inputs['price'] = $this->request->getPost('price', ['trim', 'striptags']);
			$inputs['price_alternative'] = $this->request->getPost('price_alternative', ['trim', 'striptags']);
			$inputs['short_description'] = $this->request->getPost('short_desc', 'trim');
			$inputs['full_description'] = $this->request->getPost('full_desc', 'trim');
			$inputs['meta_keywords'] = $this->request->getPost('keywords', ['trim', 'striptags']);
			$inputs['meta_description'] = $this->request->getPost('description', ['trim', 'striptags']);

			$validation = new Validation();

			$validation->isNotEmpty([
				'Название' => $inputs['name'],
				'Тип' => $inputs['type'],
				'Страна-производитель' => $inputs['country'],
				'Основная валюта' => $inputs['main_curancy'],
				'Цена' => $inputs['price']
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

			// Если пройдена вся валидация
			if ($validation->validate())
			{
				// Добавляем возможные варианты стран, типов и брендов для автодополнения
				Models\Country::addCountry($inputs['country']);
				Models\PossibleBrand::addBrand($inputs['brand']);
				Models\PossibleProductType::addType($inputs['type']);

				$product = new Models\Product();

				$product->name = $inputs['name'];
				$product->type = $inputs['type'];
				$product->articul = $inputs['articul'];
				$product->model = $inputs['model'];
				$product->brand = $inputs['brand'];
				$product->main_curancy = $inputs['main_curancy'];
				$product->price_alternative = $inputs['price_alternative'];
				$product->short_description = $inputs['short_description'];
				$product->full_description = $inputs['full_description'];
				$product->meta_keywords = $inputs['keywords'];
				$product->meta_description = $inputs['description'];
				$product->public = 0;
				$product->novelty = 0;
				$product->country_id = Models\Country::findFirst([
					'name = ?1',
					'bind' => [1 => $inputs['country']]
				])->id;
				$product->seo_name = Models\Product::generateSeoName($product);

				$prices = Models\Exchange::setPrices($product->main_curancy, floatval($inputs['price']));
				$product->price_eur = $prices['price_eur'];
				$product->price_usd = $prices['price_usd'];
				$product->price_uah = $prices['price_uah'];

				if ($product->save()) {
					if (Models\Product::isUniqueSeoName($product->seo_name)) // Если SEO-название уникально, то перенаправляем на дальнейшее редактирование
					{
						return $this->response->redirect('admin/editproduct/' . $product->id . '/');

					} else // Иначе идем на страницу для редактирования SEO-названия
					{
						return $this->response->redirect('admin/editseoname/' . $product->id . '/');
					}
				} else{
					$this->view->errors = ['БД' => $product->getMessages()];
				}
			} else // Иначе передаем ошибки в представление
			{
				$this->view->errors = $validation->getMessages();
			}
		}

		$this->view->data = $inputs;
		echo $this->view->render('admin/products/add');
	}

	public function editProductAction()
	{
		$id = $this->dispatcher->getParams()[0];
		$this->tag->prependTitle('Редактирование');

		if ($id && preg_match('/\d+/', $id)) // Если ID есть, обрабатываем запрос
		{
			/** @var Models\Product $product */
			$product = Models\Product::getProductById($id);  // объект товара, с которым работаем

			if (!$product)
				return $this->response->redirect('admin');

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
			$inputs['top'] = $product->top;
			$inputs['novelty'] = $product->novelty;

			if ($inputs['main_curancy'] == 'eur')
				$inputs['price'] = $product->price_eur;
			elseif ($inputs['main_curancy'] == 'usd')
				$inputs['price'] = $product->price_usd;
			elseif ($inputs['main_curancy'] == 'uah')
				$inputs['price'] = $product->price_uah;

			if (count($product->_categories))
			{
				$productCats = [];
				foreach ($product->_categories as $cat)
				{
					$productCats[] = Models\Category::getCategoryWithFullName($cat->id);
				}
				$inputs['categories'] = $productCats;
			}

			// Заносим ссылки видео для товара
			if (count($product->videos))
			{
				foreach ($product->getVideos(['order' => 'sort']) as $video)
				{
					$tempVideo = [];
					$tempVideo['id'] = $video->id;
					$tempVideo['name'] = ($video->name) ? $video->name : $video->href;
					$tempVideo['href'] = $video->href;
					$tempVideo['sort'] = $video->sort;
					$tempVideo['product_id'] = $video->product_id;
					$inputs['video'][] = $tempVideo;
				}
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
				$inputs['short_description'] = $this->request->getPost('short_desc', 'trim');
				$inputs['full_description'] = $this->request->getPost('full_desc', 'trim');
				$inputs['meta_keywords'] = $this->request->getPost('keywords', ['trim', 'striptags']);
				$inputs['meta_description'] = $this->request->getPost('description', ['trim', 'striptags']);
				$inputs['public'] = $this->request->getPost('public', ['trim', 'striptags']);
				$inputs['top'] = $this->request->getPost('top', ['trim', 'striptags']);
				$inputs['novelty'] = $this->request->getPost('novelty', ['trim', 'striptags']);

				$validation = new Validation();

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
					Models\PossibleBrand::addBrand($inputs['brand']);
					Models\PossibleProductType::addType($inputs['type']);

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

					$prices = Models\Exchange::setPrices($inputs['main_curancy'], floatval($inputs['price']));
					$product->price_eur = $prices['price_eur'];
					$product->price_usd = $prices['price_usd'];
					$product->price_uah = $prices['price_uah'];

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
					if ($inputs['top'] == 'on') $product->top = $inputs['top'] = 1;
					else $product->top = $inputs['top'] = 0;

					if ($inputs['novelty'] == 'on') $product->novelty = $inputs['novelty'] = 1;
					else $product->novelty = $inputs['novelty'] = 0;

					$product->seo_name = $inputs['seo_name'];

					if ($product->save())
						$this->view->errors = ['success' => ['Данные успешно сохранены']];
					else {
						$this->view->errors = ['БД' => $product->getMessages()];
					}

				} else // Иначе передаем ошибки в представление
				{
					$this->view->errors = $validation->getMessages();
				}
			}

			$fotos = $product->getImages();

			//Список акций
			$allSales = Models\Sale::query()
				->where('type_id = 5')
				->columns(['id', 'name'])
				->orderBy('expiration, name')
				->execute()
				->toArray();
			$salesForView = [];
			if ($product->getSales()) {
				foreach ($product->getSales() as $sale) {
					$tempSale = [];
					$tempSale['id'] = $sale->id;
					$tempSale['href'] = $this->url->get('sales/show/') . $sale->seo_name;
					$tempSale['name'] = $sale->name;
					$salesForView[] = $tempSale;
				}
			}


			$this->view->data = $inputs;
			$this->view->id = $id;
			$this->view->categories = json_encode(Models\Category::find()->toArray());
			$this->view->types = Models\PossibleProductType::getAllTypesAsString();
			$this->view->countries = Models\Country::getAllTypesAsString();
			$this->view->brands = Models\PossibleBrand::getAllTypesAsString();
			$this->view->parameters = Models\ProductParam::getParamsByProductId($id);
			$this->view->fotos = $fotos;
			$this->view->files = $product->files;
			$this->view->allSales = json_encode($allSales);
			$this->view->sales = $salesForView;

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

		if ($id && preg_match('/\d+/', $id))
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
				'seo_name = :name: AND id != :id:',
				'bind' => ['name' => $product->seo_name, 'id' => $id]
			]);

			if (count($sameProducts) > 0)
			{
				$this->view->sameProducts = $sameProducts;
			}

			$data = [
				'id' => $product->id,
				'seo_name' => $product->seo_name,
				'type' => $product->type,
				'articul' => $product->articul,
				'model' => $product->model,
				'brand' => $product->brand
			];

			$data['country'] = Models\Country::findFirst($product->country_id)->name;

			if ($product->main_curancy == 'eur')
				$data['price'] = $product->price_eur;
			elseif ($product->main_curancy == 'usd')
				$data['price'] = $product->price_usd;
			elseif ($product->main_curancy == 'uah')
				$data['price'] = $product->price_uah;

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

					$data['seo_name'] = $product->seo_name;

					// Товары с таким же SEO-названием
					$sameProducts = Models\Product::find([
						'seo_name = :name: AND id != :id:',
						'bind' => ['name' => $product->seo_name, 'id' => $product->id]
					]);

					// Если повторы все-равно есть
					if (count($sameProducts) > 0)
					{
						$this->view->sameProducts = $sameProducts;

					} else // иначе перенаправляемся к дальнейшему редактированию
					{
						return $this->response->redirect('admin/editproduct/' . $product->id);
					}

				} else { // иначе отправляем ошибки в представление
					$this->view->errors = $validation->getMessages();
				}
			}

			$this->view->data = $data;
		}

		echo $this->view->render('admin/products/editseo');
	}

	public function addCategoryToProductAction()
	{
		$categoryId = $this->dispatcher->getParams()[0];
		$productId = $this->dispatcher->getParams()[1];

		if ($this->request->isAjax() && $categoryId && $productId && preg_match('/\d+/', $categoryId) && preg_match('/\d+/', $productId))
		{
			$prodCat = new Models\ProductCategory();
			$prodCat->product_id = $productId;
			$prodCat->category_id = $categoryId;

			$sameCats = Models\ProductCategory::find([
				'product_id = :prod: AND category_id = :cat:',
				'bind' => ['prod' => $productId, 'cat' => $categoryId]
			]);

			if (count($sameCats) > 0)
			{
				echo null;
				return false;
			}

			if ($prodCat->save())
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
			$prodCat = Models\ProductCategory::findFirst([
				'product_id = :prodId: AND category_id = :catId:',
				'bind' => ['prodId' => $productId, 'catId' => $catId]
			]);

			if ($prodCat->delete())
				echo json_encode(true);
			else
				echo json_encode(false);

		} else
		{
			return $this->response->redirect('admin');
		}
	}

	public function getJsonParametersAction()
	{
		if ($this->request->isAjax())
		{
			echo Models\PossibleParameter::getAllParameters(true);

		} else
		{
			$this->response->redirect('admin');
		}
	}

	public function addParamAction()
	{
		if ($this->request->isAjax() && $this->request->isPost())
		{
			$prodId = $this->dispatcher->getParams()[0];
			$paramName = strtolower(strip_tags(trim($this->request->getPost('key'))));
			$paramValue = strip_tags(trim($this->request->getPost('value')));

			if ($prodId && preg_match('/\d+/', $prodId) && $paramName && $paramValue)
			{
				Models\PossibleParameter::addParameter($paramName); // добавляем название параметра в ощий список
				Models\PossibleParameter::addParameter($paramValue); // добавляем значение параметра в ощий список

				$prodParam = Models\ProductParam::findFirst([
					'name = :name: AND product_id = :id:',
					'bind' => ['name' => $paramName, 'id' => $prodId]
				]);

				if (!$prodParam)
				{
					$param = new Models\ProductParam();
					$param->name = $paramName;
					$param->value = $paramValue;
					$param->product_id = $prodId;
					$param->sort = Models\ProductParam::find([
						'product_id = :id:',
						'bind' => ['id' => $prodId]
					])->count();

					if ($param->save())
					{
						echo $param->id;
					}
					else
						echo 0;
				} else
				{
					echo 0;
				}

			} else
			{
				echo 0;
			}
		}
	}

	public function editParamAction()
	{
		$id = $this->dispatcher->getParams()[0];
		if ($id && preg_match('/\d+/', $id))
		{
			$param = Models\ProductParam::findFirst($id);

			$this->view->data = [
				'id' => $param->id,
				'name' => $param->name,
				'value' => $param->value,
				'product_id' => $param->product_id,
				'sort' => $param->sort
			];

			$allParams = Models\PossibleParameter::find();

			$possibleParamsNames = [];

			foreach ($allParams as $p) {
				$possibleParamsNames[] = $p->name;
			}

			$this->view->possibleValues = json_encode($possibleParamsNames);

			if ($this->request->isPost()) // POST запрос
			{
				$inputs['name'] = $this->request->getPost('name', ['trim', 'striptags']);
				$inputs['value'] = $this->request->getPost('value', ['trim', 'striptags']);
				$inputs['sort'] = $this->request->getPost('sort', ['trim', 'striptags', 'alphanum']);

				$validation = new Validation();
				$validation->isNotEmpty([
					'Название параметра' => $inputs['name'],
					'Значение параметра' => $inputs['value'],
					'Порядок' => $inputs['sort']
				], false);

				if ($validation->validate())
				{
					Models\PossibleParameter::addParameter($inputs['name']);
					Models\PossibleParameter::addParameter($inputs['value']);

					$param->name = $inputs['name'];
					$param->value = $inputs['value'];
					$param->sort = $inputs['sort'];

					if ($param->save())
					{
						$this->view->data = [
							'id' => $param->id,
							'name' => $param->name,
							'value' => $param->value,
							'product_id' => $param->product_id,
							'sort' => $param->sort
						];

						$this->view->errors = ['success' => ['Данные успешно сохранены']];

					} else {

						$this->view->data = [
							'id' => $param->id,
							'name' => $inputs['name'],
							'value' => $inputs['value'],
							'product_id' => $param->product_id,
							'sort' => $inputs['sort']
						];

						$this->view->errors = ['БД' => $param->getMessages()];
					}
				} else {

					$this->view->data = [
						'id' => $param->id,
						'name' => $inputs['name'],
						'value' => $inputs['value'],
						'product_id' => $param->product_id,
						'sort' => $inputs['sort']
					];

					$this->view->errors = $validation->getMessages();
				}
			}

			echo $this->view->render('admin/products/editparam');

		} else {

			return $this->response->redirect('admin');
		}
	}

	public function deleteParamAction()
	{
		if ($this->request->isAjax())
		{
			$paramId = $this->dispatcher->getParams()[0];
			if ($paramId && preg_match('/\d+/', $paramId))
			{
				$param = Models\ProductParam::findFirst($paramId);
				if ($param->delete())
					echo json_encode(true);
				else
					echo json_encode(false);
			}

		} else
		{
			$this->response->redirect('admin');
		}


	}

	public function sortParamsAction ()
	{
		if ($this->request->isAjax() && $this->request->isPost())
		{
			$paramIds = json_decode($this->request->getPost('ids'));

			for ($i = 0; $i < count($paramIds); $i++)
			{
				$param = Models\ProductParam::findFirst($paramIds[$i]);

				if ($param)
				{
					$param->sort = $i;
					$param->save();
				}
			}

		} else
		{
			return $this->response->redirect('admin');
		}
	}

	public function uploadFotoAction()
	{
		if (!$this->request->isAjax()) {
			echo 'null';
			return false;
		}

		$productId = $this->request->getQuery('prodId', ['striptags', 'trim']);

		$image = Models\ImageProduct::uploadImageAndReturn($productId);

		// Возвращаем тумбу для админки
		if ($image) {
			$result['id'] = $image->id;
			$result['path'] = $image->imgAdminPath;

			echo json_encode($result);
			return true;
		}
		else {
			echo "false";
			return false;
		}
	}

	public function sortFotosAction()
	{
		if ($this->request->isAjax() && $this->request->isPost()) {
			$fotoIds = json_decode($this->request->getPost('ids'));

			for ($i = 0; $i < count($fotoIds); $i++)
			{
				/** @var Models\ImageProduct | null $foto */
				$foto = Models\ImageProduct::findFirst($fotoIds[$i]);

				if ($foto) {
					$foto->sort = $i;
					$foto->dbSave();
				}
			}

		} else
		{
			return $this->response->redirect('admin');
		}
	}

	public function deleteProductFotoAction()
	{
		if (!$this->request->isAjax() || !$this->request->isPost()) {
			echo 'false';
			return false;
		}

		$id = $this->request->getPost('id', ['trim', 'striptags']);

		/** @var Models\ImageProduct | null $image */
		$image = Models\ImageProduct::query()
			->where('id = ?1')->bind([1 => $id])
			->execute()
			->getFirst();

		if (!$image) {
			echo "false";
			return false;
		}

		if ($image->deleteImages()) {
			echo "true";
			return true;
		}
		else {
			echo "false";
			return false;
		}
	}

	public function uploadFileAction()
	{
		if (!$this->request->isAjax())
		{
			echo null;
			return false;
		}

		$productId = trim(strip_tags($_GET['prodId']));

		$file = new Upload($_FILES['files']);

		if (!preg_match('/\d+/', $productId))
		{
			$file->clean();
			echo 'false';
			return false;
		}

		$bdFile = new Models\ProductFileModel();
		$bdFile->name = $file->file_src_name_body;
		$bdFile->pathname = '/';
		$bdFile->product_id = $productId;

		if ($bdFile->save()) // Создаем файлы
		{
			$folder = 'products/' . $bdFile->product_id;
			if (!file_exists($folder))
			{
				mkdir($folder, 0777, true);
			}

			$file->file_new_name_body = $bdFile->id . '__' . Translit::get_seo_keyword($bdFile->name, true);
			$file->process($folder);

			if ($file->processed)
			{
				$bdFile->pathname = $folder . '/' . str_replace('\\', '/', $file->file_dst_name);
				$bdFile->save();

				$result['id'] = $bdFile->id;
				$result['name'] = $bdFile->name;
				$result['path'] = $bdFile->pathname;

				echo json_encode($result);
				$file->clean();
				return true;
			}
		} else {
			$file->clean();
			echo 'false';
			return false;
		}
	}

	public function deleteFileAction()
	{
		if (!$this->request->isAjax() && !$this->request->isPost())
		{
			echo 'false';
			return false;
		}

		$id = trim(strip_tags($this->dispatcher->getParams()[0]));

		$bdFile = Models\ProductFileModel::findFirst($id);

		if (Models\ProductFileModel::deleteFiles($bdFile->pathname))
		{
			$bdFile->delete();
			echo 'true';
			return true;

		} else {
			echo 'false';
			return false;
		}
	}

	public function settingsAction()
	{
		$this->tag->prependTitle('Настройки');

		$data['curancy_eur'] = Models\Exchange::findFirst(['curancy = \'eur\''])->value;
		$data['curancy_usd'] = Models\Exchange::findFirst(['curancy = \'usd\''])->value;

		if ($this->request->isPost())
		{
			$inputs['curancy_eur'] = $this->request->getPost('curancy_eur', ['trim', 'striptags']);
			$inputs['curancy_usd'] = $this->request->getPost('curancy_usd', ['trim', 'striptags']);

			$validation = new Validation();
			$validation->isNotEmpty([
				'курс евро' => $inputs['curancy_eur'],
				'курс usd' => $inputs['curancy_usd']
			], false);

			$validation->isFloat([
				'курс евро' => $inputs['curancy_eur'],
				'курс usd' => $inputs['curancy_usd']
			], false);

			if ($validation->validate())
			{
				$curancyEur = Models\Exchange::findFirst(['curancy = \'eur\'']);
				$curancyUsd = Models\Exchange::findFirst(['curancy = \'usd\'']);

				$curancyEur->value = $inputs['curancy_eur'];
				$curancyUsd->value = $inputs['curancy_usd'];

				$data['curancy_eur'] = $inputs['curancy_eur'];
				$data['curancy_usd'] = $inputs['curancy_usd'];

				if ($curancyEur->save() && $curancyUsd->save())
				{
					$this->view->errors = ['success' => ['Данные успешно сохранены']];

					// Пересчет всех цен
					foreach (Models\Product::find() as $product)
					{
						$prices = [];
						$priceName = 'price_' . $product->main_curancy;
						$prices = Models\Exchange::setPrices($product->main_curancy, $product->$priceName);

						$product->price_eur = $prices['price_eur'];
						$product->price_usd = $prices['price_usd'];
						$product->price_uah = $prices['price_uah'];

						$product->save();
					}
				} else {
					$errors['error'][] = $curancyEur->getMessages();
					$errors['error'][] = $curancyUsd->getMessages();
					$this->view->errors = $errors;
				}
			} else {
				$this->view->errors = $validation->getMessages();
			}
		}

		$this->view->data = $data;
		echo $this->view->render('admin/settings/index');
	}

	public function uploadFotoCategoryAction()
	{
		if (!$this->request->isAjax()) {
			echo "false";
			return false;
		}

		$categoryId = $this->request->getQuery('catId', ['striptags', 'trim']);
		$image = Models\ImageCategory::uploadFotosAndReturn($categoryId);

		if ($image) {
			$result['id'] = $image->id;
			$result['path'] = $image->imgPath;

			echo json_encode($result);
			return true;
		}
	}

	public function deleteCategoryFotoAction()
	{
		if (!$this->request->isAjax() || !$this->request->isPost()) {
			echo null;
			return false;
		}

		$id = $this->request->getPost('id', ['trim', 'striptags']);
		/** @var Models\ImageCategory $bdFile */
		$bdFile = Models\ImageCategory::findFirst($id);

		if (!preg_match('/\d+/', $id)) {
			echo 'false';
			return false;
		}

		$bdFile->setDI();
		if ($bdFile && $bdFile->isFileExists())
		{
			Models\ImageCategory::deleteFiles($this->url->path('public_html/Uploads/db_images/' . $bdFile->id . '__category.' . $bdFile->extension));
		}

		if ($bdFile->delete()) {
			echo 'true';
			return true;
		} else {
			echo 'false';
			return false;
		}
	}

	public function sortVideoAction()
	{
		if (!$this->request->isAjax() || !$this->request->isPost())
			return $this->response->redirect('admin');

		$ids = json_decode($this->request->getPost('ids'));

		foreach ($ids as $index => $id)
		{
			$video = Models\ProductVideo::findFirst($id);
			if ($video)
			{
				$video->sort = $index;
				$video->save();
			}
		}
	}

	public function addVideoAction()
	{
		if (!$this->request->isAjax() || !$this->request->isPost())
			return $this->response->redirect('admin');

		$name = trim(strip_tags($this->request->getPost('name')));
		$href = trim(strip_tags($this->request->getPost('href')));
		$prodId = trim(strip_tags($this->request->getPost('prodId')));

		$video = new Models\ProductVideo();
		if ($name)
			$video->name = $name;
		else
			$video->name = $href;
		$video->href = $href;
		$video->product_id = $prodId;
		$video->sort = Models\ProductVideo::find([
			'product_id = ?1',
			'bind' => [1 => $prodId]
		])->count();
		if ($video->save())
		{
			$tempVideo = [
				'id' => $video->id,
				'name' => $video->name,
				'href' => $video->href
			];
			echo json_encode($tempVideo);
			return true;
		} else {
			echo 'false';
			return false;
		}
	}

	public function deleteVideoAction()
	{
		if (!$this->request->isAjax() || !$this->request->isPost())
			return $this->response->redirect('admin');

		$id = trim(strip_tags($this->dispatcher->getParams()[0]));
		if ($id && preg_match('/\d+/', $id))
		{
			$video = Models\ProductVideo::findFirst($id);
			if ($video)
			{
				$video->delete();
				echo 'true';
				return true;
			} else {
				echo 'false';
				return false;
			}
		} else {
			echo 'false';
			return false;
		}
	}

	public function editVideoAction()
	{
		if (!$this->request->isPost() || !$this->request->isAjax())
			return $this->response->redirect('admin');

		$id = trim(strip_tags($this->dispatcher->getParams()[0]));
		$name = trim(strip_tags($this->request->getPost('name')));
		$href = trim(strip_tags($this->request->getPost('href')));
		if ($id && preg_match('/\d+/', $id))
		{
			$video = Models\ProductVideo::findFirst($id);
			if (!$video)
			{
				echo 'false';
				return false;
			}
			$video->name = $name;
			$video->href = $href;
			if ($video->save())
			{
				echo 'true';
				return true;
			} else {
				echo 'false';
				return false;
			}
		} else {
			echo 'false';
			return false;
		}
	}

	public function pagesAction()
	{
		$this->tag->prependTitle('Статические страницы');

		$sth = $this->di['pdo']->prepare('SELECT * FROM pages_types');
		$sth->execute();
		$types = $sth->fetchAll();

		$pagesForView = [];
		if (!$types || !count($types)) $pageForView = null;
		$sth2 = $this->di['pdo']->prepare('SELECT * FROM pages WHERE type_id = ? ORDER BY sort, time DESC');

		foreach ($types as $type) {
			$pagesForView[$type['full_name']] = [];
			$sth2->execute([$type['id']]);
			$pages = $sth2->fetchAll();
			if ($pages && count($pages)) {
				foreach ($pages as $page) {
					$pagesForView[$type['full_name']][] = $page;
				}
			}
		}

		$this->view->pages = $pagesForView;

		echo $this->view->render('admin/pages/list');
	}

	public function addPageAction()
	{
		$this->tag->prependTitle('Добавление страницы');

		$pageForView = [];

		if ($this->request->isPost())
		{
			$inputs = [];
			$inputs['name'] = trim(strip_tags($this->request->getPost('name')));
			$inputs['seo_name'] = Translit::get_seo_keyword($inputs['name'], true);
			$inputs['type_id'] = trim(strip_tags($this->request->getPost('type-id')));
			$inputs['expiration'] = trim(strip_tags($this->request->getPost('expiration')));
			$inputs['short_content'] = trim($this->request->getPost('short-content'));
			$inputs['full_content'] = trim($this->request->getPost('full-content'));
			$inputs['video_content'] = trim($this->request->getPost('video-content'));
			$inputs['meta_keywords'] = trim(strip_tags($this->request->getPost('meta-keywords')));
			$inputs['meta_description'] = trim(strip_tags($this->request->getPost('meta-description')));
			$inputs['sort'] = trim(strip_tags($this->request->getPost('sort')));
			$inputs['public'] = trim(strip_tags($this->request->getPost('public')));

			$validation = new Validation();
			$validation->isNotEmpty([
				'Название' => $inputs['name'],
				'Тип' => $inputs['type_id']
			], false);
			$validation->isInRangeString([
				'Название страницы' => $inputs['name'],
				'СЕО название' => $inputs['seo_name']
			], 2, 150, false);
			$validation->isLessThanMaxValueString([
				'Meta keywords' => $inputs['meta_keywords'],
				'Meta description' => $inputs['meta_description']
			], 200, false);
			if (!preg_match('/\d+/', $inputs['type_id']))
				$validation->setMessageManual('Тип', 'Неверно указан тип');
			$samePages = Models\Page::findFirst([
				'seo_name = ?1',
				'bind' => [1 => $inputs['seo_name']]
			]);
			if ($samePages)
				$validation->setMessageManual('СЕО название', 'Такое название уже существует');
			if ($validation->validate())
			{
				$newPage = new Models\Page();
				$newPage->name = $inputs['name'];
				$newPage->seo_name = $inputs['seo_name'];
				$newPage->type_id = $inputs['type_id'];
				if ($inputs['expiration']) {
					$date = date('YmdHis', strtotime($inputs['expiration']));
					if ($date) {
						$newPage->expiration = $date;
					}
				}
				$newPage->short_content = $inputs['short_content'];
				$newPage->full_content = $inputs['full_content'];
				$newPage->video_content = $inputs['video_content'];
				$newPage->meta_keywords = $inputs['meta_keywords'];
				$newPage->meta_description = $inputs['meta_description'];
				$newPage->sort = $inputs['sort'];
				if ($inputs['public'] == 'on') {
					$newPage->public = 1;
				} else {
					$newPage->public = 0;
				}
				$newPage->time = date('YmdHis', time());
				if ($newPage->save()) {
					return $this->response->redirect('admin/editpage/' . $newPage->seo_name);
				} else {
					$this->view->errors = ['БД' => $newPage->getMessages()];
					$pageForView['name'] = $inputs['name'];
					$pageForView['seo_name'] = $inputs['seo_name'];
					$pageForView['types'] =[];
					$types = Models\PageType::find();
					if (count($types))
					{
						foreach ($types as $type)
						{
							$tempType = [];
							$tempType['id'] = $type->id;
							$tempType['name'] = $type->full_name;
							if ($tempType['id'] == $inputs['type_id']) {
								$tempType['active'] = true;
							} else {
								$tempType['active'] = false;
							}
							$pageForView['types'][] = $tempType;
						}
					}
					$pageForView['expiration'] = $inputs['expiration'];
					$pageForView['short_content'] = $inputs['short_content'];
					$pageForView['full_content'] = $inputs['full_content'];
					$pageForView['video_content'] = $inputs['video_content'];
					$pageForView['meta_keywords'] = $inputs['meta_keywords'];
					$pageForView['meta_description'] = $inputs['meta_description'];
					$pageForView['sort'] = $inputs['sort'];
					$pageForView['public'] = $inputs['public'];
				}
			} else {
				$this->view->errors = $validation->getMessages();
				$pageForView['name'] = $inputs['name'];
				$pageForView['seo_name'] = $inputs['seo_name'];
				$pageForView['types'] =[];
				$types = Models\PageType::find();
				if (count($types))
				{
					foreach ($types as $type)
					{
						$tempType = [];
						$tempType['id'] = $type->id;
						$tempType['name'] = $type->full_name;
						if ($tempType['id'] == $inputs['type_id']) {
							$tempType['active'] = true;
						} else {
							$tempType['active'] = false;
						}
						$pageForView['types'][] = $tempType;
					}
				}
				$pageForView['expiration'] = $inputs['expiration'];
				$pageForView['short_content'] = $inputs['short_content'];
				$pageForView['full_content'] = $inputs['full_content'];
				$pageForView['video_content'] = $inputs['video_content'];
				$pageForView['meta_keywords'] = $inputs['meta_keywords'];
				$pageForView['meta_description'] = $inputs['meta_description'];
				$pageForView['sort'] = $inputs['sort'];
				$pageForView['public'] = $inputs['public'];
			}
		}

		if (!$pageForView['types'])
		{
			$types = Models\PageType::find();
			if (count($types))
			{
				foreach ($types as $type)
				{
					$tempType = [];
					$tempType['id'] = $type->id;
					$tempType['name'] = $type->full_name;
					$tempType['active'] = false;
					$pageForView['types'][] = $tempType;
				}
			}
		}

		$this->view->page = $pageForView;

		echo $this->view->render('admin/pages/new');
	}

	public function deletePageAction()
	{
		$seoName = trim(strip_tags($this->dispatcher->getParams()[0]));
		if (!mb_strlen($seoName)) {
			return $this->dispatcher->forward([
				'controller' => 'admin',
				'action' => 'pages'
			]);
		}
		$page = Models\Page::findFirst([
			'seo_name = ?1',
			'bind' => [1 => $seoName]
		]);
		if ($page) {
			$pageImages = Models\PageImageModel::find([
				'page_id = ?1',
				'bind' => [1 => $page->id]
			]);
			if (count($pageImages)) {
				foreach ($pageImages as $image) {
					$imgPath = 'staticPages/images' . $image->id . '__page_list.' . $image->extension;
					if (file_exists($imgPath)) {
						Models\PageImageModel::deleteFiles($imgPath);
					}
					$imgPath = 'staticPages/images/' . $image->id . '__admin_thumb.' . $image->extension;
					if (file_exists($imgPath)) {
						Models\PageImageModel::deleteFiles($imgPath);
					}
					$imgPath = 'staticPages/images/' . $image->id . '__page_description.' . $image->extension;
					if (file_exists($imgPath)) {
						Models\PageImageModel::deleteFiles($imgPath);
					}
				}
			}
			$page->delete();
			return $this->response->redirect('admin/pages');
		}
	}

	public function editPageAction()
	{
		$seoName = trim(strip_tags($this->dispatcher->getParams()[0]));
		/** @var Models\CompanyPage | Models\Project | Models\Video | Models\News | Models\Sale | Models\InfoPage | null $page */
		$pages = Models\Page::query()
			->where('seo_name = ?1')->bind([1 => $seoName])
			->execute()->filter(function(Models\Page $item) {
				$newPage = null;
				switch ($item->type_id) {
					case 1: $newPage = new Models\CompanyPage(); break;
					case 2: $newPage = new Models\Project(); break;
					case 3: $newPage = new Models\Video(); break;
					case 4: $newPage = new Models\News(); break;
					case 5: $newPage = new Models\Sale(); break;
					case 6: $newPage = new Models\InfoPage(); break;
				}
				$newPage->id = $item->id;
				$newPage->name = $item->name;
				$newPage->short_content = $item->short_content;
				$newPage->full_content = $item->full_content;
				$newPage->video_content = $item->video_content;
				$newPage->seo_name = $item->seo_name;
				$newPage->type_id = $item->type_id;
				$newPage->meta_keywords = $item->meta_keywords;
				$newPage->meta_description = $item->meta_description;
				$newPage->public = $item->public;
				$newPage->sort = $item->sort;
				$newPage->time = $item->time;
				$newPage->expiration = $item->expiration;
				$newPage->setImages();
				return $newPage;
			});

		if (!count($pages)) {
			return $this->dispatcher->forward([
				'controller' => 'admin',
				'action' => 'pages'
			]);
		}
		else $page = $pages[0];
		$this->tag->prependTitle('Редактирование страницы');
		$pageForView = [];
		$pageForView['id'] = $page->id;
		$pageForView['name'] = $page->name;
		$pageForView['seo_name'] = $page->seo_name;
		$types = Models\PageType::find();
		if (count($types))
		{
			foreach ($types as $type)
			{
				$tempType = [];
				$tempType['id'] = $type->id;
				$tempType['name'] = $type->full_name;
				if ($tempType['id'] == $page->type_id) {
					$tempType['active'] = true;
				} else {
					$tempType['active'] = false;
				}
				$pageForView['types'][] = $tempType;
			}
		}
		if ($page->expiration) {
			$pageForView['expiration'] = date('Y-m-d', strtotime($page->expiration));
		}

		$pageForView['short_content'] = $page->short_content;
		$pageForView['full_content'] = $page->full_content;
		$pageForView['video_content'] = $page->video_content;
		$pageForView['meta_keywords'] = $page->meta_keywords;
		$pageForView['meta_description'] = $page->meta_description;
		$pageForView['sort'] = $page->sort;
		$pageForView['public'] = ($page->public == 1) ? 'on' : 'off';
		$pageForView['fotos'] = $page->getImages();


		if ($this->request->isPost()) {
			$inputs = [];
			$inputs['name'] = trim(strip_tags($this->request->getPost('name')));
			$inputs['seo_name'] = Translit::get_seo_keyword($inputs['name'], true);
			$inputs['type_id'] = trim(strip_tags($this->request->getPost('type-id')));
			$inputs['expiration'] = trim(strip_tags($this->request->getPost('expiration')));
			$inputs['short_content'] = trim($this->request->getPost('short-content'));
			$inputs['full_content'] = trim($this->request->getPost('full-content'));
			$inputs['video_content'] = trim($this->request->getPost('video-content'));
			$inputs['meta_keywords'] = trim(strip_tags($this->request->getPost('meta-keywords')));
			$inputs['meta_description'] = trim(strip_tags($this->request->getPost('meta-description')));
			$inputs['sort'] = trim(strip_tags($this->request->getPost('sort')));
			$inputs['public'] = trim(strip_tags($this->request->getPost('public')));

			$validation = new Validation();
			$validation->isNotEmpty([
				'Название' => $inputs['name'],
				'Тип' => $inputs['type_id']
			], false);
			$validation->isInRangeString([
				'Название страницы' => $inputs['name'],
				'СЕО название' => $inputs['seo_name']
			], 2, 150, false);
			$validation->isLessThanMaxValueString([
				'Meta keywords' => $inputs['meta_keywords'],
				'Meta description' => $inputs['meta_description']
			], 200, false);
			if (!preg_match('/\d+/', $inputs['type_id'])) {
				$validation->setMessageManual('Тип', 'Неверно указан тип');
			}
			$samePages = Models\Page::findFirst([
				'seo_name = ?1 AND id <> ?2',
				'bind' => [1 => $inputs['seo_name'], 2 => $page->id]
			]);
			if ($samePages)
				$validation->setMessageManual('СЕО название', 'Такое название уже существует');
			if ($validation->validate())
			{
				$page->name = $inputs['name'];
				$page->seo_name = $inputs['seo_name'];
				$page->type_id = $inputs['type_id'];
				if ($inputs['expiration']) {
					$date = strtotime($inputs['expiration']);
					if ($date) {
						$page->expiration = date('YmdHis', $date);
					}
				}
				$page->short_content = $inputs['short_content'];
				$page->full_content = $inputs['full_content'];
				$page->video_content = $inputs['video_content'];
				$page->meta_keywords = $inputs['meta_keywords'];
				$page->meta_description = $inputs['meta_description'];
				$page->sort = $inputs['sort'];
				if ($inputs['public'] == 'on') {
					$page->public = 1;
				} else {
					$page->public = 0;
				}
				if ($page->save()) {
					return $this->response->redirect('admin/pages/');
				} else {
					$this->view->errors = ['БД' => $page->getMessages()];
					$pageForView['id'] = $page->id;
					$pageForView['name'] = $inputs['name'];
					$pageForView['seo_name'] = $inputs['seo_name'];
					$pageForView['types'] =[];
					$types = Models\PageType::find();
					if (count($types))
					{
						foreach ($types as $type)
						{
							$tempType = [];
							$tempType['id'] = $type->id;
							$tempType['name'] = $type->full_name;
							if ($tempType['id'] == $inputs['type_id']) {
								$tempType['active'] = true;
							} else {
								$tempType['active'] = false;
							}
							$pageForView['types'][] = $tempType;
						}
					}
					$pageForView['expiration'] = $inputs['expiration'];
					$pageForView['short_content'] = $inputs['short_content'];
					$pageForView['full_content'] = $inputs['full_content'];
					$pageForView['video_content'] = $inputs['video_content'];
					$pageForView['meta_keywords'] = $inputs['meta_keywords'];
					$pageForView['meta_description'] = $inputs['meta_description'];
					$pageForView['sort'] = $inputs['sort'];
					$pageForView['public'] = $inputs['public'];
				}
			} else {
				$this->view->errors = $validation->getMessages();
				$pageForView['id'] = $page->id;
				$pageForView['name'] = $inputs['name'];
				$pageForView['seo_name'] = $inputs['seo_name'];
				$pageForView['types'] =[];
				$types = Models\PageType::find();
				if (count($types))
				{
					foreach ($types as $type)
					{
						$tempType = [];
						$tempType['id'] = $type->id;
						$tempType['name'] = $type->full_name;
						if ($tempType['id'] == $inputs['type_id']) {
							$tempType['active'] = true;
						} else {
							$tempType['active'] = false;
						}
						$pageForView['types'][] = $tempType;
					}
				}
				$pageForView['expiration'] = $inputs['expiration'];
				$pageForView['short_content'] = $inputs['short_content'];
				$pageForView['full_content'] = $inputs['full_content'];
				$pageForView['video_content'] = $inputs['video_content'];
				$pageForView['meta_keywords'] = $inputs['meta_keywords'];
				$pageForView['meta_description'] = $inputs['meta_description'];
				$pageForView['sort'] = $inputs['sort'];
				$pageForView['public'] = $inputs['public'];
			}
		}

		$this->view->page = $pageForView;

		echo $this->view->render('admin/pages/edit');
	}

	public function uploadStaticPageFotoAction()
	{
		if (!$this->request->isAjax() || !$this->request->isPost()) {
			echo null;
			return false;
		}

		$pageId = trim(strip_tags($this->dispatcher->getParams()[0]));
		/** @var Models\Page | null $page */
		$page = Models\Page::findFirst($pageId);
		if (!$pageId) {
			echo "false";
			return false;
		}

		if ($page->type_id == 1) {
			$image = Models\ImageCompany::uploadImageAndReturn($pageId);

			// Возвращаем тумбу для админки
			if ($image) {
				$result['id'] = $image->id;
				$result['path'] = $image->imgAdminPath;

				echo json_encode($result);
				return true;
			}
			else {
				echo "false";
				return false;
			}
		}
		elseif ($page->type_id == 2) {
			$image = Models\ImageProject::uploadImageAndReturn($pageId);

			// Возвращаем тумбу для админки
			if ($image) {
				$result['id'] = $image->id;
				$result['path'] = $image->imgAdminPath;

				echo json_encode($result);
				return true;
			}
			else {
				echo "false";
				return false;
			}
		}
		elseif ($page->type_id == 3) {
			$image = Models\ImageInfo::uploadImageAndReturn($pageId);

			// Возвращаем тумбу для админки
			if ($image) {
				$result['id'] = $image->id;
				$result['path'] = $image->imgAdminPath;

				echo json_encode($result);
				return true;
			}
			else {
				echo "false";
				return false;
			}
		}
		elseif ($page->type_id == 4) {
			$image = Models\ImageNews::uploadImageAndReturn($pageId);

			// Возвращаем тумбу для админки
			if ($image) {
				$result['id'] = $image->id;
				$result['path'] = $image->imgAdminPath;

				echo json_encode($result);
				return true;
			}
			else {
				echo "false";
				return false;
			}
		}
		elseif ($page->type_id == 5) {
			$image = Models\ImageSale::uploadImageAndReturn($pageId);

			// Возвращаем тумбу для админки
			if ($image) {
				$result['id'] = $image->id;
				$result['path'] = $image->imgAdminPath;

				echo json_encode($result);
				return true;
			}
			else {
				echo "false";
				return false;
			}
		}
		elseif ($page->type_id == 6) {
			$image = Models\ImageInfo::uploadImageAndReturn($pageId);

			// Возвращаем тумбу для админки
			if ($image) {
				$result['id'] = $image->id;
				$result['path'] = $image->imgAdminPath;

				echo json_encode($result);
				return true;
			}
			else {
				echo "false";
				return false;
			}
		}
		else {
			echo "false";
			return false;
		}
	}

	public function deleteStaticPageFotoAction()
	{
		if (!$this->request->isAjax() || !$this->request->isPost()) {
			echo 'false';
			return false;
		}
		$id = $this->request->getPost('id', ['trim', 'striptags']);
		/** @var Models\Image $bdImage */
		$bdImage = Models\Image::findFirst($id);
		if (!$bdImage) {
			echo "false";
			return false;
		}

		$image = null;
		switch ($bdImage->belongs) {
			case 'company': $image = new Models\ImageCompany(); break;
			case 'project': $image = new Models\ImageProject(); break;
			case 'video': $image = new Models\ImageVideo(); break;
			case 'news': $image = new Models\ImageNews(); break;
			case 'sale': $image = new Models\ImageSale(); break;
			case 'info': $image = new Models\ImageInfo(); break;
		}
		$image->id = $bdImage->id;
		$image->extension = $bdImage->extension;

		if ($image->deleteImage()) {
			echo 'true';
			return true;
		}
		else {
			echo 'false';
			return false;
		}
	}

	public function sortStaticPagesFotos()
	{
		if ($this->request->isAjax() && $this->request->isPost())
		{
			$fotoIds = json_decode($this->request->getPost('ids'));
			for ($i = 0; $i < count($fotoIds); $i++)
			{
				$foto = Models\PageImageModel::findFirst($fotoIds[$i]);
				if ($foto)
				{
					$foto->sort = $i;
					$foto->save();
				}
			}
		} else
		{
			return $this->response->redirect('admin');
		}
	}

	public function addSaleToProductAction()
	{
		if (!$this->request->isAjax() || !$this->request->isPost())
			return $this->response->redirect('admin');

		$prodId = $this->request->getPost('prodId', ['trim', 'int']);
		$saleId = $this->request->getPost('saleId', ['trim', 'int']);
		if (!$prodId || !$saleId || !preg_match('/\d+/', $prodId) || !preg_match('/\d+/', $saleId)) {
			echo "false";
			return false;
		}
		$product = Models\Product::findFirst($prodId);
		if (!$product) {
			echo "false";
			return false;
		}
		$prodSale = $product->getSales([
			'[\App\Models\PageModel].id = ' . $saleId
		]);
		if (count($prodSale)) {
			echo "false";
			return false;
		}
		$searchedSale = Models\Page::findFirst($saleId);
		if (!$searchedSale) {
			echo "false";
			return false;
		}
		$product->sales = $searchedSale;
		if ($product->save()) {
			$result = [];
			$result['name'] = $searchedSale->name;
			$result['href'] = $this->url->get('sales/show/') . $searchedSale->seo_name;
			echo json_encode($result);
		} else {
			echo "false";
		}
	}

	public function deleteSaleFromProductAction()
	{
		if (!$this->request->isAjax() || !$this->request->isPost())
			return $this->response->redirect('admin');

		$prodId = $this->request->getPost('prodId', ['trim', 'int']);
		$saleId = $this->request->getPost('saleId', ['trim', 'int']);
		if (!$prodId || !$saleId || !preg_match('/\d+/', $prodId) || !preg_match('/\d+/', $saleId)) {
			echo "false";
			return false;
		}
		$product = Models\Product::findFirst($prodId);
		if (!$product) {
			echo "false";
			return false;
		}
		$productSales = $product->getProductSales('page_id = ' . $saleId);
		if (count($productSales)) {
			if ($productSales[0]->delete()) {
				echo "true";
				return true;
			} else {
				echo "false";
				return false;
			}
		} else {
			echo "false";
			return false;
		}
	}
}