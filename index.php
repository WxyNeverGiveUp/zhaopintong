<?php

// change the following paths if necessary
$yii=dirname(__FILE__).'/yii-1.1.14.f0fee9/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
// specify how many levels of call stack should be shown in each log message

error_reporting(E_ALL &~ E_NOTICE);

require_once($yii);                                                                                                                           
Yii::createWebApplication($config)->run();
