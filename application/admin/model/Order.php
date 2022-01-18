<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Controller;
use app\admin\model\AuthGroupAccess;
use app\admin\model\FinancialDetails as Financial;
use service\PHPExcelService;
use traits\ModelTrait;
use app\admin\model\Shop;
use think\Request;
class Order extends Model
{
    use ModelTrait;
    //获取订单总金额
    public static function getOrderInfo($type,$or_id,$where=""){
    	switch($type){
    		case "orderMoney"://订单金额
    		return Db::name('item')->where('or_id',$or_id)->sum('it_number*it_price');
    		break;
            case "OrderInfo":
            
            break;
            case "OrderPrice":
        $model = new Financial;
        $price = array();
        $price['pay_price_wx'] = 0;//微信支付金额
        $price['pay_price_cash'] = 0;//现金支付金额
        $price['pay_price_alipay'] = 0;//支付宝支付金额
        $list = self::getTime($where, $model,'f_time')->field('f_money,f_channel,f_time')->where('f_type',1)->select()->toArray();
        if (empty($list)) {
            $price['pay_price_wx'] = 0;
            $price['pay_price_cash'] = 0;
            $price['pay_price_alipay'] = 0;
        }
        foreach ($list as $v) {
            if ($v['f_channel'] == 1) {
                $price['pay_price_cash'] = bcadd($price['pay_price_cash'], $v['f_money'], 2);
            } elseif ($v['f_channel'] == 2) {
                $price['pay_price_wx'] = bcadd($price['pay_price_wx'], $v['f_money'], 2);
            } elseif ($v['f_channel'] == 3) {
                $price['pay_price_alipay'] = bcadd($price['pay_price_alipay'], $v['f_money'], 2);
            }
        }
        return $price;
            break;
    	}
    }

    //按ID获取订单数据
    public static function getValuebyId($id,$field=''){
    	return self::where('id',$id)->value($field);
    }
    //提取订单号
    public static function getOrderId($type = 'procure'){
        $or_id = self::where(['or_type' => $type,'or_status' => 0])->value('or_id');
        if(!isset($or_id)){
        $last_orid = self::max('id');
        $or_id = "VM_".date("Ymd",time())."-".session("userid")."-".($last_orid+1);
        //创建订单记录
        $data = [
            'or_id' => $or_id,
            'or_type' => $type,
            'or_user' => session("username"),
            'or_create_time' => time(),
            'or_shop' => session('shop')
        ];
        self::insert($data);
        }
        return $or_id;
    }

    /**
     * 最近交易
     */
    public static function trans()
    {
        $trans = self::alias('o')->join('member m','o.or_contact=m.id')->field('or_contact,or_money,member_name')->where("or_type='pos' or or_type='sales'")->where('or_verify_status',1)->order('o.or_create_time DESC')->limit('6')->select()->toArray();
        return $trans;
    }

    /**
     * 获取普通商品数
     */
    public static function getOrdinary($where)
    {
        $ordinary = self::getTimeWhere($where)->where('or_paied', 'eq', 1)->sum('or_money');
        return $ordinary;
    }

    public static function getTimeWhere($where, $model = null)
    {
        return self::getTime($where)->where('or_paied', 1);//时间内完成的单据
    }

