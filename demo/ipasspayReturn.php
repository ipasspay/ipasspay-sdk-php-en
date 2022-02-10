<?php

//require '../vendor/autoload.php';
//1.If you use Composer to obtained the SDK and does not use any framework, please open the first line code, the reason is the path should be accessible to autoload.php under file named vendor.
//2.If you use zip file package directly, please put the decompressed files named src into the project by yourself and ensure that the SDK package can be referenced correctly.


use Ipasspay\IpasspayChannel\service\IpasspayService;

    //Synchronous notification(Page will be redirected to the address after customer payment is completed. )

    //This return page can be provided to users who have completed payment. If necessary, simple information can be displayed on this page according to the order results to improve user experience.
    //However, it is not recommended to do actual transaction business data processing on this page, because this page is directly provided by iPasspay to the browser of payment users for visit, so users may not be accessed, and data security and timeliness cannot be guaranteed.
    echo "Synchronous Response\n";

    //Verify signature, or ignore this step.
    $ipasspay_service=new IpasspayService('sandbox');//Env can be 'live' or 'sandbox'. By default, it is 'live'.

    //Note: If you need to dynamically configure merchant's API information in the program, please use setConfig($config) to change the data configured in the ipasspayconfig.php file
    /*$config['merchant_id']='111111';
    $config['app_id']='222222';
    $config['api_secret']='333333';
    if (!$ipasspay_service->setConfig($config)->verifyNotifyOrder($request_data)) {*/
    if (!$ipasspay_service->verifyNotifyOrder()) {
        //Validation fails
        echo "Validation Failure\n";
    } else {
        echo "Validation Success\n";
    }
    //Get more info for verify signature, it can also be used for logging, and let iPasspay technicians check it for you.
    echo 'Verify Signature String：'.$ipasspay_service->getVerifySignString()."\n";
    echo 'Verify Signature：'.$ipasspay_service->getVerifySign()."\n";

    //todo You can use PHP method directly to get the request parameters for your business processing......(It's not recommended)
    echo 'Synchronous Notification Data：：'.json_encode($_REQUEST)."\n";
    //todo You can also use SDK method to get the request parameters for your business processing......(It's not recommended)
    echo 'The Data in the SDK is：'.json_encode($ipasspay_service->getNotifyData())."\n";
    //--------------------
    exit;
