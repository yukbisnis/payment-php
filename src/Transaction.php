<?php

namespace Yubi\Payment;

class Transaction extends Request
{

    protected $endpoint = '/v1/transaction';

    public function status($invoice)
    {

        $this->setEndpoint('GET', $this->endpoint.'/'.$invoice.'/status');
        $this->withAuth();

        $this->hit();

        if ($this->status == true) {
            $data = json_decode(json_encode($this->responseData));

            $this->data = $data->response->results[0];
        }

        return $this;
    }

    public function create($attr = [])
    {
        $this->setEndpoint('POST', $this->endpoint);
        $this->setBody($this->formatAttr($attr));
        $this->withAuth();

        $this->hit();

        if ($this->status == true) {
            $data = json_decode(json_encode($this->responseData));

            $this->data = $data->response->results[0];
        }

        return $this;
    }

    protected function formatAttr($value)
    {

        $data = [
            'externalId' => $this->generateExternalId(),
            'email' => '',
            'phone' => '',
            'name' => '',
            'channelCode' => '',
            'channelDetailCode' => '',
            'amount' => '',
            'expirationDate' => '',
            'successRedirectUrl' => '',
            'failurRedirectUrl' => '',
            'isPayFee' => false
        ];

        foreach ($data as $k => $v) {
            if (array_key_exists($k, $value)) {
                $data[$k] = $value[$k];
            }
        }

        return $data;
    }

    public function generateExternalId()
    {
        return \Ramsey\Uuid\Uuid::uuid4()->toString();
    }
}
