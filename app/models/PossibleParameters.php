<?php
/**
 * Created by Ruslan Koloskov
 * Date: 26.05.14
 * Time: 10:37
 */

namespace App\Models;


class PossibleParameters extends \Phalcon\Mvc\Model
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
		$paramsObjs = PossibleParameters::query()
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
		} else
		{
			if(count($paramsObjs) > 0) return $paramsObjs;
			else return null;
		}
	}

	public static function addParameter($paramName)
	{
		$params = PossibleParameters::find();
		$result = 0;

		foreach ($params as $param)
		{
			if ($param->name == $paramName)
			{
				$result++;
				break;
			}
		}

		if ($result == 0)
		{
			$parameter = new PossibleParameters();
			$parameter->name = $paramName;
			$parameter->save();
		}
	}
}