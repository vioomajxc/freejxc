<?php /*a:2:{s:67:"D:\wamp\www\freejxc\application\admin\view\member\addMcategory.html";i:1577553543;s:57:"D:\wamp\www\freejxc\application\admin\view\pub\modal.html";i:1580923563;}*/ ?>
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
        <form class="layui-form layui-form-pane" id="lotus-add-form" action="addMcategory" method="post">


            <div class="layui-form-item">
                <label class="layui-form-label">
                    上级分类
                </label>
                <div class="layui-input-block">
                    <select lay-filter="aihao" name="pid">
                        <option value="0">顶级分类</option>
                        <?php if(is_array($mcategory) || $mcategory instanceof \think\Collection || $mcategory instanceof \think\Paginator): $i = 0; $__LIST__ = $mcategory;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities(str_repeat('丨--',$vo['level']-1)); ?><?php echo htmlentities($vo['mcategory_name']); ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">分类名称</label>
                <div class="layui-input-block">
                    <input type="text"  name="mcategory_name" lay-verify="required" autocomplete="off"  class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">享受折扣</label>
                <div class="layui-input-block">
                    <input type="text"  name="discount" lay-verify="required" autocomplete="off"  class="layui-input" value="10">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label"><a target="_blank" href="https://www.layui.com/doc/element/icon.html#table">图标(点)</a></label>
                <div class="layui-input-block">
                    <input type="text"    name="icon"  autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">
                    是否启用
                </label>
                <div class="layui-input-block">
                    <select lay-filter="aihao" name="status">
                        <option value="1">启用</option>
                        <option value="0">禁止</option>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">排序权重</label>
                <div class="layui-input-block">
                    <input type="number"    value="0"  name="sort" lay-verify="required"  autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button style="margin-left: 30%" class="layui-btn" lay-submit="" lay-filter="toSubmit">提交</button>
                    <button id="reset" type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>

        </form>
    </div>
</div>


</body>
</html>