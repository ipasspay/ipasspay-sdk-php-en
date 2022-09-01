<?php

//require '../vendor/autoload.php';
//1.If you use Composer to obtained the SDK and does not use any framework, please open the first line code, the reason is the path should be accessible to autoload.php under file named vendor.
//2.If you use zip file package directly, please put the decompressed files named src into the project by yourself and ensure that the SDK package can be referenced correctly.

use Ipasspay\IpasspayChannel\service\IpasspayService;

    //Asynchronous notification(iPasspay will send notification if the order status changes)

    //The asynchronous notification processing example is performed by the server background. If you need to test the asynchronous notification, you can record the result in a log file
    //Different systems record logs in different ways. In this case, standard PHP methods such as error_log are used to record logs. You can view the output in the corresponding PHP log file
    error_log('Received an asynchronous notification from iPasspay');

    //Verify signature, or ignore this step.
    $ipasspay_service=new IpasspayService('sandbox');//Env can be 'live' or 'sandbox'. By default, it is 'live'.

    //Note: If you need to dynamically configure merchant's API information in the program, please use setConfig($config) to change the data configured in the ipasspayconfig.php file
    /*$config['merchant_id']='111111';
    $config['app_id']='222222';
    $config['api_secret']='333333';
    if (!$ipasspay_service->setConfig($config)->verifyNotifyOrder($request_data)) {*/
    if (!$ipasspay_service->verifyNotifyOrder()) {
        //Validation fails
        error_log("Validation Failure");
    } else {
        error_log("Validation Success");
    }
    //Get more info for verify signature, it can also be used for logging, and let iPasspay technicians check it for you.
    error_log('Verify Signature String：'.$ipasspay_service->getVerifySignString());
    error_log('Verify Signature：'.$ipasspay_service->getVerifySign());

    //todo You can use PHP method directly to get the request parameters for your business processing......
    error_log('Asynchronous Notification Data：'.json_encode($_REQUEST));
    //todo You can also use SDK method to get the request parameters for your business processing......
    error_log('The Data in the SDK is：'.json_encode($ipasspay_service->getNotifyData()));
    //--------------------

    //If you confirm that the notification is successfully received, you do not need iPasspay to notify the data again. Please use notifySuccess method or return corresponding data according to the document
    //If an exception occurs during processing, and you need iPasspay to notify the data again, please use notifyFail method, or return the corresponding data according to the document
    echo $ipasspay_service->notifySuccess();
    //echo $ipasspay_service->notifyFail();
    exit;
