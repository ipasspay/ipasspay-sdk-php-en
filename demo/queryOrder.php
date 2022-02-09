<?php

//require '../vendor/autoload.php';
//1.If you use Composer to obtained the SDK and does not use any framework, please open the first line code, the reason is the path should be accessible to autoload.php under file named vendor.
//2.If you use zip file package directly, please put the decompressed files named src into the project by yourself and ensure that the SDK package can be referenced correctly.


use Ipasspay\IpasspayChannel\config\IpasspayConfig;
use Ipasspay\IpasspayChannel\service\IpasspayService;

    //Order query endpoint
    //1.There's no need to worry about timestamp, signatures, and verification, the SDK is already integrated, it can be called directly to complete encryption and communication.
    //2.For more details about the parameters, please refer to the API doc：https://www.apihome.dev/ipasspay.biz/en-us/#api-other-get_order_info
    //3.Note：merchant_id、app_id、api_secret can be configurations in src/Ipasspay/IpasspayChannel/Config/IpasspayConfig.php.

    $request_data['order_no']='1643273266828';//Replace your own order_no
    //------------------------------------

    //Try to initiate a order query request
    $ipasspay_service=new IpasspayService('sandbox');//Env can be 'live' or 'sandbox'. By default, it is 'live'.
    if (!$ipasspay_service->queryOrder($request_data)) {
        //The request is exception
        echo 'Error Code：'.$ipasspay_service->getErrorCode()."\n";
        echo 'Error Message：'.$ipasspay_service->getErrorMsg()."\n";
        exit;
    }

    //The request is successful
    echo 'HTTP Status Code：'.$ipasspay_service->getResponseHttpStatus()."\n";
    echo 'Response Code：'.$ipasspay_service->getResponseCode()."\n";
    echo 'Response Message：'.$ipasspay_service->getResponseMsg()."\n";
    echo 'Response Data：'.json_encode($ipasspay_service->getResponseData(),JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES)."\n";

    switch ($ipasspay_service->getResponseCode()) {
        case IpasspayConfig::RESPONSE_CODE['SUCCESS']:
            //The response is normal（Note：There is no signature in the response of order query）
            $response_data=$ipasspay_service->getResponseData();
            //todo please use the response data(array) for business processing......
            break;
        case IpasspayConfig::RESPONSE_CODE['REQUEST FAIL']:
        case IpasspayConfig::RESPONSE_CODE['INVALID PARAMETER']:
        default:
            //The response is abnormal
            echo $ipasspay_service->getResponseMsg();//Get more info for abnormal
            break;
    }
    //---------------------------------

    //If the request is abnormal, you can get more info by using following methods, it can also be used for logging, and let iPasspay technicians check it for you.
    //echo 'Request Url：'.$ipasspay_service->getRequestUrl()."\n";
    //echo 'Request Data：'.json_encode($ipasspay_service->getRequestData(),JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES)."\n";
    //echo 'Response Data：'.json_encode($ipasspay_service->getResponseOriginData(),JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES)."\n";
    //echo 'Signature String：'.$ipasspay_service->getSignString()."\n";
    //echo 'Signature：'.$ipasspay_service->getSign()."\n";
    exit;
