<?php
namespace ThomasPaul\Shopware6Api\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use ThomasPaul\Shopware6Api\Domain\Repository\ProductRepository;
use ThomasPaul\Shopware6Api\Domain\Model\Product;

class ProductController extends ActionController
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function listAction()
    {
        $products = $this->productRepository->findAll();
        $this->view->assign('products', $products);
    }

    public function showAction(Product $product)
    {
        $this->view->assign('product', $product);
    }
} 