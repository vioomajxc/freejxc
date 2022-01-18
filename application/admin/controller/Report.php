<?php
namespace app\admin\controller;
// +----------------------------------------------------------------------
// | 通用操作控制器
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://www.lotusadmin.top/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: xingma <qq 12612019>
// +----------------------------------------------------------------------
use think\Controller;
use app\admin\model\AuthGroupAccess;
use app\admin\model\UserLog;
use org\Auth;
use think\Db;
use think\Session;
use app\admin\model\AuthGroup;
use app\admin\model\AuthRule;
use app\admin\model\Order;
use app\admin\model\Item;
use app\admin\model\Goods;
use app\admin\model\FinancialDetails as Financial;
use think\Hook;
use service\JsonService;
use service\UtilService as Util;
use app\admin\model\Shop;
use app\admin\model\Member;

class Report extends Base
{
	//商品统计
    public function goods(){
        return $this->fetch("goods",[
            'is_layui'=>true,
            'year'=>getMonth('y')
        ]);
    }

    /**
     * 获取产品曲线图数据
     */
    public function get_echarts_product($type='',$data=''){
        return JsonService::successful(Goods::getChatrdata($type,$data));

    }
    /**
     * 获取销量
     */
    public function get_echarts_maxlist($data=''){
        return JsonService::successful(Goods::getMaxList(compact('data')));
    }

    /**
     * 获取退货
     */
    public function getTuiPriesList(){
        return JsonService::successful(Goods::TuiProductList());
    }
    /**
     * 获取利润
     */
    public function get_echarts_profity($data=''){
        return JsonService::successful(Goods::ProfityTop10(compact('data')));
    }
    /**
     * 获取缺货列表
     */
    public function getLackList(){
        $where=Util::getMore([
            ['page',1],
            ['limit',20],
        ]);
        return JsonService::successlayui(Goods::getLackList($where));
    }

 
    public function gethreaderValue($chart,$where=[]){
        if($where){
            switch($where['date']){
                case null:case 'today':case 'week':case 'year':
                if($where['date']==null){
                    $where['date']='month';
                }
                $sum_user=Member::whereTime('member_regtime',$where['date'])->count();
                if($sum_user==0) return 0;
                $counts=bcdiv($chart,$sum_user,4)*100;
                return $counts;
                break;
                case 'quarter':
                    $quarter=Member::getMonth('n');
                    $quarter[0]=strtotime($quarter[0]);
                    $quarter[1]=strtotime($quarter[1]);
                    $sum_user=Member::where('member_regtime','between',$quarter)->count();
                    if($sum_user==0) return 0;
                    $counts=bcdiv($chart,$sum_user,4)*100;
                    return $counts;
                default:
                    //自定义时间
                    $quarter=explode('-',$where['date']);
                    $quarter[0]=strtotime($quarter[0]);
                    $quarter[1]=strtotime($quarter[1]);
                    $sum_user=Member::where('member_regtime','between',$quarter)->count();
                    if($sum_user==0) return 0;
                    $counts=bcdiv($chart,$sum_user,4)*100;
                    return $counts;
                    break;
            }
        }else{
            $num=Member::count();
            $chart=$num!=0?bcdiv($chart,$num,5)*100:0;
            return $chart;
        }
    }

