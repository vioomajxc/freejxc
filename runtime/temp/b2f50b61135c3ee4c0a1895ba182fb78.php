<?php /*a:3:{s:64:"D:\wamp\www\freejxc\application\admin\view\report\financial.html";i:1581094187;s:61:"D:\wamp\www\freejxc\application\admin\view\pub\container.html";i:1580970373;s:62:"D:\wamp\www\freejxc\application\admin\view\pub\buildchart.html";i:1581074324;}*/ ?>
<!doctype html>
<html class="x-admin-sm">
<head>
    <meta charset="UTF-8">
    <title>后台登录</title>
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="stylesheet" href="/static/css/font.css">
<!--    <link rel="stylesheet" href="/static/css/xadmin.css">-->
    <link rel="stylesheet" href="/static/lib/layui/css/layui.css">
    <script src="/static/lib/layui/layui.all.js" charset="utf-8"></script>
    <script type="text/javascript" src="/static/js/xadmin.js"></script>
    <script type="text/javascript" src="/static/js/jquery.min.js"></script>
    <script type="text/javascript" src="/static/js/jquery.form.js"></script>
    <script type="text/javascript" src="/static/js/lotus.js"></script>
    
<link rel="stylesheet" href="/static/lib/daterangepicker/daterangepicker.css">
<link href="/system/frame/css/plugins/footable/footable.core.css" rel="stylesheet">
<script src="/static/lib/sweetalert2/sweetalert2.all.min.js"></script>
<script src="/static/lib/moment.js"></script>
<script src="/static/lib/daterangepicker/daterangepicker.js"></script>
<link href="/system/frame/css/bootstrap.min.css?v=3.4.0" rel="stylesheet">

    
    <link href="/system/frame/css/font-awesome.min.css?v=4.3.0" rel="stylesheet">
<script src="/static/lib/echarts.common.min.js"></script>
<script src="/static/lib/requirejs/require.js"></script>
<link href="/system/frame/css/style.min.css?v=3.0.0" rel="stylesheet">
<script src="/system/frame/js/bootstrap.min.js"></script>
    <script>
        window.controlle="report";
        window.module="admin";
    </script>

<script>
    var hostname = location.hostname;
    if(location.port) hostname += ':' + location.port;
    requirejs.config({
        map: {
            '*': {
                'css': '/static/lib/requirejs/require-css.js'
            }
        },
        shim:{
            'iview':{
                deps:['css!iviewcss']
            },
            'layer':{
                deps:['css!layercss']
            }
        },
        baseUrl:'//'+hostname+'/',
        paths: {
            'static':'static',
            'system':'system',
            'vue':'static/lib/vue/dist/vue.min',
            'axios':'static/lib/axios.min',
            'iview':'static/lib/iview/dist/iview.min',
            'iviewcss':'static/lib/iview/dist/styles/iview',
            'lodash':'static/lib/lodash',
            'layer':'static/lib/layer/layer',
            'layercss':'static/lib/layer/theme/default/layer',
            'jquery':'static/lib/jquery/jquery.min',
            'moment':'static/lib/moment',
            'sweetalert':'static/lib/sweetalert2/sweetalert2.all.min'

        },
        basket: {
            excludes:['system/js/index','system/util/mpVueComponent','system/util/mpVuePackage']
//            excludes:['system/util/mpFormBuilder','system/js/index','system/util/mpVueComponent','system/util/mpVuePackage']
        }
    });
</script>
<script type="text/javascript" src="/system/util/mpFrame.js"></script>
    <style>
    body{
        background-color: white;
    }
    .lotus-form{
        padding-top: 15px;
    }
    /*.layui-fluid {*/
    /*    position: relative;*/
    /*    margin: 0 auto;*/
    /*    padding: 5px 5px;*/
    /*}*/
    .layui-table-view {
        margin: 0px 0px;
    }
    </style>
</head>
<body>

