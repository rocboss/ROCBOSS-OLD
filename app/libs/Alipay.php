<?php

class Alipay
{
    private $alipayConfig;
    
    private $alipayGatewayNew = 'https://mapi.alipay.com/gateway.do?';
    
    private $httpsVerifyUrl = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
    
    private $httpVerifyUrl = 'http://notify.alipay.com/trade/notify_query.do?';
    
    public function __construct($config)
    {
        $alipayConfig = array(
            'partner' => $config['pid'],
            'key' => $config['key'],
            'sign_type' => strtoupper('MD5'),
            'input_charset' => strtolower('utf-8'),
            'cacert' => $config['cacert'],
            'transport' => $config['transport']
        );

        $this->alipayConfig = $alipayConfig;
    }
    
    public function buildRequestMysign($params)
    {
        $prestr = $this->createLinkstring($params);
        
        $mysign = '';
        
        switch (strtoupper(trim($this->alipayConfig['sign_type'])))
        {
            case 'MD5':
                $mysign = $this->md5Sign($prestr, $this->alipayConfig['key']);
                break;
            
            default:
                break;
        }
        
        return $mysign;
    }
    
    public function buildRequestParams($params)
    {
        $params = $this->filterParams($params);
        
        $params = $this->sortArgument($params);
        
        $mysign = $this->buildRequestMysign($params);
        
        $params['sign'] = $mysign;
        
        $params['sign_type'] = strtoupper(trim($this->alipayConfig['sign_type']));
        
        return $params;
    }
    
    public function buildRequestForm($params, $method, $name)
    {
        $params = $this->buildRequestParams($params);
        
        $sHtml = "<form id='alipaySubmit' style='display: none;' name='alipaySubmit' action='" . $this->alipayGatewayNew . "_input_charset=" . trim(strtolower($this->alipayConfig['input_charset'])) . "' method='" . $method . "'>";
        
        while (list($key, $val) = each($params))
        {
            $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
        }
        
        $sHtml = $sHtml . "<input type='submit' value='" . $name . "'></form>";
        
        $sHtml = $sHtml . "<script>document.forms['alipaySubmit'].submit();</script>";
        
        return $sHtml;
    }
    
    public function buildRequestHttp($params)
    {
        $sResult = '';
        
        $request_data = $this->buildRequestParams($params);
        
        $sResult = $this->getHttpResponsePOST($this->alipayGatewayNew, $this->alipayConfig['cacert'], $request_data, trim(strtolower($this->alipayConfig['input_charset'])));
        
        return $sResult;
    }
    
    public function verifyNotify()
    {
        if (empty($_POST))
        {
            return false;
        }
        else
        {
            $isSign = $this->getSignVeryfy($_POST, $_POST['sign']);
            
            $responseTxt = 'true';
            
            if (!empty($_POST['notify_id']))
            {
                $responseTxt = $this->getResponse($_POST['notify_id']);
            }
            
            
            if (preg_match('/true$/i', $responseTxt) && $isSign)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }
    
    public function verifyReturn()
    {
        if (empty($_GET))
        {
            return false;
        }
        else
        {
            $isSign = $this->getSignVeryfy($_GET, $_GET['sign']);
            
            $responseTxt = 'true';
            
            if (!empty($_GET['notify_id']))
            {
                $responseTxt = $this->getResponse($_GET['notify_id']);
            }
            
            
            if (preg_match('/true$/i', $responseTxt) && $isSign)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }
    
    private function getSignVeryfy($params, $sign)
    {
        $para_filter = $this->filterParams($params);
        
        $para_sort = $this->sortArgument($para_filter);
        
        $prestr = $this->createLinkstring($para_sort);
        
        $isSgin = false;

        switch (strtoupper(trim($this->alipayConfig['sign_type'])))
        {
            case 'MD5':
                $isSgin = $this->md5Verify($prestr, $sign, $this->alipayConfig['key']);
                break;
                
            default:
                $isSgin = false;
        }
        
        return $isSgin;
    }
    
    private function getResponse($notify_id)
    {
        $transport = strtolower(trim($this->alipayConfig['transport']));
        
        $partner = trim($this->alipayConfig['partner']);
        
        $veryfy_url = '';
        
        if ($transport == 'https')
        {
            $veryfy_url = $this->httpsVerifyUrl;
        }
        else
        {
            $veryfy_url = $this->httpVerifyUrl;
        }
        
        $veryfy_url = $veryfy_url . 'partner=' . $partner . '&notify_id=' . $notify_id;
        
        $responseTxt = $this->getHttpResponseGET($veryfy_url, $this->alipayConfig['cacert']);
        
        return $responseTxt;
    }
    
    private function createLinkstring($para)
    {
        $arg = '';

        while (list($key, $val) = each($para))
        {
            $arg .= $key . '=' . $val . '&';
        }

        $arg = substr($arg, 0, count($arg) - 2);
        
        if (get_magic_quotes_gpc())
        {
            $arg = stripslashes($arg);
        }
        
        return $arg;
    }
    
    
    private function filterParams($para)
    {
        $para_filter = array();

        while (list($key, $val) = each($para))
        {
            if ($key == 'sign' || $key == 'sign_type' || $val == '')

                continue;

            else

                $para_filter[$key] = $para[$key];
        }

        return $para_filter;
    }

    private function sortArgument($para)
    {
        ksort($para);

        reset($para);
        
        return $para;
    }
    
    private function getHttpResponsePOST($url, $cacert_url, $para, $input_charset = '')
    {
        
        if (trim($input_charset) != '')
        {
            $url = $url . '_input_charset=' . $input_charset;
        }
        
        $curl = curl_init($url);
        
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        
        curl_setopt($curl, CURLOPT_CAINFO, $cacert_url);
        
        curl_setopt($curl, CURLOPT_HEADER, 0);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        curl_setopt($curl, CURLOPT_POST, true);
        
        curl_setopt($curl, CURLOPT_POSTFIELDS, $para);
        
        $responseText = curl_exec($curl);
        
        curl_close($curl);
        
        return $responseText;
    }
    
    private function getHttpResponseGET($url, $cacert_url)
    {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_HEADER, 0);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        
        curl_setopt($curl, CURLOPT_CAINFO, $cacert_url);

        $responseText = curl_exec($curl);

        curl_close($curl);
        
        return $responseText;
    }
     
    private function md5Sign($prestr, $key)
    {
        $prestr = $prestr . $key;

        return md5($prestr);
    }
    
    private function md5Verify($prestr, $sign, $key)
    {
        $prestr = $prestr . $key;

        $mysgin = md5($prestr);
        
        if ($mysgin == $sign)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
?>