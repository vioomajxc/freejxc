<?php
namespace app\admin\model;
use think\Model;
use app\admin\model\MemberCard as mCard;
use app\admin\model\Order;
class Member extends Model
{
    protected  $name = 'member';

    protected $autoWriteTimestamp = true;

    /*
     *  获取某季度,某年某年后的时间戳
     *
     * self::getMonth('n',1) 获取当前季度的上个季度的时间戳
     * self::getMonth('n') 获取当前季度的时间戳
     */
    public static function getMonth($time='',$ceil=0){
        if(empty($time)){
            $firstday = date("Y-m-01",time());
            $lastday = date("Y-m-d",strtotime("$firstday +1 month -1 day"));
        }else if($time=='n'){
            if($ceil!=0)
                $season = ceil(date('n') /3)-$ceil;
            else
                $season = ceil(date('n') /3);
            $firstday=date('Y-m-01',mktime(0,0,0,($season - 1) *3 +1,1,date('Y')));
            $lastday=date('Y-m-t',mktime(0,0,0,$season * 3,1,date('Y')));
        }else if($time=='y'){
            $firstday=date('Y-01-01');
            $lastday=date('Y-12-31');
        }else if($time=='h'){
            $firstday = date('Y-m-d', strtotime('this week +'.$ceil.' day')) . ' 00:00:00';
            $lastday = date('Y-m-d', strtotime('this week +'.($ceil+1).' day')) . ' 23:59:59';
        }
        return array($firstday,$lastday);
    }

    public static function getcount(){
        return self::count();
    }

    /*
    *获取会员某个时间段的消费信息
    *
    * reutrn Array || number
    */
    public static function consume($where,$status='',$keep=''){
        $model = new self;
        $member_id=[];
        if(is_array($where)){
            if($where['status']!='')  $model=$model->where('member_status',$where['status']);
            if($where['shop']!='') $model = $model->where('member_shop',$where['shop']);
            switch ($where['date']){//处理日期段
                case null:case 'today':case 'week':case 'year':
                if($where['date']==null){
                    $where['date']='month';
                }
                    $model=$model->whereTime('member_regtime',$where['date']);
                break;
                case 'quarter'://本季度
                    $quarter=self::getMonth('n');
                    $startTime=strtotime($quarter[0]);
                    $endTime=strtotime($quarter[1]);
                        $model = $model->where('member_regtime','>',$startTime)->where('member_regtime','<',$endTime);
                    break;
                default:
                    //自定义时间
                    if(strstr($where['date'],'-')!==FALSE){
                        list($startTime,$endTime)=explode('-',$where['date']);
                        $model = $model->where('member_regtime','>',strtotime($startTime))->where('member_regtime','<',strtotime($endTime));
                    }else{
                        $model=$model->whereTime('member_regtime','month');
                    }
                    break;
            }
        }else{
            if(is_array($status)){//主统计中的会员消费
                $model=$model->where('member_regtime','>',$status[0])->where('member_regtime','<',$status[1]);
                //$model = new Order;
                //$model=$model->where('or_create_time','>',$status[0])->where('or_create_time','<',$status[1]);
            }
        }
        if($keep===true){//新增会员数
            return $model->count();
        }
        if($status==='default'){
            return $model->group('from_unixtime(or_create_time,\'%Y-%m-%d\')')->field('count(id) num,from_unixtime(or_create_time,\'%Y-%m-%d\') member_regtime,id')->select()->toArray();
        }
        if($status==='grouping'){
            return $model->group('city')->field('city')->select()->toArray();
        }
        $mid=$model->field('id')->select()->toArray();//会员集合
        foreach ($mid as $val){
            $member_id[]=$val['id'];
        }
        if(empty($member_id)){
            $member_id=[0];
        }
        if($status==='xiaofei'){
            $list=Order::where('or_contact','in',$member_id)
                ->group('or_create_time')
                ->where("or_type='pos' or or_type='sales'")
                ->field('sum(or_money) as top_number,or_create_time')
                ->select()
                ->toArray();
            $series=[
                'name'=>isset($list[0]['or_id'])?date('Y-m-d',$list[0]['or_create_time']):'',
                'type'=>'pie',
                'radius'=> ['40%', '50%'],
                'data'=>[]
            ];
            foreach($list as $key=>$val){
                $series['data'][$key]['value']=$val['top_number'];
                $series['data'][$key]['name']=date('Y-m-d',$val['or_create_time']);
            }
            return $series;
        }else if($status==='form'){//地域分布
            $list=$model->field('count(id) n,city')->group('city')->limit(0,10)->select()->toArray();
            $count=self::getcount();//总会员数
            $option=[
                'legend_date'=>[],
                'series_date'=>[]
            ];
            foreach($list as $key=>$val){
                $num=$count!=0?(bcdiv($val['n'],$count,2))*100:0;
                $t=['name'=>$num.'%  '.(empty($val['city'])?'未知':$val['city']),'icon'=>'circle'];
                $option['legend_date'][$key]=$t;
                $option['series_date'][$key]=['value'=>$num,'name'=>$t['name']];
            }
            return $option;
        }else{//新增消耗
            $number=$model->alias('A')->join('order B','B.or_contact=A.id')->where('A.id','in',$member_id)->whereOr(['or_type'=>'pos','or_type'=>'sales'])->sum('or_money');
            return round($number,2);
        }
    }
    /*
     * 获取 会员某个时间段的消费或者TOP20排行
     *
     * return Array  || number
     */
    public static function getSpend($date,$status=''){
        $model=new Order;
        $model=$model->alias('A');
        switch ($date){
            case null:case 'today':case 'week':case 'year':
            if($date==null) $date='month';
            $model=$model->whereTime('A.or_create_time',$date);
            break;
            case 'quarter':
                list($startTime,$endTime)=self::getMonth('n');
                $model = $model->where('A.or_create_time','>',strtotime($startTime));
                $model = $model->where('A.or_create_time','<',strtotime($endTime));
                break;
            default:
                list($startTime,$endTime)=explode('-',$date);
                $model = $model->where('A.or_create_time','>',strtotime($startTime));
                $model = $model->where('A.or_create_time','<',strtotime($endTime));
                break;
        }
        if($status===true){
            return $model->join('member B','B.id=A.or_contact')->DISTINCT(true)->group('A.or_contact')->sum('A.or_money');
        }
        $list=$model->join('member B','B.id=A.or_contact')
        ->join('member_card C','C.member_id=B.id')
            ->field('sum(A.or_money) as total_money,A.or_create_time,B.member_name,C.card_balance')
            ->order('total_money desc')
            ->DISTINCT(true)->group('A.or_contact')
            ->limit(0,20)
            ->select()
            ->toArray();
        if(!isset($list[0]['total_money'])){
            $list=[];
        }
        return $list;
    }

