<?php /*a:2:{s:65:"D:\wamp\www\freejxc\application\admin\view\stocks\stocksList.html";i:1580792230;s:57:"D:\wamp\www\freejxc\application\admin\view\pub\modal.html";i:1580923563;}*/ ?>
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
                    <form class="lotus-search-form layui-form layui-col-space5" method="get" action="stocksListJson">
                        <div class="layui-inline layui-show-xs-block">
                        <select name="house_id">
                        <option value="">所属仓库</option>
                        <?php if(is_array($storehouse) || $storehouse instanceof \think\Collection || $storehouse instanceof \think\Paginator): $i = 0; $__LIST__ = $storehouse;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['house_name']); ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                        </div>
                        <div class="layui-inline layui-show-xs-block">
                        <select name="category">
                        <option value="">商品分类</option>
                        <?php if(is_array($category) || $category instanceof \think\Collection || $category instanceof \think\Paginator): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities(str_repeat('丨--',$vo['level']-1)); ?><?php echo htmlentities($vo['category_name']); ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
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
                    <table id="lotus-table"  lay-data="{page:true,limits:[20,50,100],loading:true,toolbar:'#toolbarDemo',url:'stocksListJson',hash:''}"  class="layui-table layui-hide" >
                        <thead>
                        <tr>
                            <th lay-data="{field:'id', sort: true,width:'8%',align:'center'}">ID</th>
                            <th lay-data="{field:'goodsname'}">商品名称</th>
                            <th lay-data="{field:'unit',width:'6%'}">单位</th>
                            <th lay-data="{field:'category_name',width:'8%'}">分类</th>
                            <th lay-data="{field:'price',width:'10%',align:'center',templet:'#priceTpl'}">单价</th>
                            <th lay-data="{field:'house_name',width:'10%',align:'center'}">仓库</th>
                            <th lay-data="{field:'shop_name',width:'15%',align:'center'}">门店</th>
                            <th lay-data="{field:'numbers',width:'6%',align:'center'}">数量</th>
                        </tr>
                        </thead>
                    </table>
                    <script type="text/html" id="priceTpl">
                        <span>￥{{d.price.toFixed(2)}}</span>
                    </script>
                    <script type="text/html" id="toolbarDemo">
                        <div class = "layui-btn-container" >
                        </div >
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('.lotus-search-btn').on('click',function () {
        var where = {
            category:$('select[name=category]').val(),
            goodsname:$('input[name=goodsname]').val(),
            house_id:$('select[name=house_id]').val(),
        };
        lotus.table(where);
    })
</script>


</body>
</html>