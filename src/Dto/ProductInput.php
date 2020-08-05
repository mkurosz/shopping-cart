<?php

namespace App\Dto;

use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ProductInput.
 */
class ProductInput
{
    /**
     * Name.
     *
     * @var string|null
     * @Type("string")
     *
     * @Assert\NotBlank(groups={"CreateProduct"})
     * @Assert\Type("string")
     */
    private $name;

    /**
     * Price.
     *
     * @var int|null
     * @Type("integer")
     *
     * @Assert\NotBlank(groups={"CreateProduct"})
     * @Assert\Type("integer")
     */
    private $price;

    /**
     * Currency code.
     *
     * @var string|null
     * @Type("string")
     *
     * @Assert\NotBlank(groups={"CreateProduct"})
     * @Assert\Type("string")
     * @Assert\Choice(callback={"App\Enum\CurrencyCode", "getValues"})
     */
    private $currencyCode;

    /**
     * ProductInput constructor.
     *
     * @param string|null $name
     * @param int|null $price
     * @param string|null $currencyCode
     */
    public function __construct(
        ?string $name,
        ?int $price,
        ?string $currencyCode
    ) {
        $this->name = $name;
        $this->price = $price;
        $this->currencyCode = $currencyCode;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Get price.
     *
     * @return int|null
     */
    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     * Get $currency code.
     *
     * @return string|null
     */
    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }
}
