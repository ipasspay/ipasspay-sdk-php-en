<?php
namespace Ipasspay\baseChannel\logic;
//The extension package provides basic abstract classes for writing channel specific extension packages, which is easy to unify standards and writing ideas, and can also integrate some code.

use Ipasspay\baseChannel\validate\Validate;

abstract class ChannelCommonLogic
{
    //The business logic class or business logic base class in each channel extension package inherits this class

    protected $request_data_field=[];//Mandatory field
    protected $optional_data_field=[];//Optional field

    /* @var Validate */
    protected $validate_obj=null;//Validation class

    protected $request_url = '';
    protected $request_data = [];
    protected $config=[];

    protected $notify_data = [];//Notification data

    protected $sign_field= [];
    protected $sign_string= '';
    protected $sign= '';

    protected $verify_sign_field= [];
    protected $verify_sign_string= '';
    protected $verify_sign= '';

    protected $error_code = 0;//Error code
    protected $error_msg = '';//Error message

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    public function getRequestUrl() {
        return $this->request_url;
    }

    public function getRequestData() {
        return $this->request_data;
    }

    public function getErrorCode() {
        return $this->error_code;
    }

    public function getErrorMsg() {
        return $this->error_msg;
    }

    abstract public function createData($params);//Request data encapsulation, implemented by subclasses.

    //The normal method of generating request data, using request_data_field to construct request_data on the incoming data, currently it is the most basic processing
    public function createCommonData($params)
    {
        //Filter data other than $request_datA_field and $optional_datA_field, and use trim method to process the string data
        if (is_array($params)) {
            foreach ($params as $k=>$v) {
                if (in_array($k,$this->request_data_field) || in_array($k,$this->optional_data_field)) {
                    if (is_string($v)) {
                        $param_value=trim($v);
                    } else {
                        $param_value=$v;
                    }
                    if ($param_value!==null && $param_value!=='') {
                        $this->request_data[$k]=$param_value;
                    }
                }
            }
        }
        return $this;
    }

    abstract public function signData();//Signature, implemented by subclasses.

    public function getSignString() {
        return $this->sign_string;
    }

    public function getSign() {
        return $this->sign;
    }

    abstract public function verifySign($data);//Signature verification

    public function getVerifySignString() {
        return $this->verify_sign_string;
    }

    public function getVerifySign() {
        return $this->verify_sign;
    }

    public function getNotifyData() {
        return $this->notify_data;
    }
}