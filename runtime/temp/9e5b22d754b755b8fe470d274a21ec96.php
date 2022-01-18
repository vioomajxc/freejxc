<?php /*a:2:{s:67:"D:\wamp\www\freejxc\application\admin\view\member\rechargelist.html";i:1580757926;s:57:"D:\wamp\www\freejxc\application\admin\view\pub\modal.html";i:1580923563;}*/ ?>
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

<div class="layui-fluid layui-anim layui-anim-upbit" >
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body ">
                    <form class="layui-form layui-col-space8" id="ajaxForm" method="get" action="rechargeListJson">
                        <div class="layui-inline layui-show-xs-block">
                            <input class="layui-input" autocomplete="off" placeholder="开始日" name="start" id="start"></div>
                        <div class="layui-inline layui-show-xs-block">
                            <input class="layui-input" autocomplete="off" placeholder="截止日" name="end" id="end"></div>
                        <div class="layui-inline layui-show-xs-block">
                            <input type="text" name="card_no" placeholder="请输入卡号" autocomplete="off" class="layui-input"></div>
                        <div class="layui-inline layui-show-xs-block">
                            <a class="layui-btn lotus-search-btn"  lay-filter="search">
                                <i class="layui-icon">&#xe615;</i></a>
                        </div>
                    </form>
                </div>
                <div class="layui-card-body layui-table-body layui-table-main">
                    <table id="lotus-table"  lay-data="{page:true,limits:[10,20,100],loading:true,toolbar:'#toolbarDemo',url:'rechargeListJson',hash:'',totalRow:true}"  class="layui-table layui-hide" lay-filter="recharge">
                        <thead>
                        <tr>
                            <th lay-data="{field:'id',type:'radio'}"></th>
                            <th lay-data="{field:'id',sort: true,width:'6%',totalRowText:'合计'}">ID</th>
                            <th lay-data="{field:'card_no',sort:true,width:'15%'}">会员卡号</th>
                            <th lay-data="{field:'member_name'}">会员名称</th>
                            <th lay-data="{field:'money',width:'10%',templet:'#moneyTpl',totalRow:true}">充值金额</th>
                            <th lay-data="{field:'time',templet:'<div>{{ layui.util.toDateString(d.time * 1000) }}</div>',width:'15%'}">充值日期</th>
                        </tr>
                        </thead>
                    </table>

                    <script type="text/html" id="toolbarDemo">
                        <div class = "layui-btn-container" >
                            <button class="layui-btn layui-btn-sm" lay-event="getRecharge"><i class="layui-icon"></i>充值</button>
                        </div >
                    </script>
                    <script type="text/html" id="moneyTpl">
                        <span>￥{{d.money}}</span>
                    </script>
                    <script type="text/html" id="statusTpl">
                      {{#  if(d.member_status==1){   }}
                      <span class="layui-badge layui-bg-green">可用</span>
                        {{# }else{ }}
                        <span class="layui-badge">禁止</span>
                      {{# } }}
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
layui.use('table', function(){
  var table = layui.table;
  
  //头工具栏事件
  table.on('toolbar(recharge)', function(obj){
    var checkStatus = table.checkStatus(obj.config.id); //获取选中行状态
    switch(obj.event){
      case 'getRecharge':
        var data = checkStatus.data;  //获取选中行数据
        xadmin.open('会员充值','cardRecharge.html?card_no='+data[0].card_no,720);
      break;
    };
  });
});
</script>
<script>
    $('.lotus-search-btn').on('click',function () {
        var where = {
            start:$('input[name=start]').val(),
            card_no:$('input[name=card_no]').val(),
            end:$('input[name=end]').val(),
        };
        lotus.table(where);
    })
</script>


</body>
</html>