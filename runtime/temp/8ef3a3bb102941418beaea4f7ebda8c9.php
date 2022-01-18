<?php /*a:3:{s:62:"D:\wamp\www\freejxc\application\admin\view\report\procure.html";i:1581227804;s:61:"D:\wamp\www\freejxc\application\admin\view\pub\container.html";i:1580970373;s:62:"D:\wamp\www\freejxc\application\admin\view\pub\buildchart.html";i:1581074324;}*/ ?>
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
    
<style>
    #app{background:#ECECEC;}
    .layui-input-block button{
        border: 1px solid rgba(0,0,0,0.1);
    }
    .layui-card-body{
        padding-left: 10px;
        padding-right: 10px;
    }
    .layui-card-body p.layuiadmin-big-font {
        font-size: 36px;
        color: #666;
        line-height: 36px;
        padding: 5px 0 10px;
        overflow: hidden;
        text-overflow: ellipsis;
        word-break: break-all;
        white-space: nowrap;
    }
    .layuiadmin-badge, .layuiadmin-btn-group, .layuiadmin-span-color {
        position: absolute;
        right: 15px;
    }
    .layuiadmin-badge {
        top: 50%;
        margin-top: -9px;
        color: #01AAED;
    }
    .layuiadmin-span-color i {
        padding-left: 5px;
    }
    .block-rigit{
        text-align: right;
    }
    .block-rigit button{
        width: 100px;
        letter-spacing: .5em;
        line-height: 28px;
    }
    .layuiadmin-card-list{
        padding: 1.6px;
    }
    .layuiadmin-card-list p.layuiadmin-normal-font {
        padding-bottom: 10px;
        font-size: 20px;
        color: #666;
        line-height: 24px;
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

<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">搜索条件</div>
                <div class="layui-card-body">
                    <div class="layui-carousel layadmin-carousel layadmin-shortcut" lay-anim="" lay-indicator="inside" lay-arrow="none" style="background:none">
                        <div class="layui-card-body">
                            <div class="layui-row layui-col-space10 layui-form-item">
                                <div class="layui-col-lg12">
                                    <label class="layui-form-label">时间范围:</label>
                                    <div class="layui-input-block" data-type="data" v-cloak="">
                                        <button class="layui-btn layui-btn-sm" type="button" v-for="item in dataList" @click="setData(item)" :class="{'layui-btn-primary':data!=item.value}">{{item.name}}</button>
                                        <button class="layui-btn layui-btn-sm" type="button" ref="time" @click="setData({value:'zd',is_zd:true})" :class="{'layui-btn-primary':data!='zd'}">自定义</button>
                                        <button type="button" class="layui-btn layui-btn-sm layui-btn-primary" v-show="showtime==true" ref="date_time"><?php echo htmlentities($year['0']); ?> - <?php echo htmlentities($year['1']); ?></button>
                                    </div>
                                </div>
                                <div class="layui-col-lg12">
                                    <div class="layui-input-block">
                                        <button @click="search" type="button" class="layui-btn layui-btn-sm layui-btn-normal">
                                            <i class="layui-icon layui-icon-search"></i>搜索</button>
                                        <button @click="refresh" type="reset" class="layui-btn layui-btn-primary layui-btn-sm">
                                            <i class="layui-icon layui-icon-refresh" ></i>刷新</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div :class="item.col!=undefined ? 'layui-col-sm'+item.col+' '+'layui-col-md'+item.col:'layui-col-sm3 layui-col-md3'" v-for="item in badge" v-cloak="">
            <div class="layui-card">
                <div class="layui-card-header">
                    {{item.name}}
                    <span class="layui-badge layuiadmin-badge" :class="item.background_color">{{item.field}}</span>
                </div>
                <div class="layui-card-body">
                    <p class="layuiadmin-big-font">￥{{item.count|number_format=2}}</p>
                    <p v-show="item.content!=undefined">
                        {{item.content}}
                        <span class="layuiadmin-span-color">{{item.sum}}<i :class="item.class"></i></span>
                    </p>
                </div>
            </div>
        </div>
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">采购金额统计</div>
                <div class="layui-card-body layui-row">
                    <div class="layui-col-md12">
                        <div class="layui-btn-container" ref="echarts_procure" style="height:300px"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">采购退货统计</div>
                <div class="layui-card-body layui-row">
                    <div class="layui-col-md12">
                        <div class="layui-btn-container" ref="echarts_return" style="height:300px"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">店铺采购饼状图</div>
                <div class="layui-card-body layui-row">
                    <div class="layui-col-md12">
                        <div class="layui-btn-container" ref="echarts_shop" style="height:300px"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md6">
                <div class="layui-card">
                    <div class="layui-card-header">供货商排行榜 <span class="layui-badge layui-bg-orange">TOP<?php echo htmlentities($limit); ?></span></div>
                    <div class="layui-card-body layui-row">
                        <table class="layui-table">
                            <thead>
                                <tr>
                                    <th>排名</th>
                                    <th>供货商</th>
                                    <th>金额</th>
                                    <th>采购时间</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(is_array($procureList) || $procureList instanceof \think\Collection || $procureList instanceof \think\Paginator): $i = 0; $__LIST__ = $procureList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                <tr>
                                    <td><?php echo htmlentities($key+1); ?></td>
                                    <td><?php echo htmlentities($vo['supplier_name']); ?></td>
                                    <td><?php echo htmlentities(number_format($vo['money'],2)); ?></td>
                                    <td><?php echo htmlentities($vo['add_time']); ?></td>
                                </tr>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="layui-col-md6">
                <div class="layui-card">
                    <div class="layui-card-header">采购产品排行榜 <span class="layui-badge layui-bg-orange">TOP<?php echo htmlentities($limit); ?></span></div>
                    <div class="layui-card-body layui-row">
                        <table class="layui-table">
                            <thead>
                            <tr>
                                <th>排名</th>
                                <th>商品</th>
                                <th>金额</th>
                                <th>采购时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($goodsList) || $goodsList instanceof \think\Collection || $goodsList instanceof \think\Paginator): $i = 0; $__LIST__ = $goodsList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <tr>
                                <td><?php echo htmlentities($key+1); ?></td>
                                <td><?php echo htmlentities($vo['goodsname']); ?></td>
                                <td><?php echo htmlentities(number_format($vo['money'],2)); ?></td>
                                <td><?php echo htmlentities($vo['add_time']); ?></td>
                            </tr>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/system/js/layuiList.js"></script>
