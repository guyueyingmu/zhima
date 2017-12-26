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
$result = json_decode( json_encode( $result),true);
$access_token = $result['alipay_system_oauth_token_response']['access_token'];

// $this->getUserScore($access_token);


$aopObj->auth_token            = $access_token;
echo $access_token;
require_once('aop/request/ZhimaCreditScoreGetRequest.php');

$request = new ZhimaCreditScoreGetRequest ();
//        var_dump($request);exit;
$data['transaction_id'] = "201512100936588040000000465144";
$data['product_code']   = "w1010100100000000001";

$request->setBizContent(json_encode($data));

$result = $aopObj->execute ( $request  );
var_dump($result);exit;


$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
$resultCode = $result->$responseNode->code;
if(!empty($resultCode)&&$resultCode == 10000){
    echo "success";
} else {
    echo "失败";
}
    

