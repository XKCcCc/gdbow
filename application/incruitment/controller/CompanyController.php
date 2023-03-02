<?php
namespace app\incruitment\controller;
use think\Controller;
use think\Request;
use think\Session;
use app\incruitment\model\Company;

class CompanyController extends Controller
{
    //获取公司详情页面
    public function companydetail()
    {
        return $this->fetch();
    }

    //获取公司管理页面
    public function companyadmin()
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

    //公司添加保存
    public function companyAddSave()
    {
        $postData = Request::instance()->post();
        if(isset($_FILES["com_img"]))
        {
            $postData["com_img"] = $_FILES["com_img"];
        }
        switch(Company::saveData($postData))
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

    //公司删除
    public function companyDelete()
    {
        // 判断是否有用户登录，没有则跳转到登录页面
        if(!Session::get("name"))
        {
            return $this->error("请先登录", url("Login/login"));
        }
        if(Session::get("name") != "admin")
        {
            return $this->error("权限不够，无法访问", url("Incruit/index"));
        }
        $getData = Request::instance()->get();
        $id = $getData["id"];
        if(Company::deleteData($id))
        {
            return 1;//删除成功！
        }
        else
        {
            return 0;//删除失败，请重试！
        }
    }
}