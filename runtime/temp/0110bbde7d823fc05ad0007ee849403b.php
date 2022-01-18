<?php /*a:2:{s:61:"D:\wamp\www\freejxc\application\admin\view\index\welcome.html";i:1580937539;s:57:"D:\wamp\www\freejxc\application\admin\view\pub\modal.html";i:1580923563;}*/ ?>
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
    <script src="/static/lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="/static/js/xadmin.js"></script>
    <script type="text/javascript" src="/static/js/jquery.min.js"></script>
    <script type="text/javascript" src="/static/js/jquery.form.js"></script>
    <script type="text/javascript" src="/static/js/lotus.js"></script>
    <script type="text/javascript" src="/static/js/jquery.jqprint-0.3.js"></script>
    
    
    
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

<link rel="stylesheet" href="/static/css/xadmin.css">
<script src="/static/lib/echarts/echarts.common.min.js"></script>
<script src="/static/lib/echarts/theme/macarons.js"></script>
<script src="/static/lib/echarts/theme/westeros.js"></script>
<script src="/static/lib/requirejs/require.js"></script>
<link href="/system/frame/css/style.min.css" rel="stylesheet">
<link href="/system/frame/css/bootstrap.min.css" rel="stylesheet">
<script src="/system/frame/js/bootstrap.min.js"></script>
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
<style>
    body{
        background-color: white;
    }
</style>
<div class="layui-fluid layui-anim layui-anim-upbit">
    <div class="layui-row layui-col-space15">

        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">数据统计</div>
                <div class="layui-card-body ">
                    <ul class="layui-row layui-col-space10 layui-this x-admin-carousel x-admin-backlog">
                        <li class="layui-col-md3 layui-col-xs6">
                            <a href="javascript:;" onclick="xadmin.add_tab('商品管理','/index.php/admin/goods/goodslist.html')" class="x-admin-backlog-body">
                                <h3>商品数</h3>
                                <p>
                                    <cite><?php echo htmlentities($topData['goodsNumbers']); ?></cite></p>
                            </a>
                        </li>
                        <li class="layui-col-md3 layui-col-xs6">
                            <a href="javascript:;" class="x-admin-backlog-body">
                                <h3>会员数</h3>
                                <p>
                                    <cite><?php echo htmlentities($topData['memberNumbers']); ?></cite></p>
                            </a>
                        </li>
                        <li class="layui-col-md3 layui-col-xs6">
                            <a href="javascript:;" class="x-admin-backlog-body">
                                <h3>订单数</h3>
                                <p>
                                    <cite><?php echo htmlentities($topData['orderNumbers']); ?></cite></p>
                            </a>
                        </li>
                        <li class="layui-col-md3 layui-col-xs6">
                            <a href="javascript:;" class="x-admin-backlog-body">
                                <h3>库存数</h3>
                                <p>
                                    <cite><?php echo htmlentities($topData['stockNumbers']); ?></cite></p>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
