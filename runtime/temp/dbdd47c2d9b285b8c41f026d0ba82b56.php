<?php /*a:3:{s:61:"D:\wamp\www\freejxc\application\index\view\index\payCash.html";i:1577975252;s:56:"D:\wamp\www\freejxc\application\index\view\pub\head.html";i:1577692251;s:56:"D:\wamp\www\freejxc\application\index\view\pub\foot.html";i:1566110242;}*/ ?>
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
<form class="layui-form" lay-filter="lotus-form-filter" id="lotus-edit-form" action="payMent" method="post">
<input type="hidden" name="or_id" value="<?php echo htmlentities($or_id); ?>">
<input type="hidden" name="pay_type" value="cash">
<input type="hidden" name="memberid" value="<?php echo htmlentities($members['id']); ?>">
<input type="hidden" name="pay_money" id="pay_money" value="<?php echo htmlentities($money-$payed); ?>">
<div class="layui-row">
    <table class="info_Table" width="100%">
        <tr>
            <th>订单号：</th>
            <td style="color:#F00;"><?php echo htmlentities($or_id); ?></td>
        </tr>
        <tr>
            <th>会员名称：</th>
            <td><?php echo htmlentities($members['member_name']); ?></td>
        </tr>
        <tr>
            <th>会员卡余额：</th>
            <td>￥<?php echo htmlentities(number_format($members['card_balance'],2)); ?></td>
        </tr>
        <tr>
            <th>订单金额：</th>
            <td>￥<?php echo htmlentities(number_format($allmoney,2)); ?></td>
        </tr>
        <tr>
            <th>已付金额：</th>
            <td>￥<?php echo htmlentities(number_format($payed,2)); ?></td>
        </tr>
        <tr>
            <th>折后金额：</th>
            <td style="font:bold 14px Arial;color:#f00;">￥<?php echo htmlentities(number_format($money-$payed,2)); ?></td>
        </tr>
        <tr>
            <th>会员卡号：</th>
            <td><?php echo htmlentities($members['card_no']); ?>（享<?php echo htmlentities($members['discount']); ?>折）</td>
        </tr>
        <tr>
            <th>金额：</th>
            <td><input type="text" class="layui-input" lay-verify="required" style="width:150px;" name="payed" id="payed" value="<?php echo htmlentities($money-$payed); ?>"></td>
        </tr>
        <tr>
            <th></th>
            <td><button class="layui-btn" lay-submit lay-filter="toSubmit">结 账</button></td>
        </tr>
    </table>
</div>
</form>

<script>
    $('#payed').focus();
</script>
</body>
<script>
     var is_remember = false;
</script>
<script src="/static/js/addon.js"></script>
</html>



