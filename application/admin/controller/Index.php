<?php
namespace app\admin\controller;

use think\facade\Hook;
use think\Controller;
use think\Db;
use app\admin\model\Goods as GoodsModel;
use app\admin\model\Order;
use app\admin\model\Item;
use service\JsonService as Json;


class Index extends  Base
{
    public function index()
    {
        return $this->fetch();
    }
    public function welcome()
    {
        $topData['goodsNumbers'] = Db::name('goods')->count();
        $topData['memberNumbers'] = Db::name('member')->count();
        $topData['orderNumbers'] = Db::name('order')->where('or_verify_status',1)->whereOr(['or_type'=>'pos','or_type'=>'sales'])->count();
        $topData['stockNumbers'] = Db::name('stocks')->sum('numbers');
        /*首页第一行统计*/
        $now_month = strtotime(date('Y-m'));//本月
        $pre_month = strtotime(date('Y-m',strtotime('-1 month')));//上月
        $now_day = strtotime(date('Y-m-d'));//今日
        $pre_day = strtotime(date('Y-m-d',strtotime('-1 day')));//昨天时间戳
        $beforyester_day = strtotime(date('Y-m-d',strtotime('-2 day')));//前天时间戳

        //订单数->昨日
        $now_day_order_p = Order::where('or_paied',1)->whereTime('or_create_time','yesterday')->whereOr(['or_type'=>'pos','or_type'=>'sales'])->count();
        $pre_day_order_p = Order::where('or_paied',1)->where('or_create_time','gt',$pre_day)->whereOr(['or_type'=>'pos','or_type'=>'sales'])->where('or_create_time','lt',$now_day)->count();
        $first_line['d_num'] = [
            'data' => $now_day_order_p ? $now_day_order_p : 0,
            'percent' => abs($now_day_order_p - $pre_day_order_p),
            'is_plus' => $now_day_order_p - $pre_day_order_p > 0 ? 1 : ($now_day_order_p - $pre_day_order_p == 0 ? -1 : 0)
        ];

        //交易额->昨天
        $now_month_order_p = Order::where('or_paied',1)->whereTime('or_create_time','yesterday')->whereOr(['or_type'=>'pos','or_type'=>'sales'])->sum('or_money');
        $pre_month_order_p = Order::where('or_paied',1)->where('or_create_time','gt',$beforyester_day)->where('or_create_time','lt',$pre_day)->whereOr(['or_type'=>'pos','or_type'=>'sales'])->sum('or_money');
        $first_line['d_price'] = [
            'data' => $now_month_order_p > 0 ? $now_month_order_p : 0,
            'percent' => abs($now_month_order_p - $pre_month_order_p),
            'is_plus' => $now_month_order_p - $pre_month_order_p > 0 ? 1 : ($now_month_order_p - $pre_month_order_p == 0 ? -1 : 0)
        ];

        //交易额->月
        $now_month_order_p = Order::where('or_paied',1)->whereTime('or_create_time','month')->sum('or_money');
        $pre_month_order_p = Order::where('or_paied',1)->where('or_create_time','gt',$pre_month)->where('or_create_time','lt',$now_month)->value('sum(or_money)');
        $first_line['m_price'] = [
            'data' => $now_month_order_p > 0 ? $now_month_order_p : 0,
            'percent' => abs($now_month_order_p - $pre_month_order_p),
            'is_plus' => $now_month_order_p - $pre_month_order_p > 0 ? 1 : ($now_month_order_p - $pre_month_order_p == 0 ? -1 : 0)
        ];

        //新会员->日
        $now_day_user = DB::name('Member')->where('member_regtime','gt',$now_day)->count();
        $pre_day_user = DB::name('Member')->where('member_regtime','gt',$pre_day)->where('member_regtime','lt',$now_day)->count();
        $pre_day_user = $pre_day_user ? $pre_day_user : 0;
        $first_line['day'] = [
            'data' => $now_day_user ? $now_day_user : 0,
            'percent' => abs($now_day_user - $pre_day_user),
            'is_plus' => $now_day_user - $pre_day_user > 0 ? 1 : ($now_day_user - $pre_day_user == 0 ? -1 : 0)
        ];

        //新会员->月
        $now_month_user = DB::name('Member')->where('member_regtime','gt',$now_month)->count();
        $pre_month_user = DB::name('Member')->where('member_regtime','gt',$pre_month)->where('member_regtime','lt',$now_month)->count();
        $first_line['month'] = [
            'data' => $now_month_user ? $now_month_user : 0,
            'percent' => abs($now_month_user - $pre_month_user),
            'is_plus' => $now_month_user - $pre_month_user > 0 ? 1 : ($now_month_user - $pre_month_user == 0 ? -1 : 0)
        ];

        //本月订单总数
        $now_order_info_c = Order::where('or_create_time','gt',$now_month)->count();
        $pre_order_info_c = Order::where('or_create_time','gt',$pre_month)->where('or_create_time','lt',$now_month)->count();
        $order_info['first'] = [
            'data' => $now_order_info_c ? $now_order_info_c : 0,
            'percent' => abs($now_order_info_c - $pre_order_info_c),
            'is_plus' => $now_order_info_c - $pre_order_info_c > 0 ? 1 : ($now_order_info_c - $pre_order_info_c == 0 ? -1 : 0)
        ];

        //上月订单总数
        $second_now_month = strtotime(date('Y-m',strtotime('-1 month')));
        $second_pre_month = strtotime(date('Y-m',strtotime('-2 month')));
        $now_order_info_c = Order::where('or_create_time','gt',$pre_month)->where('or_create_time','lt',$now_month)->count();
        $pre_order_info_c = Order::where('or_create_time','gt',$second_pre_month)->where('or_create_time','lt',$second_now_month)->count();
        $order_info["second"] = [
            'data' => $now_order_info_c ? $now_order_info_c : 0,
            'percent' => abs($now_order_info_c - $pre_order_info_c),
            'is_plus' => $now_order_info_c - $pre_order_info_c > 0 ? 1 : ($now_order_info_c - $pre_order_info_c == 0 ? -1 : 0)
        ];
        $second_line['order_info'] = $order_info;


        $this->assign([
            'first_line' => $first_line,
            'second_line' => $second_line,
            'topData' => $topData,
        ]);
        return $this->fetch('welcome');
    }

