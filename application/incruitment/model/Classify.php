<?php
namespace app\incruitment\model;
use think\Model;

class Classify extends Model
{
    //获取所有数据    
    public function allData()
    {
        $data = self::select();
        return $data;
    }

    //保存数据
    static function saveData($data)
    {
        if(self::create($data))
        {
            return 1;//添加成功！
        }
        else
        {
            return 0;//添加失败，请重试
        }
    }

    //删除数据
    static function deleteData($type)
    {
        if(self::where("type", $type)->delete())
        {
            return 1;//删除成功！
        }
        else
        {
            return 0;//删除失败，请重试！
        }
    }
}