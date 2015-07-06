<?php
# 系统加密类

namespace system\util;

class Secret
{
    public function long2str($v, $w)
    {
        $len = count($v);
        $n   = ($len - 1) << 2;
        if ($w)
        {
            $m = $v[$len - 1];
            if (($m < $n - 3) || ($m > $n))
                return false;
            $n = $m;
        }
        $s = array();
        for ($i = 0; $i < $len; $i++)
        {
            $s[$i] = pack("V", $v[$i]);
        }
        if ($w)
        {
            return substr(join('', $s), 0, $n);
        }
        else
        {
            return join('', $s);
        }
    }
    
    public function str2long($s, $w)
    {
        $v = unpack("V*", $s . str_repeat("\0", (4 - strlen($s) % 4) & 3));
        $v = array_values($v);
        if ($w)
        {
            $v[count($v)] = strlen($s);
        }
        return $v;
    }
    
    public function int32($n)
    {
        while ($n >= 2147483648)
            $n -= 4294967296;
        while ($n <= -2147483649)
            $n += 4294967296;
        return (int) $n;
    }
    
    public function xxtea_encrypt($str, $key)
    {
        if ($str == "")
        {
            return "";
        }
        $v = $this->str2long($str, true);
        $k = $this->str2long($key, false);
        if (count($k) < 4)
        {
            for ($i = count($k); $i < 4; $i++)
            {
                $k[$i] = 0;
            }
        }
        $n     = count($v) - 1;
        $z     = $v[$n];
        $y     = $v[0];
        $delta = 0x9E3779B9;
        $q     = floor(6 + 52 / ($n + 1));
        $sum   = 0;
        while (0 < $q--)
        {
            $sum = $this->int32($sum + $delta);
            $e   = $sum >> 2 & 3;
            for ($p = 0; $p < $n; $p++)
            {
                $y  = $v[$p + 1];
                $mx = $this->int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ $this->int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
                $z  = $v[$p] = $this->int32($v[$p] + $mx);
            }
            $y  = $v[0];
            $mx = $this->int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ $this->int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
            $z  = $v[$n] = $this->int32($v[$n] + $mx);
        }
        return $this->long2str($v, false);
    }
    
    public function xxtea_decrypt($str, $key)
    {
        if ($str == "")
        {
            return "";
        }
        $v = $this->str2long($str, false);
        $k = $this->str2long($key, false);
        if (count($k) < 4)
        {
            for ($i = count($k); $i < 4; $i++)
            {
                $k[$i] = 0;
            }
        }
        $n     = count($v) - 1;
        $z     = $v[$n];
        $y     = $v[0];
        $delta = 0x9E3779B9;
        $q     = floor(6 + 52 / ($n + 1));
        $sum   = $this->int32($q * $delta);
        while ($sum != 0)
        {
            $e = $sum >> 2 & 3;
            for ($p = $n; $p > 0; $p--)
            {
                $z  = $v[$p - 1];
                $mx = $this->int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ $this->int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
                $y  = $v[$p] = $this->int32($v[$p] - $mx);
            }
            $z   = $v[$n];
            $mx  = $this->int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ $this->int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
            $y   = $v[0] = $this->int32($v[0] - $mx);
            $sum = $this->int32($sum - $delta);
        }
        return $this->long2str($v, true);
    }
    
    public function encrypt($txt, $key = 'abcd9667676effff')
    {
        $s = urlencode(base64_encode($this->xxtea_encrypt($txt, $key)));
        $s = str_replace('%2F', '%252F', $s);
        return $s;
    }
    
    public function decrypt($txt, $key = 'abcd9667676effff')
    {
        $txt = str_replace('%252F', '%2F', $txt);
        return $this->xxtea_decrypt(base64_decode(urldecode($txt)), $key);
    }
}
?>