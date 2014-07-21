<?php
/**
 * Created by Ruslan Koloskov
 * Date: 03.07.14
 * Time: 15:50
 */

namespace App\Models;


class News extends Page
{
	public $path;

	public function setImages()
	{
		if ($this->_images === null) {
			$this->_images = ImageNews::query()
				->where('belongs = \'news\'')
				->andWhere('belongs_id = ?1', [1 => $this->id])
				->orderBy('sort')
				->execute()->filter(function(ImageNews $item) {
					$item->setPaths();
					return $item;
				});

			if (!$this->_images || !count($this->_images)) {
				$this->_images = false;
				$this->_mainImage = false;
			}
			else {
				$this->_mainImage = $this->_images[0];
			}
		}
	}

	public function getImages()
	{
		if ($this->_images === null) {
			$this->setImages();
			$this->getImages();
		}
		elseif ($this->_images === false) return null;
		else return $this->_images;
	}

	public function getMainImage()
	{
		if ($this->_mainImage === null) {
			$this->setImages();
			$this->getMainImage();
		}
		elseif ($this->_mainImage === false) return false;
		else return $this->_mainImage;
	}

	public static function getProjects()
	{
		/** @var self[] $pages */
		$pages = self::query()
			->where('type_id = \'4\'')
			->andWhere('public = \'1\'')
			->orderBy('sort, name')
			->execute()->filter(function(self $page) {
				$page->time = strtotime($page->time);
				$page->path = \Phalcon\DI::getDefault()['url']->get('page/show/' . $page->seo_name);
				return $page;
			});

		if (count($pages)) return $pages;
		else return null;
	}

	public static function getPageBySeoName($seoName)
	{
		/** @var Project $page */
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