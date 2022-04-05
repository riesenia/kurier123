<?php
/**
 * This file is part of riesenia/kurier123 package.
 *
 * (c) RIESENIA.com
 */

declare(strict_types=1);

namespace Riesenia\Kurier123;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

/**
 * Client for sending packages through 123Kurier API.
 */
class Api
{
    /** @var Client */
    protected $_client;

    /** @var string */
    protected $_baseUri = 'https://123kurier.jpsoftware.sk/atol-api';

    /** @var string */
    protected $_username;

    /** @var string */
    protected $_password;

    /** @var array */
    protected $_errors = [];

    /**
     * 123Kurier API client constructor.
     *
     * @param string $username
     * @param string $password
     */
    public function __construct(string $username, string $password)
    {
        $this->_client = new Client();
        $this->_username = $username;
        $this->_password = $password;
    }

    /**
     * Create shipments.
     *
     * @param array $shipment
     *
     * @return array|null
     */
    public function createShipment(array $shipment): ?array
    {
        return $this->_callApi('/order', ['orders' => $shipment]);
    }

    /**
     * Generate PDF/ZPL tickets.
     *
     * @param array  $orderNumbers
     * @param string $type
     *
     * @return string|null
     */
    public function generateTickets(array $orderNumbers, string $type = 'A4'): ?string
    {
        $response = $this->_callApi('/order/ticket', [
            'orders' => $orderNumbers,
            'type' => $type
        ]);

        if (!$response) {
            return $response;
        }

        return $response['pdf'];
    }

    /**
     * Get order status history.
     *
     * @param array $orderNumbers
     *
     * @return array|null
     */
    public function statusHistory(array $orderNumbers): ?array
    {
        return $this->_callApi('/order/statushistory', ['orderNumbers' => $orderNumbers]);
    }

    /**
     * Get errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->_errors;
    }

    /**
     * Call remote API method.
     *
     * @param string $action
     * @param array  $data
     *
     * @return array|null
     */
    protected function _callApi(string $action, array $data = []): ?array
    {
        $response = $this->_client->post($this->_baseUri . $action, [
            RequestOptions::JSON => $data,
            'auth' => [
                $this->_username,
                $this->_password
            ]
        ]);

        if ($response->getStatusCode() != 200) {
            $this->_errors[] = 'Unable to fetch response from API. Status code: ' . $response->getStatusCode();

            return null;
        }

        $responseData = \json_decode($response->getBody()->getContents(), true);

        if (\json_last_error() !== JSON_ERROR_NONE) {
            $this->_errors[] = 'Unable to parse response data. Reason: ' . \json_last_error_msg();

            return null;
        }

        return $responseData;
    }
}
