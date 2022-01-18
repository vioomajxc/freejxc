<?php /*a:1:{s:59:"D:\wamp\www\freejxc\application\admin\view\login\login.html";i:1580709159;}*/ ?>
<!doctype html>
<html  class="x-admin-sm">
<head>
    <meta charset="UTF-8">
    <title>唯马进销存登录</title>
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="stylesheet" href="/static/css/font.css">
    <link rel="stylesheet" href="/static/css/login.css">
    <link rel="stylesheet" href="/static/css/xadmin.css">
    <script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script src="/static/lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="/static/js/jquery.form.js"></script>
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="login-bg">

<div class="login layui-anim layui-anim-up">
    <div class="message">
        <h2 class="login-title">唯马进销存登陆</h2>
    </div>
    <form method="post" class="layui-form"  action="login">
        <input  id="username" name="username" placeholder="管理员用户名" autofocus="autofocus" type="text" lay-verify="required" class="layui-input" >
        <hr class="hr15">
        <input id="password" name="password" lay-verify="required" placeholder="密码"  type="password" class="layui-input">
        <hr class="hr15">
        <div class="layui-input-inline">
        <input id="logincode" name="logincode" lay-verify="required" placeholder="验证码" type="text" class="layui-input layui-col-md4">
        </div>
        <div class="layui-input-inline"><img src="/admin/login/getcode?length=4&font_size=20&width=128&height=42&use_noise=1&use_curve=0" onclick="this.src='/admin/login/getcode?length=4&font_size=20&width=128&height=42&use_noise=1&use_curve=0&time='+Math.random();" style="cursor: pointer;" title="点击获取"></div>
        <span style="display:block;float:right;height:40px;line-height:40px;font-size:14px;"><a href="<?php echo url('/admin/login/forget'); ?>">忘记密码？</a></span>
        <hr class="hr15">
        <input  id="submit"  value="登录" lay-submit lay-filter="login" style="width:100%;" type="submit">
        
    </form>
</div>

<script>
    layui.use(['layer','form'],function () {
        var form = layui.form;
        form.on('submit(login)', function(data){
            //登陆效果
            $('#submit').val('登陆中,项目给颗星星吧...');
            $.ajax({
                url: 'login',
                type: 'post',
                dataType: 'json',
                data:{
                    username: data.field.username,
                    password:data.field.password,
                    logincode:data.field.logincode
                },
            })
                .done(function(data){
                    console.log(data);
                    if(data.code==0){
                        layer.msg(data.msg);
                    }else{
                        layer.msg(data.msg,{icon:1,offset:'t'},function(){
                            location.href = data.url;
                        });

                    }
                })
            return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
        });
        })
</script>
</body>
</html>