    public function get_member_index($where,$name){
        switch ($where['date']){//按日期统计会员数据
            case null:
                $days = date("t",strtotime(date('Y-m',time())));
                $dates=[];
                $series=[];
                $times_list=[];
                foreach ($name as $key=>$val){
                    for($i=1;$i<=$days;$i++){
                        if(!in_array($i.'日',$times_list)){
                            array_push($times_list,$i.'日');
                        }
                        $time=$this->gettime(date("Y-m",time()).'-'.$i);
                        if($key==0){
                            $dates['data'][]=Member::where('member_regtime','between',$time)->count();//会员数
                        }else if($key==1){
                            $dates['data'][]=Member::consume(true,$time);//会员消费
                        }
                    }
                    $dates['name']=$val;
                    $dates['type']='line';
                    $series[]=$dates;
                    unset($dates);
                }
                return ['time'=>$times_list,'series'=>$series];
            case 'today':
                $dates=[];
                $series=[];
                $times_list=[];
                foreach ($name as $key=>$val){
                    for($i=0;$i<=24;$i++){
                        $strtitle=$i.'点';
                        if(!in_array($strtitle,$times_list)){
                            array_push($times_list,$strtitle);
                        }
                        $time=$this->gettime(date("Y-m-d ",time()).$i);
                        if($key==0){
                            $dates['data'][]=Member::where('member_regtime','between',$time)->count();
                        }else if($key==1){
                            $dates['data'][]=Member::consume(true,$time);
                        }
                    }
                    $dates['name']=$val;
                    $dates['type']='line';
                    $series[]=$dates;
                    unset($dates);
                }
                return ['time'=>$times_list,'series'=>$series];
            case "week":
                $dates=[];
                $series=[];
                $times_list=[];
                foreach ($name as $key=>$val){
                    for($i=0;$i<=6;$i++){
                        if(!in_array('星期'.($i+1),$times_list)){
                            array_push($times_list,'星期'.($i+1));
                        }
                        $time=Member::getMonth('h',$i);
                        if($key==0){
                            $dates['data'][]=Member::where('member_regtime','between',[strtotime($time[0]),strtotime($time[1])])->count();
                        }else if($key==1){
                            $dates['data'][]=Member::consume(true,[strtotime($time[0]),strtotime($time[1])]);
                        }
                    }
                    $dates['name']=$val;
                    $dates['type']='line';
                    $series[]=$dates;
                    unset($dates);
                }
                return ['time'=>$times_list,'series'=>$series];
            case 'year':
                $dates=[];
                $series=[];
                $times_list=[];
                $year=date('Y');
                foreach ($name as $key=>$val){
                    for($i=1;$i<=12;$i++){
                        if(!in_array($i.'月',$times_list)){
                            array_push($times_list,$i.'月');
                        }
                        $t = strtotime($year.'-'.$i.'-01');
                        $arr= explode('/',date('Y-m-01',$t).'/'.date('Y-m-',$t).date('t',$t));
                        if($key==0){
                            $dates['data'][]=Member::where('member_regtime','between',[strtotime($arr[0]),strtotime($arr[1])])->count();
                        }else if($key==1){
                            $dates['data'][]=Member::consume(true,[strtotime($arr[0]),strtotime($arr[1])]);
                        }
                    }
                    $dates['name']=$val;
                    $dates['type']='line';
                    $series[]=$dates;
                    unset($dates);
                }
                return ['time'=>$times_list,'series'=>$series];
            case 'quarter':
                $dates=[];
                $series=[];
                $times_list=[];
                foreach ($name as $key=>$val){
                    for($i=1;$i<=4;$i++){
                        $arr=$this->gettime('quarter',$i);
                        if(!in_array(implode('--',$arr).'季度',$times_list)){
                            array_push($times_list,implode('--',$arr).'季度');
                        }
                        if($key==0){
                            $dates['data'][]=Member::where('member_regtime','between',[strtotime($arr[0]),strtotime($arr[1])])->count();
                        }else if($key==1){
                            $dates['data'][]=member::consume(true,[strtotime($arr[0]),strtotime($arr[1])]);
                        }
                    }
                    $dates['name']=$val;
                    $dates['type']='line';
                    $series[]=$dates;
                    unset($dates);
                }
                return ['time'=>$times_list,'series'=>$series];
            default:
                $list=Member::consume($where,'default');
                $dates=[];
                $series=[];
                $times_list=[];
                foreach ($name as $k=>$v){
                    foreach ($list as $val){
                        $date=$val['add_time'];
                        if(!in_array($date,$times_list)){
                            array_push($times_list,$date);
                        }
                        if($k==0){
                            $dates['data'][]=$val['num'];
                        }else if($k==1){
                            $dates['data'][]=Member::where(['id'=>$val['id']])->sum('card_money');
                        }
                    }
                    $dates['name']=$v;
                    $dates['type']='line';
                    $series[]=$dates;
                    unset($dates);
                }
                return ['time'=>$times_list,'series'=>$series];
        }
    }
    public function gettime($time='',$season=''){
        if(!empty($time) && empty($season)){
            $timestamp0 = strtotime($time);
            $timestamp24 =strtotime($time)+86400;
            return [$timestamp0,$timestamp24];
        }else if(!empty($time) && !empty($season)){
            $firstday=date('Y-m-01',mktime(0,0,0,($season - 1) *3 +1,1,date('Y')));
            $lastday=date('Y-m-t',mktime(0,0,0,$season * 3,1,date('Y')));
            return [$firstday,$lastday];
        }
    }
    /*采购统计*/
    public function procure(){
        $limit=10;
        $toplist=Order::getTopList($limit,$type='procure');
        $this->assign([
            'limit'=>$limit,
            'year'=>getMonth('y'),
            'procureList'=>$toplist['procure'],
            'goodsList'=>$toplist['goods']
        ]);
        return $this->fetch();
    }
    /*销售统计*/
    public function sales(){
        $limit=10;
        $toplist=Order::getTopList($limit,'sales');
        $this->assign([
            'limit'=>$limit,
            'year'=>getMonth('y'),
            'salesList'=>$toplist['sales'],
            'goodsList'=>$toplist['goodsale']
        ]);
        return $this->fetch();
    }
    /*
     * 获取 会员业务的
     * 购物会员统计
     * 分销商业务人数和提现人数统计
     * 分销商业务佣金和提现金额统计
     * 曲线图
     * $data 时间
     */
    public function getBusinesChart($data=''){
        return JsonService::successful(Order::getBusinesChart(compact('data')));
    }
    /*
    * 获取 会员业务
    * 会员总余额 分销商总佣金 分销商总佣金余额 分销商总提现佣金 本月分销商业务佣金 本月分销商佣金提现金额
    * 上月分销商业务佣金 上月分销商佣金提现金额
    * $where 查询条件
    *
    * return array
    */
    public function getBusinesHeade($data){
        return JsonService::successful(Order::getBusinesHeade(compact('data')));
    }

    
}