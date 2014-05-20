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

	public function userAction($id)
	{
		$this->tag->prependTitle('Редактирование');
		$user = Models\Users::findById($id);
		$roles = Models\Roles::find();

		// POST запрос
		if ($this->request->isPost()) {

			$name = $this->request->getPost('name', ['striptags', 'trim']);
			$email = $this->request->getPost('email', ['email', 'trim']);
			$role = $this->request->getPost('role', ['alphanum', 'trim']);

			$validation = new \App\Validation();

			$validation->isEmpty([
				'Имя' => $name,
				'Права' => $role
			]);

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

			$validation->isEmpty([
				'login' => $login,
				'password' => $password,
				'name' => $name,
				'role' => $role
			]);

			$validation->isUniqueUser($login);

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
		$id = $this->dispatcher->getParam('id');

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

	public function getCategoriesAction($parent = null)
	{
		if($this->request->isAjax() && $parent != null) {
			$categories = Models\Category::getCategories($parent);

			if($categories != null)
				echo json_encode($categories);
			else {
				echo json_encode(null);
			}

		} else {
			$this->response->redirect('admin/categories');
		}
	}

	public function addCategoryAction($parent)
	{
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

			$validation->isEmpty([
				'Категория' => $name,
				'Порядок' => $sort
			]);

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

	public function editCategoryAction($id = null)
	{
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
			$validation->isEmpty([
				'Категория' => $name,
				'Порядок' => $sort
			]);
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

	public function deleteCategoryAction($id = null)
	{
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

		$this->view->setVars([
			'mainCategories' => $mainCats,
			'products' => $products
		]);

		echo $this->view->render('admin/products/index');
	}

	public function getProductsAction($categoryId = null)
	{
		if($this->request->isAjax()) {
			echo $categoryId;
		} else {
			$this->response->redirect('admin/notfound');
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

				$product->$priceName = $price;
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

				$product->save();

				// Если SEO-название уникально, то перенаправляем на дальнейшее редактирование
				if(Models\Product::isUniqueSeoName($product->seo_name))
				{
					return $this->response->redirect('admin/editproduct/' . $product->_id);

				} else // Иначе идем на страницу для редактирования SEO-названия
				{
					return $this->response->redirect('admin/editseoname/' . $product->_id);
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

	public function editProductAction($id = null)
	{
		$this->tag->prependTitle('Редактирование');

		// Если ID есть,...
		if ($id && strlen($id) == 24)
		{


		} else // иначе отправляемся ...
		{
			return $this->response->redirect('admin');
		}

		echo $this->view->render('admin/products/edit');
	}

	public function editSeoNameAction($id = null)
	{
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
}