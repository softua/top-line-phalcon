<?php
/**
 * Created by Ruslan Koloskov
 * Date: 26.05.14
 * Time: 10:37
 */

namespace App\Models;


class ProductParameter extends \Phalcon\Mvc\Collection
{
	public function getSource()
	{
		return 'product_parameters';
	}

	/**
	 * Получение всех параметров
	 * @param bool $needJson определяет нужно ли отдавать JSON
	 * @return array|null|string Возвращает массив параметров, если они есть, иначе null. Если $needJson = true, вернет JSON
	 */
	public static function getAllParameters($needJson = false)
	{
		$paramsObjs = ProductParameter::find([
			'sort' => ['name' => 1]
		]);

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
		$params = ProductParameter::find();
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
			$parameter = new ProductParameter();
			$parameter->name = $paramName;
			$parameter->save();
		}
	}
} 