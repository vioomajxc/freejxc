<?php /*a:2:{s:60:"D:\wamp\www\freejxc\application\admin\view\stocks\allot.html";i:1580900305;s:57:"D:\wamp\www\freejxc\application\admin\view\pub\modal.html";i:1580923563;}*/ ?>
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
                <div class="layui-card-body">
                    <form class="layui-form layui-col-space8" method="post" id="lotus-sigle-edit-form" action="allot" lay-filter="lotus-form-filter">
                        <div class="layui-row">
                            <div class="layui-inline">
                            <label class="layui-form-label">订单号：</label>
                            <div class="layui-input-inline">
                            <input type="text" name="or_id" autocomplete="off" class="layui-input" value="<?php echo htmlentities($or_id); ?>" readonly="readonly">
                        </div>
                    </div>
                            
                        <div class="layui-inline">
                            <label class="layui-form-label">原始仓库：</label>
                        <div class="layui-input-inline">
                            <select  lay-verify="required" name="or_house">
                        <option value="">选择仓库</option>
                        <?php if(is_array($house) || $house instanceof \think\Collection || $house instanceof \think\Paginator): $i = 0; $__LIST__ = $house;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['house_name']); ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                        </div>
                    </div>

                    <div class="layui-inline">
                            <label class="layui-form-label">目标仓库：</label>
                        <div class="layui-input-inline">
                            <select  lay-verify="required" name="or_house1">
                        <option value="">选择仓库</option>
                        <?php if(is_array($house) || $house instanceof \think\Collection || $house instanceof \think\Paginator): $i = 0; $__LIST__ = $house;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['house_name']); ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                        </div>
                    </div>
                    </div>
                    <div class="layui-row">

                        <div class="layui-inline">
                            <label class="layui-form-label">操作员：</label>
                            <div class="layui-input-inline" style="width:100px;">
                            <select class="layui-select" name="or_user" lay-filter="hc_select" autocomplete="off" lay-search lay-verify="required">
                        <option value="">操作员</option>
                        <?php if(is_array($user) || $user instanceof \think\Collection || $user instanceof \think\Paginator): $i = 0; $__LIST__ = $user;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo htmlentities($vo['username']); ?>"><?php echo htmlentities($vo['fullname']); ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                        </div>
                        </div>
                            
                        <div class="layui-inline">
                            <label class="layui-form-label">日期：</label>
                        <div class="layui-input-inline">
                            <input class="layui-input" autocomplete="off" placeholder="订单日期" name="or_create_time" id="start" value="<?php echo htmlentities($ctime); ?>">
                        </div>
                    </div>
                            
                        <div class="layui-inline">
                            <button style="margin-left: 30%" class="layui-btn" lay-submit="" lay-filter="toSubmit">保存订单</button>
                        </div>
                    </div>
                    </form>
                </div>

                <div class="layui-card-body layui-table-body layui-table-main layui-col-md12">
                    <table id="lotus-table" lay-filter="lotus-table"  lay-data="{page:true,limits:[20,50,100],loading:true,toolbar:'#toolbarDemo',url:'/admin/pub/ItemJson?or_id=<?php echo htmlentities($or_id); ?>',hash:'',totalRow:true,cellMinWidth: 80,done:function(res){
var sumTotal=0;//统计金额
layui.each(res.data,function(index,d){
sumTotal+=d.it_number * d.it_price;
})
this.elem.next().find('.layui-table td[data-field=sum] .layui-table-cell:last').text(sumTotal.toFixed(2))
}}"  class="layui-table layui-hide" >
                        <thead>
                            <tr>
                                <th lay-data="{field:'id', width:80,fixed: 'left',unresize:true,sort: true,totalRowText:'合计',align:'center'}">ID</th>
                                <th lay-data="{field:'goodsname'}">商品名</th>
                                <th lay-data="{field:'unit',width:'8%',align:'center'}">单位</th>
                                <th lay-data="{field:'spec',width:'10%',align:'center'}">规格</th>
                                <th lay-data="{field:'it_number',edit:'text',totalRow:true,width:'8%',align:'center'}">数量</th>
                                <th lay-data="{field:'it_price',edit:'text',width:'10%',align:'center'}">单价</th>
                                <th lay-data="{field:'sum',totalRow:true,templet:function(d){
                                return (d.it_number*d.it_price).toFixed(2);
                            },align:'center',width:'12%'}">金额</th>
                                <th lay-data="{'width':'12%',field:'id',templet:'#actionTpl'}">操作</th>
                            </tr>
                        </thead>
                    </table>
                    <script type="text/html" id="toolbarDemo">
                        <div class = "layui-btn-container" >
                            <button class="layui-btn layui-btn-sm" onclick="xadmin.open('选择要添加的商品','/admin/pub/selectGoods.html?or_id=<?php echo htmlentities($or_id); ?>&ordertype=allot',800,600)"><i class="layui-icon"></i>选择商品</button>
                            <button class="layui-btn layui-btn-sm layui-btn-danger" onclick="lotus.del('0','<?php echo htmlentities($or_id); ?>','/admin/pub/delitemallgoods')"><i class="layui-icon layui-icon-delete"></i>全部删除</button>
                        </div >
                    </script>
                    <script type="text/html" id="actionTpl">
                        <button onclick="lotus.del('{{d.id}}','0','/admin/pub/delitemGoods')" class="layui-btn layui-btn-danger layui-btn-xs">删除</button>
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var lotusFormVal = {
        'or_user':"<?php echo htmlentities($curUser); ?>"
    }
    lotus.editSingleForm(lotusFormVal);
</script>


</body>
</html>