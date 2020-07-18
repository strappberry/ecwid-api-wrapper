<?php

namespace Strappberry\EcwidApi\Services;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Support\Arr;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Strappberry\EcwidApi\Classes\Order;
use Strappberry\EcwidApi\Enums\OrderFulfillmentStatus;

class EcwidApiService
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    private $categoriesCache = [];

    /**
     * EcwidApiService constructor.
     * @param  HttpClient  $httpClient
     */
    public function __construct()
    {
        $storeId = config('ecwid-api.store_id', null);
        $accessToken = config('ecwid-api.access_token', null);

        if (!$storeId) {
            throw new \InvalidArgumentException("Ecwid Store Id cannot be empty");
        }
        if (!$accessToken) {
            throw new \InvalidArgumentException("Ecwid Accress Token cannot be empty");
        }

        $handlerStack = HandlerStack::create();
        $handlerStack->before('prepare_body', Middleware::mapRequest(function (RequestInterface $request) use (
            $accessToken
        ) {
            $uri = $request->getUri();
            $query = $uri->getQuery();
            if ($query) {
                $query .= '&';
            }
            $query .= 'token='.$accessToken;

            $uri = $uri->withQuery($query);

            $request = $request->withUri($uri);

            return $request;
        }), 'add_api_token');

        $httpClient = new HttpClient([
            'base_uri' => "https://app.ecwid.com/api/v3/{$storeId}/",
            'handler' => $handlerStack,
        ]);

        $this->httpClient = $httpClient;
    }

    /**
     * @param  string|UriInterface  $uri
     * @param  array  $options
     * @param  bool  $onlyOneRequest
     * @return \Illuminate\Support\Collection
     */
    protected function getAllResources($uri, $options = [], $onlyOneRequest = false)
    {
        $resources = collect([]);
        $startOffset = Arr::get($options, 'query.offset', 0);
        $options = Arr::except($options, 'query.offset');
        do {
            $newOptions = array_merge_recursive($options, [
                'query' => [
                    'offset' => $startOffset,
                ]
            ]);
            $response = $this->httpClient->get($uri, $newOptions);
            $jsonResponse = json_decode($response->getBody()->getContents(), true);
            $startOffset += $jsonResponse['count'];

            $responseItems = collect($jsonResponse['items']);

            $resources = $resources->merge($responseItems);
        } while ($responseItems->count() != 0 && !$onlyOneRequest);

        return $resources;
    }

    public function getCategory($categoryId)
    {
        $response = $this->httpClient->get('categories/'.$categoryId);
        $jsonResponse = json_decode($response->getBody()->getContents(), true);
        return $jsonResponse;
    }

    public function getCategoryName($categoryId)
    {
        if (!!$category_name = Arr::get($this->categoriesCache, $categoryId)) {
            return $category_name;
        }
        $category = $this->getCategory($categoryId);
        $category_name = Arr::get($category, 'name');
        Arr::set($this->categoriesCache, $categoryId, $category_name);
        return $category_name;
    }

    public function searchProducts($filters = [])
    {
        $products = $this->getAllResources('products', [
            'query' => $filters,
        ]);

        return $products;
    }

    public function searchOrders($filters = [])
    {
        $orders = $this->getAllResources('orders', [
            'query' => $filters,
        ], Arr::has($filters, 'orderNumber'));

        return $orders;
    }

    public function createOrder($orderDetails)
    {
        if (!is_array($orderDetails) && get_class($orderDetails) !== Order::class) {
            throw new \InvalidArgumentException('$orderDetails is not a valid type in `createOrder` method');
        }
        if (get_class($orderDetails) === Order::class) {
            $orderDetails = $orderDetails->data();
        }

        $response = $this->httpClient->post('orders', [
            'json' => $orderDetails
        ]);
        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        return $jsonResponse;
    }

    public function updateOrderFulfillmentStatus($orderId, OrderFulfillmentStatus $newFulfillmentStatus)
    {
        $response = $this->httpClient->put('orders/'. $orderId, [
            'json' => [
                'fulfillmentStatus' => $newFulfillmentStatus->getValue(),
            ]
        ]);
        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        return $jsonResponse;
    }

    public function deleteOrder($orderId)
    {
        $response = $this->httpClient->delete('orders/'.$orderId);
        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        return $jsonResponse;
    }
}