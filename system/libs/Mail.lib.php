<?php
class Mail
{
    private $_userName;
    private $_password;
    private $_sendServer;
    protected $_port = 25;
    protected $_from;
    protected $_to;
    protected $_subject;
    protected $_body;
    protected $_socket;
    protected $_errorMessage;
    public function setServer($server, $username = "", $password = "", $port = 25)
    {
        $this->_sendServer = $server;
        $this->_port       = $port;
        if (!empty($username))
        {
            $this->_userName = base64_encode($username);
        }
        if (!empty($password))
        {
            $this->_password = base64_encode($password);
        }
        return true;
    }
    public function setFrom($from)
    {
        $this->_from = $from;
        return true;
    }
    public function setReceiver($to)
    {
        $this->_to = $to;
        return true;
    }
    public function setMailInfo($subject, $body)
    {
        $this->_subject = $subject;
        $this->_body    = base64_encode($body);
        if (!empty($attachment))
        {
            $this->_attachment = $attachment;
        }
        return true;
    }
    public function sendMail()
    {
        $command = $this->getCommand();
        $this->socket();
        foreach ($command as $value)
        {
            if ($this->sendCommand($value[0], $value[1]))
            {
                continue;
            }
            else
            {
                return false;
            }
        }
        $this->close();
        return true;
    }
    public function error()
    {
        if (!isset($this->_errorMessage))
        {
            $this->_errorMessage = "";
        }
        return $this->_errorMessage;
    }
    protected function getCommand()
    {
        $separator = "----=_Part_" . md5($this->_from . time()) . uniqid();
        $command   = array(
            array(
                "HELO sendmail\r\n",
                250
            )
        );
        if (!empty($this->_userName))
        {
            $command[] = array(
                "AUTH LOGIN\r\n",
                334
            );
            $command[] = array(
                $this->_userName . "\r\n",
                334
            );
            $command[] = array(
                $this->_password . "\r\n",
                235
            );
        }
        $command[] = array(
            "MAIL FROM: <" . $this->_from . ">\r\n",
            250
        );
        $header    = "FROM: <" . $this->_from . ">\r\n";
        $command[] = array(
            "RCPT TO: <" . $this->_to . ">\r\n",
            250
        );
        $header .= "TO: <" . $this->_to . ">\r\n";
        $header .= "Subject: " . $this->_subject . "\r\n";
        $header .= "Content-Type: multipart/alternative;\r\n";
        $header .= "\t" . 'boundary="' . $separator . '"';
        $header .= "\r\nMIME-Version: 1.0\r\n";
        $header .= "\r\n--" . $separator . "\r\n";
        $header .= "Content-Type:text/html; charset=utf-8\r\n";
        $header .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $header .= $this->_body . "\r\n";
        $header .= "--" . $separator . "\r\n";
        $header .= "\r\n.\r\n";
        $command[] = array(
            "DATA\r\n",
            354
        );
        $command[] = array(
            $header,
            250
        );
        $command[] = array(
            "QUIT\r\n",
            221
        );
        return $command;
    }
    protected function sendCommand($command, $code)
    {
        try
        {
            if (socket_write($this->_socket, $command, strlen($command)))
            {
                if (empty($code))
                {
                    return true;
                }
                $data = trim(socket_read($this->_socket, 1024));
                if ($data)
                {
                    $pattern = "/^" . $code . "/";
                    if (preg_match($pattern, $data))
                    {
                        return true;
                    }
                    else
                    {
                        $this->_errorMessage = "Error:" . $data . "|**| command:";
                        return false;
                    }
                }
                else
                {
                    $this->_errorMessage = "Error:" . socket_strerror(socket_last_error());
                    return false;
                }
            }
            else
            {
                $this->_errorMessage = "Error:" . socket_strerror(socket_last_error());
                return false;
            }
        }
        catch (Exception $e)
        {
            $this->_errorMessage = "Error:" . $e->getMessage();
        }
    }
    private function socket()
    {
        if (!function_exists("socket_create"))
        {
            $this->_errorMessage = "Extension sockets must be enabled";
            return false;
        }
        $this->_socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
        if (!$this->_socket)
        {
            $this->_errorMessage = socket_strerror(socket_last_error());
            return false;
        }
        socket_set_block($this->_socket);
        if (!socket_connect($this->_socket, $this->_sendServer, $this->_port))
        {
            $this->_errorMessage = socket_strerror(socket_last_error());
            return false;
        }
        socket_read($this->_socket, 1024);
        return true;
    }
    private function close()
    {
        if (isset($this->_socket) && is_object($this->_socket))
        {
            $this->_socket->close();
            return true;
        }
        $this->_errorMessage = "No resource can to be close";
        return false;
    }
}
?>