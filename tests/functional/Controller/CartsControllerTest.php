<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\MyWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CartsControllerTest extends MyWebTestCase
{
    /**
     * @param int $cartId
     * @param int $expectedCode
     * @param array $expectedResponse
     *
     * @dataProvider getCartProvider
     */
    public function testGetCart(
        int $cartId,
        int $expectedCode,
        array $expectedResponse
    ) {
        $client = static::createClient();
        $client->request('GET', sprintf('/api/carts/%s', $cartId));

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertEquals($expectedResponse, $this->decodeResponse($client->getResponse()));
    }

    /**
     * @return array
     */
    public function getCartProvider()
    {
        return [
            'cart 1' => [
                1,
                Response::HTTP_OK,
                [
                    'total_price' => 4998,
                    'total_price_formatted' => '49,98',
                    'id' => 1,
                    'cart_items' => [
                        [
                            'count' => 1,
                            'product' => [
                                'price_formatted' => '39,99',
                                'id' => 3,
                                'name' => 'The Return of Sherlock Holmes',
                                'price' => 3999,
                                'currency_code' => 'PLN',
                            ],
                        ], [
                            'count' => 1,
                            'product' => [
                                'price_formatted' => '9,99',
                                'id' => 6,
                                'name' => 'The Trial',
                                'price' => 999,
                                'currency_code' => 'PLN',
                            ],
                        ],
                    ]
                ],
            ],
            'cart 2' => [
                2,
                Response::HTTP_OK,
                [
                    'total_price' => 16993,
                    'total_price_formatted' => '169,93',
                    'id' => 2,
                    'cart_items' => [
                        [
                            'count' => 2,
                            'product' => [
                                'price_formatted' => '59,99',
                                'id' => 1,
                                'name' => 'The Godfather',
                                'price' => 5999,
                                'currency_code' => 'PLN',
                            ],
                        ], [
                            'count' => 1,
                            'product' => [
                                'price_formatted' => '49,95',
                                'id' => 2,
                                'name' => 'Steve Jobs',
                                'price' => 4995,
                                'currency_code' => 'PLN',
                            ],
                        ],
                    ]
                ],
            ],
        ];
    }

    public function testPostCarts() {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/carts'
        );
        $response = $this->decodeResponse($client->getResponse());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertEquals(0, $response['total_price']);
        $this->assertEquals('0,00', $response['total_price_formatted']);
        $this->assertEquals([], $response['cart_items']);
    }

    /**
     * @param int $cartId
     * @param int $productId
     * @param int $expectedCode
     * @param array $expectedResponse
     *
     * @dataProvider patchCartsProductsAddProvider
     */
    public function testPatchCartsProductsAdd(
        int $cartId,
        int $productId,
        int $expectedCode,
        array $expectedResponse
    ) {
        $client = static::createClient();
        $client->request(
            'PATCH',
            sprintf('/api/carts/%s/products/%s/add', $cartId, $productId)
        );

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertEquals($expectedResponse, $this->decodeResponse($client->getResponse()));
    }

    /**
     * @return array
     */
    public function patchCartsProductsAddProvider()
    {
        return [
            'add product 3 to cart 1' => [
                1,
                3,
                Response::HTTP_OK,
                [
                    'total_price' => 8997,
                    'total_price_formatted' => '89,97',
                    'id' => 1,
                    'cart_items' => [
                        [
                            'count' => 2,
                            'product' => [
                                'price_formatted' => '39,99',
                                'id' => 3,
                                'name' => 'The Return of Sherlock Holmes',
                                'price' => 3999,
                                'currency_code' => 'PLN',
                            ],
                        ], [
                            'count' => 1,
                            'product' => [
                                'price_formatted' => '9,99',
                                'id' => 6,
                                'name' => 'The Trial',
                                'price' => 999,
                                'currency_code' => 'PLN',
                            ],
                        ],
                    ]
                ],
            ],
            'add product 3 to cart 2' => [
                2,
                3,
                Response::HTTP_BAD_REQUEST,
                [
                    'errors' => [
                        'property' => '',
                        'message' => 'Limit of products on the cart exceeded.',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $cartId
     * @param int $productId
     * @param int $expectedCode
     * @param array $expectedResponse
     *
     * @dataProvider patchCartsProductsRemoveProvider
     */
    public function testPatchCartsProductsRemove(
        int $cartId,
        int $productId,
        int $expectedCode,
        array $expectedResponse
    ) {
        $client = static::createClient();
        $client->request(
            'PATCH',
            sprintf('/api/carts/%s/products/%s/remove', $cartId, $productId)
        );

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertEquals($expectedResponse, $this->decodeResponse($client->getResponse()));
    }

    /**
     * @return array
     */
    public function patchCartsProductsRemoveProvider()
    {
        return [
            'remove product 3 from cart 1' => [
                1,
                3,
                Response::HTTP_OK,
                [
                    'total_price' => 999,
                    'total_price_formatted' => '9,99',
                    'id' => 1,
                    'cart_items' => [
                        [
                            'count' => 1,
                            'product' => [
                                'price_formatted' => '9,99',
                                'id' => 6,
                                'name' => 'The Trial',
                                'price' => 999,
                                'currency_code' => 'PLN',
                            ],
                        ],
                    ]
                ],
            ],
            'remove product 1 from cart 2' => [
                2,
                1,
                Response::HTTP_OK,
                [
                    'total_price' => 10994,
                    'total_price_formatted' => '109,94',
                    'id' => 2,
                    'cart_items' => [
                        [
                            'count' => 1,
                            'product' => [
                                'price_formatted' => '59,99',
                                'id' => 1,
                                'name' => 'The Godfather',
                                'price' => 5999,
                                'currency_code' => 'PLN',
                            ],
                        ], [
                            'count' => 1,
                            'product' => [
                                'price_formatted' => '49,95',
                                'id' => 2,
                                'name' => 'Steve Jobs',
                                'price' => 4995,
                                'currency_code' => 'PLN',
                            ],
                        ],
                    ]
                ],
            ],
            'remove product 5 from cart 1' => [
                1,
                5,
                Response::HTTP_BAD_REQUEST,
                [
                    'errors' => [
                        'property' => '',
                        'message' => 'Cannot remove not existing product from the cart.',
                    ],
                ],
            ],
        ];
    }
}