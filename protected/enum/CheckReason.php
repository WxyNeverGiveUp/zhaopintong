<?php

/**
 * Created by PhpStorm.
 * User: xiaozhi
 * Date: 2017/10/20
 * Time: 10:03
 */
class CheckReason extends SplEnum
{
    const __default = self::IncompleteInfor;
    const IncompleteInfor = "信息不完整";
    const WrongInfor = "信息有误";

}