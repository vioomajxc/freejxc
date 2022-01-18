<?php /*a:2:{s:65:"D:\wamp\www\freejxc\application\admin\view\member\memberList.html";i:1580489237;s:57:"D:\wamp\www\freejxc\application\admin\view\pub\modal.html";i:1580923563;}*/ ?>
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
                    <form class="layui-form layui-col-space8" id="ajaxForm" method="get" >
                        <div class="layui-inline layui-show-xs-block">
                            <input class="layui-input" autocomplete="off" placeholder="开始日" name="start" id="start"></div>
                        <div class="layui-inline layui-show-xs-block">
                            <input class="layui-input" autocomplete="off" placeholder="截止日" name="end" id="end"></div>
                        <div class="layui-inline layui-show-xs-block">
                            <input type="text" name="member_name" placeholder="请输入名称" autocomplete="off" class="layui-input"></div>
                        <div class="layui-inline layui-show-xs-block">
                            <a class="layui-btn lotus-search-btn"  lay-filter="search">
                                <i class="layui-icon">&#xe615;</i></a>
                        </div>
                    </form>
                </div>
                <div class="layui-card-body layui-table-body layui-table-main">
                    <table id="lotus-table"  lay-data="{page:true,limits:[10,20,100],loading:true,toolbar:'#toolbarDemo',url:'memberListJson',hash:''}"  class="layui-table layui-hide" >
                        <thead>
                        <tr>
                            <th lay-data="{field:'id',sort: true,width:'6%',align:'center'}">ID</th>
                            <th lay-data="{field:'member_code',sort:true,width:'12%'}">会员代码</th>
                            <th lay-data="{field:'member_name'}">会员名称</th>
                            <th lay-data="{field:'mcategory_name',width:'8%'}">分类</th>
                            <th lay-data="{field:'member_regtime',templet:'<div>{{ layui.util.toDateString(d.member_regtime * 1000) }}</div>',width:'15%'}">注册日期</th>
                            <th lay-data="{field:'member_status',templet:'#statusTpl',width:'8%'}">状态</th>
                            <th lay-data="{'width':'180',field:'id',templet:'#actionTpl',width:'15%'}">操作</th>
                        </tr>
                        </thead>
                    </table>

                    <script type="text/html" id="toolbarDemo">
                        <div class = "layui-btn-container" >
                            <button class="layui-btn layui-btn-sm" onclick="xadmin.open('添加会员','addMember.html',720)"><i class="layui-icon"></i>添加</button>
                        </div >
                    </script>
                    <script type="text/html" id="actionTpl">
                        {{#  if(d.member_status==1){   }}
                        <button onclick="xadmin.open('会员开卡','addMemberCard.html?id={{d.id}}')" class="layui-btn  layui-btn-xs layui-btn-normal">开卡</button>
                        {{#   } }}
                        <button onclick="xadmin.open('编辑会员','editMember.html?id={{d.id}}')" class="layui-btn  layui-btn-xs">编辑</button>
                        <button onclick="lotus.del('{{d.id}}','delMember')" class="layui-btn layui-btn-danger layui-btn-xs">删除</button>
                    </script>
                    <script type="text/html" id="statusTpl">
                        <input type='checkbox' name='status' lay-skin='switch' value="{{d.id}}" lay-filter='status' lay-text='正常|禁止'  {{ d.member_status == 1 ? 'checked' : '' }}>
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
            member_name:$('input[name=member_name]').val(),
            end:$('input[name=end]').val(),
        };
        lotus.table(where);
    })

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
            url: "<?php echo url('admin/member/set_status'); ?>",
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