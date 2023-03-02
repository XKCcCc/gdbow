<?php
namespace app\incruitment\model;
use think\Model;
use app\incruitment\model\Resumebox;

class Resume extends Model
{
  //获取筛选数据
  public function screenData($key, $value)
  {
    $data = self::where($key, $value)->select();
    return $data;
  }

  //保存数据
  static function saveData($data)
  {
    $now = time();
    $data["pos_cont"] = str_replace(PHP_EOL, "", $data["pos_cont"]);
    $data["item_cont"] = str_replace(PHP_EOL, "", $data["item_cont"]);
    $save1 = "/var/www/html/incruitment/public/static/images/upload/".$now.".jpg";
    // 判断是否有上传简历头像
    if(isset($data["user_img"]["tmp_name"]))
    {
      if(!move_uploaded_file($data["user_img"]["tmp_name"], $save1))
        {
          return 0;//添加图片失败,请重试！
        }
      $data["user_img"] = $now.".jpg";
    }
    $objData = self::create($data);//静态保存数据
    $objData = json_decode(json_encode($objData)); 
    if(isset($objData->user))
    {
      return 1;//保存成功！
    }
    else
    {
      return 2;//保存失败，请重试！
    }
  }
  
  //修改数据
  static function modifyData($data)
  {
    $data["pos_cont"] = str_replace(PHP_EOL, "", $data["pos_cont"]);
    $data["item_cont"] = str_replace(PHP_EOL, "", $data["item_cont"]); 
    // 判断图片与旧图片是否相同，相同则没有修改图片,不同则有修改图片
    if($data["user_img"] != $data["old_user_img"])
    {
      if($data["old_user_img"] == "user.jpg")
      {
        $save = "/var/www/html/incruitment/public/static/images/upload/".time().".jpg";
        if(!move_uploaded_file($data["user_img"]["tmp_name"], $save))
        {
          return 0;//修改图片失败，请重试！
        }
        self::update(["user_img" => time().".jpg"], ["id" => $data["id"]], true);
      }
      else
      {
        $save = "/var/www/html/incruitment/public/static/images/upload/".$data["old_user_img"];
        if(!move_uploaded_file($data["user_img"]["tmp_name"], $save))
        {
          return 0;//修改图片失败，请重试！
        }
      }
    }
    if(self::update(["name" => $data["name"], "gender" => $data["gender"], "birthday" => $data["birthday"], 
                  "edu_bg" => $data["edu_bg"], "phone" => $data["phone"], "mail" => $data["mail"], 
                  "edu_time" => $data["edu_time"], "school" => $data["school"], "profess" => $data["profess"], 
                  "major" => $data["major"], "position" => $data["position"], "pos_time" => $data["pos_time"],
                  "company" => $data["company"], "pos_cont" => $data["pos_cont"], "item" => $data["item"],
                  "item_cont" => $data["item_cont"]],
                  ["id" => $data["id"]], true))
                  {
                    return 1;//修改成功！
                  }
                  else{
                    return 2;//修改失败，请重试！
                  }
  }

  //删除数据
  static function deleteData($id)
  {
    $resume = self::where("id", $id)->find();
    $resume = json_decode(json_encode($resume));
    if($resume->user_img != "user.jpg")
    {
        unlink("/var/www/html/incruitment/public/static/images/upload/".$resume->user_img);
    }
    if(self::where("id", $id)->delete())
    {
        $rebObj = new Resumebox;
        $rebObj->where("user", $resume->user)->delete();
        return 1;//删除成功
    }
    else
    {
        return 0;//删除失败,请重试
    }
  }

  //投简历
  static function pushData($data)
  {
    if(!$data["user"])
    {
        return 0;// 投历失败，请先登录
    }
    $userRem = self::where("user", $data["user"])->find();
    if(!$userRem)
    {
        return 1;// 投历失败，请先添加简历
    }
    $rebObj = new Resumebox;
    $data["push_time"] = date('m-d h:i',time());
    if(Resumebox::create($data))
    {
      return 2;// 投历成功
    }
    else
    {
        return 3;//投历失败，请重试！
    }
  }
}