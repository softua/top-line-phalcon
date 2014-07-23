<?php
/**
 * Created by Ruslan Koloskov
 * Date: 16.04.14
 * Time: 13:57
 */

namespace App;

class Validation
{
	// [ login => [ message-1, message-2,... ], password => [ message-1,... ] ]
	private $messages = [];

	/***
	 * Проверка на пустые значения переданных полей
	 * @param array $params ['название поля' => 'значение поля']
	 * @param bool $returnResult Нужен результат или нет (true/false)
	 * @return bool Возвращает false, если необходим результат и проверка не пройдена.
	 *
	 * Возвращает true, если результат возвращать не нужно или проверка пройдена
	 */
	public function isNotEmpty($params, $returnResult)
	{
		$flag = true;

		// Если нужно вернуть результат (true/false)
		if ($returnResult)
		{
			foreach ($params as $name => $value)
			{
				if ($value == null)
				{
					$flag = false;
					$this->messages[$name][] = 'Поле "' . $name . '" не может быть пустым';
				}
			}
		} else // Не нужно возвращать результат
		{
			foreach ($params as $name => $value)
			{
				if ($value == null)
					$this->messages[$name][] = 'Поле "' . $name . '" не может быть пустым';
			}
		}

		return $flag;
	}

	/***
	 * Проверка на уникальность пользователя
	 * @param string $login
	 * @param bool $returnResult Нужен результат или нет (true/false)
	 * @return bool Возвращает false, если необходим результат и проверка не пройдена.
	 * Возвращает true, если результат возвращать не нужно или проверка пройдена
	 */
	public function isUniqueUser($login, $returnResult)
	{
		$flag = true;

		$dbUser = Models\User::find([
			'conditions' => ['login' => $login]
		]);

		$dbUser = Models\User::query()
			->where('login = :login:')
			->bind(['login' => $login])
			->execute();

		// Если нужно вернуть результат
		if($returnResult)
		{
			if (count($dbUser) > 0)
			{
				$flag = false;
				$this->messages['login'][] = 'Пользователь с таким логином уже существует!';
			}
		} else // Если результат не нужен
		{
			if (count($dbUser) > 0)
			{
				$this->messages['login'][] = 'Пользователь с таким логином уже существует!';
			}
		}

		return $flag;
	}

	/***
	 * Проверка на минимально допустипую длину строки
	 * @param array $params ['название поля' => 'значение поля']
	 * @param int $minValue минимально допустипое значение поля
	 * @param bool $returnResult Нужен результат или нет (true/false)
	 * @return bool Возвращает false, если необходим результат и проверка не пройдена.
	 *
	 * Возвращает true, если результат возвращать не нужно или проверка пройдена
	 */
	public function isMoreThanMinValueString($params, $minValue, $returnResult)
	{
		$flag = true;

		// Если нужно вернуть результат
		if($returnResult)
		{
			foreach($params as $name => $value)
			{
				if(mb_strlen($value, 'UTF-8') < $minValue)
				{
					$flag = false;
					$this->messages[$name][] = $name . ' не может быть менше ' . $minValue . ' символов';
				}
			}
		} else // Если не нужно возвращать результат
		{
			foreach($params as $name => $value)
			{
				if(mb_strlen($value, 'UTF-8') < $minValue)
				{
					$this->messages[$name][] = $name . ' не может быть менше ' . $minValue . ' символов';
				}
			}
		}

		return $flag;
	}

	/***
	 * Проверка на максимально допустипую длину строки
	 * @param array $params ['название поля' => 'значение поля']
	 * @param int $maxValue максимально допустипое значение поля
	 * @param bool $returnResult Нужен результат или нет (true/false)
	 * @return bool Возвращает false, если необходим результат и проверка не пройдена.
	 *
	 * Возвращает true, если результат возвращать не нужно или проверка пройдена
	 */
	public function isLessThanMaxValueString($params, $maxValue, $returnResult)
	{
		$flag = true;

		// Если нужно вернуть результат
		if($returnResult)
		{
			foreach($params as $name => $value)
			{
				if(mb_strlen($value, 'UTF-8') > $maxValue)
				{
					$flag = false;
					$this->messages[$name][] = $name . ' не может быть больше ' . $maxValue . ' символов';
				}
			}
		} else // Если не нужно возвращать результат
		{
			foreach($params as $name => $value)
			{
				if(mb_strlen($value, 'UTF-8') > $maxValue)
				{
					$this->messages[$name][] = $name . ' не может быть больше ' . $maxValue . ' символов';
				}
			}
		}

		return $flag;
	}

