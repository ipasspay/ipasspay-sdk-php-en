<?php

//require '../vendor/autoload.php';
//1.If you use Composer to obtained the SDK and does not use any framework, please open the first line code, the reason is the path should be accessible to autoload.php under file named vendor.
//2.If you use zip file package directly, please put the decompressed files named src into the project by yourself and ensure that the SDK package can be referenced correctly.

use Ipasspay\IpasspayChannel\config\IpasspayConstant;
use Ipasspay\IpasspayChannel\service\IpasspayService;

    //Refund endpoint
    //1.There's no need to worry about signatures, and verification, the SDK is already integrated, it can be called directly to complete encryption and communication.
    //2.For more details about the parameters, please refer to the API doc：https://www.apihome.dev/ipasspay.biz/en-us/#api-other-refund
    //3.Note：merchant_id、app_id、api_secret can be configurations in src/Ipasspay/IpasspayChannel/Config/IpasspayConfig.php.

    $request_data['gateway_order_no']='11024995227413376';//Replace your own gateway_order_no
    $refund_no=time().mt_rand(100,999);//Replace your own refund_no
    $request_data['refund_no']=$refund_no;
    $request_data['refund_amount']='1.00';//Replace your own refund amount
    $request_data['refund_desc']='Test Refund';//Replace your own refund_desc, it is optional.
    //------------------------------------

    //Try to initiate a refund request
    $ipasspay_service=new IpasspayService('sandbox');//Env can be 'live' or 'sandbox'. By default, it is 'live'.

    //Note: If you need to dynamically configure merchant's API information in the program, please use setConfig($config) to change the data configured in the ipasspayconfig.php file
    /*$config['merchant_id']='111111';
    $config['app_id']='222222';
    $config['api_secret']='333333';
    if (!$ipasspay_service->setConfig($config)->refund($request_data)) {*/
    if (!$ipasspay_service->refund($request_data)) {
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
        case IpasspayConstant::RESPONSE_CODE['SUCCESS']:
            //The response is normal（Note：There is no signature in the response of refund）
            $response_data=$ipasspay_service->getResponseData();
            //todo please use the response data(array) for business processing......
            break;
        case IpasspayConstant::RESPONSE_CODE['REQUEST FAIL']:
        case IpasspayConstant::RESPONSE_CODE['INVALID PARAMETER']:
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
