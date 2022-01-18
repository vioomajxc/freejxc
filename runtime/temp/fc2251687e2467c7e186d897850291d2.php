<?php /*a:3:{s:61:"D:\wamp\www\freejxc\application\admin\view\report\member.html";i:1581170700;s:61:"D:\wamp\www\freejxc\application\admin\view\pub\container.html";i:1580970373;s:62:"D:\wamp\www\freejxc\application\admin\view\pub\buildchart.html";i:1581074324;}*/ ?>
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
<style>
    .btn-group-sm>.btn, .btn-sm{
         padding: 4px 10px;
         font-size: 12px;
     }
    .btn{
        padding: 4px 10px;
        font-size: 12px;

    }
    .search-form{
        margin-top: 0;
    }
    .search-form .search-item span{
        margin-right: 0;
    }
    .search-form .search-item{
        padding: 0;
    }
    .search-form .search-item-css{
        padding: 6px 0;
    }
</style>

    
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
                <div class="row">
                    <div class="m-b m-l">
                        <form action="" class="form-inline search-form">
                                <div class="search-item" data-name="date">
                                    <span>创建时间：</span>
                                    <button type="button" class="btn btn-outline btn-link" data-value="">本月</button>
                                    <button type="button" class="btn btn-outline btn-link" data-value="today">今天</button>
                                    <button type="button" class="btn btn-outline btn-link" data-value="week">本周</button>
                                    <button type="button" class="btn btn-outline btn-link" data-value="quarter">本季度</button>
                                    <button type="button" class="btn btn-outline btn-link" data-value="year">本年</button>
                                    <div class="datepicker" style="display: inline-block;">
                                        <button type="button" class="btn btn-outline btn-link" data-value="<?php echo !empty($where['date']) ? htmlentities($where['date']) : 'no'; ?>">自定义</button>
                                    </div>
                                    <input class="search-item-value" type="hidden" name="date" value="<?php echo htmlentities($where['date']); ?>" />
                                </div>
                                <div class="search-item search-item-css" data-name="shop">
                                    <span>选择店铺：</span>
                                    <button type="button" class="btn btn-outline btn-link" data-value="">全部</button>
                                    <?php if(is_array($shops) || $shops instanceof \think\Collection || $shops instanceof \think\Paginator): $i = 0; $__LIST__ = $shops;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                    <button type="button" class="btn btn-outline btn-link" data-value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['shop_name']); ?></button>
                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                    <input class="search-item-value" type="hidden" name="shop" value="<?php echo htmlentities($where['shop']); ?>" />
                                </div>
                                <div class="search-item search-item-css" data-name="status">
                                    <span>选择状态：</span>
                                    <button type="button" class="btn btn-outline btn-link" data-value="">默认</button>
                                    <button type="button" class="btn btn-outline btn-link" data-value="1">正常</button>
                                    <button type="button" class="btn btn-outline btn-link" data-value="0">锁定</button>
                                    <input class="search-item-value" type="hidden" name="status" value="<?php echo htmlentities($where['status']); ?>" />
                                </div>
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

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
                </div>
                <div class="ibox-content">
                    <div  data-hide="true" id="container" style="height: 390px;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>消费统计</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
                <div class="ibox-content" data-hide="true" id="user_index" style="height: 310px"></div>
                <div class="ibox-content"  style="height: 115px">
                    <div class="col-sm-6" style="border-right: 1px solid #CCCCCC">
                        <p style="font-size: 12px"><?php echo htmlentities($consume['rightTitle']['title']); ?></p>
                        <p style="font-size: 16px;color:#ed5565"><i class="fa <?php echo htmlentities($consume['rightTitle']['icon']); ?>" style="padding-right: 10px;"></i>&nbsp;&nbsp;￥<?php echo htmlentities(number_format($consume['rightTitle']['number'],2)); ?></p>
                    </div>
                    <div class="col-sm-6">
                        <p style="font-size: 12px"><?php echo htmlentities($consume['leftTitle']['title']); ?></p>
                        <p style="font-size: 16px;color:#23c6c8;"><i class="fa <?php echo htmlentities($consume['leftTitle']['icon']); ?>" style="padding-right: 10px;">&nbsp;&nbsp;<?php echo htmlentities(number_format($consume['leftTitle']['count'],2)); ?></i></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="row">
<div class="col-sm-6">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>地域分布</h5>
            <div class="ibox-tools">
                <a class="collapse-link">
                    <i class="fa fa-chevron-up"></i>
                </a>
            </div>
        </div>
        <div class="ibox-content">
            <div  id="distribution" style="height:290px;"></div>
        </div>
    </div>
