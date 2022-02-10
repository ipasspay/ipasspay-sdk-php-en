<?php
namespace Ipasspay\baseChannel\communication;
//The extension package provides basic abstract classes for writing channel specific extension packages, which is easy to unify standards and writing ideas, and can also integrate some code.

abstract class ChannelCommunication
{
    //Channel communication is responsible for data request and data reception, as well as encryption, decryption, signature and verification, which are non-perceptive to the outside.
    //Add information such as original request data, response data, request url, and whether to make a network request to facilitate service processing and log saving.
    //The communication class in each channel extension package inherits this class and implements its own encryption and decryption, signature and verification, as well as data request and data reception.

    protected $is_send=false;//Is there a channel communication going on? If not, the platform will try the next channel.

    protected $send=true;//When using the getResponse method, is it a real request, it is useful when creating data for the host interface.

    //Request url, provide method output externally.
    protected $request_url = '';
    //Request data, provide method output externally.
    protected $request_data = [];

    //Request origin data，provide method output externally, it is useful in SOAP mode.
    protected $request_origin_data = [];

    //Response origin data，provide method output externally.
    protected $response_origin_data = [];

    protected $error_code = 0;//Error code.
    protected $error_msg = '';//Error message.

    public function setRequestUrl($request_url) {
        $this->request_url=$request_url;
        return $this;
    }

    public function setRequestData($request_data) {
        $this->request_data=$request_data;
        return $this;
    }

    public function setSend($send) {
        $this->send=$send;
        return $this;
    }

    public function getRequestUrl() {
        return $this->request_url;
    }

    public function getRequestData() {
        return $this->request_data;
    }

    public function getRequestOriginData() {
        return $this->request_origin_data;
    }

    public function getResponseOriginData() {
        return $this->response_origin_data;
    }

    public function isSend() {
        return $this->is_send;
    }

    //Interaction
    abstract public function getResponse();

    public function getErrorCode() {
        return $this->error_code;
    }

    public function getErrorMsg() {
        return $this->error_msg;
    }
}