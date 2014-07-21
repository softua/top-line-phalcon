<?
/**
 * Created by Ruslan Koloskov
 * Date: 11.04.14
 * Time: 13:17
 */

namespace App\Controllers;
use App,
	App\Models;
class BaseFrontController extends \Phalcon\Mvc\Controller
{
	public function initialize()
	{
		$this->tag->setTitle($this->di->get('config')->name);
		$this->tag->setTitleSeparator(' :: ');

		$this->view->top_products = $this->getTopProducts();
		$this->view->static_pages = $this->getSubmenuWithCompanyPages();
		$this->view->infoPages = Models\InfoPage::getInfoPages($this->di);
	}

	public function getTopProducts()
	{
		$topProducts = Models\Product::query()
			->where('top = \'1\'')
			->andWhere('public = \'1\'')
			->limit(5)
			->execute()->filter(function(Models\Product $product) {
				$product->setImages();
				$product->setPath();
				return $product;
			});
		return $topProducts;
	}

	public function getSubmenuWithCompanyPages()
	{
		$staticCompanyPages = Models\Page::find([
			'type_id = 1 AND public = 1',
			'order' => 'name'
		]);
		if (count($staticCompanyPages)) {
			$pages = [];
			foreach ($staticCompanyPages as $page) {
				$tempPage = [];
				$tempPage['name'] = $page->name;
				$tempPage['href'] = '/company/show/' . $page->seo_name;
				$pages[] = $tempPage;
			}
			return $pages;
		} else {
			return null;
		}
	}
}