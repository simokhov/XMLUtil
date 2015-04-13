<?php

// Include
require 'XMLUtil.php';

var_dump(XMLUtil::getAsArray('./example.xml')['testDoc']['header'][0]);
//array(2) {
//    'guid' =>
//  string(36) "7a6b85d7-71d0-4c05-bbd7-56699603d109"
//  'createDateTime' =>
//  string(19) "2013-02-06T08:52:28"
//}


var_dump(XMLUtil::getAsObject('./example.xml')->testDoc->header[0]);

//class stdClass#7 (2) {
//  public $guid =>
//  string(36) "7a6b85d7-71d0-4c05-bbd7-56699603d1a5"
//  public $createDateTime =>
//  string(19) "2013-02-06T08:52:28"
//}g