<?php
namespace ThomasPaul\Shopware6Api\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use ThomasPaul\Shopware6Api\Domain\Repository\ProductRepository;
use ThomasPaul\Shopware6Api\Domain\Model\Product;
use Psr\Http\Message\ResponseInterface;

class ProductController extends ActionController
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function listAction(): ResponseInterface
    {
        $products = $this->productRepository->findAll();
        $this->view->assign('products', $products);
        
        return $this->htmlResponse();
    }

    public function showAction(Product $product): ResponseInterface
    {
        $this->view->assign('product', $product);
        
        return $this->htmlResponse();
    }
} 