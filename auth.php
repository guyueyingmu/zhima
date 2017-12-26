<?php

$parmars =  $_GET;

if(!$parmars['auth_code']){
    echo 4444;
}

require_once('aop/request/AlipaySystemOauthTokenRequest.php');
$authRequest = new AlipaySystemOauthTokenRequest();

require_once('aop/AopClient.php');
$aopObj = new AopClient();

require_once('zhima.config.php');


foreach($zhimaConf as $key=>$value){
    $aopObj->$key = $value;
}
$aopObj->rsaPrivateKeyFilePath = 'rsa_private_key.pem';
$aopObj->alipayPublicKey       = 'rsa_public_key.pem';


$authRequest->setCode($parmars['auth_code']);
$authRequest->setGrantType('authorization_code');

try{
    $result = $aopObj->execute($authRequest);

}catch (Exception $e){

}
var_dump($result);exit;
$access_token = $result->getAccessToken();
var_dump($access_token);exit;
// $this->getUserScore($access_token);


$this->aopObj->auth_token            = $access_token;
require_once('/aop/request/ZhimaCreditScoreGetRequest.php');

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
    

