<?php /*a:2:{s:57:"D:\wamp\www\freejxc\application\admin\view\goods\add.html";i:1581053606;s:57:"D:\wamp\www\freejxc\application\admin\view\pub\modal.html";i:1580923563;}*/ ?>
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

<div class="layui-fluid lotus-form">
    <div class="layui-row">
        <form class="layui-form layui-form-pane" id="lotus-add-form" action="addGoods" method="post">
            
            <div class="layui-form-item">
                <div class="layui-row">
                    <div class="layui-inline">
                <label class="layui-form-label">货&nbsp;&nbsp;&nbsp;号</label>
                <div class="layui-input-inline">
                    <input type="text" id="sku" name="sku" placeholder="请输入商品编码/货号" lay-verify="required|sku" autocomplete="off" class="layui-input" autofocus="autofocus">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">条&nbsp;&nbsp;&nbsp;码</label>
                <div class="layui-input-inline">
                    <input type="text" id="barcode" name="barcode" placeholder="请输入商品条码" autocomplete="off" class="layui-input">
                </div>
            </div>
        </div>
        <div class="layui-row">
            <div class="layui-block">
                <label class="layui-form-label">商品名称</label>
                <div class="layui-input-block">
                    <input type="text" id="goodsname"  name="goodsname" lay-verify="required|goodsname" autocomplete="off" placeholder="请输入商品名称" class="layui-input">
                </div>
            </div>
            <div class="layui-inline"></div>
            </div>
                
                <div class="layui-row">
                    <div class="layui-inline">
                <label class="layui-form-label">分&nbsp;&nbsp;&nbsp;类</label>
                <div class="layui-input-inline">
                    <select lay-filter="aihao" name="category">
                        <option value="0">顶级分类</option>
                        <?php if(is_array($category) || $category instanceof \think\Collection || $category instanceof \think\Paginator): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities(str_repeat('丨--',$vo['level']-1)); ?><?php echo htmlentities($vo['category_name']); ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
            </div>
                <div class="layui-inline">
                    <label class="layui-form-label">单&nbsp;&nbsp;&nbsp;位</label>
                <div class="layui-input-inline">
                    <input type="text" id="unit"  name="unit" lay-verify="required|unit" placeholder="请输入商品单位" autocomplete="off" class="layui-input">
                </div>
                </div>
            </div>
                <div class="layui-row">
                    <div class="layui-inline">
                            <label class="layui-form-label">参考进价</label>
                            <div class="layui-input-inline">
                            <input type="text"  name="cost" lay-verify="required" placeholder="参考进价" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                            <label class="layui-form-label">供应商</label>
                            <div class="layui-input-inline">
                            <select class="layui-select" name="contact" lay-filter="hc_select" autocomplete="off" lay-search>
                        <option value="">选择供应商</option>
                        <?php if(is_array($supplier) || $supplier instanceof \think\Collection || $supplier instanceof \think\Paginator): $i = 0; $__LIST__ = $supplier;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['supplier_name']); ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                    </div>
                    
                    </div>
                </div>
                <div class="layui-row">
                    <div class="layui-inline">
                <label class="layui-form-label">销售价</label>
                <div class="layui-input-inline">
                    <input type="text"  name="price" placeholder="销售价" autocomplete="off" class="layui-input">
                </div>
            </div>

                    <div class="layui-inline">
                <label class="layui-form-label">规格</label>
                <div class="layui-input-inline">
                    <input type="text"  name="spec" lay-verify="required" placeholder="商品规格" autocomplete="off" class="layui-input">
                </div>
            </div>
            
        </div>
        <div class="layui-row">
                    <div class="layui-inline">
                <label class="layui-form-label">库存预警</label>
                <div class="layui-input-inline">
                    <input type="text"  name="replenishment" placeholder="请输入库存预警数" autocomplete="off" class="layui-input" value="10">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">净重</label>
                <div class="layui-input-inline">
                    <input type="text"  name="net" placeholder="净重" autocomplete="off" class="layui-input">
                </div>
            </div>
        </div>
        <div class="layui-row">
                    <div class="layui-inline">
                <label class="layui-form-label">毛重</label>
                <div class="layui-input-inline">
                    <input type="text"  name="wet" placeholder="毛重" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">产地</label>
                <div class="layui-input-inline">
                    <input type="text"  name="org" placeholder="产地" autocomplete="off" class="layui-input">
                </div>
            </div>
        </div>

        <div class="layui-row">
                    <div class="layui-inline">
                <label class="layui-form-label">时间</label>
                <div class="layui-input-inline">
                    <input type="text"  name="create_time" placeholder="创建日期" autocomplete="off" class="layui-input" id="end" value="<?php echo htmlentities($ctime); ?>">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-inline">
                    <select lay-filter="aihao" name="status">
                        <option value="1">启用</option>
                        <option value="0">禁止</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="layui-row">
            <div class="layui-block">
                <label class="layui-form-label">商品描述</label>
                <div class="layui-input-block">
                    <textarea  name="comment" style="height:80px;" class="layui-input"></textarea>
                </div>
            </div>
            <div class="layui-inline"></div>
            </div>
    </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button style="margin-left: 20%" class="layui-btn" lay-submit="" lay-filter="toSubmit">提交</button>
                    <button id="reset" type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>

        </form>
    </div>
</div>


</body>
</html>