<?php /*a:2:{s:63:"D:\wamp\www\freejxc\application\admin\view\goods\goodsList.html";i:1580757567;s:57:"D:\wamp\www\freejxc\application\admin\view\pub\modal.html";i:1580923563;}*/ ?>
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
                    <form class="lotus-search-form layui-form layui-col-space5" method="get" action="goodsListJson">
                        <div class="layui-inline layui-show-xs-block">
                            <input class="layui-input" autocomplete="off" placeholder="开始日" name="start" id="start"></div>
                        <div class="layui-inline layui-show-xs-block">
                            <input class="layui-input" autocomplete="off" placeholder="截止日" name="end" id="end"></div>
                        <div class="layui-inline layui-show-xs-block">
                            <input type="text" name="goodsname" placeholder="请输入商品名" autocomplete="off" class="layui-input"></div>
                        <div class="layui-inline layui-show-xs-block">
                            <a class="layui-btn lotus-search-btn" lay-submit="" lay-filter="search">
                                <i class="layui-icon">&#xe615;</i></a>
                        </div>
                    </form>
                </div>
                <div class="layui-card-body layui-table-body layui-table-main">
                    <table id="lotus-table"  lay-data="{page:true,limits:[10,20,100],loading:true,toolbar:'#toolbarDemo',url:'goodsListJson',hash:''}"  class="layui-table layui-hide" >
                        <thead>
                            <tr>
                                <th lay-data="{field:'id', width:'6%',align:'center', sort: true}">ID</th>
                                <th lay-data="{field:'goodsname'}">商品名</th>
                                <th lay-data="{field:'unit',width:'6%'}">单位</th>
                                <th lay-data="{field:'category_name',width:'8%'}">分类</th>
                                <th lay-data="{field:'price',width:'8%',templet:'#priceTpl'}">单价</th>
                                <th lay-data="{field:'lead_time',templet:'#leadtimeTpl',width:'10%'}">日期</th>
                                <th lay-data="{field:'status',templet:'#statusTpl',width:'8%'}">状态</th>
                                <th lay-data="{field:'word',width:'10%'}">助记词</th>
                                <th lay-data="{'width':'12%',field:'id',templet: '#actionTpl'}">操作</th>
                            </tr>
                        </thead>
                    </table>
                    <script type="text/html" id="toolbarDemo">
                        <div class = "layui-btn-container" >
                            <button class="layui-btn layui-btn-sm" onclick="xadmin.open('添加商品','addGoods.html',720)"><i class="layui-icon"></i>添加</button>
                        </div >
                    </script>
                    <script type="text/html" id="priceTpl">
                        <span>￥{{d.price.toFixed(2)}}</span>
                    </script>
                    <script type="text/html" id="actionTpl">
                        <!--button onclick="xadmin.open('编辑商品属性','editGoods.html?id={{d.id}}',720)" class="layui-btn layui-btn-xs">属性</button-->
                        <button onclick="xadmin.open('编辑商品','editGoods.html?id={{d.id}}',720)" class="layui-btn layui-btn-xs">编辑</button>
                        <button onclick="lotus.del('{{d.id}}','delGoods')" class="layui-btn layui-btn-danger layui-btn-xs">删除</button>
                        <!--button onclick="xadmin.open('商品详情','desGoods.html?id={{d.id}}')" class="layui-btn layui-btn-success layui-btn-xs">详情</button-->
                    </script>
                    <script type="text/html" id="leadtimeTpl">
                        <div>{{ layui.util.toDateString(d.create_time * 1000,"yyyy-MM-dd") }}</div>
                    </script>
                    <script type="text/html" id="statusTpl">
                        <input type='checkbox' name='status' lay-skin='switch' value="{{d.id}}" lay-filter='status' lay-text='正常|禁止'  {{ d.status == 1 ? 'checked' : '' }}>
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
            goodsname:$('input[name=goodsname]').val(),
            end:$('input[name=end]').val(),
        };
        lotus.table(where);
    })

  layui.use('table', function(){
  var table = layui.table
  ,form = layui.form;
   //监听状态操作
  form.on('switch(status)', function(obj){
    if(obj.elem.checked)
        status=1;
    else
        status=0;
    $.ajax({
            url: "<?php echo url('admin/goods/goods_status'); ?>",
            type: 'post',
            dataType: 'json',
            data:{status:status,id:this.value},
        })
            .done(function(data){
                    if(data.code==1){
                        layer.msg(data.msg,{icon:1,time:500});
                            layui.table.reload('lotus-table');
                    }
                })
  });
});
</script>


</body>
</html>