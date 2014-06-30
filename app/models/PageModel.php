<?php
/**
 * Created by Ruslan Koloskov
 * Date: 20.06.14
 * Time: 11:10
 */

namespace App\Models;

class PageModel extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'pages';
	}

	public function initialize()
	{
		$this->hasManyToMany('id', '\App\Models\ProductSale', 'page_id', 'product_id', '\App\Models\Product', 'id', [
			'alias' => 'products'
		]);
		$this->belongsTo('type_id', '\App\Models\PageType', 'id', [
			'alias' => 'type'
		]);
		$this->hasMany('id', '\App\Models\PageImage', 'page_id', [
			'alias' => 'images'
		]);
	}

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
	public $time;
	public $expiration;

	public static function getAllPagesByType($typeId)
	{
		if (!$typeId || !preg_match('/\d+/', $typeId))
			return false;

		$pages = self::find([
			'type_id = ?1',
			'bind' => [1 => $typeId]
		]);
		if (count($pages))
		{
			$pagesArray = [];
			foreach ($pages as $page)
			{
				$tempPage = [];
				$tempPage['name'] = $page->name;
				$tempPage['seo_name'] = $page->seo_name;
				$tempPage['type'] = Models\PageType::findFirst($page->type_id)->full_name;
				$tempPage['public'] = $page->public;
				$pagesArray[] = $tempPage;
			}
			return $pagesArray;
		} else {
			return false;
		}
	}
}