<div id="app">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>销售订单</h5>
                    <div class="pull-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-white" :class="{'active': active == 'thirtyday'}" v-on:click="getlist('thirtyday')">30天</button>
                            <button type="button" class="btn btn-xs btn-white" :class="{'active': active == 'week'}" v-on:click="getlist('week')">周</button>
                            <button type="button" class="btn btn-xs btn-white" :class="{'active': active == 'month'}" v-on:click="getlist('month')">月</button>
                            <button type="button" class="btn btn-xs btn-white" :class="{'active': active == 'year'}" v-on:click="getlist('year')">年</button>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-9" style="width:80%;float:left;">
                            <div class="flot-chart-content echarts" ref="order_echart" id="flot-dashboard-chart1"></div>
                        </div>
                        <div class="col-lg-3" style="width:20%;float:right;">
                            <ul class="stat-list">
                                <li>
                                    <h2 class="no-margins ">{{pre_cycleprice}}</h2>
                                    <small>{{precyclename}}销售额</small>
                                </li>
                                <li>
                                    <h2 class="no-margins ">{{cycleprice}}</h2>
                                    <small>{{cyclename}}销售额</small>
                                    <div class="stat-percent text-navy" v-if='cycleprice_is_plus ===1'>
                                        {{cycleprice_percent}}%
                                        <i  class="fa fa-level-up"></i>
                                    </div>
                                    <div class="stat-percent text-danger" v-else-if='cycleprice_is_plus === -1'>
                                        {{cycleprice_percent}}%
                                        <i class="fa fa-level-down"></i>
                                    </div>
                                    <div class="stat-percent" v-else>
                                        {{cycleprice_percent}}%
                                    </div>
                                    <div class="progress progress-mini">
                                        <div :style="{width:cycleprice_percent+'%'}" class="progress-bar box"></div>
                                    </div>
                                </li>
                                <li>
                                    <h2 class="no-margins ">{{pre_cyclecount}}</h2>
                                    <small>{{precyclename}}订单总数</small>
                                </li>
                                <li>
                                    <h2 class="no-margins">{{cyclecount}}</h2>
                                    <small>{{cyclename}}订单总数</small>
                                    <div class="stat-percent text-navy" v-if='cyclecount_is_plus ===1'>
                                        {{cyclecount_percent}}%
                                        <i class="fa fa-level-up"></i>
                                    </div>
                                    <div class="stat-percent text-danger" v-else-if='cyclecount_is_plus === -1'>
                                        {{cyclecount_percent}}%
                                        <i  class="fa fa-level-down"></i>
                                    </div>
                                    <div class="stat-percent " v-else>
                                        {{cyclecount_percent}}%
                                    </div>
                                    <div class="progress progress-mini">
                                        <div :style="{width:cyclecount_percent+'%'}" class="progress-bar box"></div>
                                    </div>
                                </li>


                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>会员</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="flot-chart">
                                <div class="flot-chart-content" ref="user_echart" id="flot-dashboard-chart2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body ">
                    <table class="layui-table">
                        <tbody>
                        <tr>
                            <th>版权所有</th>
                            <td>
                                <a href="https://www.viooma.com" target="_blank">Power By ViooMa 访问官网</a></td>
                        </tr>
                        <tr>
                            <th>技术支持</th>
                            <td>QQ(12612019)</td></tr>
                            <tr>
                            <th>捐赠作者:</th>
                            <td><button id="pay" class="layui-btn layui-btn-radius layui-btn-normal">捐赠作者</button></td>
                        </tr>
                        <!--tr>
                            <th>官方免费交流群:</th>
                            <td><a target="_blank" href="//shang.qq.com/wpa/qunwpa?idkey=7f1adcd32277ae1b02c801a7c488e07e5997beecf32d7a0776850807bbfe3e57"><img border="0" src="//pub.idqqimg.com/wpa/images/group.png" alt="PHP-LotusAdmin官方论坛" title="PHP-LotusAdmin官方论坛"></a></td>
                        </tr-->
                        <tr>
                            <th>如需订制请联系开发者:</th>
                            <td><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=12612019&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:12612019:51" alt="点击这里给我发消息" title="联系站长"/></a></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <style id="welcome_style"></style>
    </div>
</div>
</div>


<style scoped>
    .box{width:0px;}
