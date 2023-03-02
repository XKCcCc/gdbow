<?php
namespace app\incruitment\controller;
use think\Controller;
use think\Request;
use think\Session;
use app\incruitment\model\Position;
use app\incruitment\model\Classify;
use app\incruitment\model\Advertise;
use app\incruitment\model\Resumebox;

class PositionController extends Controller
{
    //获取职位管理页面
    public function positionadmin()
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

    //获取职位详情页面
    public function positiondetail()
    {  
        return $this->fetch();
    }

    //获取职位添加页面
    public function positionadd()
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

    //获取职位修改页面
    public function positionmodify()
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

    //职位添加保存
    public function posAddSave(){
        $postData = Request::instance()->post();
        if(isset($_FILES["com_img"]))
        {
            $postData["com_img"] = $_FILES["com_img"];
        }
        if(isset($_FILES["pub_img"]))
        {
            $postData["pub_img"] = $_FILES["pub_img"];
        }
        switch(Position::saveData($postData))
        {
            case 0;
                return 0;//添加图片失败，请重试！
                break;
            case 1:
                return 1;//添加成功
                break;
            case 2:
                return 2;//添加失败，请重试！
                break;
        }
    }

    //职位修改保存
    public function positionModifySave()
    {
        $postData = Request::instance()->post();
        if(isset($_FILES["com_img"]))
        {
            $postData["com_img"] = $_FILES["com_img"];
        }
        if(isset($_FILES["pub_img"]))
        {
            $postData["pub_img"] = $_FILES["pub_img"];
        }

        switch(Position::modifyData($postData))
        {
            case 0:
                return 0;//修改图片失败，请重试！
                break;
            case 1:
                return 1;//修改成功
                break;
            case 2:
                return 2;//修改失败，请重试！
                break;
        }
    }

    //职位删除
    public function positionDelete()
    {
        // 删除职位
        $getData = Request::instance()->get();
        $id = $getData["id"];
        if(Position::deleteData($id))
        {
            return 1;//删除成功
        }
        else
        {
            return 0;//删除失败，请重试！
        }
    }
}