    /**
     * 会员图表
     */
    public function memberchart(){
        header('Content-type:text/json');

        $starday = date('Y-m-d',strtotime('-30 day'));
        $yesterday = date('Y-m-d');

        $user_list = Db::name('member')->where('member_regtime','between time',[$starday,$yesterday])
            ->field("FROM_UNIXTIME(member_regtime,'%m-%e') as day,count(*) as count")
            ->group("FROM_UNIXTIME(member_regtime, '%Y%m%e')")
            ->order('member_regtime asc')
            ->select();//->toArray();
        $chartdata = [];
        $data = [];
        $chartdata['legend'] = ['会员数'];//分类
        $chartdata['yAxis']['maxnum'] = 0;//最大值数量
        $chartdata['xAxis'] = [date('m-d')];//X轴值
        $chartdata['series'] = [0];//分类1值
        if(!empty($user_list)) {
            foreach ($user_list as $k=>$v){
                $data['day'][] = $v['day'];
                $data['count'][] = $v['count'];
                if($chartdata['yAxis']['maxnum'] < $v['count'])
                    $chartdata['yAxis']['maxnum'] = $v['count'];
            }
            $chartdata['xAxis'] = $data['day'];//X轴值
            $chartdata['series'] = $data['count'];//分类1值
        }
        return Json::success('ok',$chartdata);
    }

