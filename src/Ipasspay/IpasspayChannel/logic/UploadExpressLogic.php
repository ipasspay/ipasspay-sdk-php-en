<?php
namespace Ipasspay\IpasspayChannel\logic;

use Ipasspay\IpasspayChannel\config\IpasspayConfig;

class UploadExpressLogic extends IpasspayChannelCommonLogic
{
    protected $request_url_key = "upload_express_url";

    protected $request_data_field = [
        "merchant_id",
        "app_id",
        "gateway_order_no",
        "express_company",
        "express_no",
        "timestamp",
    ];

    //Signature array
    protected $sign_field=[
        "merchant_id",
        "app_id",
        "gateway_order_no",
        "express_company",
        "express_no",
        "timestamp",
    ];

    public function createData($params)
    {
        $this->request_data['timestamp']=time();
        //Initialize the request data
        if (!$this->setRequestUrl()->createCommonData($params)->appendData()->validateData()) {
            $this->error_code=IpasspayConfig::ERROR_CODE['REQUEST PARAM ERROR'];
            $this->error_msg=$this->validate_obj->getError();
            return false;
        }

        //Signature
        $this->signData();
        return true;
    }
}