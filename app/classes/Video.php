<?php
/**
 * Created by Ruslan Koloskov
 * Date: 05.07.14
 * Time: 17:40
 */

namespace App;


class Video
{
	protected $_di;
	protected $_url;

	public $id;
	public $name;
	public $href;
	public $sort;
	public $productId;

	public function setDi($di)
	{
		$this->_di = $di;
		$this->_url = $this->_di->get('url');
	}

	public static function getVideosByProduct($di, Product $prod)
	{
		$videos = Models\ProductVideoModel::query()
			->where('product_id = ?1')->bind([1 => $prod->id])
			->orderBy('sort')
			->execute();

		if (!count($videos)) return null;

		$videoArray = [];
		foreach ($videos as $video) {
			$newVideo = new self();
			$newVideo->setDi($di);
			$newVideo->id = $video->id;
			$newVideo->name = $video->name;
			$newVideo->href = $video->href;
			$newVideo->sort = $video->sort;
			$newVideo->productId = $video->product_id;
			$videoArray[] = $newVideo;
		}

		if (count($videoArray)) return $videoArray;
		else return null;
	}
}