<?php
/**
 * Created by Ruslan Koloskov
 * Date: 01.06.14
 * Time: 14:34
 */

namespace App\Models;


class Exchange extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'settings_exchanges';
	}

	//DB properties
	public $id;
	public $curancy;
	public $value;

	public $dbFields = [
		'id', 'curancy', 'value'
	];

	public function dbSave($data = null)
	{
		if ($this->save($data, $this->dbFields)) return true;
		else return false;
	}

	public static function setPrices($mainCurancy, $price)
	{
		$result = [
			'price_eur' => 0,
			'price_usd' => 0,
			'price_uah' => 0
		];

		if ($price && $price > 0)
		{
			$eur = self::findFirst(1)->value;
			$usd = self::findFirst(2)->value;

			if ($mainCurancy == 'eur')
			{
				$result['price_eur'] = $price;
				$result['price_usd'] = $price * $eur / $usd;
				$result['price_uah'] = $price * $eur;

			} elseif ($mainCurancy == 'usd')
			{
				$result['price_eur'] = $price * $usd / $eur;
				$result['price_usd'] = $price;
				$result['price_uah'] = $price * $usd;

			} elseif ($mainCurancy == 'uah')
			{
				$result['price_eur'] = floatval($price / $eur);
				$result['price_usd'] = floatval($price / $usd);
				$result['price_uah'] = floatval($price);
			}

		}
		return $result;
	}
}