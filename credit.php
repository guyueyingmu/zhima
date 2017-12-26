<?php
echo  4444;exit("OK");
class Credit{

    private $aopObj = NULL;

    private $userAuthUrl = "https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?";

    public function __construct() {
        exit("PL");
        require_once('aop/AopClient.php');
        $this->aopObj = new AopClient();
    }

    public function index(){
        var_dump(1213);exit;
        $data['app_id']       =  '2017122201059023';
        $data['scope']        =  'auth_base';
        $data['redirect_uri'] =  'http://zhima.ingdu.cn/credit/creditScoreNotify';
        $data['state']        =  '345';
        $url = $this->userAuthUrl.http_build_query($data);
        header("Location:".$url);
    }

    //芝麻信用同步回调
    public function creditScoreReturn(){
        $returnData = $_GET;
        error_log(var_export($returnData,true)."\n\r",3,'/tmp/zhima_return.log');
        $result = $this->zhimaObj->getReturnResult($returnData['params'],$returnData['sign']);
        var_dump($result);

    }

    //芝麻信用异步回调
    public function creditScoreNotify(){
        $parmars =  $_GET;

        if(!$parmars['auth_code']){
            return false;
        }
        require_once(LIB_PATH.'/system/libraries/zhima/aop/request/AlipaySystemOauthTokenRequest.php');
        $authRequest = new AlipaySystemOauthTokenRequest();



        $authRequest->setCode($parmars['auth_code']);
        $authRequest->setGrantType('authorization_code');

        try{
            $result = $this->aopObj->execute($authRequest);

        }catch (Exception $e){

        }

        $access_token = $result->getAccessToken();
        $this->getUserScore($access_token);

    }

    public function getUserScore($access_token){
        require_once(LIB_PATH.'/system/libraries/zhima/zhima.config.php');
        require_once(LIB_PATH.'/system/libraries/zhima/aop/request/ZhimaCreditScoreGetRequest.php');


//        print_r($zhimaConf);exit;
        foreach($zhimaConf as $key=>$value){
            $this->aopObj->$key = $value;
        }
        $this->aopObj->rsaPrivateKeyFilePath = 'rsa_private_key.pem';
        $this->aopObj->alipayPublicKey       = 'rsa_public_key.pem';
        $this->aopObj->auth_token            = $access_token;

        $request = new ZhimaCreditScoreGetRequest ();
//        var_dump($request);exit;
        $data['transaction_id'] = "201512100936588040000000465159";
        $data['product_code']   = "w1010100100000000001";

        $request->setBizContent(json_encode($data));

        $result = $this->aopObj->execute ( $request  );
        var_dump($result);exit;

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            echo "成功";
        } else {
            echo "失败";
        }
    }

}