    /**
     * 获取时间区间
     */
    public static function getTime($where,$model=null,$prefix='or_create_time'){
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
     * 导出表格
     */
    public static function systemTable($where){
        $orderinfos=self::getOrderInfo('OrderInfo','',$where);
        if($where['export'] == 1){
            $export = [];
            $orderinfo=$orderinfos['orderinfo'];
            foreach($orderinfo as $info){
                $time=$info['pay_time'];
                $price = $info['total_money']+$info['pay_postage'];
                $zhichu = $info['coupon_price']+$info['deduction_price']+$info['cost'];
                $profit = ($info['total_money']+$info['pay_postage'])-($info['coupon_price']+$info['deduction_price']+$info['cost']);
                $deduction=$info['deduction_price'];//积分抵扣
                $coupon=$info['coupon_price'];//优惠
                $cost=$info['cost'];//成本
                $export[] = [$time,$price,$zhichu,$cost,$coupon,$deduction,$profit];
            }
            dump($export);
            PHPExcelService::setExcelHeader(['时间','营业额(元)','支出(元)','成本','优惠','积分抵扣','盈利(元)'])->setExcelTile('财务统计', '财务统计',date('Y-m-d H:i:s',time()))->setExcelContent($export)->ExcelSave();
        }

    }
    
    /*TOP业务数据*/
    public static function getTopList($limit=10,$type='procure'){
        //采购排行
        if($type=="procure"){
        $procure=self::alias('A')->join('supplier C','C.id=A.or_contact')
            ->order('money desc')
            ->group('or_contact')
            ->field(['supplier_name','sum(or_money) as money','FROM_UNIXTIME(or_create_time,"%Y-%m-%d") as add_time'])
            ->limit($limit)
            ->select();
        count($procure) && $procure=$procure->toArray();

        //采购商品排行
        $goods=self::alias('A')->join('item B','B.or_id=A.or_id')->join('goods C','C.id=B.gd_id')
            ->order('money desc')
            ->group('gd_id')
            ->field(['goodsname','sum(it_number*it_price) as money','FROM_UNIXTIME(or_create_time,"%Y-%m-%d") as add_time'])
            ->limit($limit)
            ->select();
        count($goods) && $goods=$goods->toArray();
        return compact('procure','goods');
    }else{
        $sales=self::alias('A')->join('member C','C.id=A.or_contact')
            ->order('money desc')
            ->group('or_contact')
            ->field(['member_name','sum(or_money) as money','FROM_UNIXTIME(or_create_time,"%Y-%m-%d") as add_time'])
            ->limit($limit)
            ->select();
        count($sales) && $sales=$sales->toArray();
        $goodsale=self::alias('A')->join('item B','B.or_id=A.or_id')->join('goods C','C.id=B.gd_id')
            ->order('money desc')
            ->group('gd_id')
            ->field(['goodsname','sum(it_number*it_price) as money','FROM_UNIXTIME(or_create_time,"%Y-%m-%d") as add_time'])
            ->limit($limit)
            ->select();
        count($goodsale) && $goodsale=$goodsale->toArray();
        return compact('sales','goodsale');
    }
    }
    /*
     * 获取 订单业务
     * 采购金额 退货金额
     * $where 查询条件
     *
     * return array
     */
    public static function getBusinesHeade($where){
        $type = $_GET['type'];
        if($type=='procure')
        return [
            [
                'name'=>'采购金额',
                'field'=>'元',
                'count'=>round(Order::getModelTime($where,self::where('or_verify_status',1),'or_create_time')->where('or_type','procure')->sum('or_money'),2),
                'background_color'=>'layui-bg-cyan',
                'col'=>6,
            ],
            [
                'name'=>'退货金额',
                'field'=>'元',
                'count'=>round(Order::getModelTime($where,self::where('or_verify_status',1),'or_create_time')->where('or_type','procure_return')->sum('or_money'),2),
                'background_color'=>'layui-bg-cyan',
                'col'=>6
            ]
        ];
        else if($type=='sales')
            return [
            [
                'name'=>'销售金额',
                'field'=>'元',
                'count'=>round(Order::getModelTime($where,self::where('or_verify_status',1),'or_create_time')->where('or_type','sales')->sum('or_money'),2),
                'background_color'=>'layui-bg-cyan',
                'col'=>6,
            ],
            [
                'name'=>'退货金额',
                'field'=>'元',
                'count'=>round(Order::getModelTime($where,self::where('or_verify_status',1),'or_create_time')->where('or_type','sales_return')->sum('or_money'),2),
                'background_color'=>'layui-bg-cyan',
                'col'=>6
            ]
        ];
    }
    /*
     * 获取 会员业务的
     * 购物会员统计
     *  会员访问量
     *
     * 曲线图
     *
     * $where 查询条件
     *
     * return array
     */
    public static function getBusinesChart($where,$limit=20){
        $type=$_GET['type'];
        //获取采购金额趋势图
        $list=self::getModelTime($where,self::where('or_verify_status',1),'or_create_time')
            ->field(['FROM_UNIXTIME(or_create_time,"%Y-%m-%d") as _add_time','sum(or_money) as money'])->where('or_type',$type)
            ->group('_add_time')
            ->order('_add_time asc')
            ->select();
        count($list) && $list=$list->toArray();
        $seriesdata=[];
        $xdata=[];
        $zoom='';
        foreach ($list as $item){
            $seriesdata[]=$item['money'];
            $xdata[]=$item['_add_time'];
        }
        count($xdata) > $limit && $zoom=$xdata[$limit-5];
        //退货金额走趋图
        $visit=self::getModelTime($where,self::where('or_verify_status',1),'or_create_time')
            ->field(['FROM_UNIXTIME(or_create_time,"%Y-%m-%d") as _add_time','sum(or_money) as money'])->where('or_type',$type.'_return')
            ->group('_add_time')
            ->order('_add_time asc')
            ->select();
        count($visit) && $visit=$visit->toArray();
        $visit_data=[];
        $visit_xdata=[];
        $visit_zoom='';
        foreach ($visit as $item){
            $visit_data[]=$item['money'];
            $visit_xdata[]=$item['_add_time'];
        }
        count($visit_xdata) > $limit && $visit_zoom=$visit_xdata[$limit-5];
        //店铺采购饼状图
        $count=self::getModelTime($where,self::where('or_verify_status',1),'or_create_time')->where('or_type',$type)->sum('or_money');//总金额
        $shops_count=self::getModelTime($where,self::alias('a')->join('shop b','b.id=a.or_shop'),'or_create_time')->field('sum(or_money) as money,or_shop,shop_name')
            ->where('or_type',$type)
            ->group('or_shop')
            ->select();
        $shop_data=[];
        $shop_xdata=[];
        if($count >0){
        foreach($shops_count as $key=>$val){
                $shop_xdata[]=$val['shop_name'];
                $shop_data[$key] = [
                    'value'=>bcdiv($val['money'],$count,2)*100,
                    'name'=>$val['shop_name'],
                    'itemStyle'=>[ 'red', 'orange', 'yellow', 'green', 'blue', 'indigo', 'purple' ]
                ]; 
        }
        }
        return compact('seriesdata','xdata','zoom','visit_data','visit_xdata','visit_zoom','shop_data','shop_xdata');
    }
}