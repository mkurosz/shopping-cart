<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use App\Service\CartCrudManager;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use InvalidArgumentException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * CartsController.
 */
class CartsController extends AbstractFOSRestController
{
    use ApiValidationErrorsControllerTrait;

    /**
     * Cart CRUD manager.
     *
     * @var CartCrudManager
     */
    private $cartCrudManager;

    /**
     * Validator.
     *
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * CartsController constructor.
     *
     * @param CartCrudManager $cartCrudManager
     * @param ValidatorInterface $validator
     */
    public function __construct(
        CartCrudManager $cartCrudManager,
        ValidatorInterface $validator
    ) {
        $this->cartCrudManager = $cartCrudManager;
        $this->validator = $validator;
    }

    /**
     * Get cart.
     *
     * @param Cart $cart
     *
     * @return View
     *
     * @ParamConverter("cart")
     *
     * @OA\Parameter(
     *     name="cart",
     *     in="path",
     *     type="integer",
     *     description="Cart id."
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns cart with products.",
     *     @Model(type=Cart::class)
     * )
     */
    public function getCartAction(Cart $cart): View
    {
        try {
            return $this->view(
                $cart,
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }

    /**
     * Create cart.
     *
     * @return View
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns newly created cart.",
     *     @Model(type=Cart::class)
     * )
     */
    public function postCartAction(): View
    {
        try {
            return $this->view(
                $this->cartCrudManager->create()
            );
        } catch (Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }

    /**
     * Add product to cart.
     *
     * @param Cart $cart
     * @param Product $product
     *
     * @return View
     *
     * @ParamConverter("cart")
     * @ParamConverter("product")
     *
     * @OA\Parameter(
     *     name="cart",
     *     in="path",
     *     type="integer",
     *     description="Cart id."
     * )
     * @OA\Parameter(
     *     name="product",
     *     in="path",
     *     type="integer",
     *     description="Product id."
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns cart with added product.",
     *     @Model(type=Cart::class)
     * )
     */
    public function addCartProductAction(Cart $cart, Product $product): View
    {
        try {
            if (!$cart->isAvailableToAddProduct()) {
                throw new InvalidArgumentException('Limit of products on the cart exceeded.');
            }

            return $this->view(
                $this->cartCrudManager->addProduct($cart, $product)
            );
        } catch (Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }

    /**
     * Remove product from cart.
     *
     * @param Cart $cart
     * @param Product $product
     *
     * @return View
     *
     * @ParamConverter("cart")
     * @ParamConverter("product")
     *
     * @OA\Parameter(
     *     name="cart",
     *     in="path",
     *     type="integer",
     *     description="Cart id."
     * )
     * @OA\Parameter(
     *     name="product",
     *     in="path",
     *     type="integer",
     *     description="Product id."
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns cart with removed product.",
     *     @Model(type=Cart::class)
     * )
     */
    public function patchCartProductRemoveAction(Cart $cart, Product $product): View
    {
        try {
            if (!$cart->containsProduct($product)) {
                throw new InvalidArgumentException('Cannot remove not existing product from the cart.');
            }

            return $this->view(
                $this->cartCrudManager->removeProduct($cart, $product)
            );
        } catch (Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }
}
