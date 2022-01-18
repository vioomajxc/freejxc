<?php /*a:2:{s:64:"D:\wamp\www\freejxc\application\admin\view\stocks\allotList.html";i:1580908712;s:57:"D:\wamp\www\freejxc\application\admin\view\pub\modal.html";i:1580923563;}*/ ?>
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
                    <form class="layui-form layui-col-space8" id="ajaxForm" method="get" >
                        <div class="layui-inline layui-show-xs-block">
                            <input class="layui-input" autocomplete="off" placeholder="开始日" name="start" id="start"></div>
                        <div class="layui-inline layui-show-xs-block">
                            <input class="layui-input" autocomplete="off" placeholder="截止日" name="end" id="end"></div>
                        <div class="layui-inline layui-show-xs-block">
                            <input type="text" name="or_id" placeholder="请输入订单号" autocomplete="off" class="layui-input"></div>
                        <div class="layui-inline layui-show-xs-block">
                            <a class="layui-btn lotus-search-btn"  lay-filter="search">
                                <i class="layui-icon">&#xe615;</i></a>
                        </div>
                    </form>
                </div>

                <div class="layui-card-body layui-table-body layui-table-main">
                    <table id="lotus-table"  lay-data="{page:true,limits:[10,20,100],loading:true,toolbar:'#toolbarDemo',url:'allotListJson',hash:''}"  class="layui-table layui-hide" lay-filter="lotus-table">
                        <thead>
                        <tr>
                            <th lay-data="{field:'id',type:'checkbox',fixed:'left'}"></th>
                            <th lay-data="{field:'id',sort: true,width:'6%',align:'center'}">ID</th>
                            <th lay-data="{field:'or_id',align:'center'}">单号</th>
                            <th lay-data="{field:'or_user',width:'8%',align:'center'}">操作员</th>
                            <th lay-data="{field:'house_name',width:'12%',align:'center'}">仓库</th>
                            <th lay-data="{field:'or_money',templet:'#moneyTpl',width:'10%'}">金额</th>
                            <th lay-data="{field:'or_create_time',templet:'#dateTpl',width:'10%'}">日期</th>
                            <th lay-data="{field:'or_finish',templet:'#finishTpl',sort:true,width:'8%',align:'center'}">状态</th>
                            <th lay-data="{field:'or_verify_status',templet:'#verifyTpl',sort:true,width:'8%',align:'center'}">审核</th>
                            <th lay-data="{field:'id',templet:'#actionTpl',width:'15%'}">操作</th>
                        </tr>
                        </thead>
                    </table>

                    <script type="text/html" id="toolbarDemo">
                        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="getCheckCollection"><i class="layui-icon layui-icon-rmb"></i>组合收款</button>
                    </script>
                    <script type="text/html" id="moneyTpl">
                        <span>￥{{d.or_money.toFixed(2)}}</span>
                    </script>
                    <script type="text/html" id="dateTpl">
                        <div>{{ layui.util.toDateString(d.or_create_time * 1000,"yyyy-MM-dd") }}</div>
                    </script>
                    <script type="text/html" id="actionTpl">
                        <button onclick="xadmin.open('查看调拔订单','seeOrder.html?or_id={{d.or_id}}&ordertype=<?php echo htmlentities($ordertype); ?>',900,600)" class="layui-btn  layui-btn-xs layui-btn-normal">查看</button>
                        {{#  if(d.or_finish==0){  }}
                        <button onclick="xadmin.open('编辑调拔订单','editAllot.html?or_id={{d.or_id}}',900,600)" class="layui-btn  layui-btn-xs">编辑</button>
                        {{#  } }}
                        {{#  if(d.or_verify_status==0){  }}
                        <button onclick="lotus.del('0','{{d.or_id}}','/admin/pub/delOrder')" class="layui-btn layui-btn-danger layui-btn-xs">删除</button>
                        {{#  } }}
                    </script>
                    <script type="text/html" id="finishTpl">
                      {{#  if(d.or_finish==1){   }}
                      <span class="layui-badge layui-bg-green">已完成</span>
                        {{# }else{ }}
                        <button class="layui-badge layui-btn layui-btn-sm">未完成</button>
                      {{# } }}
                    </script>
                    <script type="text/html" id="verifyTpl">
                      {{#  if(d.or_verify_status==1){   }}
                      <span class="layui-badge layui-bg-green">已审</span>
                        {{# }else{ }}
                        <button class="layui-btn layui-btn-sm layui-badge" onclick="xadmin.open('编辑调拔订单','editAllot.html?or_id={{d.or_id}}',900,600)" title="点击审核时会处理库存">未审</button>
                      {{# } }}
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('.lotus-search-btn').on('click',function () {
        var where = {
            start:$('input[name=start]').val(),
            or_id:$('input[name=or_id]').val(),
            end:$('input[name=end]').val(),
        };
        lotus.table(where);
    })
</script>



</body>
</html>