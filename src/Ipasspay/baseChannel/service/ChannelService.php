<?php
namespace Ipasspay\baseChannel\service;
//The extension package provides basic abstract classes for writing channel specific extension packages, which is easy to unify standards and writing ideas, and can also integrate some code.

use Ipasspay\baseChannel\communication\ChannelCommunication;
use Ipasspay\baseChannel\logic\ChannelCommonLogic;

abstract class ChannelService
{
    /* @var ChannelCommunication $handler */
    protected $handler;//Communication processor object
    protected $config;//Channel configuration

    protected $error_code = 0;//Error code
    protected $error_msg = '';//Error message

    /* @var ChannelCommonLogic $logic_obj */
    protected $logic_obj;//Business logic object

    public function isSend() {
        return $this->handler->isSend();
    }

    public function getRequestUrl() {
        return $this->handler->getRequestUrl();
    }

    public function getRequestData() {
        return $this->handler->getRequestData();
    }

    public function getRequestOriginData() {
        return $this->handler->getRequestOriginData();
    }

    public function getResponseOriginData() {
        return $this->handler->getResponseOriginData();
    }

    public function getLogic() {
        return $this->logic_obj;
    }

    public function getErrorCode() {
        return $this->error_code;
    }

    public function getErrorMsg() {
        return $this->error_msg;
    }

    protected function deal($params,$send=true)
    {
        //Create request data
        $request_data = $this->logic_obj->createData($params);
        if (!$request_data) {
            $this->error_code = $this->logic_obj->getErrorCode();
            $this->error_msg = $this->logic_obj->getErrorMsg();
            return false;
        }
        //Data encapsulation succeeds, and try to set parameters
        if (!$this->handler
            ->setRequestUrl($this->logic_obj->getRequestUrl())
            ->setRequestData($this->logic_obj->getRequestData())
            ->setSend($send)
            ->getResponse()) {
            $this->error_code = $this->handler->getErrorCode();
            $this->error_msg = $this->handler->getErrorMsg();
            return false;
        }
        return true;
    }

    //Try a form request
    protected function htmlRequest($post_url,$params)
    {
        $html_content='<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body><form id="autoRedirectForm" name="autoRedirectForm" action="'.$post_url.'" method="post">';
        foreach ($params as $key=>$value) {
            $html_content.='<input type="hidden" name="'.$key.'" value=\''.$value.'\'>';
        }

        $html_content.='</form>
                        <script type="text/javascript">
                          function load_submit(){document.autoRedirectForm.submit()}
                          load_submit();
                        </script>
                      </body>';
        return $html_content;
    }
}