<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-content">
                <div class="m-b m-l">
                    <div action="" class="form-inline">
                        <div class="search-item" data-name="status">
                            <span>创建时间：</span>
                            <a  class="btn <?php if(!$where['data'] && $where['date']==''): ?>btn-primary btn-sm <?php else: ?>btn-outline btn-link<?php endif; ?>" href="<?php echo Url('financial',['date'=>'']); ?>">全部</a>
                            <a  class="btn <?php if(!$where['data'] && $where['date']=='today'): ?>btn-primary btn-sm <?php else: ?>btn-outline btn-link<?php endif; ?>" href="<?php echo Url('financial',['date'=>'today']); ?>">今天</a>
                            <a  class="btn <?php if(!$where['data'] && $where['date']=='week'): ?>btn-primary btn-sm<?php else: ?>btn-outline btn-link<?php endif; ?>" href="<?php echo Url('financial',['date'=>'week']); ?>">本周</a>
                            <a  class="btn <?php if(!$where['data'] && $where['date']=='month'): ?>btn-primary btn-sm<?php else: ?>btn-outline btn-link<?php endif; ?>" href="<?php echo Url('financial',['date'=>'month']); ?>">本月</a>
                            <a  class="btn <?php if(!$where['data'] && $where['date']=='quarter'): ?>btn-primary btn-sm<?php else: ?>btn-outline btn-link<?php endif; ?>" href="<?php echo Url('financial',['date'=>'quarter']); ?>">本季度</a>
                            <a  class="btn <?php if(!$where['data'] && $where['date']=='year'): ?>btn-primary btn-sm<?php else: ?>btn-outline btn-link<?php endif; ?>" href="<?php echo Url('financial',['date'=>'year']); ?>">本年</a>
                            <div class="datepicker" style="display: inline-block;">
                                <a  class="btn btn-link <?php if($where['data']): ?>btn-primary<?php else: ?>btn-outline<?php endif; ?>" href="javascript:(0)" data-value="<?php echo !empty($where['data']) ? htmlentities($where['data']) : 'no'; ?>">自定义</a>
                            </div>
                            <form action="">
                            <input class="search-item-value" type="hidden" name="data" value="<?php echo htmlentities($where['data']); ?>" />
                            </form>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="ibox">
                            <div class="ibox-content">
                                <?php if(is_array($header) || $header instanceof \think\Collection || $header instanceof \think\Paginator): $i = 0; $__LIST__ = $header;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?>
                                <div class="col-sm-6">
                                    <div class="widget style1 <?php echo htmlentities($val['color']); ?>-bg" style="height: 120px;">
                                        <div class="row" style="margin-top: 16px;padding: 0 20px;">
                                            <div class="col-xs-4">
                                                <i class="fa <?php echo htmlentities($val['class']); ?> fa-5x"></i>
                                            </div>
                                            <div class="col-xs-8 text-right">
                                                <span> <?php echo htmlentities($val['name']); ?> </span>
                                                <h2 class="font-bold"><?php echo htmlentities($val['value']); ?></h2>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!--------------------------->

<div class="row">
        <div class="col-sm-12">
            <div class="col-sm-8">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>主要数据统计</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
<!--                                                <a class="close-link">-->
<!--                                                    <i class="fa fa-times"></i>-->
<!--                                                </a>-->
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div id="ec-goods-count"  style="height:390px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>支付方式</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                                                <!-- <a class="close-link">
                                                    <i class="fa fa-times"></i>
                                                </a> -->
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div  id="ec-order-count" style="height:390px;"></div>
                    </div>
                </div>
            </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
            <div class="col-sm-4">
                <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>支出详情</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
<!--                                                <a class="close-link">-->
<!--                                                    <i class="fa fa-times"></i>-->
<!--                                                </a>-->
                    </div>
                </div>
                <div class="ibox-content">
                    <div  id="ec-zhichu-count" style="height:300px;"></div>
                </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>营收详情</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
<!--                                                <a class="close-link">-->
<!--                                                    <i class="fa fa-times"></i>-->
<!--                                                </a>-->
                    </div>
                </div>
                <div class="ibox-content">
                    <div   id="ec-revenue-count" style="height:300px;"></div>
                </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>最近交易记录</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
<!--                                                <a class="close-link">-->
<!--                                                    <i class="fa fa-times"></i>-->
<!--                                                </a>-->
                    </div>
                </div>
                <div class="ibox-content" >
                    <div id="ec-new-count">
                        <table width="100%">
                    <?php if(is_array($trans) || $trans instanceof \think\Collection || $trans instanceof \think\Paginator): $i = 0; $__LIST__ = $trans;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <tr style="height:40px;line-height:40px;">
                        <td><?php echo htmlentities($vo['member_name']); ?></td>
                        <td>购买</td>
                        <td>￥<?php echo htmlentities($vo['or_money']); ?></td>
                    </tr>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                         </table>
                    </div>
                </div>
                </div>
            </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-content">

            <div class="text-left">
                <?php if($price['pay_price_wx'] > 0): ?>
                <div class="count-price">
                    <span>微信支付金额：<strong class="text-danger">￥<?php echo htmlentities($price['pay_price_wx']); ?></strong></span>
                </div>
                <?php endif; if($price['pay_price_cash'] > 0): ?>
                <div class="count-price">
                    <span>现金支付金额：<strong class="text-danger">￥<?php echo htmlentities(number_format($price['pay_price_cash'],2)); ?></strong></span>
                </div>
                <?php endif; if($price['pay_price_alipay'] > 0): ?>
                <div class="count-price">
                    <span>支付宝支付金额：<strong class="text-danger">￥<?php echo htmlentities($price['pay_price_alipay']); ?></strong></span>
                </div>
                <?php endif; ?>
            </div>
            <!--div class="m-t-n-lg text-right">
                <div class="input-group">
                    <span class="input-group-btn">
                        <a  class="btn btn-sm btn-info btn-outline" href="<?php echo Url('financial',['export'=>'1']); ?>">Excel导出</a>
                      </span>
                </div>
            </div-->
        </div>
    </div>
</div>

