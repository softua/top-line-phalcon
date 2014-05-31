<?php
/**
 * Created by Ruslan Koloskov
 * Date: 07.05.14
 * Time: 13:40
 */

namespace App\Models;

class Country extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'countries';
	}

	public static function getAllTypes()
	{
		$countries = Country::find();

		if(count($countries) > 0) return $countries;
		else return null;
	}

	public static  function getAllTypesAsString()
	{
		$countries = self::getAllTypes();

		$result = [];

		if($countries !== null) {
			foreach($countries as $country) {
				$result[] = $country->name;
			}

			return json_encode($result);
		} else
			return null;
	}

	public static function addCountry($country)
	{
		if ($country)
		{
			$countries = Country::query()
				->where('name = :country:')
				->bind(['country' => $country])
				->execute();

			if (count($countries) < 1)
			{
				$newCountry = new Country();
				$newCountry->name = $country;
				$newCountry->save();
			}
		}
	}
}