</div>
<div class="col-sm-6">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>消费排行榜 TOP20</h5>
            <div class="ibox-tools">
                <a class="collapse-link">
                    <i class="fa fa-chevron-up"></i>
                </a>
            </div>
        </div>
        <div class="ibox-content" style="height:290px;overflow-y: scroll;background-color: #ffffff">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th class="text-center">排名</th>
                    <th class="text-center">会员</th>
                    <th class="text-center">消费金额 ￥</th>
                    <th class="text-center">余额 ￥</th>
                </tr>
                </thead>
                <tbody>
                <?php if(is_array($user_null) || $user_null instanceof \think\Collection || $user_null instanceof \think\Paginator): $i = 0; $__LIST__ = $user_null;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <tr>
                    <td class="text-center"><?php echo htmlentities($key+1); ?></td>
                    <td class="text-center"><?php echo htmlentities($vo['member_name']); ?></td>
                    <td class="text-center"><?php echo htmlentities(number_format($vo['total_money'],2)); ?></td>
                    <td class="text-center"><?php echo htmlentities(number_format($vo['card_balance'],2)); ?></td>
                </tr>
                <?php endforeach; endif; else: echo "" ;endif; if(!$user_null): ?>
                <tr>
                    <td colspan="6" class="text-center"><h4>暂无数据</h4></td>
                </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>


<script src="/system/frame/js/content.min.js?v=1.0.0"></script>
<script>
    $('.search-item>.btn').on('click',function(){
        var that = $(this),value = that.data('value'),p = that.parent(),name = p.data('name'),form = p.parents();
        form.find('input[name="'+name+'"]').val(value);
        form.submit();
    });
    $('.search-item-value').each(function(){
        var that = $(this),name = that.attr('name'), value = that.val(),dom = $('.search-item[data-name="'+name+'"] .btn[data-value="'+value+'"]');
        dom.eq(0).removeClass('btn-outline btn-link').addClass('btn-primary btn-sm')
            .siblings().addClass('btn-outline btn-link').removeClass('btn-primary btn-sm')
    });
    (function(){
        var dom = document.getElementById("container"), myChart = echarts.init(dom), option = null;
        option = {
            tooltip: {trigger: 'axis'},
            toolbox: {left: 'right', feature: {restore: {}, saveAsImage: {}}},
            legend: {orient: 'horizontal', left: 'center', top: 25, data: <?php echo $user_index['name']?$user_index['name']:'false';?> || []},
            xAxis: {type: 'category', splitLine: {show: false}, data:<?php echo $user_index['date']?$user_index['date']:'false';?> || []},
            yAxis: {type: 'log',show :true,min:1},
            grid: {left: '3%', right: '4%', bottom: '3%', containLabel: true},
            series:<?php echo $user_index['series']?$user_index['series']:'false';?> || []
            <?php if($where['date']==null || $where['date']=='today'){?>
            ,dataZoom: [{
                endValue : <?php echo $where['date']=='today'?date('H',time()):date('d',time());?>
            }, {
                type: 'inside'
            }],
            <?php }?>
        };
        if (option && typeof option === "object") {
            myChart.setOption(option, true);
        }
    })();
    (function() {
        var dom = document.getElementById("user_index"), myChart = echarts.init(dom), option=null;
            option={
            title:{text:<?php echo empty($consume['series']['data'])?'false':json_encode($consume['title']);?> || '暂无数据'},
            tooltip: {trigger: 'item', formatter: "{a} <br/>{b}: {c} ({d}%)"},
            series: [<?php echo empty($consume['series']['data'])?'false':json_encode($consume['series']);?> || {name:'暂无数据',type:'pie',radius:['40%', '50%'],data:[{value:100,name:'暂无数据'}]}]
        };
        if (option && typeof option === "object"){
            myChart.setOption(option, true);
        }
    })();
    (function () {
        var distributionChart = echarts.init(document.getElementById("distribution"));
        option={
            tooltip: {trigger: 'item', formatter: "{a} <br/>{b}: {c} ({d}%)"},
            legend:{
                orient: 'vertical',
                x: 'left',
                data:<?php echo empty($form['legend_date'])?'false':json_encode($form['legend_date']);?> || [{name:'暂无数据',icon:'circle'}]
            },
            series: [
                {
                    name:'<?php echo isset($form['legend_date'][0]['name'])?$form['legend_date'][0]['name']:'暂无数据';?>',
                    type:'pie',
                    radius: ['70%', '90%'],
                    label: {
                        normal: {
                            show: false,
                            position: 'center'
                        },
                        emphasis: {
                            show: true,
                            textStyle: {
                                fontSize: '20',
                                fontWeight: 'bold'
                            }
                        }
                    },
                    labelLine: {
                        normal: {
                            show: false
                        }
                    },
                    data:<?php echo empty($form['series_date'])?'false':json_encode($form['series_date']);?> || [{value:100,name:'暂无数据'}]
                }
            ]
        };
        if (option && typeof option === "object") {
            distributionChart.setOption(option, true);
        }
    })();
    


    $(".open_image").on('click',function (e) {
        var image = $(this).data('image');
        $eb.openImage(image);
    })
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
    dateInput.on('cancel.daterangepicker', function(ev, picker) {
        //$("input[name=limit_time]").val('');
    });
    dateInput.on('apply.daterangepicker', function(ev, picker) {
        $("input[name=date]").val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
        $('form').submit();
    });

</script>

</body>
</html>