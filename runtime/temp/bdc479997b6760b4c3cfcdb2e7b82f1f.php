<?php /*a:3:{s:64:"D:\wamp\www\vioomajxc\application\index\view\index\handover.html";i:1580405236;s:58:"D:\wamp\www\vioomajxc\application\index\view\pub\head.html";i:1577692251;s:58:"D:\wamp\www\vioomajxc\application\index\view\pub\foot.html";i:1566110242;}*/ ?>
<!doctype html>
<html class="x-admin-sm">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlentities($site_config['site_name']); ?></title>
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="stylesheet" href="/static/css/font.css">
    <link rel="stylesheet" href="/static/css/pos.css">
    <link rel="stylesheet" href="/static/css/xadmin.css">
    <script src="/static/lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="/static/js/xadmin.js"></script>
    <script type="text/javascript" src="/static/js/jquery.min.js"></script>
    <script type="text/javascript" src="/static/js/jquery.form.js"></script>
    <script type="text/javascript" src="/static/js/lotus.js"></script>
    <!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<form class="layui-form" lay-filter="lotus-form-filter" id="lotus-edit-form" action="handOver" method="post">
<input type="hidden" name="hand_cash" value="<?php echo htmlentities($moneys[1]); ?>">
<input type="hidden" name="hand_wechat" value="<?php echo htmlentities($moneys[2]); ?>">
<input type="hidden" name="hand_alipay" value="<?php echo htmlentities($moneys[3]); ?>">
<div class="layui-row">
    <table class="info_Table" width="100%">
        <tr>
            <th>店铺：</th>
            <td style="color:#F00;"><?php echo htmlentities($shopname); ?></td>
            <th>收银员：</th>
            <td><?php echo htmlentities($userinfo['fullname']); ?></td>
        </tr>
        <tr>
            <th>开始时间：</th>
            <td><?php echo htmlentities($userinfo['last_login_time']); ?></td>
            <th>结束时间：</th>
            <td><?php echo htmlentities($handover_time); ?></td>
        </tr>
        <tr>
            <td width="50%" colspan="2">
                <table width='100%'>
                    <tr>
            <td colspan="2" style="text-align:center"><b>当班收入：</b></td>
        </tr>
        <?php if(is_array($channels) || $channels instanceof \think\Collection || $channels instanceof \think\Paginator): $k = 0; $__LIST__ = $channels;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?>
        <tr>
            <th><?php echo htmlentities($channel[$k-1]); ?>：</th>
            <td>￥<?php echo htmlentities(number_format($vo,2)); ?></td>
        </tr>
        <?php endforeach; endif; else: echo "" ;endif; ?>
                </table>
            </td>
            <td colspan="2">
                <table width='100%'>
                    <tr>
            <td colspan="2" style="text-align:center"><b>会员充值：</b></td>
        </tr>
        <?php if(is_array($comes) || $comes instanceof \think\Collection || $comes instanceof \think\Paginator): $k = 0; $__LIST__ = $comes;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?>
        <tr>
            <th><?php echo htmlentities($channel[$k-1]); ?>：</th>
            <td>￥<?php echo htmlentities(number_format($vo,2)); ?></td>
        </tr>
        <?php endforeach; endif; else: echo "" ;endif; ?>
                </table>
            </td>
        </tr>
        <tr>
            <th>今日新增会员数：</th>
            <td><?php echo htmlentities($member_count); ?></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td colspan="4" style="text-align:right;font-weight:bold;padding-right:30px;">现金总收入：￥<?php echo htmlentities(number_format($moneys[1],2)); ?>&nbsp;&nbsp;微信总收入：￥<?php echo htmlentities(number_format($moneys[2],2)); ?>&nbsp;&nbsp;支付宝总收入：￥<?php echo htmlentities(number_format($moneys[3],2)); ?></td>
        </tr>
        <tr>
            <th>钱箱留存现金：</th>
            <td><input type="text" class="layui-input" lay-verify="required" style="width:150px;" name="hand_keep" id="cash" value="0"></td>
        </tr>
        <tr>
            <th></th>
            <td><button class="layui-btn" lay-submit lay-filter="toSubmit">交 接</button></td>
        </tr>
    </table>
</div>
</form>

<script>
    $('#cash').focus();
</script>
</body>
<script>
     var is_remember = false;
</script>
<script src="/static/js/addon.js"></script>
</html>



