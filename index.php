<?php

$userAuthUrl = "https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?";
$data['app_id']       =  '2017122201059023';
$data['scope']        =  'auth_base';
$data['redirect_uri'] =  'http://zhima.ingdu.cn/credit/creditScoreNotify';
$data['state']        =  '345';
$url = $userAuthUrl.http_build_query($data);
header("Location:".'alipays://platformapi/startapp?appId=20000067&url='$url);
    