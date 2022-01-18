<?php /*a:2:{s:61:"D:\wamp\www\freejxc\application\admin\view\user\userList.html";i:1575205932;s:57:"D:\wamp\www\freejxc\application\admin\view\pub\modal.html";i:1580923563;}*/ ?>
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
                    <form class="lotus-search-form layui-form layui-col-space5" method="get" action="userListJson">
                        <div class="layui-inline layui-show-xs-block">
                            <input class="layui-input" autocomplete="off" placeholder="开始日" name="start" id="start"></div>
                        <div class="layui-inline layui-show-xs-block">
                            <input class="layui-input" autocomplete="off" placeholder="截止日" name="end" id="end"></div>
                        <div class="layui-inline layui-show-xs-block">
                            <input type="text" name="username" placeholder="请输入用户名" autocomplete="off" class="layui-input"></div>
                        <div class="layui-inline layui-show-xs-block">
                            <a class="layui-btn lotus-search-btn" lay-submit="" lay-filter="search">
                                <i class="layui-icon">&#xe615;</i></a>
                        </div>
                    </form>
                </div>
                <div class="layui-card-body layui-table-body layui-table-main">
                    <table id="lotus-table"  lay-data="{page:true,limits:[10,20,100],loading:true,toolbar:'#toolbarDemo',url:'userListJson',hash:''}"  class="layui-table layui-hide" >
                        <thead>
                            <tr>
                                <th lay-data="{field:'id', width:80, sort: true}">ID</th>
                                <th lay-data="{field:'username'}">用户名</th>
                                <th lay-data="{field:'email'}">邮箱</th>
                                <th lay-data="{field:'create_time'}">创建时间</th>
                                <th lay-data="{'width':'120',field:'id',width:130,templet: '#actionTpl'}">操作</th>
                            </tr>
                        </thead>
                    </table>
                    <script type="text/html" id="toolbarDemo">
                        <div class = "layui-btn-container" >
                            <button class="layui-btn layui-btn-sm" onclick="xadmin.open('添加用户','addUser.html')"><i class="layui-icon"></i>添加</button>
                        </div >
                    </script>
                    <script type="text/html" id="actionTpl">
                        <button onclick="xadmin.open('编辑用户','editUser.html?id={{d.id}}')" class="layui-btn layui-btn-xs">编辑</button>
                        <button onclick="lotus.del('{{d.id}}','deleteUser')" class="layui-btn layui-btn-danger layui-btn-xs">删除</button>
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
            username:$('input[name=username]').val(),
            end:$('input[name=end]').val(),
        };
        lotus.table(where);
    })
</script>


</body>
</html>