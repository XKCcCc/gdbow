<?php
namespace app\incruitment\controller;
use think\Controller;
use think\Request;
use think\Session;
use app\incruitment\model\User;

class LoginController extends Controller
{
    //获取注册页面
    public function signin()
    {
        return $this->fetch();
    }

    //获取登录页面
    public function login()
    {
        return $this->fetch();
    }

    //注销
    public function logout()
    {
        if(User::logoutAuth())
        {
            return 1;//注销成功
        }
    }

    //登录验证
    public function loginAuth()
    {
        $postData = Request::instance()->post();
        switch(User::authLogin($postData))
        {
            case 0:
                return 0;//没有此用户，请先注册
                break;
            case 1:
                return 1;//密码错误，请重试
                break;
            case 2:
                return 2;//登录成功
                break;
            case 3:
                return 3;//管理员登录成功
                break;
        }
    }

    //注册验证
    public function signinAuth(){
        $postData = Request::instance()->post();
        switch(User::authSignin($postData))
        {
            case 0:
                return 0;//该用户名已存在，请重试
                break;
            case 1:
                return 1;//两次密码输入不同，请重试
                break;
        }
    }
}
