<?php
namespace app\incruitment\model;
use think\Model;
use app\incruitment\model\Resumebox;

class Position extends Model
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

  //获取模糊查询数据
  public function searchData($key, $keyword)
  {
    $data = self::where($key, "like", "%".$keyword."%")->select();
    return $data;
  }

  //获取筛选并限制数量数据
  public function limitData($key, $value, $count)
  {
    $data = self::where($key, $value)->order("rand()")->limit($count)->select();
    return $data;
  }

  //保存数据
  static function saveData($data)
  {
    $now = time();
    $nowAdd = time()+1;
    $data["pub_time"] = date('m-d h:i',time());
    $data["duty"] = str_replace(PHP_EOL, "", $data["duty"]);
    $data["require"] = str_replace(PHP_EOL, "", $data["require"]);
    $save1 = "/var/www/html/incruitment/public/static/images/upload/".$now.".jpg";
    $save2 = "/var/www/html/incruitment/public/static/images/upload/".$nowAdd.".jpg";
    if(isset($data["com_img"]["tmp_name"]))
    {
      if(!move_uploaded_file($data["com_img"]["tmp_name"], $save1))
      {
        return 0;// 添加图片失败,请重试
      }
      $data["com_img"] = $now.".jpg";
    }
    else
    {
      $data["com_img"] = "company.jpg";
    }
    if(isset($data["pub_img"]["tmp_name"]))
    {
      if(!move_uploaded_file($data["pub_img"]["tmp_name"], $save2))
      {
        return 0;// 添加图片失败,请重试
      }
      $data["pub_img"] = $nowAdd.".jpg";
    }
    else
    {
      $data["pub_img"] = "publisher.jpg";
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

  // 修改数据
  static function modifyData($data)
  {
    $data["pub_time"] = date('m-d h:i',time());
    $data["duty"] = str_replace(PHP_EOL, "", $data["duty"]);
    $data["require"] = str_replace(PHP_EOL, "", $data["require"]); 
      // 判断是否有修改图片
      if($data["com_img"] != $data["old_com_img"])
      {
        // 若有修改图片，判断原来的旧图片是是否是默认图片
        if($data["old_com_img"] == "company.jpg")
        {
          // 是默认图片，则将新的图片保存到文件夹
          $save = "/var/www/html/incruitment/public/static/images/upload/".time().".jpg";
          if(!move_uploaded_file($data["com_img"]["tmp_name"], $save))
          {
            return 0;//修改图片失败，请重试！
          }
          self::update(["com_img" => time().".jpg"],["id" => $data["id"]], true);
        }
        else
        {
          // 不是默认图片则将新的图片替换掉原来的旧图片
          $save = "/var/www/html/incruitment/public/static/images/upload/".$data["old_com_img"];
          if(!move_uploaded_file($data["com_img"]["tmp_name"], $save))
          {
            return 0;//修改图片失败，请重试！
          }
        }
      }
      if($data["pub_img"] != $data["old_pub_img"])
      {
        // 若有修改图片，判断原来的旧图片是是否是默认图片
        if($data["old_pub_img"] == "publisher.jpg")
        {
          // 是默认图片，则将新的图片保存到文件夹
          $save = "/var/www/html/incruitment/public/static/images/upload/".time().".jpg";
          if(!move_uploaded_file($data["pub_img"]["tmp_name"], $save))
          {
            return 0;//修改图片失败，请重试！
          }
          self::update(["pub_img" => time().".jpg"],["id" => $data["id"]], true);
        }
        else
        {
          // 不是默认图片则将新的图片替换掉原来的旧图片
          $save = "/var/www/html/incruitment/public/static/images/upload/".$data["old_pub_img"];
          if(!move_uploaded_file($data["pub_img"]["tmp_name"], $save))
          {
            return 0;//修改图片失败，请重试！
          }
        }
      }
    //   // 更新数据
      if(self::update(["name" => $data["name"], "duty" => $data["duty"], "require" => $data["require"], 
                    "salary" => $data["salary"], "region" => $data["region"], "edu_bg" => $data["edu_bg"], 
                    "type" => $data["type"], "company" => $data["company"], "com_type" => $data["com_type"], 
                    "publisher" => $data["publisher"], "phone" => $data["phone"], "pub_time" => $data["pub_time"]],
                    ["id" => $data["id"]], true))
                    {
                      return 1;//修改成功！
                    }
                    else{
                      return 2;//修改失败请重试
                    }
  }

  //删除数据
  static function deleteData($id)
  {
    $pushResume = Resumebox::where("pos_id", $id)->select();
    if($pushResume)
    {
      Resumebox::where("pos_id", $id)->delete();
    }
    $position = self::where("id", $id)->find();
    $position = json_decode(json_encode($position));
    if($position->com_img != "company.jpg")
    {
        unlink("/var/www/html/incruitment/public/static/images/upload/".$position->com_img);
    }
    if($position->pub_img != "publisher.jpg")
    {
        unlink("/var/www/html/incruitment/public/static/images/upload/".$position->pub_img);
    }
    if(self::where("id", $id)->delete())
    {
        return 1;//删除成功
    }
    else
    {
        return 0;//删除失败,请重试
    }
  }
}