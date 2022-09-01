<?php
namespace Ipasspay\AirsecurecardChannel\logic;

class NotifyOrderLogic extends AirsecurecardChannelCommonLogic
{
    //Validate signature array
    protected $verify_sign_field=[
        "merchant_id",
        "app_id",
        "order_no",
        "gateway_order_no",
        "order_currency",
        "order_amount",
        "order_status",
    ];

    public function createData($params)
    {
        $this->notify_data=$params;
        return true;
    }
}