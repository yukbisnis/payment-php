<?php

namespace Yubi\Payment;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;

class Request
{

    protected $apiKey;

    protected $responseCode;

    protected $baseUrl;

    protected $endpoint;
    protected $method;
    protected $headers = [
        'Accept' => 'application/json',
    ];

    protected $options = [
        'query' => []
    ];

    // 
    protected $status;
    protected $responseData;
    protected $message;
    protected $data;

    public function __construct()
    {

        $this->baseUrl = config('payment.base_url');
        $this->apiKey = config('payment.api_key');
    }

    public function hit()
    {

        $client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => $this->headers
        ]);

        try {

            $requestClient = $client->request($this->method, $this->endpoint, $this->options);
            $response = json_decode($requestClient->getBody()->getContents());

            $this->responseCode = $requestClient->getStatusCode();

            if ($response->response->status) {
                $this->status = true;
                $this->responseData = $response;
            } else {
                $message = 'Unkown error';

                if (isset($response->response->messages) and !is_null($response->response->messages)) {
                    $message = $response->response->messages;
                } elseif (isset($response->description)) {
                    $message = $response->description;
                }

                $this->status = false;
                $this->message = $message;
            }
        } catch (ClientException $e) {
            $this->catchErrorMessage($e);
        } catch (ServerException $e) {
            $this->catchErrorMessage($e);
        } catch (ConnectException $e) {
            $this->status = false;
            $this->message = 'Connection timout';
        } catch (RequestException $e) {
            $this->catchErrorMessage($e);
        } catch (Exception $e) {
            $this->status = false;
            $this->message = $e->getMessage();
        }

        return $this;
    }

    public function withAuth()
    {
        $this->headers['Authorization'] = 'Bearer ' . $this->apiKey;
    }

    protected function catchErrorMessage($e)
    {
        $response = $e->getResponse();
        $responseBody = json_decode($response->getBody()->getContents());

        $this->responseCode = $response->getStatusCode();
        $this->status = false;

        if ($response->getStatusCode() == 422) {
            $this->responseData = $responseBody;

            $this->data = json_decode(json_encode($responseBody->response->metadata), true);
        }

        if (isset($responseBody->response->messages) and !is_null($responseBody->response->messages)) {
            $this->message = $responseBody->response->messages;
        } else {
            if ($response->getStatusCode() == 401) {
                $this->message = 'Unauthorized';
            } else {
                $this->message = 'Unknown error: ' . $response->getStatusCode();
            }
        }
    }

    public function setEndpoint($method, $data)
    {
        $this->setMethod($method);
        $this->endpoint = $data;

        return $this;
    }

    public function setMethod($data)
    {
        $this->method = $data;

        return $this;
    }

    public function setMessage($data)
    {
        $this->message = $data;

        return $this;
    }

    public function setStatus($data)
    {
        $this->status = $data;

        return $this;
    }

    public function setApiKey($data)
    {
        $this->apiKey = $data;

        return $this;
    }

    public function setHeaders($data)
    {
        $this->headers = $data;

        return $this;
    }

    public function setParams($data)
    {
        $this->options['query'] = array_merge($this->options['query'], $data);

        return $this;
    }

    public function setBody($data)
    {
        $this->options['json'] = $data;

        return $this;
    }

    public function setFormData($data)
    {
        $this->options['form_params'] = $data;
    }

    public function setOptions($data)
    {
        $this->options = array_merge($this->options, $data);

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getResponseCode()
    {
        return $this->responseCode;
    }

    public function getResponseData()
    {
        return $this->responseData;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getResponse()
    {
        return (object) [
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }
}
