<?

namespace App\Controllers;

use App\Models;
use App\Translit;
use App\Validation;
use App\Upload;

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
				$this->view->errors = ['Вход' => 'Неверный логин или пароль'];
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

				if ($category->save())
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
		if ($id == null)
		{
			return $this->response->redirect('admin/categories');
		}

		$this->tag->prependTitle('Редактирование');

		$category = Models\Category::find($id)[0];
		$this->view->category = $category;
		$fullParentCategory = Models\Category::getFullCategoryName($category->parent_id);
		$this->view->fullParentCategory = $fullParentCategory;

		// POST
		if($this->request->isPost())
		{
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
				$category->seo_name = \App\Translit::get_seo_keyword($name, true);
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

		$tempFotos = Models\CategoryImage::find([
			'category_id = :cat:',
			'bind' => ['cat' => $id]
		]);

		$i = 0;
		$fotos = [];
		foreach ($tempFotos as $foto)
		{
			$fotos[$i]['id'] = $foto->id;
			$fotos[$i]['path'] = '/' . $foto->pathname;
			$i++;
		}

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

		$types = Models\PossibleProductTypes::getAllTypesAsString();
		$countries = Models\Country::getAllTypesAsString();
		$brands = Models\PossibleBrands::getAllTypesAsString();

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

			$validation = new \App\Validation();

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
				Models\PossibleBrands::addBrand($inputs['brand']);
				Models\PossibleProductTypes::addType($inputs['type']);

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
				$product->country_id = Models\Country::findFirst([
					'name = ?1',
					'bind' => [1 => $inputs['country']]
				])->id;
				$product->seo_name = Models\Product::generateSeoName($product);

				$prices = Models\Exchange::setPrices($product->main_curancy, floatval($inputs['price']));
				$product->price_eur = $prices['price_eur'];
				$product->price_usd = $prices['price_usd'];
				$product->price_uah = $prices['price_uah'];

				if ($product->save())
				{
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

			if ($inputs['main_curancy'] == 'eur')
				$inputs['price'] = $product->price_eur;
			elseif ($inputs['main_curancy'] == 'usd')
				$inputs['price'] = $product->price_usd;
			elseif ($inputs['main_curancy'] == 'uah')
				$inputs['price'] = $product->price_uah;

			$productCategoryObjects = Models\ProductCategory::find([
				'product_id = ?1',
				'bind' => [1 => $id]
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

			// Заносим ссылки видео для товара
			$productVideos = Models\ProductVideo::find([
				'product_id = ?1',
				'bind' => [1 => $product->id],
				'order' => 'sort'
			]);
			if (count($productVideos))
			{
				foreach ($productVideos as $video)
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
					if ($inputs['top'] == 'on')
						$product->top = $inputs['top'] = 1;
					else
						$product->top = $inputs['top'] = 0;

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

			$fotos = [];
			$i = 0;
			$tempFotos = Models\ProductImage::find([
				'product_id = ?1',
				'bind' => [1 => $product->id],
				'order' => 'sort'
			]);

			foreach ($tempFotos as $bdFoto)
			{
				$fotos[$i]['id'] = $bdFoto->id;
				$fotos[$i]['path'] = '/products/' . $bdFoto->product_id . '/images/' . $bdFoto->id . '__admin_thumb.' . $bdFoto->extension;
				$i++;
			}

			$this->view->data = $inputs;
			$this->view->id = $id;
			$this->view->categories = json_encode(Models\Category::find()->toArray());
			$this->view->types = Models\PossibleProductTypes::getAllTypesAsString();
			$this->view->countries = Models\Country::getAllTypesAsString();
			$this->view->brands = Models\PossibleBrands::getAllTypesAsString();
			$this->view->parameters = Models\ProductParam::getParamsByProductId($id);
			$this->view->fotos = $fotos;
			$this->view->files = Models\ProductFile::find([
				'product_id = :product:',
				'bind' => ['product' => $product->id]
			]);

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
			echo Models\PossibleParameters::getAllParameters(true);

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
				Models\PossibleParameters::addParameter($paramName); // добавляем название параметра в ощий список
				Models\PossibleParameters::addParameter($paramValue); // добавляем значение параметра в ощий список

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

			$allParams = Models\PossibleParameters::find();

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
					Models\PossibleParameters::addParameter($inputs['name']);
					Models\PossibleParameters::addParameter($inputs['value']);

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
		/*
		 * Возможные варианты картинок:
		 * - 'original' - оригинал картинки;
		 * - 'original_w' - оригинал с водяным знаком;
		 * - 'product_description' - картинка для описания товара (290x300);
		 * - 'product_thumb' - картинка для миниатюры в описании товара (55x47);
		 * - 'product_list' - картинка для списка товаров (155x155);
		 * - 'product_top' - миниатюра для панели "Лидеры продаж" (198x160);
		 * - 'admin_thumb' - картинка для миниатюры в админке (250x150).
		*/

		if (!$this->request->isAjax())
		{
			echo null;
			return false;
		}

		$productId = trim(strip_tags($_GET['prodId']));

		$file = new Upload($_FILES['fotos'], 'ru');

		if (!$file->file_is_image || !preg_match('/\d+/', $productId))
		{
			$file->clean();
			echo 'false';
			return false;
		}

		$sort = Models\ProductImage::find(['product_id = \'' . $productId . '\''])->count();
		$bdFile = new Models\ProductImage();
		$bdFile->product_id = $productId;
		$bdFile->sort = $sort;
		$bdFile->extension = $file->file_src_name_ext;

		// Загружаем оригинальный файл

		if ($bdFile->save())
		{
			$file->file_new_name_body = $bdFile->id . '__original';
			if (!file_exists('products/' . $productId . '/images'))
			{
				mkdir('products/' . $productId . '/images', 0777, true);
			}
			$file->process('products/' . $productId . '/images');
			if (!$file->processed)
			{
				echo 'false';
				$file->clean();
				return false;
			}
		} else {
			echo 'false';
			$file->clean();
			return false;
		}

		// Загружаем оригинальный файл с водяным знаком

		$file->file_new_name_body = $bdFile->id . '__original_w';
		$file->image_watermark = 'img/watermark.png';
		$file->image_watermark_position = 'TL';
		$file->process('products/' . $productId . '/images');
		if (!$file->processed)
		{
			echo 'false';
			$file->clean();
			return false;
		}

		// Загружаем картинку для описания товара

		$file->file_new_name_body = $bdFile->id . '__product_description';
		$file->image_watermark = 'img/watermark.png';
		$file->image_watermark_position = 'TL';
		$file->image_resize = true;
		if ($file->image_src_x >= $file->image_src_y)
		{
			$file->image_x = 290;
			$file->image_ratio_y = true;
		}
		elseif ($file->image_src_x < $file->image_src_y)
		{
			$file->image_ratio_x = true;
			$file->image_y = 300;
		}
		$file->process('products/' . $productId . '/images');
		if (!$file->processed)
		{
			echo 'false';
			$file->clean();
			return false;
		}

		// Загружаем миниатюру для описания товара

		$file->file_new_name_body = $bdFile->id . '__product_thumb';
		$file->image_resize = true;
		if ($file->image_src_x >= $file->image_src_y)
		{
			$file->image_x = 55;
			$file->image_ratio_y = true;
		}
		elseif ($file->image_src_x < $file->image_src_y)
		{
			$file->image_ratio_x = true;
			$file->image_y = 47;
		}
		$file->process('products/' . $productId . '/images');
		if (!$file->processed)
		{
			echo 'false';
			$file->clean();
			return false;
		}

		// Загружаем картинку для списка товаров

		$file->file_new_name_body = $bdFile->id . '__product_list';
		$file->image_resize = true;
		if ($file->image_src_x >= $file->image_src_y)
		{
			$file->image_x = 155;
			$file->image_ratio_y = true;
		}
		elseif ($file->image_src_x < $file->image_src_y)
		{
			$file->image_ratio_x = true;
			$file->image_y = 155;
		}
		$file->process('products/' . $productId . '/images');
		if (!$file->processed)
		{
			echo 'false';
			$file->clean();
			return false;
		}

		// Загружаем картинку для панели "Лидеры продаж"

		$file->file_new_name_body = $bdFile->id . '__product_top';
		$file->image_resize = true;
		if ($file->image_src_x >= $file->image_src_y)
		{
			$file->image_x = 198;
			$file->image_ratio_y = true;
		}
		elseif ($file->image_src_x < $file->image_src_y)
		{
			$file->image_ratio_x = true;
			$file->image_y = 160;
		}
		$file->process('products/' . $productId . '/images');
		if (!$file->processed)
		{
			echo 'false';
			$file->clean();
			return false;
		}

		// Загружаем миниатюру для админки

		$file->file_new_name_body = $bdFile->id . '__admin_thumb';
		$file->image_resize = true;
		if ($file->image_src_x >= $file->image_src_y)
		{
			$file->image_x = 250;
			$file->image_ratio_y = true;
		}
		elseif ($file->image_src_x < $file->image_src_y)
		{
			$file->image_ratio_x = true;
			$file->image_y = 150;
		}
		$file->process('products/' . $productId . '/images');
		if (!$file->processed)
		{
			echo 'false';
			$file->clean();
			return false;
		}

		// Возвращаем тумбу для админки

		$result['id'] = $bdFile->id;
		$result['path'] = '/products/' . $productId . '/images/' . $bdFile->id . '__admin_thumb.' . $bdFile->extension;

		echo json_encode($result);
		$file->clean();
		return true;
	}

	public function sortFotosAction()
	{
		if ($this->request->isAjax() && $this->request->isPost())
		{
			$fotoIds = json_decode($this->request->getPost('ids'));

			for ($i = 0; $i < count($fotoIds); $i++)
			{
				$foto = Models\ProductImage::findFirst($fotoIds[$i]);

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

	public function deleteProductFotoAction()
	{
		if (!$this->request->isAjax() && !$this->request->isPost())
		{
			echo 'false';
			return false;
		}

		$id = $this->request->getPost('id', ['trim', 'striptags']);
		$bdFile = Models\ProductImage::findFirst($id);

		if ($bdFile)
		{
			Models\ProductImage::deleteFiles('products/' . $bdFile->product_id . '/images/' . $bdFile->id . '__original.' . $bdFile->extension);
			Models\ProductImage::deleteFiles('products/' . $bdFile->product_id . '/images/' . $bdFile->id . '__original_w.' . $bdFile->extension);
			Models\ProductImage::deleteFiles('products/' . $bdFile->product_id . '/images/' . $bdFile->id . '__product_description.' . $bdFile->extension);
			Models\ProductImage::deleteFiles('products/' . $bdFile->product_id . '/images/' . $bdFile->id . '__product_thumb.' . $bdFile->extension);
			Models\ProductImage::deleteFiles('products/' . $bdFile->product_id . '/images/' . $bdFile->id . '__admin_thumb.' . $bdFile->extension);
			Models\ProductImage::deleteFiles('products/' . $bdFile->product_id . '/images/' . $bdFile->id . '__product_list.' . $bdFile->extension);
			Models\ProductImage::deleteFiles('products/' . $bdFile->product_id . '/images/' . $bdFile->id . '__product_top.' . $bdFile->extension);

		} else {
			echo 'false';
			return false;
		}

		if ($bdFile->delete())
		{
			echo 'true';
			return true;

		} else {
			echo 'false';
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

		$bdFile = new Models\ProductFile();
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

		$bdFile = Models\ProductFile::findFirst($id);

		if (Models\ProductFile::deleteFiles($bdFile->pathname))
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
		if (!$this->request->isAjax())
		{
			echo null;
			return false;
		}

		$categoryId = trim(strip_tags($_GET['catId']));

		$file = new Upload($_FILES['fotos'], 'ru');

		if (!$file->file_is_image || !preg_match('/\d+/', $categoryId))
		{
			$file->clean();
			echo 'false';
			return false;
		}

		$bdFile = new Models\CategoryImage();
		$bdFile->pathname = '/';
		$bdFile->category_id = $categoryId;

		if ($bdFile->save())
		{
			if (!file_exists('categories'))
			{
				mkdir('categories', 0777, true);
			}

			$file->image_resize = true;
			$file->image_ratio_crop = true;
			if ($file->image_src_x >= $file->image_src_y)
			{
				$file->image_x = 110;
				$file->image_ratio_y = true;
			}
			elseif ($file->image_src_x < $file->image_src_y)
			{
				$file->image_y = 110;
				$file->image_ratio_x = true;
			}
			$file->file_new_name_body = $bdFile->id;
			$file->process('categories');

			if ($file->processed)
			{
				$bdFile->pathname = str_replace('\\', '/', $file->file_dst_pathname);
				$bdFile->save();
				$file->clean();
				$result['id'] = $bdFile->id;
				$result['path'] = '/' . $bdFile->pathname;

				echo json_encode($result);
				return true;
			}
		} else {
			$file->clean();
			echo 'false';
			return false;
		}

	}

	public function deleteCategoryFotoAction()
	{
		if (!$this->request->isAjax() || !$this->request->isPost())
		{
			echo null;
			return false;
		}

		$id = $this->request->getPost('id', ['trim', 'striptags']);
		$bdFile = Models\CategoryImage::findFirst($id);

		if (!preg_match('/\d+/', $id))
		{
			echo 'false';
			return false;
		}

		if ($bdFile && file_exists($bdFile->pathname))
		{
			Models\CategoryImage::deleteFiles($bdFile->pathname);
		}

		if ($bdFile->delete())
		{
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

		$pagesCompanyForView = Models\Page::getAllPagesByType(1);
		$pagesProjectForView = Models\Page::getAllPagesByType(2);

		if ($pagesCompanyForView)
			$this->view->company_pages = $pagesCompanyForView;
		if ($pagesProjectForView)
			$this->view->project_pages = $pagesProjectForView;

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
			$inputs['short_content'] = trim($this->request->getPost('short-content'));
			$inputs['full_content'] = trim($this->request->getPost('full-content'));
			$inputs['meta_keywords'] = trim(strip_tags($this->request->getPost('meta-keywords')));
			$inputs['meta_description'] = trim(strip_tags($this->request->getPost('meta-description')));
			$inputs['public'] = trim(strip_tags($this->request->getPost('public')));

			$validation = new Validation();
			$validation->isNotEmpty([
				'Название' => $inputs['name'],
				'Тип' => $inputs['type_id']
			], false);
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
				$newPage->short_content = $inputs['short_content'];
				$newPage->full_content = $inputs['full_content'];
				$newPage->meta_keywords = $inputs['meta_keywords'];
				$newPage->meta_description = $inputs['meta_description'];
				if ($inputs['public'] == 'on') {
					$newPage->public = 1;
				} else {
					$newPage->public = 0;
				}
				if ($newPage->save()) {
					return $this->response->redirect('admin/pages');
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
					$pageForView['short_content'] = $inputs['short_content'];
					$pageForView['full_content'] = $inputs['full_content'];
					$pageForView['meta_keywords'] = $inputs['meta_keywords'];
					$pageForView['meta_description'] = $inputs['meta_description'];
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
				$pageForView['short_content'] = $inputs['short_content'];
				$pageForView['full_content'] = $inputs['full_content'];
				$pageForView['meta_keywords'] = $inputs['meta_keywords'];
				$pageForView['meta_description'] = $inputs['meta_description'];
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

	}

	public function editPageAction()
	{

	}
}