    /*
     * 获取 相对于上月或者其他的数据
     *
     * return Array
     */
    public static function getPostNumber($date,$status=false,$field='A.member_regtime',$t='消费'){
        $model=new self();
        if(!$status) $model=$model->alias('A');
        switch ($date){
            case null:case 'today':case 'week':case 'year':
            if($date==null) {
                $date='last month';
                $title='相比上月用户'.$t.'增长';
            }
            if($date=='today') {
                $date='yesterday';
                $title='相比昨天用户'.$t.'增长';
            }
            if($date=='week') {
                $date='last week';
                $title='相比上周用户'.$t.'增长';
            }
            if($date=='year') {
                $date='last year';
                $title='相比去年用户'.$t.'增长';
            }
            $model=$model->whereTime($field,$date);
            break;
            case 'quarter':
                $title='相比上季度用户'.$t.'增长';
                list($startTime,$endTime)=Member::getMonth('n',1);
                $model = $model->where($field,'>',$startTime);
                $model = $model->where($field,'<',$endTime);
                break;
            default:
                list($startTime,$endTime)=explode('-',$date);
                $title='相比'.$startTime.'-'.$endTime.'时间段用户'.$t.'增长';
                $Time=strtotime($endTime)-strtotime($startTime);
                $model = $model->where($field,'>',strtotime($startTime)+$Time);
                $model = $model->where($field,'<',strtotime($endTime)+$Time);
                break;
        }
        if($status){
            return [$model->count(),$title];
        }
        $number=$model->count();
        return [$number,$title];
    }

}