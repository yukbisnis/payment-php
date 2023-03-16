<?php

namespace Yubi/Payment;

class Channel extends Request
{

    protected $endpoint = '/v1/channel';

    public function get()
    {

        $this->setEndpoint('GET', $this->endpoint);
        $this->withAuth();

        $this->hit();

        if ($this->status == true) {
            $data = json_decode(json_encode($this->responseData));

            $this->data = $data->response->results;
        }

        return $this;
    }
}
