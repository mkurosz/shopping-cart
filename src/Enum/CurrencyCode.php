<?php

namespace App\Enum;

class CurrencyCode
{
    /**
     * PLN currency code.
     *
     * @var string
     */
    public const PLN = 'PLN';

    /**
     * EUR currency code.
     *
     * @var string
     */
    public const EUR = 'EUR';

    /**
     * Get values.
     *
     * @return string[]
     */
    public static  function getValues(): array
    {
        return [self::PLN, self::EUR];
    }
}
