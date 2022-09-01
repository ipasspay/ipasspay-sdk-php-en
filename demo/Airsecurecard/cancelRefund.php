<?php

//require '../vendor/autoload.php';
//1.If you use Composer to obtained the SDK and does not use any framework, please open the first line code, the reason is the path should be accessible to autoload.php under file named vendor.
//2.If you use zip file package directly, please put the decompressed files named src into the project by yourself and ensure that the SDK package can be referenced correctly.

use Ipasspay\AirsecurecardChannel\config\AirsecurecardConstant;
use Ipasspay\AirsecurecardChannel\service\AirsecurecardService;

    //Cancel refund endpoint
    //1.There's no need to worry about timestamp, signatures, and verification, the SDK is already integrated, it can be called directly to complete encryption and communication.
    //2.For more details about the parameters, please refer to the API doc：https://www.apihome.dev/airsecurecard.biz/en-us/#api-other-cancel_refund
    //3.Note：merchant_id、app_id、api_secret can be configurations in src/Airsecurecard/AirsecurecardChannel/Config/AirsecurecardConfig.php.

    $request_data['refund_no']='1643275640662'; //Replace your own refund_no
    //------------------------------------

    //Try to initiate a cancel refund request
    $airsecurecard_service=new AirsecurecardService('sandbox');//Env can be 'live' or 'sandbox'. By default, it is 'live'.

    //Note: If you need to dynamically configure merchant's API information in the program, please use setConfig($config) to change the data configured in the airsecurecardconfig.php file
    /*$config['merchant_id']='111111';
    $config['app_id']='222222';
    $config['api_secret']='333333';
    if (!$airsecurecard_service->setConfig($config)->cancelRefund($request_data)) {*/
    if (!$airsecurecard_service->cancelRefund($request_data)) {
        //The request is exception
        echo 'Error Code：'.$airsecurecard_service->getErrorCode()."\n";
        echo 'Error Message：'.$airsecurecard_service->getErrorMsg()."\n";
        exit;
    }

    //The request is successful
    echo 'HTTP Status Code：'.$airsecurecard_service->getResponseHttpStatus()."\n";
    echo 'Response Code：'.$airsecurecard_service->getResponseCode()."\n";
    echo 'Response Message：'.$airsecurecard_service->getResponseMsg()."\n";
    echo 'Response Data：'.json_encode($airsecurecard_service->getResponseData(),JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES)."\n";

    switch ($airsecurecard_service->getResponseCode()) {
        //The response is normal
        case AirsecurecardConstant::RESPONSE_CODE['SUCCESS']:
            $response_data=$airsecurecard_service->getResponseData();
            //todo please use the response data(array) for business processing......
            break;
        case AirsecurecardConstant::RESPONSE_CODE['REQUEST FAIL']:
        case AirsecurecardConstant::RESPONSE_CODE['INVALID PARAMETER']:
        default:
            //The response is abnormal
            echo $airsecurecard_service->getResponseMsg();//Get more info for abnormal
            break;
    }
    //---------------------------------

    //If the request is abnormal, you can get more info by using following methods, it can also be used for logging, and let iPasspay technicians check it for you.
    //echo 'Request Url：'.$airsecurecard_service->getRequestUrl()."\n";
    //echo 'Request Data：'.json_encode($airsecurecard_service->getRequestData(),JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES)."\n";
    //echo 'Response Data：'.json_encode($airsecurecard_service->getResponseOriginData(),JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES)."\n";
    //echo 'Signature String：'.$airsecurecard_service->getSignString()."\n";
    //echo 'Signature：'.$airsecurecard_service->getSign()."\n";
    exit;
