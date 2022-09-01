<?php
namespace Ipasspay\AirsecurecardChannel\logic;

use Ipasspay\AirsecurecardChannel\config\AirsecurecardConstant;

class OnlinePayLogic extends AirsecurecardChannelCommonLogic
{
    protected $request_url_key = "direct_pay_url";

    //Signature array
    protected $sign_field=[
        "merchant_id",
        "app_id",
        "order_no",
        "order_amount",
        "order_currency",
    ];

    //Validate signature array
    protected $verify_sign_field=[
        "merchant_id",
        "app_id",
        "order_no",
        "gateway_order_no",
        "order_currency",
        "order_amount",
        "order_status",
    ];

    public function createData($params)
    {
        if (!$this->versionCheck()) return false;

        //Determine mandatory and optional data based on the version number
        switch ($this->config['version']) {
            case '1.0':
                $this->request_data_field=array_merge(
                    AirsecurecardConstant::PAY_PARAM['base'],
                    AirsecurecardConstant::PAY_PARAM['1.0'],
                    AirsecurecardConstant::PAY_PARAM['gateway_1.0']
                );
                $this->optional_data_field=AirsecurecardConstant::PAY_PARAM['optional'];
                break;
            case '2.0':
                $this->request_data_field=array_merge(
                    AirsecurecardConstant::PAY_PARAM['base'],
                    AirsecurecardConstant::PAY_PARAM['1.0'],
                    AirsecurecardConstant::PAY_PARAM['gateway_1.0'],
                    AirsecurecardConstant::PAY_PARAM['2.0']
                );
                $this->optional_data_field=array_merge(
                    AirsecurecardConstant::PAY_PARAM['optional']
                );
                break;
            case '3.0':
                $this->request_data_field=array_merge(
                    AirsecurecardConstant::PAY_PARAM['base'],
                    AirsecurecardConstant::PAY_PARAM['1.0'],
                    AirsecurecardConstant::PAY_PARAM['gateway_1.0'],
                    AirsecurecardConstant::PAY_PARAM['2.0'],
                    AirsecurecardConstant::PAY_PARAM['3.0']
                );
                $this->optional_data_field=array_merge(
                    AirsecurecardConstant::PAY_PARAM['optional']
                );
                break;
            default:
                $this->error_code=AirsecurecardConstant::ERROR_CODE['CONFIG ERROR'];
                $this->error_msg='Version parameter error';
                return false;
        }

        //Initialize the request data
        if (!$this->setRequestUrl()->createCommonData($params)->appendData()->validateData()) {
            $this->error_code=AirsecurecardConstant::ERROR_CODE['REQUEST PARAM ERROR'];
            $this->error_msg=$this->validate_obj->getError();
            return false;
        }

        //Signature
        $this->signData();
        return true;
    }
}