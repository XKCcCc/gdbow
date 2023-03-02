<?php
namespace app\incruitment\model;
use think\Model;
class Company extends Model
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

  //获取限制数量数据
  public function limitData($count)
  {
    $data = self::order("rand()")->limit($count)->select();
    return $data;
  }

  //添加数据
  static function saveData($data)
  {
    $now = time();
    $save = "/var/www/html/incruitment/public/static/images/upload/".$now.".jpg";
    if(isset($data["com_img"]["tmp_name"]))
    {
      if(!move_uploaded_file($data["com_img"]["tmp_name"], $save))
      {
        return 0;// 添加图片失败,请重试
      }
      $data["com_img"] = $now.".jpg";
    }
    else
    {
      $data["com_img"] = "company.jpg";
    }
    if(self::create($data))
    {
      return 1;//添加成功！
    }
    else
    {
      return 2;//添加失败，请重试！
    }
  }

  //删除数据
  static function deleteData($id)
  {
   $company = self::where("id", $id)->find();
   $company = json_decode(json_encode($company));
   $img = $company->com_img;
   if(self::where("id", $id)->delete())
   {
     if($img != "company.jpg")
     {
       unlink("/var/www/html/incruitment/public/static/images/upload/".$img);
     }
       return 1;//删除成功!
   }
   else
   {
       return 0;//删除失败,请重试！
   }
  }
}