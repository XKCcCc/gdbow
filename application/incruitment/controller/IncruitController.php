<?php
namespace app\incruitment\controller;
use think\Controller;
use think\Session;
use think\Request;
//引入redis
use think\cache\driver\Redis;

use app\incruitment\model\Position;
use app\incruitment\model\Company;
use app\incruitment\model\Classify;
use app\incruitment\model\Advertise;


class IncruitController extends Controller
{
    //获取主页页面
    public function index()
    {   
        return $this->fetch();
    }
    
    //获取期待页面
    public function blank()
    {
        return $this->fetch();
    }

    //获取分类模块页面
    public function classifymoduleadmin()
    {
        //判断是否有用户登录，没有则跳转到登录页面
        if(!Session::get("name"))
        {
            return $this->error("请先登录", url("Login/login"));
        }
        if(Session::get("name") != "admin")
        {
            return $this->error("权限不够，无法访问", url("Incruit/index"));
        }
        return $this->fetch();
    }

    //获取广告管理页面
    public function advertiseadmin(){
        //判断是否有用户登录，没有则跳转到登录页面
        if(!Session::get("name"))
        {
            return $this->error("请先登录", url("Login/login"));
        }
        if(Session::get("name") != "admin")
        {
            return $this->error("权限不够，无法访问", url("Incruit/index"));
        }
        return $this->fetch();
    }

    //分类模块添加保存
    public function classifyModuleAddSave()
    {
        $redis = new Redis;
        $redis->handler()->del("classifyList");
        $postData = Request::instance()->post();
        $res = classify::saveData($postData);
        sleep(1);
        $redis->handler()->del("classifyList");
        if($res)
        {
            return 1;//添加成功！
        }
        else
        {
            return 0;//添加失败，请重试！
        }
    }

    //分类模块删除
    public function classifyModuleDelete()
    {
        //判断是否有用户登录，没有则跳转到登录页面
        if(!Session::get("name"))
        {
            return $this->error("请先登录", url("Login/login"));
        }
        if(Session::get("name") != "admin")
        {
            return $this->error("权限不够，无法访问", url("Incruit/index"));
        }
        $redis = new Redis;
        $redis->handler()->del("classifyList");
        $getData = Request::instance()->get();
        $type = $getData["type"];
        $res = classify::deleteData($type);
        sleep(1);
        $redis->handler()->del("classifyList");
        if($res)
        {
            return 1;//删除成功！
        }
        else
        {
            return 0;//删除失败，请重试！
        }
    }

    //广告添加保存
    public function advertiseAddSave(){
        $data["img_src"] = $_FILES["advImg"]["tmp_name"];
        switch(Advertise::saveData($data))
        {
            case 0:
                return 0;//添加图片失败，请重试！
                break;
            case 1:
                return 1;//添加成功！
                break;
            case 2:
                return 0;//添加失败，请重试！
        }
    }

    //广告修改保存
    public function advertiseModifySave(){
        $postData = Request::instance()->post();
        $id = $postData["id"];
        $img = $_FILES["advImg"];
        switch(Advertise::modifyData($id, $img))
        {
            case 0:
                return 2;//修改图片失败，请重试！
                break;
            case 1:
                return 3;//修改成功！
                break;
            case 2:
                return 2;//修改失败，请重试！
        }
    }

    //广告删除
    public function advertiseDelete()
    {
        $getData = Request::instance()->get();
        $id = $getData["id"];
        if(Advertise::deleteData($id))
        {
            return 1;//删除成功！
        }
        else
        {
            return 0;//删除失败，请重试！
        }
    }
}