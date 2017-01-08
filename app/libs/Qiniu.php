<?php

class Qiniu implements \ArrayAccess
{
    const QINIU_UP_HOST = 'http://upload.qiniu.com';
    const QINIU_RS_HOST = 'http://rs.qbox.me';
    const QINIU_RSF_HOST = 'http://rsf.qbox.me';
    const QINIU_FET_HOST = 'http://iovip.qbox.me';
    protected $access_token;
    protected $secret_token;
    protected $domain;
    protected $bucket;
    protected $cache = array();
    protected $aliases = array();
    protected $ch;
    protected $headers;
    protected $options = array();
    protected $response;
    protected $info;
    protected $errno;
    protected $error;

    public function setConfig($config)
    {
        $this->access_token = $config['access_token'];
        $this->secret_token = $config['secret_token'];
        $this->domain = $config['domain'];
        $this->bucket = $config['bucket'];
    }

    # 获取空间名称
    public function getBucket()
    {
        return $this->bucket;
    }

    # 设置空间
    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
    }

    /**
     * 查看指定文件信息。
     * @param  string $key      文件名或者目录+文件名
     * @return Array|boolean     成功返回文件内容，否会返回false.
     */
    public function status($key)
    {
        list($bucket, $key) = $this->parseKey($key);

        if (is_null($bucket)) {
            die('error');
        }
        $url = self::QINIU_RS_HOST . '/stat/' . $this->encode("$bucket:$key");

        $token = $this->accessToken($url);

        $options[CURLOPT_HTTPHEADER] = array(
            'Authorization: QBox ' . $token
        );

        return $this->get($url, $options);
    }

    /**
     * 删除指定文件信息。
     * @param  string $key      文件名或者目录+文件名
     * @return NULL
     */
    public function delete($key)
    {
        list($bucket, $key) = $this->parseKey($key);

        if (is_null($bucket)) {
            die('error');
        }
        $url = self::QINIU_RS_HOST . '/delete/' . $this->encode("$bucket:$key");

        $token                       = $this->accessToken($url);

        $options[CURLOPT_HTTPHEADER] = array(
            'Authorization: QBox ' . $token
        );

        return $this->get($url, $options);
    }

    /**
     * 下载远程文件
     * @param  string $path      远程文件的路径
     * @param  string $key       保存的文件名或者目录+文件名
     * @return Array|boolean     成功返回文件内容，否会返回false.
     */
    public function fetch($path, $key)
    {
        list($bucket, $key) = $this->parseKey($key);

        if (is_null($bucket)) {
            die('error');
        }

        $url = self::QINIU_FET_HOST . '/fetch/' . $this->encode($path) . '/to/' .$this->encode("$bucket:$key");

        $token = $this->accessToken($url);

        $options[CURLOPT_HTTPHEADER] = array(
            'Authorization: QBox ' . $token
        );

        return $this->get($url, $options);
    }

    public function upload($file, $name = null, $token = null)
    {
        if (null === $token) {
            $token = $this->uploadToken($this->bucket.':'.$name);
        }

        if (!file_exists($file)) {
            die('文件不存在，构建一个临时文件');
        }

        $hash  = hash_file('crc32b', $file);

        $array = unpack('N', pack('H*', $hash));

        if (class_exists('\CURLFile')) {
            $file = new \CURLFile(realpath($file));
        } else {
            $file = '@' . realpath($file);
        }

        $postFields = array(
            'token' => $token,
            'file' => $file,
            'key' => $name,
            'crc32' => sprintf('%u', $array[1])
        );

        # 未指定文件名，使用七牛默认的随机文件名
        if (null === $name) {
            unset($postFields['key']);
        }

        $options = array(
            CURLOPT_POSTFIELDS => $postFields
        );

        return $this->get(self::QINIU_UP_HOST, $options);
    }

    public function download($file)
    {
        $baseUrl = $this->makeBaseUrl($this->domain, $file);

        $privateDownloadUrl = $this->makeRequest($baseUrl, null);

        return $privateDownloadUrl;
    }

    protected function makeBaseUrl($domain, $key)
    {
        $keyEsc = str_replace("%2F", "/", rawurlencode($key));

        return "http://$domain/$keyEsc";
    }

    protected function makeRequest($baseUrl, $mac)
    {
        $deadline = 3600;

        $deadline += time();

        $pos = strpos($baseUrl, '?');

        if ($pos !== false) {
            $baseUrl .= '&e=';
        } else {
            $baseUrl .= '?e=';
        }

        $baseUrl .= $deadline;

        $token = $this->sign($baseUrl);

        return "$baseUrl&token=$token";
    }

    protected function sign($data)
    {
        $sign = hash_hmac('sha1', $data, $this->secret_token, true);

        return $this->access_token . ':' . $this->encode($sign);
    }

    protected function parseKey($key)
    {
        $key = $this->getAlias($key);

        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $segments = explode("|", $key);

        if (count($segments) === 1) {
            $this->cache[$key] = array(
                $this->bucket,
                $segments[0]
            );
        } else {
            $temp = implode('|', array_slice($segments, 1));

            $this->cache[$key] = array(
                $segments[0],
                $temp
            );
        }

        return $this->cache[$key];
    }

    public function getAlias($key)
    {
        return isset($this->aliases[$key]) ? $this->aliases[$key] : $key;
    }

    public function uploadToken($config = array(), $deadline = 3600)
    {
        if (is_string($config)) {
            $scope  = $config;

            $config = array();
        } else {
            $scope = $config['scope'];
        }

        $config['scope']    = $scope;

        $config['deadline'] = time() + $deadline;

        foreach ($this->activeUploadSettings($config) as $key => $value) {
            if ($value) {
                $config[$key] = $value;
            }
        }

        $body = json_encode($config);

        $body = $this->encode($body);

        $sign = hash_hmac('sha1', $body, $this->secret_token, true);

        return $this->access_token . ':' . $this->encode($sign) . ':' . $body;
    }

    public function uploadSettings()
    {
        return array(
            'scope',
            'deadline',
            'callbackUrl',
            'callbackBody',
            'returnUrl',
            'returnBody',
            'asyncOps',
            'endUser',
            'exclusive',
            'detectMime',
            'fsizeLimit',
            'saveKey',
            'persistentOps',
            'persistentNotifyUrl'
        );
    }

    protected function activeUploadSettings($array)
    {
        return array_intersect_key($array, array_flip($this->uploadSettings()));
    }

    public function accessToken($url, $body = false)
    {
        $url = parse_url($url);

        $result = '';

        if (isset($url['path'])) {
            $result = $url['path'];
        }

        if (isset($url['query'])) {
            $result .= '?' . $url['query'];
        }

        $result .= "\n";
        if ($body) {
            $result .= $body;
        }

        $sign = hash_hmac('sha1', $result, $this->secret_token, true);

        return $this->access_token . ':' . $this->encode($sign);
    }

    public function get($url, $options = array())
    {
        $this->ch = curl_init();

        $this->options[CURLOPT_URL] = $url;

        $this->options = $options + $this->options;
        # 临时处理逻辑

        return $this->execute();
    }

    protected function execute()
    {
        if (!$this->option(CURLOPT_RETURNTRANSFER)) {
            $this->option(CURLOPT_RETURNTRANSFER, true);
        }

        if (!$this->option(CURLOPT_SSL_VERIFYPEER)) {
            $this->option(CURLOPT_SSL_VERIFYPEER, false);
        }

        if (!$this->option(CURLOPT_SSL_VERIFYHOST)) {
            $this->option(CURLOPT_SSL_VERIFYHOST, false);
        }

        if (!$this->option(CURLOPT_CUSTOMREQUEST)) {
            $this->option(CURLOPT_CUSTOMREQUEST, 'POST');
        }

        if ($this->headers) {
            $this->option(CURLOPT_HTTPHEADER, $this->headers);
        }

        if (class_exists('\CURLFile')) {
            $this->option(CURLOPT_SAFE_UPLOAD, true);
        } else {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                $this->option(CURLOPT_SAFE_UPLOAD, false);
            }
        }

        $this->setupCurlOptions();

        $this->response = curl_exec($this->ch);

        $this->info     = curl_getinfo($this->ch);

        if ($this->response === false) {
            $this->error = curl_error($this->ch);

            $this->errno = curl_errno($this->ch);

            curl_close($this->ch);

            return false;
        } else {
            curl_close($this->ch);

            if ($this->info['content_type'] == 'application/json') {
                $this->response = json_decode($this->response, true);
            }

            return $this->response;
        }
    }
    public function setupCurlOptions()
    {
        curl_setopt_array($this->ch, $this->options);
    }

    public function option($key, $value = null)
    {
        if (is_null($value)) {
            return !isset($this->options[$key]) ? null : $this->options[$key];
        } else {
            $this->options[$key] = $value;

            return $this;
        }
    }

    public function alias($key, $value)
    {
        $this->alias[$key] = $value;
    }

    protected function encode($str)
    {
        $trans = array(
            "+" => "-",
            "/" => "_"
        );
        return strtr(base64_encode($str), $trans);
    }

    public function __get($key)
    {
        return $this->$key;
    }

    public function offsetExists($key)
    {
        # check response;
    }

    public function offsetGet($key)
    {
        return $this->status($key);
    }

    public function offsetSet($key, $value)
    {
        # move or copy
    }

    public function offsetUnset($key)
    {
        return $this->delete($key);
    }
}
