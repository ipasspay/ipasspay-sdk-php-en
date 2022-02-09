<?php

namespace Ipasspay\IpasspayChannel\config;

class IpasspayConstant
{
    //It is not recommended to modify the parameters, we will adjust them through technical notification or version update if they need to be modified.
    const PAY_PARAM = [
        'base' => [
            'merchant_id',
            'app_id',
            'version',
        ],
        'optional'=>[
            'custom_data',
            'lang',
        ],
        "1.0"=>[
            'order_no',
            'order_currency',
            'order_amount',
            'order_items',
            'source_url',
            'asyn_notify_url',
            'syn_notify_url',
            'bill_email',
        ],
        "gateway_1.0"=>[
            'card_no',
            'card_ex_year',
            'card_ex_month',
            'card_cvv',
            'source_ip',
            'bill_firstname',
            'bill_lastname',
        ],
        '2.0'=>[
            'bill_phone',
            'bill_country',
            'bill_state',
            'bill_city',
            'bill_street',
            'bill_zip',
        ],
        '3.0'=>[
            'ship_firstname',
            'ship_lastname',
            'ship_email',
            'ship_phone',
            'ship_country',
            'ship_state',
            'ship_city',
            'ship_street',
            'ship_zip',
        ],
    ];

    //It is not recommended to modify the validation method, we will adjust them through technical notification or version update if they need to be modified.
    const PARAM_VALIDATE_RULE=[
        'merchant_id' => 'number|max:40',
        'app_id' => 'number|max:40',
        'version' => 'in:1.0,2.0,3.0',
        'order_no'=>'alphaDash|length:1,48',
        'order_currency'=>'alpha|length:3|upper',
        'order_amount'=>'float|gt:0|api_amount',
        'source_url'=>'check_url|max:200',
        'asyn_notify_url'=>'check_protocol_url|max:200',
        'syn_notify_url'=>'check_protocol_url|max:200',
        'bill_email'=>'email|max:60',

        'card_no'=> 'check_card',
        'card_ex_month' => 'number|between:1,12|length:2',
        'card_ex_year'  => 'number|between:19,99',
        'card_cvv' => 'number|length:3,5',
        'order_items' => 'max:2000',
        'source_ip' => 'check_ip',

        'bill_firstname' =>'max:200',
        'bill_lastname' =>'max:200',
        'bill_country' => 'alpha|length:2|upper',
        'bill_phone' => 'max:200',
        'bill_city' => 'max:200',
        'bill_street' => 'max:1000',
        'bill_zip'  => 'max:200',

        'ship_firstname' =>'max:200',
        'ship_lastname' =>'max:200',
        'ship_country' => 'alpha|length:2|upper',
        'ship_phone' => 'max:200',
        'ship_city' => 'max:200',
        'ship_street' => 'max:1000',
        'ship_zip' => 'max:200',
        'ship_email' => 'email|max:60',

        'custom_data' => 'max:2000',
        'timestamp' => 'number|length:10',

        'gateway_order_no' => 'number|max:40',
        'refund_no'=>'alphaDash|length:1,48',
        'refund_amount'=>'float|gt:0|api_amount',
        'refund_desc'=> 'max:200',

        'start_datetime' => 'dateFormat:Y-m-d H:i:s',
        'end_datetime' => 'dateFormat:Y-m-d H:i:s',
        'order_status' => 'number',

        'express_company' => 'chsAlphaNum',
        'express_no' => 'chsAlphaNum',

        'page' => 'number',
    ];

    const ERROR_CODE = [
        'CONFIG ERROR' => -101,
        'REQUEST PARAM ERROR' => -102,
        'REQUEST URL ERROR' => -103,
        'REQUEST INTERFACE EXCEPTION' => -104,
    ];

    const RESPONSE_CODE = [
        'SUCCESS' => 0,
        'REQUEST FAIL' => -1,
        'REQUEST ERROR' => -206,
        'INVALID PARAMETER' => -100,
    ];
}