<?php
namespace app\admin\model;
use think\Model;
class UserLog extends Model
{
    protected  $name = 'user_log';

    protected $autoWriteTimestamp = true;


    static function  addLog($param)
    {
        $model = new self();
        $model->save($param);
    }
}