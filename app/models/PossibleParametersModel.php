<?php
/**
 * Created by Ruslan Koloskov
 * Date: 26.05.14
 * Time: 10:37
 */

namespace App\Models;


class PossibleParametersModel extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'possible_params';
	}

	/**
	 * Получение всех параметров
	 * @param bool $needJson определяет нужно ли отдавать JSON
	 * @return array|null|string Возвращает массив параметров, если они есть, иначе null. Если $needJson = true, вернет JSON
	 */
	public static function getAllParameters($needJson = false)
	{
		$paramsObjs = self::query()
			->order('name')
			->execute();

		if ($needJson)
		{
			$names = [];

			foreach ($paramsObjs as $obj)
			{
				$names[] = $obj->name;
			}

			if(count($names) > 0) echo json_encode($names);
			else echo json_encode(null);

		} else {

			if(count($paramsObjs) > 0) return $paramsObjs;
			else return null;
		}
	}

	public static function addParameter($paramName)
	{
		if ($paramName)
		{
			$params = self::findFirst([
				'name = :name:',
				'bind' => ['name' => $paramName]
			]);

			if (!$params)
			{
				$parameter = new self();
				$parameter->name = $paramName;
				$parameter->save();
			}
		}
	}
}