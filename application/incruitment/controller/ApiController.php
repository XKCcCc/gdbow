<?php
namespace app\incruitment\controller;
use think\Controller;
use think\Request;
use think\Session;
//引入redis
use think\cache\driver\Redis;

use app\incruitment\model\Position;
use app\incruitment\model\Company;
use app\incruitment\model\Classify;
use app\incruitment\model\Advertise;
use app\incruitment\model\Resumebox;
use app\incruitment\model\Resume;


class ApiController extends Controller
{
    //获取所有职位数据
    public function getAllPositionData()
    {
        $posObj = new Position;
        $data = $posObj->allData();
        $data = json_encode($data);
        return $data;
    }

    //获取筛选职位数据
    public function getScreenPositionData(){
        $param = input();
        $paramKey = key($param);
        $paramValue = input($paramKey);
        $posObj = new Position;
        $data = $posObj->screenData($paramKey, $paramValue);
        $data = json_decode(json_encode($data));
        //请求数据参数为id则为职位详情页面请求数据，需计算职位对应公司发布职位数量
        if($paramKey == "id")
        {
            $screenData = $posObj->screenData("company", $data[0]->company);
            $count = count($screenData);
            $data[0]->positionNum = $count;
        }
        $data = json_encode($data);
        return $data;
    }

    //获取限制职位数据
    public function getLimitPositionData()
    {
        $getData = Request::instance()->get();
        $count = $getData["count"];
        $type = $getData["type"];
        $posObj = new Position;
        $data = $posObj->limitData("type", $type, $count);
        $data = json_encode($data);
        return $data;
    }

    //获取搜索职位数据
    public function getSearchPositionData()
    {
        $getData = Request::instance()->get();
        $keyword = $getData["keyword"];
        $posObj = new Position;
        if($keyword)
        {
            $data = $posObj->searchData("name", $keyword);
        }
        else
        {
            $data = $posObj->allData();
        }
        $data = json_encode($data);
        return $data;
    }

    //获取所有公司数据
    public function getAllCompanyData()
    {
        $comObj = new Company;
        $data = $comObj->allData();
        //计算各公司对应的发布职位数量
        $data = json_decode(json_encode($data));
        $posObj = new Position;
        for($i = 0; $i < count($data); $i++)
        {
            $screenPos = $posObj->screenData("company", $data[$i]->company);
            $count = count($screenPos);
            $data[$i]->positionNum = $count;
        }
        $data = json_encode($data);
        return $data;
    }

    //获取筛选公司数据
    public function getScreenCompanyData()
    {
        $getData = Request::instance()->get();
        $company = $getData["company"];
        $comObj = new Company;
        $data = $comObj->screenData("company", $company);
        //计算公司的发布职位数量
        $data = json_decode(json_encode($data));
        $posObj = new Position;
        $screenPos = $posObj->screenData("company", $data[0]->company);
        $count = count($screenPos);
        $data[0]->positionNum = $count;
        $data = json_encode($data);
        return $data;
    }

    //获取限制公司数据
    public function getLimitCompanyData()
    {
        $getData = Request::instance()->get();
        $count = $getData["count"];
        $comObj = new Company;
        $data = $comObj->limitData($count);
        //计算各公司对应的发布职位数量
        $data = json_decode(json_encode($data));
        $posObj = new Position;
        for($i = 0; $i < count($data); $i++)
        {
            $screenPos = $posObj->screenData("company", $data[$i]->company);
            $count = count($screenPos);
            $data[$i]->positionNum = $count;
        }
        $data = json_encode($data);
        return $data;
    }

    //获取筛选简历数据
    public function getScreenResumeData()
    {
        $param = input();
        $paramKey = key($param);
        $paramValue = input($paramKey);
        $remObj = new Resume;
        $data = $remObj->screenData($paramKey, $paramValue);
        $data = json_encode($data);
        return $data;
    }

    //获取所有投历数据
    public function getAllPushResumeData()
    {
        $remObj = new Resumebox;
        $resumeboxData = $remObj->allData();
        $resumeboxData = json_decode(json_encode($resumeboxData));
        $count = count($resumeboxData);
        $data = array();
        //投历信息拼接
        for($i = 0; $i < $count; $i++)
        {
            //简历数据
            $resObj = new Resume;
            $resumeData = $resObj->screenData("user", $resumeboxData[$i]->user);
            $resumeData = json_decode(json_encode($resumeData));
            $data[$i]["resume_id"] = $resumeData[0]->id;
            $data[$i]["name"] = $resumeData[0]->name;
            $data[$i]["gender"] = $resumeData[0]->gender;
            $data[$i]["edu_bg"] = $resumeData[0]->edu_bg;
            $data[$i]["phone"] = $resumeData[0]->phone;
            //职位数据
            $posObj = new Position;
            $posData = $posObj->screenData("id", $resumeboxData[$i]->pos_id);
            $posData = json_decode(json_encode($posData));
            $data[$i]["position_id"] = $posData[0]->id;
            $data[$i]["position"] = $posData[0]->name;
            $data[$i]["publisher"] = $posData[0]->publisher;
            $data[$i]["pub_phone"] = $posData[0]->phone;
            //投历时间和id
            $data[$i]["push_id"] = $resumeboxData[$i]->id;
            $data[$i]["push_time"] = $resumeboxData[$i]->push_time;
        }
        $data = json_encode($data);
        return $data;
    }

    //获取筛选广告数据
    public function getScreenAdvertiseData()
    {
        $getData = Request::instance()->get();
        $type = $getData["type"];
        $advObj = new Advertise;
        $data = $advObj->screenData("type", $type);
        $data = json_encode($data);
        return $data;
    }

    //获取所有分类模块数据
    public function getAllClassifyModuleData()
    {
        $redis = new Redis;
        $clasObj = new Classify;
        $data = $redis->handler()->smembers("classifyList");
        //无redis缓存数据
        if(!$data)
        {
            $data = $clasObj->allData();
            $data = json_decode(json_encode($data));
            for($i = 0; $i < count($data); $i++)
            {
                $arr[$i] = $data[$i]->type;
            }
            for($i = 0; $i < count($arr); $i++)
            {
                $redis->handler()->sadd("classifyList", $arr[$i]);
            }
            $arr = json_encode($arr);
            return $arr;
        }
        //有缓存数据
        else
        {
            $data = json_encode($data);
            return $data;
        }
    }

    //获取用户数据
    public function getUserData()
    {
        if(Session::has("name"))
        {
            $user = Session::get('name');
            return $user;
        }
        else
        {
            return NULL;
        }
    }
    
    //获取用户cookie用户名密码
    public function getUserCookieData()
    {
        if(isset($_COOKIE["user"]))
        {
            $userData["user"] = $_COOKIE["user"];
            $userData["password"] = $_COOKIE["password"];
        }
        else
        {
            $userData["user"] = NULL;
            $userData["password"] = NULL;
        }
        $data = json_encode($userData);
        return $data;
    }
}