<?

namespace App\Controllers;

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

			$users = \App\Models\Users::find([
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

		$allUsers = \App\Models\Users::find();
		$roles = \App\Models\Roles::find();

		$this->view->setVars([
			'users' => $allUsers,
			'roles' => $roles
		]);

		echo $this->view->render('admin/users/list');
	}

	public function userAction($id)
	{
		$this->tag->prependTitle('Редактирование');
		$user = \App\Models\Users::findById($id);
		$roles = \App\Models\Roles::find();

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
				'roles' => \App\Models\Roles::find()
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
					'roles' => \App\Models\Roles::find(),
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

		$this->view->setVar('mainCategories', \App\Models\Category::getMainCategories());

		echo $this->view->render('admin/categories/categories');
	}

	public function getCategoriesAction($parent = null)
	{
		if($this->request->isAjax() && $parent != null) {
			$categories = \App\Models\Category::getCategories($parent);

			if($categories != null)
				echo json_encode($categories);
			else {
				echo json_encode(null);
			}

		} else {
			$this->response->redirect('admin/categories');
		}
	}

	public function addCategoryAction($parent = 0)
	{
		$this->tag->prependTitle('Редактирование');

		$fullParentCategory = \App\Models\Category::getFullCategoryName($parent);

		$this->view->setVar('fullParentCategory', $fullParentCategory);

		// POST запрос
		if ($this->request->isPost()) {

			$category = new \App\Models\Category();

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

		$category = \App\Models\Category::findById($id);
		$this->view->setVar('category', $category);
		$fullParentCategory = \App\Models\Category::getFullCategoryName($category->parent);
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
			$category = \App\Models\Category::findById($id);
			$children = \App\Models\Category::getCategories($id);
			if($children === null)
				$category->delete();

			return $this->response->redirect('admin/categories');
		}
	}

	public function propertiesAction()
	{
		$this->tag->prependTitle('Параметры товаров');

		$props = \App\Models\ProductProperty::getAllProperties();

		$this->view->setVar('properties', $props);

		echo $this->view->render('admin/properties/index');
	}

	public function addPropertyAction()
	{
		$this->tag->prependTitle('Добавление свойства');

		// POST запрос
		if ($this->request->isPost()) {

			$name = $this->request->getPost('name', ['striptags', 'trim']);

			$validation = new \App\Validation();

			$validation->isEmpty([
				'Свойство' => $name
			]);

			if ($validation->validate()) {
				$property = new \App\Models\ProductProperty();
				$property->name = $name;
				$property->save();

				return $this->response->redirect('admin/properties');
			} else {
				$errors = $validation->getMessages();
				$this->view->setVar('errors', $errors);
			}
		}

		echo $this->view->render('admin/properties/add');
	}

	public function editPropertyAction($id)
	{
		$this->tag->prependTitle('Редактирование свойства');

		$property = \App\Models\ProductProperty::findById($id);
		if(count($property) < 0) {
			$this->response->redirect([
				'controller' => 'admin',
				'action' => 'properties'
			]);
		}

		$this->view->setVar('property', $property);

		// POST запрос
		if ($this->request->isPost()) {

			$name = $this->request->getPost('name', ['striptags', 'trim']);

			$validation = new \App\Validation();

			$validation->isEmpty([
				'Свойство' => $name
			]);

			if ($validation->validate()) {
				$property->name = $name;
				$property->save();

				return $this->response->redirect('admin/properties');
			} else {
				$errors = $validation->getMessages();
				$this->view->setVar('errors', $errors);
			}
		}

		echo $this->view->render('admin/properties/edit');
	}
}