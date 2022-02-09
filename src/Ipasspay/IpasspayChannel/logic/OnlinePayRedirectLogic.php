<?php
namespace Ipasspay\IpasspayChannel\logic;

use Ipasspay\IpasspayChannel\config\IpasspayConfig;

class OnlinePayRedirectLogic extends IpasspayChannelCommonLogic
{
    protected $request_url_key = "redirect_pay_url";

    //Signature array, iPasspay unique, no public property
    protected $sign_field=[
        "merchant_id",
        "app_id",
        "order_no",
        "order_amount",
        "order_currency",
    ];

    public function createData($params)
    {
        if (!$this->versionCheck()) return false;

        //Determine mandatory and optional data based on the version number
        switch ($this->config['version']) {
            case '1.0':
                $this->request_data_field=array_merge(IpasspayConfig::PAY_PARAM['base'],IpasspayConfig::PAY_PARAM['1.0']);
                $this->optional_data_field=IpasspayConfig::PAY_PARAM['optional'];
                break;
            case '2.0':
                $this->request_data_field=array_merge(IpasspayConfig::PAY_PARAM['base'],IpasspayConfig::PAY_PARAM['1.0']);
                $this->optional_data_field=array_merge(IpasspayConfig::PAY_PARAM['optional'],IpasspayConfig::PAY_PARAM['2.0']);
                break;
            case '3.0':
                $this->request_data_field=array_merge(IpasspayConfig::PAY_PARAM['base'],IpasspayConfig::PAY_PARAM['1.0']);
                $this->optional_data_field=array_merge(
                    IpasspayConfig::PAY_PARAM['optional'],
                    IpasspayConfig::PAY_PARAM['2.0'],
                    IpasspayConfig::PAY_PARAM['3.0']
                );
                break;
            default:
                $this->error_code=IpasspayConfig::ERROR_CODE['CONFIG ERROR'];
                $this->error_msg='Version parameter error';
                return false;
        }

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