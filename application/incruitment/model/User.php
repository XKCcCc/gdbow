<?php
namespace app\incruitment\model;
use think\Model;
use think\Session;

class User extends Model
{
    //注册验证
    static function authSignin($data)
    {
        $map = array("user" => $data["user"]);
        $user = self::get($map);
        if($user)
        {
            //已有此用户
            return 0;
        }
        self::create(["user" => $data["user"], "password" => $data["password"]], true);
        return 1;
    }

    //登录验证
    static function authLogin($data)
    {
        $map = array("user" => $data["user"]);
        $user = self::get($map);
        if(!$user)
        {
            //没有此用户
            return 0;
        }
        if($user->getData("password") !== $data["password"])
        {
            //密码错误
            return 1;
        }
        Session::set("name", $data["user"]);
        //记住密码
        if($data["remember"])
        {
            cookie("user", $data["user"],3600);
            cookie("password", $data["password"], 3600);
        }
        else
        {
            $_COOKIE["user"] = NULL;
            $_COOKIE["password"] = NULL;
        }
        if($data["user"] == "admin" && $data["password"] == "admin")
        {
            //管理员用户
            return 3;
        }
        //登录成功
        return 2;
    }

    //注销
    static function logoutAuth()
    {
        Session::delete("name");
        return 1;//注销成功
    }
}