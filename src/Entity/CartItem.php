<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @ORM\Entity()
 *
 * @ExclusionPolicy("all")
 */
class CartItem implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * Id.
     *
     * @var int
     * @Type("integer")
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Count.
     *
     * @var int
     * @Type("integer")
     *
     * @ORM\Column(type="integer")
     *
     * @Expose
     */
    private $count;

    /**
     * Cart.
     *
     * @var Cart
     * @Type("App\Entity\Cart")
     *
     * @ORM\ManyToOne(targetEntity=Cart::class, inversedBy="cartItems")
     * @ORM\JoinColumn(name="cart_id", referencedColumnName="id")
     */
    private $cart;

    /**
     * Product.
     *
     * @var Product
     * @Type("App\Entity\Product")
     *
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="cartItems")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     *
     * @Expose
     */
    private $product;

    /**
     * Cart item constructor.
     *
     * @param int $count
     * @param Product $product
     */
    public function __construct(
        int $count,
        Product $product
    ) {
        $this->count = $count;
        $this->product = $product;
        $this->updateTimestamps();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get count.
     *
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Set count.
     *
     * @param int $count
     *
     * @return CartItem
     */
    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Increase count by given number.
     *
     * @param int $increase
     *
     * @return CartItem
     */
    public function increaseCount(int $increase): self
    {
        $this->count += $increase;

        return $this;
    }

    /**
     * Decrease count by given number.
     *
     * @param int $decrease
     *
     * @return CartItem
     */
    public function decreaseCount(int $decrease): self
    {
        $this->count -= $decrease;

        return $this;
    }

    /**
     * Get cart.
     *
     * @return Cart
     */
    public function getCart(): Cart
    {
        return $this->cart;
    }

    /**
     * Set cart.
     *
     * @param Cart $cart
     *
     * @return CartItem
     */
    public function setCart(Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * Get product.
     *
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * Set product.
     *
     * @param Product $product
     *
     * @return CartItem
     */
    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }
}
