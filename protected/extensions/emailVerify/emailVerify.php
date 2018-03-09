<?php 

/**
 * 检测一个邮件地址是否真实存在
 */
class emailVerify {
    public $debug = FALSE;
    public $time_out = 30;
    public $host_name = '202.198.133.191';
    public $smtp_port = 25;
    public $log_str = "";
    public $from = '';
    public $sock;
    public function __construct($from = "", $smtp_port = 25) {
        $this->smtp_port = $smtp_port;
        $this->from = $from;
        $this->time_out = 30; //is used in fsockopen()
        $this->sock = FALSE;
    }
    public function check_mail($address) {
        $this->log_write("BEGINING CHECK address: ". $address . "\n");
        if(!$this->is_email($address)) {
            $this->log_write("not an email address.\n");
            return FALSE;
        }
        //
        if (TRUE !== $this->smtp_sockopen($address)) {
            return FALSE;
        }
        if (!$this->smtp_putcmd("HELO", $this->host_name)) {
            $this->log_write("sending HELO command\n");
            return FALSE;
        }
        if (!$this->smtp_putcmd("MAIL", "FROM:<" . $this->from . ">")) {
            $this->log_write("sending MAIL FROM command\n");
            return FALSE;
        }
        if (!$this->smtp_putcmd("RCPT", "TO:<" . $address . ">")) {
            $this->log_write("sending MAIL TO command\n");
            return FALSE;
        }
        return TRUE;
    }
    
    private function is_email($address) {
        return preg_match("/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i",$address);
    }
    private function smtp_sockopen($address) {
        return $this->smtp_sockopen_mx($address);
    }
    private function smtp_sockopen_mx($address) {
        $domain = preg_replace("/^.+@([^@]+)$/i", "$1", $address);
        if (!@getmxrr($domain, $MXHOSTS)) {
            $this->log_write("Error: Cannot resolve MX \"" . $domain . "\"\n");
            return FALSE;
        }
        foreach ($MXHOSTS as $host) {
            $this->log_write("Trying to " . $host . ":" . $this->smtp_port . "\n");
            $this->sock = @fsockopen($host, $this->smtp_port, $errno, $errstr, $this->time_out);
            if (!($this->sock && $this->smtp_ok())) {
                $this->log_write("Warning: Cannot connect to mx host " . $host . "\n");
                $this->log_write("Error: " . $errstr . " (" . $errno . ")\n");
                continue;
            }
            $this->log_write("Connected to mx host " . $host . "\n");
            return TRUE;
        }
        $this->log_write("Error: Cannot connect to any mx hosts (" . implode(", ", $MXHOSTS) . ")\n");
        return FALSE;
    }
    private function smtp_putcmd($cmd, $arg = "") {
        if ($arg != "") {
            if ($cmd == "")
                $cmd = $arg;
            else
                $cmd = $cmd . " " . $arg;
        }
        fputs($this->sock, $cmd . "\r\n");
        $this->smtp_debug("> " . $cmd . "\n");
        return $this->smtp_ok();
    }
    private function smtp_ok() {
        $response = str_replace("\r\n", "", fgets($this->sock, 512));
        $this->smtp_debug($response . "\n\n");
        if (!ereg("^[23]", $response)) {
            fputs($this->sock, "QUIT\r\n");
            fgets($this->sock, 512);
            $this->log_write("Error: Remote host returned \"" . $response . "\"\n");
            return FALSE;
        }
        return TRUE;
    }
    private function log_write($str) {
        $this->log_str .= $str;
    }
    public function get_log() {
        $str = $this->log_str;
        $this->log_str = '';
        return $str;
    }
    private function smtp_debug($message) {
        if ($this->debug) {
            echo $message;
        }
    }
}

 ?>