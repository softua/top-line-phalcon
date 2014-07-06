<?php
/**
 * Created by Ruslan Koloskov
 * Date: 03.07.14
 * Time: 15:50
 */

namespace App;


class InfoPage extends Page
{
	public $fullContent;
	public $shortContent;
	public $sort;
	public $path;

	public static function getInfoPages($di)
	{
		/** @var Models\PageModel[] $pages */
		$pages = Models\PageModel::query()
			->where('type_id = 6')
			->andWhere('public = 1')
			->orderBy('sort, name')
			->execute();

		if (!count($pages)) return null;
		$infoPages = [];
		foreach ($pages as $page) {
			$infoPage = new self();
			$infoPage->setDi($di);
			$infoPage->id = $page->id;
			$infoPage->name = $page->name;
			$infoPage->seoName = $page->seo_name;
			$infoPage->typeId = $page->type_id;
			$infoPage->shortContent = $page->short_content;
			$infoPage->fullContent = $page->full_content;
			$infoPage->metaKeywords = $page->meta_keywords;
			$infoPage->metaDescription = $page->meta_description;
			$infoPage->public = $page->public;
			$infoPage->time = strtotime($page->time);
			$infoPage->sort = $page->sort;
			$infoPage->path = $infoPage->_url->get('page/show/' . $infoPage->seoName);
			$infoPages[] = $infoPage;
		}

		if (count($infoPages)) return $infoPages;
		else return null;
	}

	public static function getPageBySeoName($di, $seoName)
	{
		/** @var Models\PageModel $page */
		$page = parent::getPageBySeoName($seoName);
		if (!$page) return null;

		$infoPage = new self();
		$infoPage->setDi($di);
		$infoPage->id = $page->id;
		$infoPage->name = $page->name;
		$infoPage->seoName = $page->seo_name;
		$infoPage->typeId = $page->type_id;
		$infoPage->shortContent = $page->short_content;
		$infoPage->fullContent = $page->full_content;
		$infoPage->metaKeywords = $page->meta_keywords;
		$infoPage->metaDescription = $page->meta_description;
		$infoPage->public = $page->public;
		$infoPage->time = strtotime($page->time);
		$infoPage->sort = $page->sort;
		$infoPage->path = $infoPage->_url->get('page/show/' . $infoPage->seoName);
		$infoPage->setImages();

		return $infoPage;
	}
}