</style>
<script>
     require(['vue','axios','layer'],function(Vue,axios,layer){
        $("#pay").bind('click',function () {
            var layer = layui.layer;
            layer.alert('<img width="100%" src="/static/images/wechat.png">',{
                title:'微信捐助开发者,请辛苦的作者喝杯咖啡吧!!!',
                offset:['10%','30%'],
                cancel:function(){
                    layer.msg('喵喵喵,小气的家伙不跟你玩啦,小锤锤捶胸口',{anim:6,icon:5});
                },
                btnAlign: 'c',
                btn:[]
            });
        });
        new Vue({
            el:"#app",
            data:{
                option:{},
                myChart:{},
                active:'thirtyday',
                cyclename:'最近30天',
                precyclename:'上个30天',
                cyclecount:0,
                cycleprice:0,
                cyclecount_percent:0,
                cycleprice_percent:0,
                cyclecount_is_plus:0,
                cycleprice_is_plus:0,
                pre_cyclecount:0,
                pre_cycleprice:0
            },
            methods:{
                info:function () {
                    var that=this;
                    axios.get("<?php echo Url('memberchart'); ?>").then((res)=>{
                        that.myChart.user_echart.setOption(that.userchartsetoption(res.data.data));
                    });
                },
                getlist:function (e) {
                    var that=this;
                    var cycle = e!=null ? e :'thirtyday';
                    axios.get("<?php echo Url('orderchart'); ?>?cycle="+cycle).then((res)=>{
                            that.myChart.order_echart.clear();
                            that.myChart.order_echart.setOption(that.orderchartsetoption(res.data.data));
                            that.active = cycle;
                            switch (cycle){
                                case 'thirtyday':
                                    that.cyclename = '最近30天';
                                    that.precyclename = '上个30天';
                                    break;
                                case 'week':
                                    that.precyclename = '上周';
                                    that.cyclename = '本周';
                                    break;
                                case 'month':
                                    that.precyclename = '上月';
                                    that.cyclename = '本月';
                                    break;
                                case 'year':
                                    that.cyclename = '去年';
                                    that.precyclename = '今年';
                                    break;
                                default:
                                    break;
                            }
                            var data=res.data.data;
                            if(data.length) {
                                that.cyclecount = data.cycle.count.data;
                                that.cyclecount_percent = data.cycle.count.percent;
                                that.cyclecount_is_plus = data.cycle.count.is_plus;
                                that.cycleprice = data.cycle.price.data;
                                that.cycleprice_percent = data.cycle.price.percent;
                                that.cycleprice_is_plus = data.cycle.price.is_plus;
                                that.pre_cyclecount = data.pre_cycle.count.data;
                                that.pre_cycleprice = data.pre_cycle.price.data;
                            }
                    });
                },
                orderchartsetoption:function(data){

                        this.option = {
                            tooltip: {
                                trigger: 'axis',
                                axisPointer: {
                                    type: 'cross',
                                    crossStyle: {
                                        color: '#999'
                                    }
                                }
                            },
                            toolbox: {
                                feature: {
                                    dataView: {show: true, readOnly: false},
                                    magicType: {show: true, type: ['line', 'bar']},
                                    restore: {show: false},
                                    saveAsImage: {show: true}
                                }
                            },
                            legend: {
                                data:data.legend
                            },
                            grid: {
                                x: 70,
                                x2: 50,
                                y: 60,
                                y2: 50
                            },
                            xAxis: [
                                {
                                    type: 'category',
                                    data: data.xAxis,
                                    axisPointer: {
                                        type: 'shadow'
                                    },
                                    axisLabel:{
                                        interval: 0,
                                        rotate:40
                                    }


                                }
                            ],
                            yAxis:[{type : 'value'}],
//                            yAxis: [
//                                {
//                                    type: 'value',
//                                    name: '',
//                                    min: 0,
//                                    max: data.yAxis.maxprice,
////                                    interval: 0,
//                                    axisLabel: {
//                                        formatter: '{value} 元'
//                                    }
//                                },
//                                {
//                                    type: 'value',
//                                    name: '',
//                                    min: 0,
//                                    max: data.yAxis.maxnum,
//                                    interval: 5,
//                                    axisLabel: {
//                                        formatter: '{value} 个'
//                                    }
//                                }
//                            ],
                            series: data.series
                        };
                    return  this.option;
                },
                userchartsetoption:function(data){
                    this.option = {
                        tooltip: {
                            trigger: 'axis',
                            axisPointer: {
                                type: 'cross',
                                crossStyle: {
                                    color: '#999'
                                }
                            }
                        },
                        toolbox: {
                            feature: {
                                dataView: {show: false, readOnly: false},
                                magicType: {show: true, type: ['line', 'bar']},
                                restore: {show: false},
                                saveAsImage: {show: false}
                            }
                        },
                        legend: {
                            data:data.legend
                        },
                        grid: {
                            x: 70,
                            x2: 50,
                            y: 60,
                            y2: 50
                        },
                        xAxis: [
                            {
                                type: 'category',
                                data: data.xAxis,
                                axisPointer: {
                                    type: 'shadow'
                                }
                            }
                        ],
                        yAxis: [
                            {
                                type: 'value',
                                name: '人数',
                                min: 0,
                                max: data.yAxis.maxnum,
                                interval: 5,
                                axisLabel: {
                                    formatter: '{value} 人'
                                }
                            }
                        ],
//                        series: data.series
                        series : [ {
                            name : '人数',
                            type : 'bar',
                            barWidth : '50%',
                            itemStyle: {
                                normal: {
                                    label: {
                                        show: true, //开启显示
                                        position: 'top', //在上方显示
                                        textStyle: { //数值样式
                                            color: '#666',
                                            fontSize: 12
                                        }
                                    }
                                }
                            },
                            data : data.series
                        } ]

                    };
                    return  this.option;
                },
                setChart:function(name,myChartname){
                    this.myChart[myChartname] = echarts.init(name,'macarons');//初始化echart
                }
            },
            mounted:function () {
                const self = this;
                this.setChart(self.$refs.order_echart,'order_echart');//订单图表
                this.setChart(self.$refs.user_echart,'user_echart');//用户图表
                this.info();
                this.getlist();
                $('.opFrames').on('click',function () {
                    parent.addframes($(this).data('href'),'',$(this).data('name'));
                });
            }
        });
    });
</script>

</body>
</html>