<script>
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                option: {},
                badge:[],
                dataList: [
                    {name: '全部', value: ''},
                    {name: '今天', value: 'today'},
                    {name: '本周', value: 'week'},
                    {name: '本月', value: 'month'},
                    {name: '本季度',value:'quarter'},
                    {name: '本年', value: 'year'},
                ],
                status:'',
                Promoter:'',
                data: '',
                myChart: {},
                showtime: false,
            },
            methods:{
                getBusinesHeade:function(){
                    var that=this;
                    layList.baseGet(layList.Url({a:'getBusinesHeade',q:{data:this.data,type:'procure'}}),function (rem) {
                        that.badge=rem.data;
                    });
                },
                getBusinesChart:function(){
                    var that=this;
                    layList.baseGet(layList.Url({a:'getBusinesChart',q:{data:this.data,type:'procure'}}),function (rem) {
                        var user=that.setoption(rem.data.seriesdata,rem.data.xdata,null,null,null,rem.data.zoom);
                        that.myChart.list.setOption(user);
                        var visit=that.setoption(rem.data.visit_data,rem.data.visit_xdata,null,'采购退货金额曲线图','line',rem.data.visit_zoom);
                        that.myChart.user.setOption(visit);
                        var shop=that.setoption(rem.data.shop_data,rem.data.shop_xdata,null,'店铺采购饼状图','pic','');
                        that.myChart.shop.setOption(shop);
                    });
                },
                setoption:function(seriesdata,xdata,legend,title,type,Zoom){
                    var _type=type || 'line';
                    var _title=title || '采购金额趋势图';
                    switch (_type){
                        case 'line':
                            this.option={
                                title: {text:_title,x:'center'},
                                xAxis : [{type : 'category', data :xdata,}],
                                yAxis : [{type : 'value'}],
                                series:[{type:_type,data:seriesdata,markPoint :{
                                        data : [
                                            {type : 'max', name: '最大值'},
                                            {type : 'min', name: '最小值'}
                                        ]
                                    },
                                    itemStyle:{
                                        color:'#81BCFF'
                                    }
                                }
                                ],
                            };
                            if(legend!=undefined || legend!=null){
                                this.option.legend={left:'center',data:legend};
                            }
                            break;
                        case 'lines':
                            this.option={
                                title: {text:_title,x:'center'},
                                xAxis : [{type : 'category', data :xdata,}],
                                yAxis : [{type : 'value'}],
                                series:seriesdata,
                            };
                            if(legend!=undefined || legend!=null){
                                this.option.legend={left:'left',data:legend};
                            }
                            break;
                        case 'pic':
                            this.option={
                                title: {text:_title,left:'center'},
                                legend: {data:xdata,bottom:10,left:'center'},
                                tooltip: {trigger: 'item'},
                                series:[{ type: 'pie', radius : '65%', center: ['50%', '50%'], selectedMode: 'single',data:seriesdata}],
                            };
                            break;
                    }
                    if(Zoom!=''){
                        this.option.dataZoom=[{startValue:Zoom},{type:'inside'}];
                    }
                    return  this.option;
                },
                search:function(){
                    this.getBusinesHeade();
                    this.getBusinesChart();
                },
                refresh:function () {
                    this.data='';
                    this.getBusinesHeade();
                    this.getBusinesChart();
                },
                setChart:function(name,myChartname){
                    this.myChart[myChartname]=echarts.init(name);
                },
                setStatus:function (item) {
                    this.status=item.value;
                },
                setData:function(item){
                    var that=this;
                    if(item.is_zd==true){
                        that.showtime=true;
                        this.data=this.$refs.date_time.innerText;
                    }else{
                        this.showtime=false;
                        this.data=item.value;
                    }
                },
            },
            mounted:function () {
                this.setChart(this.$refs.echarts_procure,'list');
                this.setChart(this.$refs.echarts_return,'user');
                this.setChart(this.$refs.echarts_shop,'shop');
                this.getBusinesHeade();
                this.getBusinesChart();
                var that=this;
                layList.laydate.render({
                    elem:this.$refs.date_time,
                    trigger:'click',
                    eventElem:this.$refs.time,
                    range:true,
                    change:function (value){
                        that.data=value;
                    }
                });
            }
        })
    })
</script>


</body>
</html>