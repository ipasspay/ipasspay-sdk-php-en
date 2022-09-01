<?php
namespace Ipasspay\AirsecurecardChannel\logic;

use Ipasspay\baseChannel\logic\ChannelCommonLogic;
use Ipasspay\baseChannel\validate\Validate;
use Ipasspay\AirsecurecardChannel\config\AirsecurecardConstant;

abstract class AirsecurecardChannelCommonLogic extends ChannelCommonLogic
{
    protected $request_url_key='request_url';

    // Get the request url
    protected function setRequestUrl()
    {
        if (isset($this->config[$this->request_url_key])) {
            $this->request_url=$this->config[$this->request_url_key];
        }
        return $this;
    }

    protected function appendData()
    {
        //Some parameters are read from the configuration
        if (isset($this->config['merchant_id'])) {
            $this->request_data['merchant_id']=$this->config['merchant_id'];
        }
        if (isset($this->config['app_id'])) {
            $this->request_data['app_id']=$this->config['app_id'];
        }
        if (isset($this->config['version'])) {
            $this->request_data['version']=$this->config['version'];
        }
        return $this;
    }

    protected function versionCheck() {
        if (!isset($this->config['version'])) {
            $this->error_code=AirsecurecardConstant::ERROR_CODE['CONFIG ERROR'];
            $this->error_msg='no version parameter in config file';
            return false;
        }
        return true;
    }

    protected function validateData() {
        //The verification rules can be configured in AirsecurecardConstant
        //Add require for mandatory data
        $validate_rule=AirsecurecardConstant::PARAM_VALIDATE_RULE;
        foreach ($this->request_data_field as $v){
            if (isset($validate_rule[$v])) {
                $validate_rule[$v]='require|'.$validate_rule[$v];
            } else {
                $validate_rule[$v]='require';
            }
        }
        $this->validate_obj = new Validate();
        return $this->validate_obj->rule($validate_rule)->check($this->request_data);
    }

    // Generate the signature
    public function signData()
    {
        $this->sign_string='';
        foreach ($this->sign_field as $v) {
            if (isset($this->request_data[$v])) {
                $this->sign_string.=$this->request_data[$v];
            }
        }
        $this->sign_string .= $this->getApiSecret();
        $this->sign = $this->request_data['signature'] = hash('sha256', $this->sign_string);
        return $this;
    }

    private function getApiSecret() {
        $api_secret='';
        if (isset($this->config['api_secret'])) {
            $api_secret=$this->config['api_secret'];
        }
        return $api_secret;
    }

    // Verify the signature
    public function verifySign($data)
    {
        if (!isset($data['signature']) || !is_string($data['signature'])) {
            return false;
        }
        $this->verify_sign_string='';
        foreach ($this->verify_sign_field as $v) {
            if (isset($data[$v])) {
                $this->verify_sign_string.=$data[$v];
            }
        }
        $this->verify_sign_string .= $this->getApiSecret();
        $this->verify_sign=hash('sha256', $this->verify_sign_string);
        return $data['signature'] == $this->verify_sign;
    }
}