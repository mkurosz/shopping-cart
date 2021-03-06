<?php

namespace App\Controller;

use App\Dto\ProductInput;
use App\Entity\Product;
use App\Service\ProductCrudManager;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use InvalidArgumentException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * ProductsController.
 */
class ProductsController extends AbstractFOSRestController
{
    use ApiValidationErrorsControllerTrait;

    /**
     * Cart CRUD manager.
     *
     * @var ProductCrudManager
     */
    private $productCrudManger;

    /**
     * Validator.
     *
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * ProductsController constructor.
     *
     * @param ProductCrudManager $productCrudManger
     * @param ValidatorInterface $validator
     */
    public function __construct(
        ProductCrudManager $productCrudManger,
        ValidatorInterface $validator
    ) {
        $this->productCrudManger = $productCrudManger;
        $this->validator = $validator;
    }

    /**
     * Get products.
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @QueryParam(
     *     name="page",
     *     requirements="\d+",
     *     default="1",
     *     description="Page of the results list."
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns products list.",
     *     @OA\Schema(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Product::class))
     *     )
     * )
     */
    public function getProductsAction(ParamFetcher $paramFetcher): View
    {
        try {
            return $this->view(
                $this->productCrudManger->getProducts($paramFetcher->get('page')),
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }

    /**
     * Create product.
     *
     * @param ProductInput $productInput
     *
     * @return View
     *
     * @ParamConverter("productInput", converter="fos_rest.request_body")
     *
     * @OA\Parameter(
     *     name="productInput",
     *     in="body",
     *     description="Product details.",
     *     @Model(type=ProductInput::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns newly created product.",
     *     @Model(type=Product::class)
     * )
     */
    public function postProductAction(ProductInput $productInput): View
    {
        try {
            $validationErrors = $this->validator->validate($productInput, null, ['Default', 'CreateProduct']);

            if ($validationErrors->count()) {
                return $this->createValidationErrorsResponse($validationErrors);
            }

            return $this->view(
                $this->productCrudManger->createFromDto($productInput)
            );
        } catch (Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }

    /**
     * Update product.
     *
     * @param ProductInput $productInput
     * @param Product|null $product
     *
     * @return View
     *
     * @ParamConverter("productInput", converter="fos_rest.request_body")
     * @ParamConverter("product")
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns updated product.",
     *     @Model(type=Product::class)
     * )
     */
    public function patchProductAction(ProductInput $productInput, ?Product $product = null): View
    {
        try {
            if (!$product instanceof Product) {
                throw new InvalidArgumentException('Product for given id does not exist.');
            }

            $validationErrors = $this->validator->validate($productInput, null, ['Default', 'UpdateProduct']);

            if ($validationErrors->count()) {
                return $this->createValidationErrorsResponse($validationErrors);
            }

            return $this->view(
                $this->productCrudManger->updateFromDto($product, $productInput)
            );
        } catch (Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }

    /**
     * Delete product.
     *
     * @param Product|null $product
     *
     * @return View
     *
     * @ParamConverter("product")
     *
     * @OA\Response(
     *     response=204,
     *     description="Returns http no content.",
     * )
     */
    public function deleteProductAction(?Product $product = null): View
    {
        try {
            if (!$product instanceof Product) {
                throw new InvalidArgumentException('Product for given id does not exist.');
            }

            $this->productCrudManger->delete($product);

            return $this->view([], Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }
}
