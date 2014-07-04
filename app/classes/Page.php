<?php
/**
 * Created by Ruslan Koloskov
 * Date: 03.07.14
 * Time: 15:50
 */

namespace App;


class Page
{
	protected $_di;
	protected $_url;

	public $id;
	public $name;
	public $seoName;
	public $typeId;
	public $metaKeywords;
	public $metaDescription;
	public $public;
	public $time;
	protected  $_images = [];
	protected $_mainImage;

	protected function _setImages()
	{
		$images = Models\PageImageModel::query()
			->where('page_id = ?1')->bind([1 => $this->id])
			->orderBy('sort')
			->execute();

		if (count($images)) {
			foreach ($images as $item) {
				$image = new PageImage();
				$image->setDi($this->_di);
				$image->id = $item->id;
				$image->extension = $item->extension;
				$image->pageId = $item->page_id;
				$image->sort = $item->sort;
				$image->setPath();
				$this->_images[] = $image;
			}
			$this->_mainImage = $this->_images[0];
		} else {
			$this->_images = false;
		}
	}

	public function setDi($di)
	{
		$this->_di = $di;
		$this->_url = $this->_di->get('url');
	}

	public function setImages()
	{
		if (is_array($this->_images) && empty($this->_images)) {
			$this->_setImages();
		}
	}

	public function getImages()
	{
		if (is_array($this->_images) && empty($this->_images)) {
			$this->_setImages();
			if ($this->_images === false) {
				return false;
			} else {
				return $this->_images;
			}
		} else {
			return $this->_images;
		}
	}

	public function getMainImage()
	{
		if ($this->_mainImage) {
			return $this->_mainImage;
		} else {
			return false;
		}
	}

	public static function getPageBySeoName($seoName)
	{
		if (!trim(strip_tags($seoName))) {
			return false;
		}

		$page = Models\PageModel::query()
			->where('seo_name = ?1')->bind([1 => $seoName])
			->execute()
			->getFirst();

		if ($page) {
			return $page;
		} else {
			return false;
		}
	}
}