<script src="{__FRAME_PATH}js/content.min.js?v=1.0.0"></script>
<script>
    (function(){
        var option = {
            title: {
                text: '数据统计'
            },
            tooltip: {
                trigger: 'axis'
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                splitLine: {show: false},
                data: <?=json_encode($orderDays)?>
            },
            yAxis: {
                splitLine: {
                    show: false
                },
                type: 'value'
            },
            legend: {
                orient: 'horizontal',
                left: 'center',
                top: 25,
                data: ['营业额','支出','盈利']
            },
            toolbox: {
                left: 'right',
                feature: {
                    restore: {},
                    saveAsImage: {}
                }
            },
            dataZoom: [{
                startValue: '<?php
                    $index = count($orderDays) > 30 ? count($orderDays)-30 : 0;
                    if(isset($orderDays[$index]))
                        echo $orderDays[$index];
                    ?>'
            }, {
                type: 'inside'
            }],
            visualMap: {
                top: 10,
                right: 10,
                pieces: [{
                    gt: 0,
                    lte: 50,
                    color: '#096'
                }, {
                    gt: 50,
                    lte: 100,
                    color: '#ffde33'
                }, {
                    gt: 100,
                    lte: 150,
                    color: '#ff9933'
                }, {
                    gt: 150,
                    lte: 200,
                    color: '#cc0033'
                }, {
                    gt: 200,
                    lte: 300,
                    color: '#660099'
                }, {
                    gt: 300,
                    color: '#7e0023'
                }],
                outOfRange: {
                    color: '#999'
                }
            },
            series: <?= json_encode($Statistic)?>
        };
        var myChart = echarts.init(document.getElementById('ec-goods-count'),'light');
        myChart.setOption(option);
    })();
    (function(){
        var option = {
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b}: {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                x: 'left',
                data:['商品成本','优惠券抵扣','积分抵扣','推广人佣金','会员充值赠送']
            },
            series: [
                {
                    name:'支出',
                    type:'pie',
                    radius: ['40%', '70%'],
                    avoidLabelOverlap: false,
                    label: {
                        normal: {
                            show: false,
                            position: 'center'
                        },
                        emphasis: {
                            show: true,
                            textStyle: {
                                fontSize: '15',
                                fontWeight: 'bold'
                            }
                        }
                    },
                    labelLine: {
                        normal: {
                            show: false
                        }
                    },
                    data:<?= json_encode($data)?>
                }
            ]
        };
        var myChart = echarts.init(document.getElementById('ec-zhichu-count'),'light');
            myChart.setOption(option);
    })();
    (function(){
        var option = {
            color: ['#3398DB'],
            tooltip : {
                trigger: 'axis',
                axisPointer : {
                    type : 'shadow'
                }
            },
            grid: {
                left: '3%',
                right: '4%',
                top: '4%',
                containLabel: true
            },
            yAxis : [
                {
                    inverse:true,
                    axisLine: {show: false},
                    type : 'category',
                    data : ['1、普通商品', '2、拼团商品', '3、会员充值','4、秒杀商品'],
                    axisTick: {
                        alignWithLabel: true
                    }
                }
            ],
            xAxis : [
                {
                    show:false,
                    type : 'value'
                }
            ],
            series : [
                {
                    name:'金额',
                    type:'bar',
                    barWidth: '60%',
                    data:<?= json_encode([$ordinary,$pink,$recharge,$seckill])?>
                }
            ]
        };
        var myChart = echarts.init(document.getElementById('ec-revenue-count'),'light');
            myChart.setOption(option);
    })();
    (function(){
        var option = {
            title: {
                text: '支付统计'
            },
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient: 'horizontal',
                left: 'left',
                top: 25,
                data: <?=urldecode(json_encode(array_keys($orderCount)))?>,
                selected:<?php
                $data = [];
                $selected = [];
                foreach ($orderCount as $k=>$count){
                    $data[] = ['value'=>$count,'name'=>$k];
                    $selected[$k] = $count>0;
                }
                echo urldecode(json_encode($selected));
                ?>
            },
            series : [
                {
                    name: '订单数量',
                    type: 'pie',
                    radius : '55%',
                    center: ['50%', '60%'],
                    data:<?=urldecode(json_encode($data))?>,
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };
        var myChart = echarts.init(document.getElementById('ec-order-count'),'light');
        myChart.setOption(option);
    })();
    var dateInput =$('.datepicker');
    dateInput.daterangepicker({
        autoUpdateInput: false,
        "opens": "center",
        "drops": "down",
        "ranges": {
            '今天': [moment(), moment().add(1, 'days')],
            '昨天': [moment().subtract(1, 'days'), moment()],
            '上周': [moment().subtract(6, 'days'), moment()],
            '前30天': [moment().subtract(29, 'days'), moment()],
            '本月': [moment().startOf('month'), moment().endOf('month')],
            '上月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        "locale" : {
            applyLabel : '确定',
            cancelLabel : '取消',
            fromLabel : '起始时间',
            toLabel : '结束时间',
            format : 'YYYY/MM/DD',
            customRangeLabel : '自定义',
            daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],
            monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月',
                '七月', '八月', '九月', '十月', '十一月', '十二月' ],
            firstDay : 1
        }
    });
    dateInput.on('apply.daterangepicker', function(ev, picker) {
        $("input[name=data]").val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
        $('form').submit();
    });
</script>


</body>
</html>