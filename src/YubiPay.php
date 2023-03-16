<?php

namespace Yubi/Payment;

class YubiPay extends Singleton
{

    public static function getChannel($apiKey = null)
    {
        $data = (new Channel);
        if (!is_null($apiKey)) {
            $data->setApiKey($apiKey);
        }
        $data->get();

        return $data->getResponse();
    }

    public static function createTransaction($attr = [], $apiKey = null)
    {
        $data = (new Transaction);
        if (!is_null($apiKey)) {
            $data->setApiKey($apiKey);
        }
        $data->create($attr);

        return $data->getResponse();
    }
}
