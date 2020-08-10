<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\VirtualProperty;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @ORM\Entity(repositoryClass=CartRepository::class)
 * @ORM\Table(name="cart")
 * @ExclusionPolicy("all")
 */
class Cart implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * Max number of products.
     *
     * @var int
     */
    private const MAX_NUMBER_OF_PRODUCTS = 3;

    /**
     * Id.
     *
     * @var int
     * @Type("integer")
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @Expose
     */
    private $id;

    /**
     * Cart items.
     *
     * @var CartItem[]
     * @Type("ArrayCollection<App\Entity\CartItem>")
     *
     * @ORM\OneToMany(targetEntity=CartItem::class, mappedBy="cart", orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\OrderBy({"count" = "DESC"})
     *
     * @Expose
     */
    private $cartItems;

    /**
     * Cart constructor.
     */
    public function __construct() {
        $this->cartItems = new ArrayCollection();
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
     * @return Collection|CartItem[]
     */
    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }

    /**
     * Add product.
     *
     * @param Product $product
     *
     * @return Cart
     */
    public function addProduct(Product $product): self
    {
        $cartItem = $this->findCartItem($product);

        if ($cartItem instanceof CartItem) {
            $cartItem->increaseCount(1);
        } else {
            $newCartItem = new CartItem(
                1,
                $product
            );
            $newCartItem->setCart($this);

            $this->cartItems->add($newCartItem);
        }

        return $this;
    }

    /**
     * Remove product.
     *
     * @param Product $product
     *
     * @return Cart
     */
    public function removeProduct(Product $product): self
    {
        $cartItem = $this->findCartItem($product);

        if (!$cartItem instanceof CartItem) {
            return $this;
        }

        if ($cartItem->getCount() > 1) {
            $cartItem->decreaseCount(1);
        } else {
            $this->cartItems->removeElement($cartItem);
        }

        return $this;
    }

    /**
     * Contains product.
     *
     * @param Product $product
     *
     * @return bool
     */
    public function containsProduct(Product $product): bool
    {
        return $this->findCartItem($product) instanceof CartItem;
    }

    /**
     * Checks allows to add product.
     *
     * @return bool
     */
    public function isAvailableToAddProduct(): bool
    {
        return $this->getNumberOfProducts() < self::MAX_NUMBER_OF_PRODUCTS;
    }

    /**
     * Returns total price of all products on the cart.
     *
     * @return int
     *
     * @VirtualProperty
     * @SerializedName("total_price")
     */
    public function getTotalPrice(): int
    {
        $total = 0;

        foreach($this->cartItems as $cartItem)
        {
            $total += $cartItem->getCount() * $cartItem->getProduct()->getPrice();
        }

        return $total;
    }

    /**
     * Returns formatted total price of all products on the cart.
     *
     * @return string
     *
     * @VirtualProperty
     * @SerializedName("total_price_formatted")
     */
    public function getTotalPriceFormatted(): string
    {
        return number_format($this->getTotalPrice() / 100, 2, ',', '');
    }

    /**
     * Finds cart item for given product.
     *
     * @param Product $product
     *
     * @return CartItem|null
     */
    private function findCartItem(Product $product): ?CartItem
    {
        $filtered = $this->cartItems->filter(function(CartItem $filtered) use ($product) {
            return $filtered->getProduct()->getId() === $product->getId();
        });

        return $filtered->isEmpty() ? null : $filtered->first();
    }

    /**
     * Returns total number of products on the cart.
     *
     * @return int
     */
    private function getNumberOfProducts(): int
    {
        $total = 0;

        foreach($this->cartItems as $cartItem)
        {
            $total += $cartItem->getCount();
        }

        return $total;
    }
}
