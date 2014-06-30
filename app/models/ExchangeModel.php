<?php
/**
 * Created by Ruslan Koloskov
 * Date: 01.06.14
 * Time: 14:34
 */

namespace App\Models;


class ExchangeModel extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'settings_exchanges';
	}

	public $id;
	public $curancy;
	public $value;

	public static function setPrices($mainCurancy, $price)
	{
		$result = [
			'price_eur' => 0,
			'price_usd' => 0,
			'price_uah' => 0
		];

		if ($price && $price > 0)
		{
			$eur = Exchange::findFirst(1)->value;
			$usd = Exchange::findFirst(2)->value;

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