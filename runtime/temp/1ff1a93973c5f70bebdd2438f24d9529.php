<?php /*a:3:{s:64:"D:\wamp\www\freejxc\application\index\view\member\payWechat.html";i:1579264302;s:56:"D:\wamp\www\freejxc\application\index\view\pub\head.html";i:1577692251;s:56:"D:\wamp\www\freejxc\application\index\view\pub\foot.html";i:1566110242;}*/ ?>
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
<input type="hidden" name="memberid" value="<?php echo htmlentities($memberid); ?>">
<input type="hidden" name="pay_type" value="wechat">
<input type="hidden" name="cardno" value="<?php echo htmlentities($cardno); ?>">
<input type="hidden" name="cardmoney" id="cardmoney" value="<?php echo htmlentities($cardmoney); ?>">
<input type="hidden" name="addmoney" id="addmoney" value="<?php echo htmlentities($addmoney); ?>">
<div class="layui-row">
    <table class="info_Table" width="100%">
        <tr>
            <th>会员名称：</th>
            <td><?php echo htmlentities($members['member_name']); ?></td>
        </tr>
        <tr>
            <th>会员身份证：</th>
            <td><?php echo htmlentities($members['member_card']); ?></td>
        </tr>
        <tr>
            <th>会员电话：</th>
            <td><?php echo htmlentities($members['member_phone']); ?></td>
        </tr>
        <tr>
            <th>充值金额：</th>
            <td>￥<?php echo htmlentities(number_format($cardmoney,2)); ?></td>
        </tr>
        <tr>
            <th>赠送金额：</th>
            <td>￥<?php echo htmlentities(number_format($addmoney,2)); ?></td>
        </tr>
        <tr>
            <th>总金额：</th>
            <td style="font:bold 14px Arial;color:#f00;">￥<?php echo htmlentities(number_format($cardmoney+$addmoney,2)); ?></td>
        </tr>
        <tr>
            <th>会员卡号：</th>
            <td><?php echo htmlentities($cardno); ?></td>
        </tr>
        <tr>
            <th>微信付款码：</th>
            <td><input type="text" class="layui-input" lay-verify="required" style="width:150px;" name="auth_code" id="auth_code"></td>
        </tr>
        <tr>
            <th></th>
            <td><button class="layui-btn" lay-submit lay-filter="toSubmit">结 账</button></td>
        </tr>
    </table>
</div>
</form>

<script>
    $('#auth_code').focus();
</script>
</body>
<script>
     var is_remember = false;
</script>
<script src="/static/js/addon.js"></script>
</html>



