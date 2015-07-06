<?php
# 对应于Request，它代表了一个HTTP响应。这个对象包括了返回头，HTTP状态码和返回体。

namespace system\net;

class Response
{
    protected $status = 200;
    
    protected $headers = array();
    
    protected $body;
    
    public static $codes = array(100 => 'Continue', 101 => 'Switching Protocols', 102 => 'Processing', 200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content', 207 => 'Multi-Status', 208 => 'Already Reported', 226 => 'IM Used', 300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', 306 => '(Unused)', 307 => 'Temporary Redirect', 308 => 'Permanent Redirect', 400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Timeout', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Payload Too Large', 414 => 'URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Range Not Satisfiable', 417 => 'Expectation Failed', 422 => 'Unprocessable Entity', 423 => 'Locked', 424 => 'Failed Dependency', 426 => 'Upgrade Required', 428 => 'Precondition Required', 429 => 'Too Many Requests', 431 => 'Request Header Fields Too Large', 500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported', 506 => 'Variant Also Negotiates', 507 => 'Insufficient Storage', 508 => 'Loop Detected', 510 => 'Not Extended', 511 => 'Network Authentication Required');
    
    public function status($code = null)
    {
        if ($code === null)
        {
            return $this->status;
        }
        
        if (array_key_exists($code, self::$codes))
        {
            $this->status = $code;
        }
        else
        {
            throw new \Exception('Invalid status code.');
        }
        
        return $this;
    }
    
    public function header($name, $value = null)
    {
        if (is_array($name))
        {
            foreach ($name as $k => $v)
            {
                $this->headers[$k] = $v;
            }
        }
        else
        {
            $this->headers[$name] = $value;
        }
        
        return $this;
    }
    
    public function write($str)
    {
        $this->body .= $str;
        
        return $this;
    }
    
    public function clear()
    {
        $this->status  = 200;
        
        $this->headers = array();
        
        $this->body    = '';
        
        return $this;
    }
    
    public function cache($expires)
    {
        if ($expires === false)
        {
            $this->headers['Expires']       = 'Mon, 26 Jul 1997 05:00:00 GMT';
            
            $this->headers['Cache-Control'] = array(
                'no-store, no-cache, must-revalidate',
                'post-check=0, pre-check=0',
                'max-age=0'
            );
            
            $this->headers['Pragma']        = 'no-cache';
        }
        else
        {
            $expires                        = is_int($expires) ? $expires : strtotime($expires);
            
            $this->headers['Expires']       = gmdate('D, d M Y H:i:s', $expires) . ' GMT';
            
            $this->headers['Cache-Control'] = 'max-age=' . ($expires - time());
            
            if (isset($this->headers['Pragma']) && $this->headers['Pragma'] == 'no-cache')
            {
                unset($this->headers['Pragma']);
            }
        }
        return $this;
    }
    
    public function sendHeaders()
    {
        if (strpos(php_sapi_name(), 'cgi') !== false)
        {
            header(sprintf('Status: %d %s', $this->status, self::$codes[$this->status]), true);
        }
        else
        {
            header(sprintf('%s %d %s', (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1'), $this->status, self::$codes[$this->status]), true, $this->status);
        }
        
        foreach ($this->headers as $field => $value)
        {
            if (is_array($value))
            {
                foreach ($value as $v)
                {
                    header($field . ': ' . $v, false);
                }
            }
            else
            {
                header($field . ': ' . $value);
            }
        }
        
        if (($length = strlen($this->body)) > 0)
        {
            header('Content-Length: ' . $length);
        }
        
        return $this;
    }
    
    public function send()
    {
        if (ob_get_length() > 0)
        {
            ob_end_clean();
        }
        
        if (!headers_sent())
        {
            $this->sendHeaders();
        }
        
        exit($this->body);
    }
}
?>