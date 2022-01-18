<?php /*a:3:{s:60:"D:\wamp\www\freejxc\application\admin\view\report\goods.html";i:1580985460;s:61:"D:\wamp\www\freejxc\application\admin\view\pub\container.html";i:1580970373;s:62:"D:\wamp\www\freejxc\application\admin\view\pub\buildchart.html";i:1581074324;}*/ ?>
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
	#app{background:#ececec;}
    .layui-input-block button{
        border: 1px solid rgba(0,0,0,0.2);
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
    <div class="layui-row layui-col-space15" id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">搜索条件</div>
                <div class="layui-card-body">
                    <div class="layui-carousel layadmin-carousel layadmin-shortcut" lay-anim="" lay-indicator="inside" lay-arrow="none" style="background:none">
                        <div class="layui-card-body">
                            <div class="layui-row layui-col-space10 layui-form-item">
                                <!--div class="layui-col-lg12">
                                    <label class="layui-form-label">产品状态:</label>
                                    <div class="layui-input-block" v-cloak="">
                                        <button class="layui-btn layui-btn-sm" :class="{'layui-btn-primary':status!=item.value}" @click="setStatus(item)" type="button" v-for="item in statusList">{{item.name}}</button>
                                    </div>
                                </div-->
                                <div class="layui-col-lg12">
                                    <label class="layui-form-label">统计时间:</label>
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
        <div class="layui-col-sm6 layui-col-md3" v-for="item in badge" v-cloak="">
            <div class="layui-card">
                <div class="layui-card-header">
                    {{item.name}}
                    <span class="layui-badge layuiadmin-badge" :class="item.background_color">{{item.field}}</span>
                </div>
                <div class="layui-card-body">
                    <p class="layuiadmin-big-font">{{item.count}}</p>
                    <p>
                        {{item.content}}
                        <span class="layuiadmin-span-color">{{item.sum}}<i :class="item.class"></i></span>
                    </p>
                </div>
            </div>
        </div>
        <div class="layui-col-sm12">
            <div class="layui-card">
                <div class="layui-card-header">图表展示:</div>
                <div class="layui-card-body layui-row">
                    <div class="layui-col-md12">
                        <div class="layui-btn-container" ref="echarts_list" id="echarts_list" style="height:400px"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6">
            <div class="layui-card">
                <div class="layui-card-header">销量排行　<span class="layui-badge layui-bg-orange">TOP10</span></div>
                <div class="layui-card-body">
                    <p class="layuiadmin-normal-font">商品销售总计：<span style="color:lightblue">{{SalesList.sum_count}}</span> 件 共计 <span style="color:coral">{{SalesList.sum_price}}</span>元</p>
                    <div class="layuiadmin-card-list" v-for="item in SalesList.list">
                        <span>{{item.store_name}}</span>
                        <div class="layui-progress layui-progress-big" lay-showpercent="yes">
                            <div class="layui-progress-bar" :class="item.class" :style="{'width':item.w+'%'}"><span class="layui-progress-text">{{item.p_count}}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6">
            <div class="layui-card">
                <div class="layui-card-header">利润排行　<span class="layui-badge layui-bg-orange">TOP10</span></div>
                <div class="layui-card-body">
                    <p class="layuiadmin-normal-font">商品销售总计：<span style="color:lightblue">{{ProfityList.sum_count}}</span> 件 利润共计 <span style="color:coral">{{ProfityList.sum_price}}</span>元</p>
                    <table class="layui-table layuiadmin-page-table" lay-skin="line">
                        <thead>
                        <tr>
                            <th>商品名称</th>
                            <th>销量占比(%)</th>
                            <th>购买个数</th>
                            <th>利润(￥)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in ProfityList.list">
                            <td><span class="first">{{item.store_name}}</span></td>
                            <td><i class="layui-icon layui-icon-log"> {{item.w}}</i></td>
                            <td><span>{{item.p_count}}</span></td>
                            <td> {{item.sum_price}} <i class="layui-icon layui-icon-rmb"></i></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="layui-row layui-col-space15">
            <div class="layui-col-sm4 layui-col-md6">
                <div class="layui-card">
                    <div class="layui-card-header">待补货商品</div>
                    <div class="layui-card-body">
                        <table class="layui-hide" id="PhList" lay-filter="PhList"></table>
                    </div>
                </div>
            </div>
            
            <div class="layui-col-sm4 layui-col-md6">
                <div class="layui-card">
                    <div class="layui-card-header">退货产品</div>
                    <div class="layui-card-body">
                        <table class="layui-table layuiadmin-page-table" lay-skin="line">
                            <thead>
                            <tr>
                                <th>商品名称</th>
                                <th>单价</th>
                                <th>退货数量</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="item in TuiPriesList">
                                <td><span class="first">{{item.store_name}}</span></td>
                                <td><i class="layui-icon layui-icon-rmb">{{item.price}}</i></td>
                                <td><span>{{item.count}}</span></td>
                            </tr>
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
    require(['vue'],function(Vue){
        new Vue({
            el:"#app",
            data:{
                option:{},
                badge:[],
                SalesList:[],
                ProfityList:[],
                negativeList:[],
                TuiPriesList:[],
                dataList:[
                    {name:'全部',value:''},
                    {name:'今天',value:'today'},
                    {name:'本周',value:'week'},
                    {name:'本月',value:'month'},
                    {name:'本季度',value:'quarter'},
                    {name:'本年',value:'year'},
                ],
                status:'',
                data:'',
                title:'全部商品',
                count:0,
                myChart:{},
                showtime:false,
            },
            watch:{

            },
            methods:{
                info:function(){
                    var that=this;
                    var index=layList.layer.load(2,{shade: [0.3,'#fff']});
                    layList.baseGet(layList.Url({a:'get_echarts_product',q:{type:this.status,data:this.data}}),function (res){
                        layList.layer.close(index);
                        that.badge=res.data.badge;
                        that.count=res.data.count;
                        var option=that.setoption(res.data.datetime,res.data.legdata,res.data.chatrList);
                        that.myChart.list.setOption(option);
                    },function () {
                        layList.layer.close(index);
                    });
                },
                getSalesList:function(){
                    var that=this;
                    layList.baseGet(layList.Url({a:'get_echarts_maxlist',q:{data:this.data}}),function (rem) {
                        that.SalesList=rem.data;
                    });
                },
                getProfityList:function(){
                    var that=this;
                    layList.baseGet(layList.Url({a:'get_echarts_profity',q:{data:this.data}}),function (rem) {
                        that.ProfityList=rem.data;
                    });
                },
                getTuiPriesList:function(){
                    var that=this;
                    layList.baseGet(layList.Url({a:'getTuiPriesList'}),function (rem) {
                        that.TuiPriesList=rem.data;
                    });
                },
                getLackList:function(){
                    var that=layList;
                    var data={
                        url:that.Url({a:'getLackList'}),
                        limit:10
                    };
                    layList.tables('PhList',data,[
                        {field: 'goodsname', title: '商品信息'},
                        {field: 'price', title: '单价',width:'20%',align:'center'},
                        {field: 'replenishment', title: '预警',width:'20%',align:'center'},
                        {field: 'numbers', title: '库存',edit:'stock',width:'20%',align:'center'},
                    ]);
                },
                setoption:function(xdata,legdata,seriesdata){
                    return this.option={
                        title: {text:this.title+'('+this.count+')'+'件',},
                        tooltip: {show: true},
                        legend: {data:legdata,},
                        xAxis : [{type : 'category', data :xdata,}],
                        yAxis : [{type : 'value'}],
                        series:seriesdata,
                    };
                },
                setChart:function(name,myChartname){
                    this.myChart[myChartname]=echarts.init(name);
                },
                setStatus:function (item){
                    this.status=item.value;
                    this.title=item.name;
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
                refresh:function () {
                    this.status='';
                    this.data='';
                    this.info();
                },
                search:function(){
                    this.info();
                }
            },
            mounted:function () {
                this.setChart(this.$refs.echarts_list,'list');
                this.info();
                this.getSalesList();
                this.getProfityList();
                this.getLackList();
                this.getTuiPriesList();
                layList.laydate.render({
                    elem:this.$refs.date_time,
                    trigger:'click',
                    eventElem:this.$refs.time,
                    range:true,
                });
            }
        });
    })
</script>


</body>
</html>