<?php
/**
 * Created by Ruslan Koloskov
 * Date: 16.04.14
 * Time: 13:57
 */

namespace App\Validation;

use App\Models;

class Validation
{
	// [ login => [ message-1, message-2,... ], password => [ message-1,... ] ]
	private $messages = [];

	/**
	 * @param array $params
	 */
	public function isEmpty($params)
	{
		foreach ($params as $name => $value) {
			if ($value == null)
				$this->messages[$name][] = 'Поле "' . $name . '" не может быть пустым';
		}
	}

	/**
	 * @param string $login
	 */
	public function isUniqueUser($login)
	{
		$dbUser = Models\Users::find([
			'conditions' => ['login' => $login]
		]);

		if (count($dbUser) > 0) {
			$this->messages['login'][] = 'Пользователь с таким логином уже существует!';
		}
	}

	public function getMessages()
	{
		return $this->messages;
	}

	/**
	 * @return bool [true/false]
	 */
	public function validate()
	{
		if (count($this->messages) == 0)
			return true;
		else
			return false;
	}
}