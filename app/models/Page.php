<?php
/**
 * Created by Ruslan Koloskov
 * Date: 20.06.14
 * Time: 11:10
 */

namespace App\Models;

class Page extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'pages';
	}

	public function onConstruct()
	{
		$this->setDI();
	}

	public function initialize()
	{
		$this->hasManyToMany('id', '\App\Models\ProductSaleModel', 'page_id', 'product_id', '\App\Models\ProductModel', 'id', [
			'alias' => 'products'
		]);
		$this->belongsTo('type_id', '\App\Models\PageTypeModel', 'id', [
			'alias' => 'type'
		]);
		$this->hasMany('id', '\App\Models\PageImageModel', 'page_id', [
			'alias' => 'images'
		]);

		$this->useDynamicUpdate(true);
	}

	protected $_di;
	protected $_url;

	//Db properties
	public $id;
	public $name;
	public $short_content;
	public $full_content;
	public $video_content;
	public $seo_name;
	public $type_id;
	public $meta_keywords;
	public $meta_description;
	public $public;
	public $sort;
	public $time;
	public $expiration;

	public $dbFields = [
		'id', 'name', 'short_content', 'full_content', 'video_content', 'seo_name', 'type_id', 'meta_keywords', 'meta_description', 'public', 'sort', 'time', 'expiration'
	];

	protected  $_images;
	protected $_mainImage;

	public function setDI($di = null)
	{
		$this->_di = \Phalcon\DI::getDefault();
		$this->_url = $this->_di['url'];
	}

	public function dbSave($data = null)
	{
		if ($this->save($data, $this->dbFields)) return true;
		else return false;
	}

	public static function getPageBySeoName($seoName)
	{
		if (!trim(strip_tags($seoName))) return false;

		/** @var Page $page */
		$page = self::query()
			->where('seo_name = ?1')->bind([1 => $seoName])
			->execute()
			->getFirst();

		if ($page) return $page;
		else return false;
	}

	public static function getAllPagesByType($typeId)
	{
		if (!$typeId || !preg_match('/\d+/', $typeId)) return null;

		$pages = self::query()
			->where('type_id = ?1')->bind([1 => $typeId])
			->execute()->filter(function($page) {
				$page->type = PageType::findFirst($page->type_id)->full_name;
				return $page;
			});
		if (count($pages)) return $pages;
		else return null;
	}
}