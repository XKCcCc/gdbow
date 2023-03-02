<?php
namespace app\incruitment\model;
use think\Model;

class Advertise extends Model
{
    // 获取筛选数据
    public function screenData($key, $value)
    {
        $data = self::where($key, $value)->select();
        return $data;
    }

    //保存数据
    static function saveData($data)
    {
        $now = time();
        $save = "/var/www/html/incruitment/public/static/images/upload/".$now.".jpg";
        if(!move_uploaded_file($data["img_src"], $save))
        {
            return 0;// 添加图片失败,请重试！
        }
        $data["img_src"] = $now.".jpg";
        $data["type"] = "carousel";
        if(self::create($data))
        {
            return 1;//添加成功！
        }
        else
        {
            return 2;//添加失败，请重试！
        }
    }

    //更新数据
    static function modifyData($id, $img)
    {
        $oneAdv = self::where('id', $id)->find();
        $oneAdv = json_decode(json_encode($oneAdv));
        $old_img = $oneAdv->img_src;

        $fileName = time().".jpg";
        $save = "/var/www/html/incruitment/public/static/images/upload/".$fileName;
        if(!move_uploaded_file($img["tmp_name"], $save))
        {
            return 0;//修改图片失败,请重试！
        }
        if(self::update(["img_src" => $fileName], ["id" => $id], true))
        {
            unlink("/var/www/html/incruitment/public/static/images/upload/".$old_img);
            return 1;//修改成功！
        }
        else
        {
            return 2;//修改失败，请重试！
        }
    }

    //删除数据
    static function deleteData($id)
    {
        $advertise = self::where("id", $id)->find();
        $advertise = json_decode(json_encode($advertise));
        $img = $advertise->img_src;
        if(self::where("id", $id)->delete())
        {
            unlink("/var/www/html/incruitment/public/static/images/upload/".$img);
            return 1;//删除成功！
        }
        else
        {
            return 0;//删除失败，请重试！
        }
    }
}