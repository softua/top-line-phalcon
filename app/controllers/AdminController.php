<?

namespace App\Controllers;

use App\Models;
use App\Validation\Validation;

class AdminController extends BaseAdminController
{
	public function beforeExecuteRoute()
	{
		$action = $this->dispatcher->getActionName();

		if (!$this->cookies->has('user-id') && $action != 'login') {
			$this->dispatcher->forward([
				'action' => 'login'
			]);
		} else {
			$this->user = Models\Users::findById(trim($this->cookies->get('user-id')->getValue()));

			$this->view->setVars([
				'name' => $this->config->name,
				'user' => $this->user,
				'url' => $this->request->getURI()
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
					$this->cookies->set('user-id', (string)$users[0]->_id);
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
		$this->session->destroy();

		$this->cookies->set('user-id', null, time() - 60);
		$this->cookies->set('PHPSESSID', null, time() - 60);

		$this->response->redirect('admin');
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

			$validation = new Validation();

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
			$validation = new Validation();

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

		echo $this->view->render('admin/categories');
	}
}