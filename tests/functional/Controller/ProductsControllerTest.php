<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\MyWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductsControllerTest extends MyWebTestCase
{
    /**
     * @param int $page
     * @param array $expectedResponse
     *
     * @dataProvider getProductsProvider
     */
    public function testGetProducts(
        int $page,
        array $expectedResponse
    ) {
        $client = static::createClient();
        $client->request('GET', sprintf('/api/products?page=%s', $page));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals($expectedResponse, $this->decodeResponse($client->getResponse()));
    }

    /**
     * @return array
     */
    public function getProductsProvider()
    {
        return [
            'page_1' => [
                1,
                [
                    'pages_count' => 2,
                    'total_count' => 6,
                    'items_per_page' => 3,
                    'items' => [
                        [
                            'price_formatted' => '59,99',
                            'id' => 1,
                            'name' => 'The Godfather',
                            'price' => 5999,
                            'currency_code' => 'PLN',
                        ], [
                            'price_formatted' => '49,95',
                            'id' => 2,
                            'name' => 'Steve Jobs',
                            'price' => 4995,
                            'currency_code' => 'PLN',
                        ], [
                            'price_formatted' => '39,99',
                            'id' => 3,
                            'name' => 'The Return of Sherlock Holmes',
                            'price' => 3999,
                            'currency_code' => 'PLN',
                        ],
                    ]
                ],
            ],
            'page_2' => [
                2,
                [
                    'pages_count' => 2,
                    'total_count' => 6,
                    'items_per_page' => 3,
                    'items' => [
                        [
                            'price_formatted' => '29,99',
                            'id' => 4,
                            'name' => 'The Little Prince',
                            'price' => 2999,
                            'currency_code' => 'PLN',
                        ], [
                            'price_formatted' => '19,99',
                            'id' => 5,
                            'name' => 'I Hate Myselfie!',
                            'price' => 1999,
                            'currency_code' => 'PLN',
                        ], [
                            'price_formatted' => '9,99',
                            'id' => 6,
                            'name' => 'The Trial',
                            'price' => 999,
                            'currency_code' => 'PLN',
                        ],
                    ]
                ],
            ],
            'page_3' => [
                3,
                [
                    'pages_count' => 2,
                    'total_count' => 6,
                    'items_per_page' => 3,
                    'items' => []
                ],
            ],
        ];
    }

    public function testGetProductsFailed() {
        $client = static::createClient();
        $client->request('GET', '/api/products?page=0');

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertEquals(
            [
                'errors' => [
                    'property' => '',
                    'message' => 'Offset must be a positive integer or zero, -3 given'
                ]
            ],
            $this->decodeResponse($client->getResponse())
        );
    }

    /**
     * @param string|null $productName
     * @param int|null $productPrice
     * @param string|null $productCurrencyCode
     * @param array $expectedResponse
     *
     * @dataProvider postProductsProvider
     */
    public function testPostProducts(
        ?string $productName,
        ?int $productPrice,
        ?string $productCurrencyCode,
        array $expectedResponse
    ) {
        $payload = [];

        if ($productName !== null) {
            $payload['name'] = $productName;
        }
        if ($productPrice !== null) {
            $payload['price'] = $productPrice;
        }
        if ($productCurrencyCode !== null) {
            $payload['currency_code'] = $productCurrencyCode;
        }

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $this->encodeToJson($payload)
        );
        $response = $this->decodeResponse($client->getResponse());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertEquals($expectedResponse['price'], $response['price']);
        $this->assertEquals($expectedResponse['price_formatted'], $response['price_formatted']);
        $this->assertEquals($expectedResponse['name'], $response['name']);
        $this->assertEquals($expectedResponse['currency_code'], $response['currency_code']);
    }

    /**
     * @return array
     */
    public function postProductsProvider()
    {
        return [
            'add_new_product' => [
                'Peaky Blinders',
                10000,
                'PLN',
                [
                    'price_formatted' => '100,00',
                    'name' => 'Peaky Blinders',
                    'price' => 10000,
                    'currency_code' => 'PLN',
                ],
            ],
        ];
    }

    /**
     * @param string|null $productName
     * @param int|null $productPrice
     * @param string|null $productCurrencyCode
     * @param int $expectedCode
     * @param array $expectedResponse
     *
     * @dataProvider postProductsFailedProvider
     */
    public function testPostProductsFailed(
        ?string $productName,
        ?int $productPrice,
        ?string $productCurrencyCode,
        int $expectedCode,
        array $expectedResponse
    ) {
        $payload = [];

        if ($productName !== null) {
            $payload['name'] = $productName;
        }
        if ($productPrice !== null) {
            $payload['price'] = $productPrice;
        }
        if ($productCurrencyCode !== null) {
            $payload['currency_code'] = $productCurrencyCode;
        }

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $this->encodeToJson($payload)
        );

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertEquals($expectedResponse, $this->decodeResponse($client->getResponse()));
    }

    /**
     * @return array
     */
    public function postProductsFailedProvider()
    {
        return [
            'missing name' => [
                null,
                10000,
                'PLN',
                Response::HTTP_BAD_REQUEST,
                [
                    'errors' => [
                        0 => [
                            'property' => 'name',
                            'message' => 'This value should not be blank.'
                        ]
                    ]
                ],
            ],
            'missing price' => [
                'Peaky Blinders',
                null,
                'PLN',
                Response::HTTP_BAD_REQUEST,
                [
                    'errors' => [
                        0 => [
                            'property' => 'price',
                            'message' => 'This value should not be blank.'
                        ]
                    ]
                ],
            ],
            'missing currency code' => [
                'Peaky Blinders',
                10000,
                null,
                Response::HTTP_BAD_REQUEST,
                [
                    'errors' => [
                        0 => [
                            'property' => 'currencyCode',
                            'message' => 'This value should not be blank.'
                        ]
                    ]
                ],
            ],
            'invalid currency code' => [
                'Peaky Blinders',
                10000,
                'AAA',
                Response::HTTP_BAD_REQUEST,
                [
                    'errors' => [
                        0 => [
                            'property' => 'currencyCode',
                            'message' => 'The value you selected is not a valid choice.'
                        ]
                    ]
                ],
            ],
        ];
    }

    /**
     * @param int $productId
     * @param string|null $productName
     * @param int|null $productPrice
     * @param string|null $productCurrencyCode
     * @param int $expectedCode
     * @param array $expectedResponse
     *
     * @dataProvider patchProductsProvider
     */
    public function testPatchProducts(
        int $productId,
        ?string $productName,
        ?int $productPrice,
        ?string $productCurrencyCode,
        int $expectedCode,
        array $expectedResponse
    ) {
        $payload = [];

        if ($productName !== null) {
            $payload['name'] = $productName;
        }
        if ($productPrice !== null) {
            $payload['price'] = $productPrice;
        }
        if ($productCurrencyCode !== null) {
            $payload['currency_code'] = $productCurrencyCode;
        }

        $client = static::createClient();
        $client->request(
            'PATCH',
            sprintf('/api/products/%s', $productId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $this->encodeToJson($payload)
        );

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertEquals($expectedResponse, $this->decodeResponse($client->getResponse()));
    }

    /**
     * @return array
     */
    public function patchProductsProvider()
    {
        return [
            'edit_product_name' => [
                1,
                'Peaky Blinders',
                null,
                null,
                Response::HTTP_OK,
                [
                    'price_formatted' => '59,99',
                    'id' => 1,
                    'name' => 'Peaky Blinders',
                    'price' => 5999,
                    'currency_code' => 'PLN',
                ],
            ],
            'edit_product_price' => [
                2,
                null,
                10000,
                null,
                Response::HTTP_OK,
                [
                    'price_formatted' => '100,00',
                    'id' => 2,
                    'name' => 'Steve Jobs',
                    'price' => 10000,
                    'currency_code' => 'PLN',
                ],
            ],
            'edit_product_currency' => [
                3,
                null,
                null,
                'EUR',
                Response::HTTP_OK,
                [
                    'price_formatted' => '39,99',
                    'id' => 3,
                    'name' => 'The Return of Sherlock Holmes',
                    'price' => 3999,
                    'currency_code' => 'EUR',
                ],
            ],
            'invalid currency code' => [
                1,
                'Peaky Blinders',
                10000,
                'AAA',
                Response::HTTP_BAD_REQUEST,
                [
                    'errors' => [
                        0 => [
                            'property' => 'currencyCode',
                            'message' => 'The value you selected is not a valid choice.'
                        ]
                    ]
                ],
            ],
            'not existing product' => [
                100,
                'Not existing',
                1234,
                'ZZZ',
                Response::HTTP_BAD_REQUEST,
                [
                    'errors' => [
                        'property' => '',
                        'message' => 'Product for given id does not exist.',
                    ],
                ],
            ],
        ];
    }

    public function testDeleteProducts() {
        $client = static::createClient();
        $client->request(
            'DELETE',
            sprintf('/api/products/%s', 1)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        $this->assertNull($this->decodeResponse($client->getResponse()));
    }

    public function testDeleteProductsFailed() {
        $client = static::createClient();
        $client->request(
            'DELETE',
            sprintf('/api/products/%s', 100)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertEquals(
            [
                'errors' => [
                    'property' => '',
                    'message' => 'Product for given id does not exist.',
                ],
            ],
            $this->decodeResponse($client->getResponse())
        );
    }
}
