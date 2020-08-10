<?php

namespace App\Service;

use App\Dto\ProductInput;
use App\Entity\Product;
use App\ReadModel\ListingResult;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Product CRUD manager.
 */
class ProductCrudManager extends AbstractCrudManger
{
    /**
     * Default items per page.
     *
     * @var int
     */
    private const DEFAULT_ITEMS_PER_PAGE = 3;

    /**
     * Product repository.
     *
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @param ProductRepository $productRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        ProductRepository $productRepository
    ) {
        parent::__construct($entityManager, $logger);

        $this->productRepository = $productRepository;
    }

    /**
     * Get list of products.
     *
     * @param int $page
     * @param int $itemsPerPage
     *
     * @return ListingResult
     */
    public function getProducts(int $page = 1, int $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE): ListingResult
    {
        $productsPaginator = $this->productRepository->getProducts($page, $itemsPerPage);

        $result = new ListingResult([], $productsPaginator->count(), $itemsPerPage);

        foreach ($productsPaginator as $product) {
            $result->addItem($product);
        }

        return $result;
    }

    /**
     * Create product from input.
     *
     * @param ProductInput $productInput
     *
     * @return Product
     */
    public function createFromDto(ProductInput $productInput): Product
    {
        $product = Product::createFromDto($productInput);

        $this->persistEntity($product);

        return $product;
    }

    /**
     * Update product from input.
     *
     * @param Product $product
     * @param ProductInput $productInput
     *
     * @return Product
     */
    public function updateFromDto(Product $product, ProductInput $productInput): Product
    {
        if ($productInput->getName() !== null) {
            $product->setName($productInput->getName());
        }

        if ($productInput->getPrice() !== null) {
            $product->setPrice($productInput->getPrice());
        }

        if ($productInput->getCurrencyCode() !== null) {
            $product->setCurrencyCode($productInput->getCurrencyCode());
        }

        $this->persistEntity($product);

        return $product;
    }

    /**
     * Delete product.
     *
     * @param Product $product
     *
     * @return void
     */
    public function delete(Product $product): void
    {
        $this->removeEntity($product);
    }
}
