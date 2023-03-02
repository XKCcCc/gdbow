<?php
namespace app\incruitment\controller;
use think\Controller;
use think\Request;
use think\Session;
use app\incruitment\model\Resume;
use app\incruitment\model\Resumebox;
use app\incruitment\model\Position;
use think\paginator\driver\Bootstrap;

class ResumeController extends Controller
{
    //获取简历管理页面
    public function resumeadmin()
    {
        // 判断是否有用户登录
        $res = Session::has("name");
        if(!$res)
        {
            return $this->error("请先登录", url("Login/login"));
        }
        return $this->fetch();
    }

    //获取简历添加页面
    public function resumeadd()
    {
        // 判断是否有用户登录
        $res = Session::has("name");
        if(!$res)
        {
            return $this->error("请先登录", url("Login/login"));
        }
        return $this->fetch();
    }

    //获取简历修改页面
    public function resumemodify()
    {
        // 判断是否有用户登录
        $res = Session::has("name");
        if(!$res)
        {
            return $this->error("请先登录", url("Login/login"));
        }
        return $this->fetch();
    }

    //获取投历箱页面
    public function resumebox()
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

    //获取投历详情页面
    public function resumepushdetail()
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
    
    //简历添加保存
    public function resumeAddSave()
    {
        $postData = Request::instance()->post();
        if(isset($_FILES["user_img"]))
        {
            $postData["user_img"] = $_FILES["user_img"];
        }
        else
        {
            $postData["user_img"] = "user.jpg";
        }
        switch(Resume::saveData($postData))
        {
            case 0:
                return 0;//保存图片失败，请重试！
                break;
            case 1:
                return 1;//保存成功！
                break;
            case 2:
                return 2;//保存失败请重试！
                break;
        }
    }
    
    //简历修改保存
    public function resumeModifySave()
    {
        $postData = Request::instance()->post();
        if(isset($_FILES["user_img"]))
        {
            $postData["user_img"] = $_FILES["user_img"];
        }
        switch(Resume::modifyData($postData))
        {
            case 0:
                return 0;//修改图片失败，请重试！
                break;
            case 1:
                return 1;//修改成功！
                break;
            case 2:
                return 2;//修改失败，请重试！
                break;
        }
    }

    //简历删除
    public function resumeDelete()
    {
        $getData = Request::instance()->get();
        $id = $getData["id"];
        if(Resume::deleteData($id))
        {
            return 1;//删除成功
        }
        else
        {
            return 0;//删除失败,请重试
        }
    }

    //投简历
    public function resumePush()
    {
        $getData = Request::instance()->get();
        switch(Resume::pushData($getData))
        {
            case 0:
                return 0;//投历失败，请先登录！
                break;
            case 1:
                return 1;//投历失败，请先添加简历！
                break;
            case 2:
                return 2;//投历成功！
                break;
            case 3:
                return 3;//投历失败，请重试！
                break;
        }
    }

    //投历删除
    public function resumePushDelete()
    {
        $getData = Request::instance()->get();
        $id = $getData["id"];
        $rebObj = new ResumeBox;
        if($rebObj->deleteData("id", $id))
        {
            return 1;//删除成功
        }
        else
        {
            return 0;//删除失败,请重试
        }
    }

}