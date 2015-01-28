<?php namespace Cyberduck\LaravelWpApi;

use GuzzleHttp\Client;

class WpApi
{

    protected $client;

    public function __construct($endpoint, Client $client)
    {
        $this->endpoint = $endpoint;
        $this->client   = $client;
    }

    public function posts($page = null)
    {
        return $this->_get('posts', ['page' => $page]);
    }

    public function post($slug)
    {
        return $this->_get('posts', ['filter' => ['name' => $slug]]);
    }

    public function categories()
    {
        return $this->_get('taxonomies/category/terms');
    }

    public function category_posts($slug, $page = null)
    {
        return $this->_get('posts', ['page' => $page, 'filter' => ['category_name' => $slug]]);
    }

    public function search($query, $page = null)
    {
        return $this->_get('posts', ['page' => $page, 'filter' => ['s' => $query]]);
    }

    public function archive($year, $month, $page = null)
    {
        return $this->_get('posts', ['page' => $page, 'filter' => ['year' => $year, 'month' => $month]]);
    }

    public function _get($method, array $query = array())
    {

        $client = new Client();

        try {

            $response = $client->get($this->endpoint . '/wp-json/' . $method, ['query' => $query]);

            $return = [
                'results' => $response->json(),
                'total'   => $response->getHeader('X-WP-Total'),
                'pages'   => $response->getHeader('X-WP-TotalPages')
            ];

        } catch (\GuzzleHttp\Exception\TransferException $e) {

            $error['message'] = $e->getMessage();

            if ($e->getResponse()) {
                $error['code'] = $e->getResponse()->getStatusCode();
            }

            $return = [
                'error'   => $error,
                'results' => [],
                'total'   => 0,
                'pages'   => 0
            ];

        }

        return $return;

    }

}
