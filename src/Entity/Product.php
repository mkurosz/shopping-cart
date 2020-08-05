<?php

namespace App\Entity;

use App\Dto\ProductInput;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ExclusionPolicy("all")
 */
class Product
{
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
     * Name.
     *
     * @var string
     * @Type("string")
     *
     * @ORM\Column(type="text")
     *
     * @Expose
     */
    private $name;

    /**
     * Price.
     *
     * @var int
     * @Type("int")
     *
     * @ORM\Column(type="integer")
     *
     * @Expose
     */
    private $price;

    /**
     * Currency code.
     *
     * @var string
     * @Type("string")
     *
     * @ORM\Column(type="string")
     *
     * @Expose
     */
    private $currencyCode;

    /**
     * Cart items.
     *
     * @var CartItem[]
     * @Type("ArrayCollection<App\Entity\CartItem>")
     *
     * @ORM\OneToMany(targetEntity=CartItem::class, mappedBy="product", orphanRemoval=true)
     * @ORM\OrderBy({"count" = "DESC"})
     */
    private $cartItems;

    /**
     * Product constructor.
     *
     * @param string $name
     * @param int $price
     * @param string $currencyCode
     */
    public function __construct(
        string $name,
        int $price,
        string $currencyCode
    ) {
        $this->name = $name;
        $this->price = $price;
        $this->currencyCode = $currencyCode;
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
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get price.
     *
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * Set price.
     *
     * @param int $price
     *
     * @return Product
     */
    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get currency code.
     *
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * Set currency code.
     *
     * @param string $currencyCode
     *
     * @return Product
     */
    public function setCurrencyCode(string $currencyCode): self
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    /**
     * Returns formatted price.
     *
     * @return string
     *
     * @VirtualProperty
     * @SerializedName("price_formatted")
     */
    public function getPriceFormatted(): string
    {
        return number_format($this->getPrice() / 100, 2, ',', '');
    }

    /**
     * Create product from ProductInput
     *
     * @param ProductInput $productInput
     *
     * @return Product
     */
    public static function createFromDto(
        ProductInput $productInput
    ): self {
        return new self(
            $productInput->getName(),
            $productInput->getPrice(),
            $productInput->getCurrencyCode()
        );
    }
}
