<?php

namespace Ipasspay\IpasspayChannel\config;

class IpasspayConfig
{
    //Fill in the blanks based on the actual situation
    const ENV_CONFIG = [
        'live'=>[
            "merchant_id" => "",
            "app_id" => "",
            "version" => "",
            "api_secret"=>"",

            "direct_pay_url" => "https://service.ipasspay.biz/gateway/OpenApi/onlinePay",
            "redirect_pay_url" => "https://service.ipasspay.biz/gateway/Index/checkout",
            "query_order_url" => "https://service.ipasspay.biz/gateway/OpenApi/getOrderDetail",
            "query_order_list_url" => "https://service.ipasspay.biz/gateway/OpenApi/getOrderList",
            "refund_url" => "https://service.ipasspay.biz/gateway/OpenApi/refund",
            "cancel_refund_url" => "https://service.ipasspay.biz/gateway/OpenApi/cancelRefund",
            "upload_express_url" => "https://service.ipasspay.biz/gateway/OpenApi/uploadExpress",
        ],
        'sandbox'=>[
            "merchant_id" => "10011019120317101413249927981",
            "app_id" => "19120455974948131",
            "version" => "2.0",
            "api_secret"=>"LEMiaDoJCGzVp0nZzoWzWmgxdlKc",

            "direct_pay_url" => "https://sandbox.service.ipasspay.biz/gateway/OpenApi/onlinePay",
            "redirect_pay_url" => "https://sandbox.service.ipasspay.biz/gateway/Index/checkout",
            "query_order_url" => "https://sandbox.service.ipasspay.biz/gateway/OpenApi/getOrderDetail",
            "query_order_list_url" => "https://sandbox.service.ipasspay.biz/gateway/OpenApi/getOrderList",
            "refund_url" => "https://sandbox.service.ipasspay.biz/gateway/OpenApi/refund",
            "cancel_refund_url" => "https://sandbox.service.ipasspay.biz/gateway/OpenApi/cancelRefund",
            "upload_express_url" => "https://sandbox.service.ipasspay.biz/gateway/OpenApi/uploadExpress",
        ],
    ];
    //------------------
}