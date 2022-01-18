<?php
namespace app\admin\model;
use think\Model;

class Category extends Model
{
    protected  $name = 'category';

    public function checkStatus($categoryname){ 
        $map=array(); 
        $map['category_name']=$categoryname; 
        $map['enterprise_code']=session('enterprisecode'); 
        return $cateInfo=$this->where($map)->find(); 
    }

}