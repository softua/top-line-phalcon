<?php
/**
 * Created by Ruslan Koloskov
 * Date: 03.07.14
 * Time: 15:50
 */

namespace App\Models;


class InfoPage extends Page
{
	public $path;

	public function setImages()
	{
		if ($this->_images === null) {
			$this->_images = ImageCompany::query()
				->where('belongs = \'project\'')
				->andWhere('belongs_id = ?1', [1 => $this->id])
				->orderBy('sort')
				->execute()->filter(function(ImageCompany $item) {
					$item->setPaths();
					return $item;
				});

			if (!$this->_images || !count($this->_images)) $this->_images = false;
		}
	}

	public function getImages()
	{
		if ($this->_images === null) {
			$this->setImages();
			if ($this->_images === null) return null;
			elseif ($this->_images === false) return null;
			else return $this->_images;
		}
		elseif ($this->_images === false) return null;
		else return $this->_images;
	}

	public static function getInfoPages()
	{
		/** @var InfoPage[] $pages */
		$pages = self::query()
			->where('type_id = 6')
			->andWhere('public = 1')
			->orderBy('sort, name')
			->execute()->filter(function(InfoPage $page) {
				$page->time = strtotime($page->time);
				$page->path = \Phalcon\DI::getDefault()['url']->get('page/show/' . $page->seo_name);
				return $page;
			});

		if (count($pages)) return $pages;
		else return null;
	}

	public static function getPageBySeoName($seoName)
	{
		/** @var InfoPage $page */
		$page = self::query()
			->where('seo_name = ?1')->bind([1 => $seoName])
			->execute()
			->getFirst();

		if (!$page) return null;

		$page->time = strtotime($page->time);
		$page->path = \Phalcon\DI::getDefault()['url']->get('page/show/' . $page->seo_name);

		return $page;
	}
}