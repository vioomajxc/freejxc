<?php
namespace app\admin\model;
use think\Model;
use app\admin\model\CardDetails;
use app\admin\model\MemberCard as mCard;

class FinancialDetails extends Model
{
    protected  $name = 'financial_details';

    /**
     * 获取用户充值
     */
    public static function getRecharge($where)
    {
            $Recharge = self::getTime($where,new self)->where('f_come', 2)->where('f_type',1)->sum('f_money');
            return $Recharge;
    }

    public static function getTimeWhere($where, $model = null)
    {
        return self::getTime($where);
    }

    /**
     * 获取时间区间
     */
    public static function getTime($where,$model=null,$prefix='f_time'){
        if ($model == null) $model = new self;
        if(!$where['date']) return $model;
        if ($where['data'] == '') {
            $limitTimeList = [
                'today'=>implode(' - ',[date('Y/m/d'),date('Y/m/d',strtotime('+1 day'))]),
                'week'=>implode(' - ',[
                    date('Y/m/d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600)),
                    date('Y-m-d', (time() + (7 - (date('w') == 0 ? 7 : date('w'))) * 24 * 3600))
                ]),
                'month'=>implode(' - ',[date('Y/m').'/01',date('Y/m').'/'.date('t')]),
                'quarter'=>implode(' - ',[
                    date('Y').'/'.(ceil((date('n'))/3)*3-3+1).'/01',
                    date('Y').'/'.(ceil((date('n'))/3)*3).'/'.date('t',mktime(0,0,0,(ceil((date('n'))/3)*3),1,date('Y')))
                ]),
                'year'=>implode(' - ',[
                    date('Y').'/01/01',date('Y/m/d',strtotime(date('Y').'/01/01 + 1year -1 day'))
                ])
            ];
            $where['data'] = $limitTimeList[$where['date']];
        }
        list($startTime, $endTime) = explode(' - ', $where['data']);
        $model = $model->where($prefix, '>', strtotime($startTime));
        $model = $model->where($prefix, '<', strtotime($endTime));
        return $model;
    }

    /**
     * 处理where条件
     */
    public static function statusByWhere($status, $model = null)
    {
        if ($model == null) $model = new self;
        if ('' === $status)
            return $model;
        else if ($status == 'weixin')//微信支付
            return $model->where('f_channel', 2);
        else if ($status == 'cash')//现金支付
            return $model->where('f_channel', 1);
        else if ($status == 'alipay')//支付宝支付
            return $model->where('f_channel', 3);
        else
            return $model->where('f_type', 1);
    }

    /**
     * 获取会员消费
     */
    public static function getConsumption($where)
    {
        $consumption=self::getTime($where,new CardDetails,'time')
        ->field('sum(money) money')
        ->where('type',0)->find()->toArray();
        return $consumption;
    }

    /**
    * 获取总财务
    **/
    public static function getFinancialInfo($where){
    	$financialinfo = self::getTime($where,new self)
            ->field('sum(f_money) total_money,from_unixtime(f_time,\'%Y-%m-%d\') pay_time')->order('f_time')->group('from_unixtime(f_time,\'%Y-%m-%d\')')->where("f_type",1)->select()->toArray();
        $costinfo = self::getTime($where,new self)
            ->field('sum(f_money) cost,from_unixtime(f_time,\'%Y-%m-%d\') pay_time')->order('f_time')->group('from_unixtime(f_time,\'%Y-%m-%d\')')->where("f_type",0)->select()->toArray();
        $procureinfo = Order::getTime($where,new Order,'or_create_time')
        ->field('sum(or_money) procure_money,from_unixtime(or_create_time,\'%Y-%m-%d\') pay_time')->order('or_create_time')->group('from_unixtime(or_create_time,\'%Y-%m-%d\')')->where('or_type','procure')->select()->toArray();
        $give = self::getTime($where,new mCard,'card_time')->sum('card_give');//会员充值赠送
        $financialinfo = array_merge($financialinfo,$costinfo,$procureinfo);
        $price = 0;//应支付
        $postage = 0;//邮费
        $deduction = 0;//会员卡扣
        $coupon = 0;//消费券
        $cost = 0;//成本
        $procure = 0;//采购成本
        foreach ($financialinfo as $info) {
            if(isset($info['total_money']))$price = bcadd($price, $info['total_money'], 2);//应支付
            $postage = bcadd($postage, 0, 2);//邮费
            $deduction = bcadd($deduction, 0, 2);//抵扣
            $coupon = bcadd($coupon, 0, 2);//优惠券
            if(isset($info['cost']))$cost = bcadd($cost,$info['cost'],2);//成本
            if(isset($info['procure_money']))$procure = bcadd($procure,$info['procure_money']);
        }

        return compact('financialinfo' , 'price', 'postage', 'deduction', 'coupon', 'cost', 'procure', 'give');
    }

}