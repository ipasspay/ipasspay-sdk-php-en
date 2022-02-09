<?php
namespace Ipasspay\IpasspayChannel\logic;

use Ipasspay\IpasspayChannel\config\IpasspayConstant;

class CancelRefundLogic extends IpasspayChannelCommonLogic
{
    protected $request_url_key = "cancel_refund_url";

    protected $request_data_field = [
        "merchant_id",
        "app_id",
        "refund_no",
        "timestamp",
    ];

    //Signature field
    protected $sign_field=[
        "merchant_id",
        "app_id",
        "refund_no",
        "timestamp",
    ];

    public function createData($params)
    {
        $this->request_data['timestamp']=time();
        //Initialize the request data
        if (!$this->setRequestUrl()->createCommonData($params)->appendData()->validateData()) {
            $this->error_code=IpasspayConstant::ERROR_CODE['REQUEST PARAM ERROR'];
            $this->error_msg=$this->validate_obj->getError();
            return false;
        }

        //Signature
        $this->signData();
        return true;
    }
}