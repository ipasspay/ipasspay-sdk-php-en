<?php
namespace Ipasspay\IpasspayChannel\communication;

use Ipasspay\baseChannel\communication\ChannelCommunication;
use Ipasspay\baseChannel\tools\Curl;
use Ipasspay\IpasspayChannel\config\IpasspayConfig;

class IpasspayChannelCommunication extends ChannelCommunication
{
    //Interaction
    public function getResponse()
    {
        if ($this->send) {
            //This step is equivalent to Curl request, which should indicate that the channel has been communicated with before launching
            $this->is_send = true;

            $this->response_origin_data = Curl::to($this->request_url)
                ->setRetryTimes(0)
                ->withTimeout(180)
                ->withData($this->request_data)
                ->returnResponseArray()
                ->post();

            if (is_array($this->response_origin_data) && isset($this->response_origin_data['status']) && isset($this->response_origin_data['content'])) {
                return true;
            }

            $this->error_code=IpasspayConfig::ERROR_CODE['REQUEST INTERFACE EXCEPTION'];
            $this->error_msg='abnormal response data';
            return false;
        } else {
            return true;
        }
    }
}