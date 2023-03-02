<?php
namespace app\incruitment\controller;
use think\Controller;
use think\Session;
use think\Request;
use app\incruitment\model\Position;
use app\incruitment\model\Advertise;

class SearchController extends Controller
{
    //获取搜索详情页面
    public function searchdetail()
    {   
        return $this->fetch();
    }
}