<?php /*a:2:{s:62:"D:\wamp\www\freejxc\application\admin\view\goods\shopList.html";i:1580470329;s:57:"D:\wamp\www\freejxc\application\admin\view\pub\modal.html";i:1580923563;}*/ ?>
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
                <div class="layui-card-body layui-table-body layui-table-main">
                    <table id="lotus-table"  lay-data="{page:true,limits:[10,20,100],loading:true,toolbar:'#toolbarDemo',url:'shopListJson',hash:''}"  class="layui-table layui-hide" >
                        <thead>
                        <tr>
                            <th lay-data="{field:'id', sort: true,width:'6%'}">ID</th>
                            <th lay-data="{field:'shop_name'}">店铺名称</th>
                            <th lay-data="{field:'shop_director',width:'10%'}">联系人</th>
                            <th lay-data="{field:'shop_phone',width:'10%'}">联系电话</th>
                            <th lay-data="{field:'status',templet:'#statusTpl',width:'8%'}">状态</th>
                            <th lay-data="{'width':'180',field:'id',width:'16%',templet:'#actionTpl'}">操作</th>
                        </tr>
                        </thead>
                    </table>

                    <script type="text/html" id="toolbarDemo">
                        <div class = "layui-btn-container" >
                            <button class="layui-btn layui-btn-sm" onclick="xadmin.open('添加店铺','addShop.html')"><i class="layui-icon"></i>添加</button>
                        </div >
                    </script>
                    <script type="text/html" id="actionTpl">
                        <button onclick="xadmin.open('添加云打印机,自动接单云端小票打印机','addPrint.html?id={{d.id}}')" class="layui-btn  layui-btn-xs"><i class="layui-icon layui-icon-print" title="为店铺添加云打印机"></i></button>
                        <button onclick="xadmin.open('编辑店铺','editShop.html?id={{d.id}}')" class="layui-btn  layui-btn-xs"><i class="layui-icon layui-icon-edit" title="编辑店铺信息"></i></button>
                        <button onclick="lotus.del('{{d.id}}','delShop')" class="layui-btn layui-btn-danger layui-btn-xs"><i class="layui-icon layui-icon-delete" title="删除店铺"></i></button>
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
            url: "<?php echo url('admin/goods/shop_status'); ?>",
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