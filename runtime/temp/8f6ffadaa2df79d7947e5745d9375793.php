<?php /*a:2:{s:65:"D:\wamp\www\freejxc\application\admin\view\system_config\set.html";i:1581832474;s:57:"D:\wamp\www\freejxc\application\admin\view\pub\modal.html";i:1580923563;}*/ ?>
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
            <form class="layui-form layui-form-pane" lay-filter="lotus-form-filter" id="lotus-sigle-edit-form" action="set" method="post">
    <div class="layui-row">
            <div class="layui-form-item">
                <label class="layui-form-label">企业名称</label>
                <div class="layui-input-block">
                    <input type="text" id="site_name"  name="site_name" lay-verify="required" autocomplete="off" placeholder="请输入" class="layui-input">
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">登陆标题</label>
                <div class="layui-input-block">
                    <input type="text" id="login_title" name="login_title"  placeholder="请输入" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">联系人</label>
                <div class="layui-input-inline">
                    <input type="text" id="admin" name="admin" lay-verify="required"  placeholder="请输入" autocomplete="off" class="layui-input">
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">手机号</label>
                <div class="layui-input-inline">
                    <input type="text" id="phone" name="phone" lay-verify="required"  placeholder="请输入" autocomplete="off" class="layui-input">
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">联系地址</label>
                <div class="layui-input-block">
                    <input type="text" id="address" name="address"  lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">邮箱</label>
                <div class="layui-input-block">
                    <input type="text" id="email" lay-verify="required" name="email"  placeholder="用以接收系统重要消息" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button  class="layui-btn" lay-submit="" lay-filter="toSubmit">提交</button>
                    <button style="display: none;" id="reset" type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    var lotusFormVal = {
        'site_name':"<?php echo htmlentities($site_config['site_name']); ?>",
        'admin':"<?php echo htmlentities($site_config['admin']); ?>",
        'phone':"<?php echo htmlentities($site_config['phone']); ?>",
        'address':"<?php echo htmlentities($site_config['address']); ?>",
        'email':"<?php echo htmlentities($site_config['email']); ?>",
        'login_title':"<?php echo htmlentities($site_config['login_title']); ?>"
    }
    lotus.editSingleForm(lotusFormVal);

</script>


</body>
</html>