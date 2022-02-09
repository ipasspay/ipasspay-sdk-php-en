<?php
namespace Ipasspay\IpasspayChannel\logic;

use Ipasspay\IpasspayChannel\config\IpasspayConstant;

class QueryOrderLogic extends IpasspayChannelCommonLogic
{
    protected $request_url_key = "query_order_url";

    protected $request_data_field = [
        "merchant_id",
        "app_id",
        "order_no",
        "timestamp",
    ];

    //Signature array
    protected $sign_field=[
        "merchant_id",
        "app_id",
        "order_no",
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