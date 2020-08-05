<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\Product;

/**
 * Cart CRUD manager.
 */
class CartCrudManager extends AbstractCrudManger
{
    /**
     * Create cart.
     *
     * @return Cart
     */
    public function create(): Cart
    {
        $cart = new Cart();

        $this->persistEntity($cart);

        return $cart;
    }

    /**
     * Add product to cart.
     *
     * @param Cart $cart
     * @param Product $product
     *
     * @return Cart
     */
    public function addProduct(Cart $cart, Product $product): Cart
    {
        $this->persistEntity(
            $cart->addProduct($product)
        );

        return $cart;
    }

    /**
     * Remove product from cart.
     *
     * @param Cart $cart
     * @param Product $product
     *
     * @return Cart
     */
    public function removeProduct(Cart $cart, Product $product): Cart
    {
        $this->persistEntity(
            $cart->removeProduct($product)
        );

        return $cart;
    }
}
