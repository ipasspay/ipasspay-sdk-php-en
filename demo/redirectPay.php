<?php

//require '../vendor/autoload.php';
//1.If you use Composer to obtained the SDK and does not use any framework, please open the first line code, the reason is the path should be accessible to autoload.php under file named vendor.
//2.If you use zip file package directly, please put the decompressed files named src into the project by yourself and ensure that the SDK package can be referenced correctly.


use Ipasspay\IpasspayChannel\service\IpasspayService;

    //Payment Gateway(Host)
    //1.There's no need to worry about timestamp, signatures, and verification, the SDK is already integrated, it can be called directly to complete encryption and communication.
    //2.For more details about the parameters, please refer to the API doc：https://www.apihome.dev/ipasspay.biz/en-us/#api-other-get_order_list
    //3.Note：merchant_id、app_id、api_secret can be configurations in src/Ipasspay/IpasspayChannel/Config/IpasspayConfig.php.

    $order_no=time().mt_rand(100,999);//Replace your own order_no
    $request_data['order_no']=$order_no;

    $request_data['order_amount']='12.00';//Replace your own order_amount
    $request_data['order_currency'] = 'USD';//Replace your own order_currency

    $order_items=[];
    $order_item['goods_name']='something';
    $order_item['quality']=2;
    $order_item['price']='6.00';
    $order_items[]=$order_item;
    $request_data['order_items'] = json_encode($order_items,JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);//Replace your own order items, json format.

    $request_data['bill_email'] = 'test1@ipasspay.com';//Replace your customer's email

    $request_data['source_url'] = 'http://www.yourdomain.com/pay?shopping_cart=123';//Customer's source URL
    //Synchronous notification is accessible on the internet, and only works under the host endpoint, that means the page will be redirected to this syn_notify_url after customer payment is completed. merchant can obtain the parameters from platform through GET method. In general we suggest merchant confirm the transaction final status from asynchronous notification.
    $request_data['syn_notify_url'] = 'https://www.yourdomain.com/ipasspayReturn.php';
    //Asynchronous notification URL, merchant will be notified when order status is changed.
    $request_data['asyn_notify_url'] = 'https://www.yourdomain.com/ipasspayNotify.php';
    //------------------------------------

    //Try to initiate a host payment request
    $ipasspay_service=new IpasspayService('sandbox');//Env can be 'live' or 'sandbox'. By default, it is 'live'.

    //Note: If you need to dynamically configure merchant's API information in the program, please use setConfig($config) to change the data configured in the ipasspayconfig.php file
    /*$config['merchant_id']='111111';
    $config['app_id']='222222';
    $config['api_secret']='333333';
    if (!$ipasspay_service->setConfig($config)->onlinePayRedirect($request_data)) {*/
    if (!$ipasspay_service->onlinePayRedirect($request_data)) {
        //The request is exception
        echo 'Error Code：'.$ipasspay_service->getErrorCode()."\n";
        echo 'Error Message：'.$ipasspay_service->getErrorMsg()."\n";
        exit;
    }

    //If you need to get the redirect url, please use redirectByGet(false) method to get it.
    /*$redirect_url=$ipasspay_service->redirectByGet(false);
    echo 'Redirect url：'.$redirect_url;
    if ($redirect_url===false) {
         //The request is exception
        echo 'Error Code：'.$ipasspay_service->getErrorCode()."\n";
        echo 'Error Message：'.$ipasspay_service->getErrorMsg()."\n";
        exit;
    }*/

    //If you need to use the SDK for automatic redirection, both the redirectByGet and redirectByPost methods can be used to redirect iPasspay's checkout page.
    if (!$ipasspay_service->redirectByPost()) {
        //The request is exception
        echo 'Error Code：'.$ipasspay_service->getErrorCode()."\n";
        echo 'Error Message：'.$ipasspay_service->getErrorMsg()."\n";
        exit;
    }
    //---------------------------------

    //If the request is abnormal, you can get more info by using following methods, it can also be used for logging, and let iPasspay technicians check it for you.
    //echo 'Request Url：'.$ipasspay_service->getRequestUrl()."\n";
    //echo 'Request Data：'.json_encode($ipasspay_service->getRequestData(),JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES)."\n";
    //echo 'Signature String：'.$ipasspay_service->getSignString()."\n";
    //echo 'Signature：'.$ipasspay_service->getSign()."\n";
    exit;
