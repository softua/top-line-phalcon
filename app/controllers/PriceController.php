<?php
/**
 * Created by Ruslan Koloskov
 * Date: 22.07.14
 * Time: 21:46
 */

namespace App\Controllers;
use App,
	App\Models;

class PriceController extends \Phalcon\Mvc\Controller
{
	public function sendEmail(Models\Client $client)
	{
		require_once __DIR__ . '/../classes/Swift/lib/swift_required.php';

		/** @var Models\User | null $manager */
		$manager = Models\User::query()
			->where('role_id = \'3\'')
			->execute()->getFirst();
		if ($manager && $manager->email) {
			$data = '<h2>' . date('d.m.Y H:i:s') . ' скачан прайс-лист.' . '</h2>' .
				'<p>' . 'Для связи с клиентом используйте e-mail: ' . '<a href="mailto:' . $client->email . '">' . $client->email . '</a>' . '</p>';
			if ($client->phone) $data .= '<h3>' . 'Также указан телефон: ' . $client->phone . '</h3>';

			$transport = \Swift_SmtpTransport::newInstance('hdc01.servercount.net', 465, 'ssl')
				->setUsername('no-reply@tip-topline.com')
				->setPassword('7TWaskME');

			$mailer = \Swift_Mailer::newInstance($transport);

			$message = \Swift_Message::newInstance('Клиент скачал прайс-лист');
			$message->setTo([$manager->email, 'softua6@gmail.com']);
			$message->setFrom('no-reply@tip-topline.com', 'Топ линия');
			$message->setBody($data);
			$message->setContentType('text/html');
			$message->setCharset('UTF-8');

			$mailer->send($message);
		}
	}

	public function getPriceAction()
	{
		if (!$this->request->isAjax() || !$this->request->isPost()) return null;

		$email = $this->request->getPost('email', ['striptags', 'trim']);
		$phone = $this->request->getPost('phone', ['striptags', 'trim']);

		$validation = new App\Validation();
		$validation->isEmail([
			'E-mail' => $email
		], false);

		if (!$email) {
			echo json_encode([
				'errors' => ['Обязательно укажите E-mail']
			]);
			return false;
		}
		elseif (!$validation->validate()) {
			echo json_encode([
				'errors' => $validation->getMessages()['E-mail']
			]);
			return false;
		}

		/** @var Models\Price | null $price */
		$price = Models\Price::query()
			->execute()->getLast();

		$client = Models\Client::query()
			->where('email = ?1')->bind([1 => $email])
			->execute()->getFirst();
		if ($client) {
			if ($price) {
				echo json_encode(['price' => $this->url->getStatic($price->pathname)]);
				$this->sendEmail($client);
				return true;
			}
			else {
				return false;
			}
		}
		else {
			$client = new Models\Client();
			$client->email = $email;
			$client->phone = $phone;
			$client->dbSave();
			if ($price) {
				echo json_encode(['price' => $this->url->getStatic($price->pathname)]);
				$this->sendEmail($client);
				return true;
			}
			else {
				return false;
			}
		}
	}
}