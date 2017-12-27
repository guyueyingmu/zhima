<?php
header("Content-type:text/html;charset=utf-8");
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

require_once('aop/request/ZhimaCreditScoreGetRequest.php');

$request = new ZhimaCreditScoreGetRequest ();
//        var_dump($request);exit;
$data['transaction_id'] = time().rand(1,1000);
$data['product_code']   = "w1010100100000000001";

$request->setBizContent(json_encode($data));

$result = $aopObj->execute ( $request ,$access_token );


$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
// var_dump($result);
$resultCode = $result->$responseNode->code;

$redirect_url = "http://www.shopyz.cn/index.php/home/User/zhima?";
if(!empty($resultCode)&&$resultCode == 10000){
    $return['zm_score'] =  $result->$responseNode->zm_score;
    header("Location:".$redirect_url.http_build_query($return));
} else {
    header("Location:".$redirect_url);
}
    

