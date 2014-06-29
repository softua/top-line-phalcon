<?php
/**
 * Created by Ruslan Koloskov
 * Date: 20.06.14
 * Time: 12:16
 */

namespace App\Models;


class PageType extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'pages_types';
	}

	public function initialize()
	{
		$this->hasMany('id', '\App\Models\Page', 'type_id', [
			'alias' => 'pages'
		]);
	}

	public $id;
	public $name;
	public $full_name;
}