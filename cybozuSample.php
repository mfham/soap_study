<?php

# 通常のSOAPだとうまくいかない。
class CybozuSoapClient extends SoapClient
{
    public function __doRequest($request, $location, $action, $version, $one_way = 0) {
        $request = preg_replace('/<env:Action/', '<Action', $request);
        $request = preg_replace('/<\/env:Action/', '</Action', $request);
        $request = preg_replace('/<env:Timestamp/', '<Timestamp', $request);
        $request = preg_replace('/<\/env:Timestamp/', '</Timestamp', $request);
        return parent::__doRequest($request, $location, $action, $version, $one_way);
    }
}

# wsdl指定
$wsdl = 'http://URL/grn.cgi?WSDL';
$client = new CybozuSoapClient(
    $wsdl,
    array(
        'trace' => 1,
        'soap_version' => SOAP_1_2
    )
);

$ns = 'http://www.w3.org/2003/05/soap-envelope';
$authHeader = new stdClass();
$authHeader->Created = "2015-12-09T14:45:00Z";
$authHeader->Expires = "2037-08-12T14:45:00Z";

$headers = array();
$headers[] = new SOAPHeader($ns, 'Action', 'UtilLogin', true);
$headers[] = new SOAPHeader($ns, 'Security', '', true);
$headers[] = new SOAPHeader($ns, 'Timestamp', $authHeader, true);

$client->__setSoapHeaders($headers);

# ID, PASSWORDを使ってAPIコール
# ログインAPI
$params = array();
$params[] = new SoapVar('login_name_sample', XSD_STRING, null, null, 'login_name');
$params[] = new SoapVar('password_sample', XSD_STRING, null, null, 'password');
$p = new SoapVar($params, SOAP_ENC_OBJECT);

try {
    # ログイン
    $result = $client->UtilLogin($p);
    # セッションID取得
    $session_id = explode('=', explode(';', $result->cookie)[0])[1];

    # CBSESSIDを使ってのAPIコール
    $client2 = new CybozuSoapClient(
        $wsdl,
        array(
            'trace' => 1,
            'soap_version' => SOAP_1_2
        )
    );
    $client2->__setCookie('CBSESSID', $session_id);

    $authHeader = new stdClass();
    $authHeader->Created = "2015-12-09T14:45:00Z";
    $authHeader->Expires = "2037-08-12T14:45:00Z";
    $headers = array();
    $headers[] = new SOAPHeader($ns, 'Action', 'ScheduleGetEvents', true);
    $headers[] = new SOAPHeader($ns, 'Security', '', true);
    $headers[] = new SOAPHeader($ns, 'Timestamp', $authHeader, true);
    $client2->__setSoapHeaders($headers);

    # パラメーターの例
    # <parameters start="2010-07-01T08:00:00" end="2010-07-03T20:00:00"> </parameters>
    # http://php.net/manual/ja/soapparam.soapparam.php
    $p = array(
        '_' => '',
        'start' => "2016-06-02T08:00:00",
        'end' => "2016-06-04T20:00:00"
    );
    var_dump($client2->ScheduleGetEvents($p));


#    var_dump($client2->__getLastRequestHeaders());
#    var_dump($client2->__getLastRequest());
#    var_dump($client2->__getLastResponse());
} catch (SoapFault $e) {
#    var_dump($e);
#    var_dump($client2->__getLastRequestHeaders());
#    var_dump($client2->__getLastResponse());
#    var_dump($client2->__getLastRequest());
}
