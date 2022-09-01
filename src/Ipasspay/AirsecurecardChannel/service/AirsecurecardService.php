<?php
namespace Ipasspay\AirsecurecardChannel\service;

use Ipasspay\baseChannel\validate\Validate;
use Ipasspay\AirsecurecardChannel\config\AirsecurecardConfig;
use Ipasspay\AirsecurecardChannel\config\AirsecurecardConstant;
use Ipasspay\AirsecurecardChannel\logic\CancelRefundLogic;
use Ipasspay\AirsecurecardChannel\logic\NotifyOrderLogic;
use Ipasspay\AirsecurecardChannel\logic\OnlinePayRedirectLogic;
use Ipasspay\baseChannel\service\ChannelService;
use Ipasspay\AirsecurecardChannel\communication\AirsecurecardChannelCommunication;
use Ipasspay\AirsecurecardChannel\logic\OnlinePayLogic;
use Ipasspay\AirsecurecardChannel\logic\QueryOrderListLogic;
use Ipasspay\AirsecurecardChannel\logic\QueryOrderLogic;
use Ipasspay\AirsecurecardChannel\logic\RefundLogic;
use Ipasspay\AirsecurecardChannel\logic\UploadExpressLogic;

class AirsecurecardService extends ChannelService
{
    protected $pay_url='';

    public function __construct($env='live')
    {
        $env_config=AirsecurecardConfig::ENV_CONFIG;
        if (isset($env_config[$env])) {
            $this->config=AirsecurecardConfig::ENV_CONFIG[$env];
        } else {
            $this->config=AirsecurecardConfig::ENV_CONFIG['live'];
        }
        $this->handler = new AirsecurecardChannelCommunication();
    }

    public function onlinePay($request_param)
    {
        $this->logic_obj = new OnlinePayLogic($this->config);
        return $this->deal($request_param);
    }

    public function onlinePayRedirect($request_param)
    {
        $this->logic_obj = new OnlinePayRedirectLogic($this->config);
        return $this->deal($request_param,false);
    }

    public function verifyNotifyOrder()
    {
        $this->logic_obj = new NotifyOrderLogic($this->config);
        $this->logic_obj->createData($_REQUEST);
        return $this->logic_obj->verifySign($_REQUEST);
    }

    public function queryOrder($request_param)
    {
        $this->logic_obj = new QueryOrderLogic($this->config);
        return $this->deal($request_param);
    }

    public function queryOrderList($request_param)
    {
        $this->logic_obj = new QueryOrderListLogic($this->config);
        return $this->deal($request_param);
    }

    public function refund($request_param)
    {
        $this->logic_obj = new RefundLogic($this->config);
        return $this->deal($request_param);
    }

    public function cancelRefund($request_param)
    {
        $this->logic_obj = new CancelRefundLogic($this->config);
        return $this->deal($request_param);
    }

    public function uploadExpress($request_param)
    {
        $this->logic_obj = new UploadExpressLogic($this->config);
        return $this->deal($request_param);
    }

    public function redirectByGet($auto_redirect=true) {
        //Encapsulate the request url for the Host request
        $parameterString = '';
        $request_data=$this->handler->getRequestData();
        if (is_array($request_data) && count($request_data) != 0) {
            $parameterString = '?' . http_build_query($request_data, null, '&');
        }
        $redirect_pay = $this->handler->getRequestUrl() . $parameterString;

        if (is_string($redirect_pay) && $redirect_pay!='') {
            if ($auto_redirect) {
                Header("Location:".$redirect_pay);
                return true;
            } else {
                return $redirect_pay;
            }
        } else {
            $this->error_code=AirsecurecardConstant::ERROR_CODE['REQUEST URL ERROR'];
            $this->error_msg='GET request url error';
            return false;
        }
    }

    public function redirectByPost() {
        $redirect_url=$this->handler->getRequestUrl();
        if (is_string($redirect_url) && $redirect_url!='') {
            echo $this->htmlRequest($redirect_url,$this->handler->getRequestData());
            return true;
        } else {
            $this->error_code=AirsecurecardConstant::ERROR_CODE['REQUEST URL ERROR'];
            $this->error_msg='GET request url error';
            return false;
        }
    }

    public function getSignString() {
        return $this->logic_obj->getSignString();
    }

    public function getSign() {
        return $this->logic_obj->getSign();
    }

    public function getResponseHttpStatus() {
        $response_origin_data=$this->handler->getResponseOriginData();
        if (isset($response_origin_data['status'])) {
            return $response_origin_data['status'];
        }
        return false;
    }

    private function getRespnseContent() {
        $response_origin_data=$this->handler->getResponseOriginData();
        if (isset($response_origin_data['content']) && is_string($response_origin_data['content'])) {
            return json_decode($response_origin_data['content'],true);
        }
        return [];
    }

    public function getResponseCode() {
        $content=$this->getRespnseContent();
        if (isset($content['errcode'])) {
            return $content['errcode'];
        }
        return AirsecurecardConstant::RESPONSE_CODE['REQUEST FAIL'];
    }

    public function getResponseMsg() {
        $content=$this->getRespnseContent();
        if (isset($content['errmsg'])) {
            return $content['errmsg'];
        }
        return '';
    }

    public function getResponseData() {
        $content=$this->getRespnseContent();
        if (isset($content['data'])) {
            return $content['data'];
        }
        return [];
    }

    public function verifyResponseData() {
        $content=$this->getRespnseContent();
        if (isset($content['data'])) {
            return $this->logic_obj->verifySign($content['data']);
        }
        return false;
    }

    public function getResponseSign() {
        $content=$this->getRespnseContent();
        if (isset($content['data']['signature'])) {
            return $content['data']['signature'];
        }
        return '';
    }

    public function needRedirect() {
        $content=$this->getRespnseContent();
        if (isset($content['data']['pay_url']) && $content['data']['pay_url']!='') {
            //Verify the validity of the pay_url
            $this->pay_url=$data['pay_url']=urldecode($content['data']['pay_url']);
            $this->validate_obj = new Validate();
            return $this->validate_obj->rule('pay_url','check_protocol_url')->check($data);
        }
        return false;
    }

    public function toPayUrl() {
        Header("Location:".$this->pay_url);
    }

    public function getPayUrl() {
        return $this->pay_url;
    }

    public function getVerifySignString() {
        return $this->logic_obj->getVerifySignString();
    }

    public function getVerifySign() {
        return $this->logic_obj->getVerifySign();
    }

    public function getNotifyData() {
        return $this->logic_obj->getNotifyData();
    }

    public function notifySuccess() {
        return 'success';
    }

    public function notifyFail() {
        return 'fail';
    }
}