    /**
     * 订单图表
     */
    public function orderchart(){
        header('Content-type:text/json');
        $cycle = $this->request->param('cycle')?:'thirtyday';//默认30天
        $datalist = [];
        switch ($cycle){
            case 'thirtyday':
                $datebefor = date('Y-m-d',strtotime('-30 day'));
                $dateafter = date('Y-m-d');
                //上期
                $pre_datebefor = date('Y-m-d',strtotime('-60 day'));
                $pre_dateafter = date('Y-m-d',strtotime('-30 day'));
                for($i=-30;$i < 0;$i++){
                    $datalist[date('m-d',strtotime($i.' day'))] = date('m-d',strtotime($i.' day'));
                }
                $order_list = Order::where('or_create_time','between time',[$datebefor,$dateafter])
                    ->field("FROM_UNIXTIME(or_create_time,'%m-%d') as day,count(*) as count,sum(or_money) as price")
                    ->group("FROM_UNIXTIME(or_create_time, '%Y%m%d')")
                    ->order('or_create_time asc')
                    ->select()->toArray();
                if(empty($order_list)) return Json::fail('无数据');
                foreach ($order_list as $k=>&$v){
                    $order_list[$v['day']] = $v;
                }
                $cycle_list = [];
                foreach ($datalist as $dk=>$dd){
                    if(!empty($order_list[$dd])){
                        $cycle_list[$dd] = $order_list[$dd];
                    }else{
                        $cycle_list[$dd] = ['count'=>0,'day'=>$dd,'price'=>''];
                    }
                }
                $chartdata = [];
                $data = [];//临时
                $chartdata['yAxis']['maxnum'] = 0;//最大值数量
                $chartdata['yAxis']['maxprice'] = 0;//最大值金额
                foreach ($cycle_list as $k=>$v){
                    $data['day'][] = $v['day'];
                    $data['count'][] = $v['count'];
                    $data['price'][] = round($v['price'],2);
                    if($chartdata['yAxis']['maxnum'] < $v['count'])
                        $chartdata['yAxis']['maxnum'] = $v['count'];//日最大订单数
                    if($chartdata['yAxis']['maxprice'] < $v['price'])
                        $chartdata['yAxis']['maxprice'] = $v['price'];//日最大金额
                }
                $chartdata['legend'] = ['订单金额','订单数'];//分类
                $chartdata['xAxis'] = $data['day'];//X轴值
                //,'itemStyle'=>$series
                $series= ['normal'=>['label'=>['show'=>true,'position'=>'top']]];
                $chartdata['series'][] = ['name'=>$chartdata['legend'][0],'type'=>'bar','itemStyle'=>$series,'data'=>$data['price']];//分类1值
                $chartdata['series'][] = ['name'=>$chartdata['legend'][1],'type'=>'bar','itemStyle'=>$series,'data'=>$data['count']];//分类2值
                //统计总数上期
                $pre_total = Order::where('or_create_time','between time',[$pre_datebefor,$pre_dateafter])
                    ->field("count(*) as count,sum(or_money) as price")
                    ->find();
                if($pre_total){
                    $chartdata['pre_cycle']['count'] = [
                        'data' => $pre_total['count']? : 0
                    ];
                    $chartdata['pre_cycle']['price'] = [
                        'data' => $pre_total['price']? : 0
                    ];
                }
                //统计总数
                $total = Order::where('or_create_time','between time',[$datebefor,$dateafter])
                    ->field("count(*) as count,sum(or_money) as price")
                    ->find();
                if($total){
                    $cha_count = intval($pre_total['count']) - intval($total['count']);
                    $pre_total['count'] = $pre_total['count']==0 ? 1 : $pre_total['count'];
                    $chartdata['cycle']['count'] = [
                        'data' => $total['count']? : 0,
                        'percent' => round((abs($cha_count)/intval($pre_total['count'])*100),2),
                        'is_plus' => $cha_count > 0 ? -1 : ($cha_count == 0 ? 0 : 1)
                    ];
                    $cha_price = round($pre_total['price'],2) - round($total['price'],2);
                    $pre_total['price'] = $pre_total['price']==0 ? 1 : $pre_total['price'];
                    $chartdata['cycle']['price'] = [
                        'data' => $total['price']? : 0,
                        'percent' => round(abs($cha_price)/$pre_total['price']*100,2),
                        'is_plus' => $cha_price > 0 ? -1 : ($cha_price == 0 ? 0 : 1)
                    ];
                }
                return Json::success('ok',$chartdata);
                break;
            case 'week':
                $weekarray=array(['周日'],['周一'],['周二'],['周三'],['周四'],['周五'],['周六']);
                $datebefor = date('Y-m-d',strtotime('-1 week Monday'));
                $dateafter = date('Y-m-d',strtotime('-1 week Sunday'));
                $order_list = Order::where('or_create_time','between time',[$datebefor,$dateafter])
                    ->field("FROM_UNIXTIME(or_create_time,'%w') as day,count(*) as count,sum(or_money) as price")
                    ->group("FROM_UNIXTIME(or_create_time, '%Y%m%e')")
                    ->order('or_create_time asc')
                    ->select()->toArray();
                //数据查询重新处理
                $new_order_list = [];
                foreach ($order_list as $k=>$v){
                    $new_order_list[$v['day']] = $v;
                }
                $now_datebefor = date('Y-m-d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600));
                $now_dateafter = date('Y-m-d',strtotime("+1 day"));
                $now_order_list = Order::where('or_create_time','between time',[$now_datebefor,$now_dateafter])
                    ->field("FROM_UNIXTIME(or_create_time,'%w') as day,count(*) as count,sum(or_money) as price")
                    ->group("FROM_UNIXTIME(or_create_time, '%Y%m%e')")
                    ->order('or_create_time asc')
                    ->select()->toArray();
                //数据查询重新处理 key 变为当前值
                $new_now_order_list = [];
                foreach ($now_order_list as $k=>$v){
                    $new_now_order_list[$v['day']] = $v;
                }
                foreach ($weekarray as $dk=>$dd){
                    if(!empty($new_order_list[$dk])){
                        $weekarray[$dk]['pre'] = $new_order_list[$dk];
                    }else{
                        $weekarray[$dk]['pre'] = ['count'=>0,'day'=>$weekarray[$dk][0],'price'=>'0'];
                    }
                    if(!empty($new_now_order_list[$dk])){
                        $weekarray[$dk]['now'] = $new_now_order_list[$dk];
                    }else{
                        $weekarray[$dk]['now'] = ['count'=>0,'day'=>$weekarray[$dk][0],'price'=>'0'];
                    }
                }
                $chartdata = [];
                $data = [];//临时
                $chartdata['yAxis']['maxnum'] = 0;//最大值数量
                $chartdata['yAxis']['maxprice'] = 0;//最大值金额
                foreach ($weekarray as $k=>$v){
                    $data['day'][] = $v[0];
                    $data['pre']['count'][] = $v['pre']['count'];
                    $data['pre']['price'][] = round($v['pre']['price'],2);
                    $data['now']['count'][] = $v['now']['count'];
                    $data['now']['price'][] = round($v['now']['price'],2);
                    if($chartdata['yAxis']['maxnum'] < $v['pre']['count'] || $chartdata['yAxis']['maxnum'] < $v['now']['count']){
                        $chartdata['yAxis']['maxnum'] = $v['pre']['count']>$v['now']['count']?$v['pre']['count']:$v['now']['count'];//日最大订单数
                    }
                    if($chartdata['yAxis']['maxprice'] < $v['pre']['price'] || $chartdata['yAxis']['maxprice'] < $v['now']['price']){
                        $chartdata['yAxis']['maxprice'] = $v['pre']['price']>$v['now']['price']?$v['pre']['price']:$v['now']['price'];//日最大金额
                    }
                }
                $chartdata['legend'] = ['上周金额','本周金额','上周订单数','本周订单数'];//分类
                $chartdata['xAxis'] = $data['day'];//X轴值
                //,'itemStyle'=>$series
                $series= ['normal'=>['label'=>['show'=>true,'position'=>'top']]];
                $chartdata['series'][] = ['name'=>$chartdata['legend'][0],'type'=>'bar','itemStyle'=>$series,'data'=>$data['pre']['price']];//分类1值
                $chartdata['series'][] = ['name'=>$chartdata['legend'][1],'type'=>'bar','itemStyle'=>$series,'data'=>$data['now']['price']];//分类1值
                $chartdata['series'][] = ['name'=>$chartdata['legend'][2],'type'=>'line','itemStyle'=>$series,'data'=>$data['pre']['count']];//分类2值
                $chartdata['series'][] = ['name'=>$chartdata['legend'][3],'type'=>'line','itemStyle'=>$series,'data'=>$data['now']['count']];//分类2值

                //统计总数上期
                $pre_total = Order::where('or_create_time','between time',[$datebefor,$dateafter])
                    ->field("count(asdf*) as count,sum(or_money) as price")
                    ->find();
                if($pre_total){
                    $chartdata['pre_cycle']['count'] = [
                        'data' => $pre_total['count']? : 0
                    ];
                    $chartdata['pre_cycle']['price'] = [
                        'data' => $pre_total['price']? : 0
                    ];
                }
                //统计总数
                $total = Order::where('or_create_time','between time',[$now_datebefor,$now_dateafter])
                    ->field("count(*) as count,sum(or_money) as price")
                    ->find();
                if($total){
                    $cha_count = intval($pre_total['count']) - intval($total['count']);
                    $pre_total['count'] = $pre_total['count']==0 ? 1 : $pre_total['count'];
                    $chartdata['cycle']['count'] = [
                        'data' => $total['count']? : 0,
                        'percent' => round((abs($cha_count)/intval($pre_total['count'])*100),2),
                        'is_plus' => $cha_count > 0 ? -1 : ($cha_count == 0 ? 0 : 1)
                    ];
                    $cha_price = round($pre_total['price'],2) - round($total['price'],2);
                    $pre_total['price'] = $pre_total['price']==0 ? 1 : $pre_total['price'];
                    $chartdata['cycle']['price'] = [
                        'data' => $total['price']? : 0,
                        'percent' => round(abs($cha_price)/$pre_total['price']*100,2),
                        'is_plus' => $cha_price > 0 ? -1 : ($cha_price == 0 ? 0 : 1)
                    ];
                }
                return Json::success('ok',$chartdata);
                break;
            case 'month':
                $weekarray=array('01'=>['1'],'02'=>['2'],'03'=>['3'],'04'=>['4'],'05'=>['5'],'06'=>['6'],'07'=>['7'],'08'=>['8'],'09'=>['9'],'10'=>['10'],'11'=>['11'],'12'=>['12'],'13'=>['13'],'14'=>['14'],'15'=>['15'],'16'=>['16'],'17'=>['17'],'18'=>['18'],'19'=>['19'],'20'=>['20'],'21'=>['21'],'22'=>['22'],'23'=>['23'],'24'=>['24'],'25'=>['25'],'26'=>['26'],'27'=>['27'],'28'=>['28'],'29'=>['29'],'30'=>['30'],'31'=>['31']);

                $datebefor = date('Y-m-01',strtotime('-1 month'));
                $dateafter = date('Y-m-d',strtotime(date('Y-m-01')));
                $order_list = Order::where('or_create_time','between time',[$datebefor,$dateafter])
                    ->field("FROM_UNIXTIME(or_create_time,'%d') as day,count(*) as count,sum(or_money) as price")
                    ->group("FROM_UNIXTIME(or_create_time, '%Y%m%e')")
                    ->order('or_create_time asc')
                    ->select()->toArray();
                //数据查询重新处理
                $new_order_list = [];
                foreach ($order_list as $k=>$v){
                    $new_order_list[$v['day']] = $v;
                }
                $now_datebefor = date('Y-m-01');
                $now_dateafter = date('Y-m-d',strtotime("+1 day"));
                $now_order_list = Order::where('or_create_time','between time',[$now_datebefor,$now_dateafter])
                    ->field("FROM_UNIXTIME(or_create_time,'%d') as day,count(*) as count,sum(or_money) as price")
                    ->group("FROM_UNIXTIME(or_create_time, '%Y%m%e')")
                    ->order('or_create_time asc')
                    ->select()->toArray();
                //数据查询重新处理 key 变为当前值
                $new_now_order_list = [];
                foreach ($now_order_list as $k=>$v){
                    $new_now_order_list[$v['day']] = $v;
                }
                foreach ($weekarray as $dk=>$dd){
                    if(!empty($new_order_list[$dk])){
                        $weekarray[$dk]['pre'] = $new_order_list[$dk];
                    }else{
                        $weekarray[$dk]['pre'] = ['count'=>0,'day'=>$weekarray[$dk][0],'price'=>'0'];
                    }
                    if(!empty($new_now_order_list[$dk])){
                        $weekarray[$dk]['now'] = $new_now_order_list[$dk];
                    }else{
                        $weekarray[$dk]['now'] = ['count'=>0,'day'=>$weekarray[$dk][0],'price'=>'0'];
                    }
                }
                $chartdata = [];
                $data = [];//临时
                $chartdata['yAxis']['maxnum'] = 0;//最大值数量
                $chartdata['yAxis']['maxprice'] = 0;//最大值金额
                foreach ($weekarray as $k=>$v){
                    $data['day'][] = $v[0];
                    $data['pre']['count'][] = $v['pre']['count'];
                    $data['pre']['price'][] = round($v['pre']['price'],2);
                    $data['now']['count'][] = $v['now']['count'];
                    $data['now']['price'][] = round($v['now']['price'],2);
                    if($chartdata['yAxis']['maxnum'] < $v['pre']['count'] || $chartdata['yAxis']['maxnum'] < $v['now']['count']){
                        $chartdata['yAxis']['maxnum'] = $v['pre']['count']>$v['now']['count']?$v['pre']['count']:$v['now']['count'];//日最大订单数
                    }
                    if($chartdata['yAxis']['maxprice'] < $v['pre']['price'] || $chartdata['yAxis']['maxprice'] < $v['now']['price']){
                        $chartdata['yAxis']['maxprice'] = $v['pre']['price']>$v['now']['price']?$v['pre']['price']:$v['now']['price'];//日最大金额
                    }

                }
                $chartdata['legend'] = ['上月金额','本月金额','上月订单数','本月订单数'];//分类
                $chartdata['xAxis'] = $data['day'];//X轴值
                //,'itemStyle'=>$series
                $series= ['normal'=>['label'=>['show'=>true,'position'=>'top']]];
                $chartdata['series'][] = ['name'=>$chartdata['legend'][0],'type'=>'bar','itemStyle'=>$series,'data'=>$data['pre']['price']];//分类1值
                $chartdata['series'][] = ['name'=>$chartdata['legend'][1],'type'=>'bar','itemStyle'=>$series,'data'=>$data['now']['price']];//分类1值
                $chartdata['series'][] = ['name'=>$chartdata['legend'][2],'type'=>'line','itemStyle'=>$series,'data'=>$data['pre']['count']];//分类2值
                $chartdata['series'][] = ['name'=>$chartdata['legend'][3],'type'=>'line','itemStyle'=>$series,'data'=>$data['now']['count']];//分类2值

                //统计总数上期
                $pre_total = Order::where('or_create_time','between time',[$datebefor,$dateafter])
                    ->field("count(*) as count,sum(or_money) as price")
                    ->find();
                if($pre_total){
                    $chartdata['pre_cycle']['count'] = [
                        'data' => $pre_total['count']? : 0
                    ];
                    $chartdata['pre_cycle']['price'] = [
                        'data' => $pre_total['price']? : 0
                    ];
                }
                //统计总数
                $total = Order::where('or_create_time','between time',[$now_datebefor,$now_dateafter])
                    ->field("count(*) as count,sum(or_money) as price")
                    ->find();
                if($total){
                    $cha_count = intval($pre_total['count']) - intval($total['count']);
                    $pre_total['count'] = $pre_total['count']==0 ? 1 : $pre_total['count'];
                    $chartdata['cycle']['count'] = [
                        'data' => $total['count']? : 0,
                        'percent' => round((abs($cha_count)/intval($pre_total['count'])*100),2),
                        'is_plus' => $cha_count > 0 ? -1 : ($cha_count == 0 ? 0 : 1)
                    ];
                    $cha_price = round($pre_total['price'],2) - round($total['price'],2);
                    $pre_total['price'] = $pre_total['price']==0 ? 1 : $pre_total['price'];
                    $chartdata['cycle']['price'] = [
                        'data' => $total['price']? : 0,
                        'percent' => round(abs($cha_price)/$pre_total['price']*100,2),
                        'is_plus' => $cha_price > 0 ? -1 : ($cha_price == 0 ? 0 : 1)
                    ];
                }
                return Json::success('ok',$chartdata);
                break;
            case 'year':
                $weekarray=array('01'=>['一月'],'02'=>['二月'],'03'=>['三月'],'04'=>['四月'],'05'=>['五月'],'06'=>['六月'],'07'=>['七月'],'08'=>['八月'],'09'=>['九月'],'10'=>['十月'],'11'=>['十一月'],'12'=>['十二月']);
                $datebefor = date('Y-01-01',strtotime('-1 year'));
                $dateafter = date('Y-12-31',strtotime('-1 year'));
                $order_list = Order::where('or_create_time','between time',[$datebefor,$dateafter])
                    ->field("FROM_UNIXTIME(or_create_time,'%m') as day,count(*) as count,sum(or_money) as price")
                    ->group("FROM_UNIXTIME(or_create_time, '%Y%m')")
                    ->order('or_create_time asc')
                    ->select()->toArray();
                //数据查询重新处理
                $new_order_list = [];
                foreach ($order_list as $k=>$v){
                    $new_order_list[$v['day']] = $v;
                }
                $now_datebefor = date('Y-01-01');
                $now_dateafter = date('Y-m-d');
                $now_order_list = Order::where('or_create_time','between time',[$now_datebefor,$now_dateafter])
                    ->field("FROM_UNIXTIME(or_create_time,'%m') as day,count(*) as count,sum(or_money) as price")
                    ->group("FROM_UNIXTIME(or_create_time, '%Y%m')")
                    ->order('or_create_time asc')
                    ->select()->toArray();
                //数据查询重新处理 key 变为当前值
                $new_now_order_list = [];
                foreach ($now_order_list as $k=>$v){
                    $new_now_order_list[$v['day']] = $v;
                }
                foreach ($weekarray as $dk=>$dd){
                    if(!empty($new_order_list[$dk])){
                        $weekarray[$dk]['pre'] = $new_order_list[$dk];
                    }else{
                        $weekarray[$dk]['pre'] = ['count'=>0,'day'=>$weekarray[$dk][0],'price'=>'0'];
                    }
                    if(!empty($new_now_order_list[$dk])){
                        $weekarray[$dk]['now'] = $new_now_order_list[$dk];
                    }else{
                        $weekarray[$dk]['now'] = ['count'=>0,'day'=>$weekarray[$dk][0],'price'=>'0'];
                    }
                }
                $chartdata = [];
                $data = [];//临时
                $chartdata['yAxis']['maxnum'] = 0;//最大值数量
                $chartdata['yAxis']['maxprice'] = 0;//最大值金额
                foreach ($weekarray as $k=>$v){
                    $data['day'][] = $v[0];
                    $data['pre']['count'][] = $v['pre']['count'];
                    $data['pre']['price'][] = round($v['pre']['price'],2);
                    $data['now']['count'][] = $v['now']['count'];
                    $data['now']['price'][] = round($v['now']['price'],2);
                    if($chartdata['yAxis']['maxnum'] < $v['pre']['count'] || $chartdata['yAxis']['maxnum'] < $v['now']['count']){
                        $chartdata['yAxis']['maxnum'] = $v['pre']['count']>$v['now']['count']?$v['pre']['count']:$v['now']['count'];//日最大订单数
                    }
                    if($chartdata['yAxis']['maxprice'] < $v['pre']['price'] || $chartdata['yAxis']['maxprice'] < $v['now']['price']){
                        $chartdata['yAxis']['maxprice'] = $v['pre']['price']>$v['now']['price']?$v['pre']['price']:$v['now']['price'];//日最大金额
                    }
                }
                $chartdata['legend'] = ['去年金额','今年金额','去年订单数','今年订单数'];//分类
                $chartdata['xAxis'] = $data['day'];//X轴值
                //,'itemStyle'=>$series
                $series= ['normal'=>['label'=>['show'=>true,'position'=>'top']]];
                $chartdata['series'][] = ['name'=>$chartdata['legend'][0],'type'=>'bar','itemStyle'=>$series,'data'=>$data['pre']['price']];//分类1值
                $chartdata['series'][] = ['name'=>$chartdata['legend'][1],'type'=>'bar','itemStyle'=>$series,'data'=>$data['now']['price']];//分类1值
                $chartdata['series'][] = ['name'=>$chartdata['legend'][2],'type'=>'line','itemStyle'=>$series,'data'=>$data['pre']['count']];//分类2值
                $chartdata['series'][] = ['name'=>$chartdata['legend'][3],'type'=>'line','itemStyle'=>$series,'data'=>$data['now']['count']];//分类2值

                //统计总数上期
                $pre_total = Order::where('or_create_time','between time',[$datebefor,$dateafter])
                    ->field("count(*) as count,sum(or_money) as price")
                    ->find();
                if($pre_total){
                    $chartdata['pre_cycle']['count'] = [
                        'data' => $pre_total['count']? : 0
                    ];
                    $chartdata['pre_cycle']['price'] = [
                        'data' => $pre_total['price']? : 0
                    ];
                }
                //统计总数
                $total = Order::where('or_create_time','between time',[$now_datebefor,$now_dateafter])
                    ->field("count(*) as count,sum(or_money) as price")
                    ->find();
                if($total){
                    $cha_count = intval($pre_total['count']) - intval($total['count']);
                    $pre_total['count'] = $pre_total['count']==0 ? 1 : $pre_total['count'];
                    $chartdata['cycle']['count'] = [
                        'data' => $total['count']? : 0,
                        'percent' => round((abs($cha_count)/intval($pre_total['count'])*100),2),
                        'is_plus' => $cha_count > 0 ? -1 : ($cha_count == 0 ? 0 : 1)
                    ];
                    $cha_price = round($pre_total['price'],2) - round($total['price'],2);
                    $pre_total['price'] = $pre_total['price']==0 ? 1 : $pre_total['price'];
                    $chartdata['cycle']['price'] = [
                        'data' => $total['price']? : 0,
                        'percent' => round(abs($cha_price)/$pre_total['price']*100,2),
                        'is_plus' => $cha_price > 0 ? -1 : ($cha_price == 0 ? 0 : 1)
                    ];
                }
                return Json::success('ok',$chartdata);
                break;
            default:
                break;
        }


    }

    public function  setTheme($theme){
        cache('theme',$theme);
        $this->success('切换主题成功');
    }

}
