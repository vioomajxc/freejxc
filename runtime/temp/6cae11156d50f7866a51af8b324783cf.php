<?php /*a:2:{s:67:"D:\wamp\www\freejxc\application\admin\view\pub\selectgoodsList.html";i:1580917113;s:57:"D:\wamp\www\freejxc\application\admin\view\pub\modal.html";i:1580923563;}*/ ?>
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
                    <form class="lotus-search-form layui-form layui-col-space5" method="get" action="selectgoodsListJson">
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                            <select class="layui-select" name="contact" lay-filter="hc_select" autocomplete="off" lay-search>
                        <option value="">选择供应商</option>
                        <?php if(is_array($supplier) || $supplier instanceof \think\Collection || $supplier instanceof \think\Paginator): $i = 0; $__LIST__ = $supplier;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['supplier_name']); ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                        </div>
                        </div>
                        <div class="layui-inline layui-show-xs-block">
                            <input type="text" name="goodsname" placeholder="请输入商品名" autocomplete="off" class="layui-input"></div>
                        <div class="layui-inline layui-show-xs-block">
                            <a class="layui-btn lotus-search-btn" lay-submit="" lay-filter="search">
                                <i class="layui-icon">&#xe615;</i></a>
                        </div>
                    </form>
                </div>
                <div class="layui-card-body layui-table-body layui-table-main">
                    <table id="lotus-table"  lay-filter="lotus-table" lay-data="{page:true,limits:[20,50,100],loading:true,toolbar:'#toolbarTpl',url:'selectgoodsListJson?or_id=<?php echo htmlentities($or_id); ?>',hash:''}"  class="layui-table layui-hide">
                        <thead>
                            <tr>
                                <th lay-data="{field:'id',type:'checkbox',fixed:'left'}"></th>
                                <th lay-data="{field:'id', sort: true,width:'9%',align:'center'}">ID</th>
                                <th lay-data="{field:'goodsname'}">商品名</th>
                                <th lay-data="{field:'unit',width:'8%',align:'center'}">单位</th>
                                <th lay-data="{field:'spec',width:'15%'}">规格</th>
                                <th lay-data="{field:'id',fixed:'right',templet: '#actionTpl',width:'12%',align:'center'}">操作</th>
                            </tr>
                        </thead>
                    </table>
                    <script type="text/html" id="toolbarTpl">
                        <div class = "layui-btn-container" >
                            <button class="layui-btn layui-btn-sm" lay-event="addBatchGoods"><i class="layui-icon"></i>选择并关闭</button>
                        </div >
                    </script>
                    <script type="text/html" id="actionTpl">
                        <button onclick="lotus.selectSingle('{{d.id}}','<?php echo htmlentities($or_id); ?>','/admin/pub/selectsingleGoods?ordertype=<?php echo htmlentities($ordertype); ?>')" class="layui-btn layui-btn-danger layui-btn-xs">选择</button>
                    </script>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
    $('.lotus-search-btn').on('click',function () {
        var where = {
            contact:$('select[name=contact]').val(),
            goodsname:$('input[name=goodsname]').val(),
        };
        lotus.table(where);
    })
</script>


</body>
</html>