<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-6-3
 * Time: 下午10:07
 */

require_once('../parser/pb_parser.php');
$test = new PBParser();
$test->parse('./performance.proto');
?>