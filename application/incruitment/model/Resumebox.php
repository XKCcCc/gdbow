<?php
namespace app\incruitment\model;
use think\Model;

class Resumebox extends Model
{
    //获取所有数据
    public function allData()
    {
        $data = self::select();
        return $data;
    }

    //获取筛选数据
    public function screenData($key, $value)
    {
        $data = self::where($key, $value)->select();
        return $data;
    }

    //删除数据
    public function deleteData($key, $value)
    {
        if(self::where($key, $value)->delete())
        {
            return 1;//删除成功
        }
        else
        {
            return 0;//删除失败
        }
    }
}