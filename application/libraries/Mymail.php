<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Mymail {
    public function mymail() {
        require_once('PHPMailer/class.phpmailer.php');
        require_once('PHPMailer/class.smtp.php');
    }
}