	/***
	 * Проверка, является ли строка float-ом
	 * @param array $params ['название поля' => 'значение поля']
	 * @param bool $returnResult нужен результат или нет (true/false)
	 * @return bool Возвращает false, если необходим результат и проверка не пройдена.
	 *
	 * Возвращает true, если результат возвращать не нужно или проверка пройдена
	 */
	public function isFloat($params, $returnResult)
	{
		$flag = true;
		$pattern = '/[\d+[\.|,]\d{0,2}|\d+]/';

		// Если нужно вернуть результат
		if ($returnResult)
		{
			foreach ($params as $name => $value)
			{
				if (preg_match($pattern, $value) < 1)
				{
					$flag = false;
					$this->messages[$name][] = 'В поле "' . $name . '" нужно указывать десятичное число с 2-мя знаками после запятой. Например: 1234.12';
				}
			}

		} else // Если не нужно возвращать результат
		{
			foreach ($params as $name => $value)
			{
				if (preg_match($pattern, $value) < 1)
				{
					$this->messages[$name][] = 'В поле "' . $name . '" нужно указывать десятичное число с 2-мя знаками после запятой. Например: 1234.12';
				}
			}
		}

		return $flag;
	}

	/***
	 * Проверка на максимальное и минимальное кол-во символов
	 * @param array $params ['название поля' => 'значение поля']
	 * @param int $minValue минимально допустимое кол-во символов
	 * @param int $maxValue максимально допустимое кол-во символов
	 * @param bool $returnResult нужен результат или нет (true/false)
	 * @return bool Возвращает false, если необходим результат и проверка не пройдена.
	 *
	 * Возвращает true, если результат возвращать не нужно или проверка пройдена
	 */
	public function isInRangeString($params, $minValue, $maxValue, $returnResult)
	{
		$flag = true;

		// Если нужно вернуть результат
		if ($returnResult)
		{
			foreach ($params as $name => $value)
			{
				if (mb_strlen($value, 'UTF-8') < $minValue || mb_strlen($value, 'UTF-8') > $maxValue)
				{
					$flag = false;
					$this->messages[$name][] = 'Поле "' . $name . '" должно находиться в диапазоне от ' . $minValue . ' до ' . $maxValue . ' символов';
				}
			}
		} else // Если не нужно возвращать результат
		{
			foreach ($params as $name => $value)
			{
				if (mb_strlen($value, 'UTF-8') < $minValue || mb_strlen($value, 'UTF-8') > $maxValue)
				{
					$this->messages[$name][] = 'Поле "' . $name . '" должно находиться в диапазоне от ' . $minValue . ' до ' . $maxValue . ' символов';
				}
			}
		}

		return $flag;
	}

	public function isEmail($params, $returnResult)
	{
		$flag = true;

		if ($returnResult) // Если нужно вернуть результат (true/false)
		{
			foreach ($params as $name => $value) {
				if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
					$flag = false;
					$this->messages[$name][] = 'Неверно указан E-mail';
				}
			}
		}
		else // Не нужно возвращать результат
		{
			foreach ($params as $name => $value) {
				if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
					$this->messages[$name][] = 'Неверно указан E-mail';
				}
			}
		}

		return $flag;
	}

	/***
	 * @return array Возвращает массив сообщений об ошибках в формате:
	 * [
	 *      название поля => [
	 *          сообщение-1,
	 *          сообщение-2
	 *      ],
	 *      название поля => [
	 *          сообщение-1
	 *      ]
	 * ]
	 */
	public function getMessages()
	{
		return $this->messages;
	}

	/***
	 * Удаляет все сообщения об ошибках
	 */
	public function deleteAllErrors()
	{
		$this->messages = [];
	}

	/***
	 * Удаление ошибок, связанных с указанным полем
	 * @param string $name название поля, ошибки по которому нужно удалить
	 */
	public function deleteMessagesByField($name)
	{
		if (isset($this->messages[$name]))
		{
			unset($this->messages[$name]);
		}
	}

	/**
	 * @return bool Возвращает false в случае наличия ошибок и true при их отсутствии
	 */
	public function validate()
	{
		if (count($this->messages) == 0)
			return true;
		else
			return false;
	}

	/**
	 * Устанавливает сообщение вручную
	 * @param string $field название поля
	 * @param string $message сообщение
	 */
	public function setMessageManual($field, $message)
	{
		$this->messages[$field][] = $message;
	}
}