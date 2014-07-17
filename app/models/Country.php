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

	//DB properties
	public $id;
	public $name;

	public $dbFields = [
		'id', 'name'
	];

	public function dbSave($data = null)
	{
		if ($this->save($data, $this->dbFields))
			return true;
		else
			return false;
	}

	public static function getAllTypes()
	{
		$countries = self::find();

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
			$countries = self::query()
				->where('name = :country:')
				->bind(['country' => $country])
				->execute();

			if (count($countries) < 1)
			{
				$newCountry = new self();
				$newCountry->name = $country;
				$newCountry->dbSave();
			}
		}
	}
}