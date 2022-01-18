<?php /*a:2:{s:62:"D:\wamp\www\freejxc\application\admin\view\financial\list.html";i:1581769610;s:57:"D:\wamp\www\freejxc\application\admin\view\pub\modal.html";i:1580923563;}*/ ?>
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
                    <form class="layui-form layui-col-space8" id="ajaxForm" method="get" action="ListJson">
                        <div class="layui-inline layui-show-xs-block">
                            <input class="layui-input" autocomplete="off" placeholder="开始日" name="start" id="start" style="width:120px;"></div>
                        <div class="layui-inline layui-show-xs-block">
                            <input class="layui-input" autocomplete="off" placeholder="截止日" name="end" id="end" style="width:120px;"></div>
                            <div class="layui-inline layui-show-xs-block">
                        <select class="layui-select" name="f_type" autocomplete="off" style="width:120px;">
                        <option value="">借贷</option>
                        <option value="0">贷</option>
                        <option value="1">借</option>
                        </select>
                            </div>
                        <div class="layui-inline layui-show-xs-block">
                                <select class="layui-select" name="f_username" autocomplete="off" lay-search>
                        <option value="">经手人</option>
                        <?php if(is_array($users) || $users instanceof \think\Collection || $users instanceof \think\Paginator): $i = 0; $__LIST__ = $users;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo htmlentities($vo['username']); ?>"><?php echo htmlentities($vo['username']); ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                            </div>
                            <div class="layui-inline layui-show-xs-block">
                                <select class="layui-select" name="f_channel" autocomplete="off" lay-search>
                        <option value="">交易途径</option>
                        <?php if(is_array($channel) || $channel instanceof \think\Collection || $channel instanceof \think\Paginator): $i = 0; $__LIST__ = $channel;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo htmlentities($key); ?>"><?php echo htmlentities($vo); ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                            </div>
                        <div class="layui-inline layui-show-xs-block">
                            <a class="layui-btn lotus-search-btn"  lay-filter="search">
                                <i class="layui-icon">&#xe615;</i></a>
                        </div>
                    </form>
                </div>

                <div class="layui-card-body layui-table-body layui-table-main">
                    <table id="lotus-table"  lay-data="{page:true,limits:[20,50,100],loading:true,toolbar:'#toolbarDemo',url:'listJson',hash:'',totalRow:true}"  class="layui-table layui-hide" lay-filter="lotus-table">
                        <thead>
                        <tr>
                            <th lay-data="{field:'id',sort: true,width:'6%',align:'center',totalRowText:'合计'}">ID</th>
                            <th lay-data="{field:'f_type',align:'center',width:'6%',templet:'#typeTpl'}">借贷</th>
                            <th lay-data="{field:'f_username',width:'8%',align:'center'}">经手人</th>
                            <th lay-data="{field:'f_reason'}">事由</th>
                            <th lay-data="{field:'f_money',templet:'#moneyTpl',width:'10%',totalRow:true}">金额</th>
                            <th lay-data="{field:'f_time',templet:'#dateTpl',width:'10%'}">日期</th>
                            <th lay-data="{field:'f_channel',templet:'#channelTpl',sort:true,width:'8%',align:'center'}">途径</th>
                            <th lay-data="{field:'or_verify_status',templet:'#handTpl',sort:true,width:'8%',align:'center'}">交接</th>
                        </tr>
                        </thead>
                    </table>

                    <script type="text/html" id="toolbarDemo">
                    </script>
                    <script type="text/html" id="moneyTpl">
                        <span>￥{{d.f_money}}</span>
                    </script>
                    <script type="text/html" id="dateTpl">
                        <div>{{ layui.util.toDateString(d.f_time * 1000,"yyyy-MM-dd") }}</div>
                    </script>
                    <script type="text/html" id="channelTpl">
                        {{#  if(d.f_channel==1){   }}
                      <span class="layui-badge layui-bg-green">现金</span>
                      {{# }else if(d.f_channel==2){ }}
                      <span class="layui-badge layui-bg-red">微信</span>
                      {{# }else if(d.f_channel==3){ }}
                      <span class="layui-badge layui-bg-blue">支付宝</span>
                      {{# }else if(d.f_channel==4){ }}
                      <span class="layui-badge layui-bg-orange">银联</span>
                      {{# }else{ }}
                      <span class="layui-badge layui-bg-black">其它</span>
                      {{# } }}
                    </script>
                    <script type="text/html" id="typeTpl">
                      {{#  if(d.f_type==1){   }}
                      <span class="layui-badge layui-bg-green">借</span>
                        {{# }else{ }}
                        <button class="layui-badge layui-btn layui-btn-sm">贷</button>
                      {{# } }}
                    </script>
                    <script type="text/html" id="handTpl">
                      {{#  if(d.handover==1){   }}
                      <span class="layui-badge layui-bg-green">成功</span>
                        {{# }else{ }}
                        <button class="layui-badge layui-btn layui-btn-sm">失败</button>
                      {{# } }}
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
            f_username:$('select[name=f_username]').val(),
            f_type:$('select[name=f_type]').val(),
            f_channel:$('select[name=f_channel]').val(),
            end:$('input[name=end]').val(),
        };
        lotus.table(where);
    })
</script>



</body>
</html>