<?php
/**
 * Created by Ruslan Koloskov
 * Date: 18.06.14
 * Time: 21:57
 */

namespace App\Controllers;
use App,
	App\Models;

class ContactsController extends BaseFrontController
{
	public function initialize()
	{
		parent::initialize();

		$this->tag->appendTitle('Контакты');
		$this->view->active_link = 'contacts';
	}

	public function indexAction()
	{
		$this->view->sidebar_categories = Models\Category::getMainCategories();

		if ($this->request->isPost()) {
			$name = $this->request->getPost('name', ['striptags', 'trim']);
			$email = $this->request->getPost('email', ['striptags', 'trim']);
			$phonePrefix = $this->request->getPost('phone-prefix', ['striptags', 'trim']);
			$phoneBody = $this->request->getPost('phone-body', ['striptags', 'trim']);
			$message = $this->request->getPost('message', ['striptags', 'trim']);

			$validation = new App\Validation();
			$validation->isNotEmpty([
				'Имя' => $name,
				'E-mail' => $email,
				'Сообщение' => $message
			], false);
			$validation->isMoreThanMinValueString([
				'Имя' => $name,
				'Сообщение' => $message
			], 3, false);
			$validation->isEmail([
				'E-mail' => $email
			], false);

			if ($validation->validate()) {
				$this->view->done = true;

				/** @var Models\User | null $manager */
				$manager = Models\User::query()
					->where('role_id = \'3\'')
					->execute()->getFirst();

				if ($manager) {
					require_once __DIR__ . '/../classes/Swift/lib/swift_required.php';

					$transport = \Swift_SmtpTransport::newInstance('hdc01.servercount.net', 465, 'ssl')
						->setUsername('no-reply@tip-topline.com')
						->setPassword('7TWaskME');

					$mailer = \Swift_Mailer::newInstance($transport);

					$body = [];
					$body['email'] = '<h4>E-mail клиента: <a href="' . $email .'">' . $email . '</a></h4>';
					$body['name'] = '<h4>Имя клиента: </h4>' . '<h3>' . $name . '</h3>';
					if ($phoneBody)
						$body['phone'] = '<h4>телефон: </h4><h3>' . $phonePrefix . $phoneBody . '</h3>';
					$body['mesage'] = '<h4>сообщение:</h4><p>' . $message . '</p>';

					$mailMessage = \Swift_Message::newInstance('Заявка клиента');
					$mailMessage->setTo([$manager->email, 'softua6@gmail.com']);
					$mailMessage->setFrom('no-reply@tip-topline.com', 'Топ линия информер');
					$mailMessage->setBody(
						implode('', $body),
						'text/html',
						'UTF-8'
					);

					$mailer->send($mailMessage);
				}
			}
			else {
				$this->view->errors = $validation->getMessages();
			}
		}

		echo $this->view->render('contacts');
	}
} 