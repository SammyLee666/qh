<?php
/**
 * Created by PhpStorm.
 * User: Sammy
 * Date: 2018/11/11
 * Time: 15:57
 */

namespace App\Library;


use App\User;

class common
{
    static public function uniqueBase()
    {
        return base64_encode(uniqid(mt_rand(10000, 99999)));
    }

    static public function promotionCode($length = 4)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        do{
            $str = '';
            for ($i = 0; $i < $length; $i++) {
                $str .= $chars[mt_rand(0, strlen($chars) - 1)];
            }
            $count = User::where("promotion_code", $str)->count();
        }while($count);

        return $str;
    }

}