<?php /*a:1:{s:61:"D:\wamp\www\vioomajxc\application\index\view\login\login.html";i:1577369952;}*/ ?>
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

<div class="login layui-anim layui-anim-up layui-col-md3" style="float:right;margin-right:5%;margin-top:10%;">
    <div class="message" style="margin-bottom:50px;">
        <h2 class="login-title"><?php echo htmlentities($site_config['site_name']); ?>POS登陆</h2>
    </div>
<!--    <div id="darkbannerwrap"></div>-->

    <form method="post" class="layui-form"  action="login">
        <div class="layui-input-inline layui-col-md12">
        <input  id="posusername" name="posusername" placeholder="用户名" autofocus="autofocus" type="text" autocomplete="off" lay-verify="required" class="layui-input" style="text-indent:8px;"><i class="layui-icon layui-icon-username login_icon"></i>
    </div>
        <hr class="hr15">
        <div class="layui-input-inline layui-col-md12">
        <input id="password" name="password" autocomplete="off" lay-verify="required" placeholder="密码"  type="password" class="layui-input" style="text-indent:8px;"><i class="layui-icon layui-icon-password login_icon"></i>
    </div>
        <hr class="hr15">
        <div class="layui-form-item layui-col-md12">
    <div class="layui-input-block" style="float:right;">
      <input type="checkbox" name="remeber" title="记住密码" class="layui-input">
    </div>
  </div>
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
                    posusername: data.field.posusername,
                    password:data.field.password,
                    remeber:data.field.remeber
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