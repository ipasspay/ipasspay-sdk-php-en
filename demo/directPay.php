<?php

//require '../vendor/autoload.php';
//1.If you use Composer to obtained the SDK and does not use any framework, please open the first line code, the reason is the path should be accessible to autoload.php under file named vendor.
//2.If you use zip file package directly, please put the decompressed files named src into the project by yourself and ensure that the SDK package can be referenced correctly.

use Ipasspay\IpasspayChannel\config\IpasspayConstant;
use Ipasspay\IpasspayChannel\service\IpasspayService;

    //Payment Gateway(Direct)
    //1.There's no need to worry about signatures, and verification, the SDK is already integrated, it can be called directly to complete encryption and communication.
    //2.For more details about the parameters, please refer to the API doc：https://www.apihome.dev/ipasspay.biz/en-us/#api-payment-pay
    //3.Note：merchant_id、app_id、api_secret can be configurations in src/Ipasspay/IpasspayChannel/Config/IpasspayConfig.php.

    $order_no=time().mt_rand(100,999);//Replace your own order_no
    $request_data['order_no']=$order_no;

    $request_data['order_amount']='12.00';//Replace your own order amount
    $request_data['order_currency'] = 'USD';//Replace your own order currency

    $order_items=[];
    $order_item['goods_name']='something';
    $order_item['quality']=2;
    $order_item['price']='6.00';
    $order_items[]=$order_item;
    $request_data['order_items'] = json_encode($order_items,JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);//Replace your own order items, json format.

    $request_data['bill_email'] = 'test1@ipasspay.com';//Replace your customer's email

    $request_data['source_url'] = 'https://www.yourdomain.com/pay?shopping_cart=123';//Customer's source URL
    //Synchronous notification is accessible on the internet, and only works under the host endpoint, that means the page will be redirected to this syn_notify_url after customer payment is completed. merchant can obtain the parameters from platform through GET method. In general we suggest merchant confirm the transaction final status from asynchronous notification.
    $request_data['syn_notify_url'] = 'https://www.yourdomain.com/ipasspayReturn.php';
    //Asynchronous notification URL, merchant will be notified when order status is changed.
    $request_data['asyn_notify_url'] = 'https://www.yourdomain.com/ipasspayNotify.php';

    //All card info are required for Payment Gateway(Direct)
    $request_data['card_no'] = '5105105105105100';//test card for Non-3DS
    //$request_data['card_no'] = '4048411801551156';//test card for 3DS
    $request_data['card_ex_year'] = '25';
    $request_data['card_ex_month'] = '12';
    $request_data['card_cvv'] = '123';
    //$request_data['source_ip'] = '127.0.0.1';//real customer's ip, we support both IPV4 and IPV6
    $request_data['source_ip'] = '2600:1700:e00:b0c0::41';//IPV6
    $request_data['bill_firstname'] = 'Pay';
    $request_data['bill_lastname'] = 'Ipass';

    //Version2.0 requires send billing info. Refer to the API doc for specific requirements.
    $request_data['bill_phone'] = '13800138000';
    $request_data['bill_country'] = 'US';
    $request_data['bill_state'] = 'AL';
    $request_data['bill_city'] = 'Birmingham';
    $request_data['bill_street'] = 'somewhere';
    $request_data['bill_zip'] = '35201';
    //------------------------------------

    //Try to initiate a direct payment request
    $ipasspay_service=new IpasspayService('sandbox');//Env can be 'live' or 'sandbox'. By default, it is 'live'.
    if (!$ipasspay_service->onlinePay($request_data)) {
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
            //The response is normal
            if (!$ipasspay_service->verifyResponseData()) { //Verify signature, or ignore this step.
                //Validation fails
                echo "Verify Signature Failure\n";
                //Get more info for verify signature, it can also be used for logging, and let iPasspay technicians check it for you.
                echo 'Verify Signature String：'.$ipasspay_service->getVerifySignString()."\n";
                echo 'Verify Signature：'.$ipasspay_service->getVerifySign()."\n";
                echo 'Response Signature：'.$ipasspay_service->getResponseSign()."\n";
                break;
            }
            //-------------

            $response_data=$ipasspay_service->getResponseData();
            //For Payment Gateway(Direct), it is necessary to determine whether there is a need to redirect (for example, the acquiring bank requires 3DS verification or access to the transfer page). If it is, the redirect url should be submitted to the customer's browser for access.
            if ($ipasspay_service->needRedirect()) {
                //When redirect url is required, the final result of the transaction is confirmed by asynchronous notification or order query
                //toPayUrl method will use Header to automatically redirect.
                $ipasspay_service->toPayUrl();
                //You can also use getPayUrl to get the redirect url if you want to process this url by yourself.
                //echo $ipasspay_service->getPayUrl();
                break;
            } else{
                //todo please use the response data(array) for business processing......
                break;
            }
        case IpasspayConstant::RESPONSE_CODE['REQUEST FAIL']:
        case IpasspayConstant::RESPONSE_CODE['REQUEST ERROR']:
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
