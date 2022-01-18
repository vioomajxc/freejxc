<?php /*a:2:{s:64:"D:\wamp\www\freejxc\application\admin\view\member\addMember.html";i:1581146223;s:57:"D:\wamp\www\freejxc\application\admin\view\pub\modal.html";i:1580923563;}*/ ?>
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
    
<script src="/static/lib/city-selector/area.js" type="text/javascript"></script>
<script src="/static/lib/city-selector/select.js" type="text/javascript"></script>

    
    
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
        <form class="layui-form layui-form-pane" id="lotus-add-form" action="addMember" method="post">


            <div class="layui-form-item">
                <label class="layui-form-label">会员名称</label>
                <div class="layui-input-block">
                    <input type="text"  name="member_name" lay-verify="required" autocomplete="off"  class="layui-input" placeholder="企业名称或个人姓名">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">身份证号</label>
                <div class="layui-input-block">
                    <input type="text"  name="member_card" lay-verify="required" autocomplete="off"  class="layui-input" size="18">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">手机号码</label>
                <div class="layui-input-block">
                    <input type="text"  name="member_phone" lay-verify="required" autocomplete="off"  class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">会员简称</label>
                <div class="layui-input-block">
                    <input type="text"  name="member_sname" autocomplete="off"  class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">分&nbsp;&nbsp;&nbsp;类</label>
                <div class="layui-input-block">
                    <select lay-filter="aihao" name="member_category" lay-verify="required">
                        <option value="">顶级分类</option>
                        <?php if(is_array($mcategory) || $mcategory instanceof \think\Collection || $mcategory instanceof \think\Paginator): $i = 0; $__LIST__ = $mcategory;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities(str_repeat('丨--',$vo['level']-1)); ?><?php echo htmlentities($vo['mcategory_name']); ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">店&nbsp;&nbsp;&nbsp;铺</label>
                <div class="layui-input-block">
                    <select lay-filter="aihao" name="member_shop" lay-verify="required">
                        <option value="">所属店铺</option>
                        <?php if(is_array($shop) || $shop instanceof \think\Collection || $shop instanceof \think\Paginator): $i = 0; $__LIST__ = $shop;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['shop_name']); ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
            <label class="layui-form-label">请选择地区</label>
                <div class="layui-inline" style="width:150px;">
                    <select name="province" id="province" lay-verify="required" lay-search lay-filter="province" autocomplete="off">
                        <option value="">省份</option>
                    </select>
                </div>
                <div class="layui-inline" style="width:150px;">
                    <select name="city" id="city" lay-verify="required" lay-search lay-filter="city">
                        <option value="">地级市</option>
                    </select>
                </div>
                <div class="layui-inline" style="width:150px;">
                    <select name="area" id="area" lay-verify="required" lay-search>
                        <option value="">县/区</option>
                    </select>
                </div>
        </div>

            <div class="layui-form-item">
                <label class="layui-form-label">街道</label>
                <div class="layui-input-block">
                    <input type="text"  name="member_address" autocomplete="off"  class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">会员网站</label>
                <div class="layui-input-block">
                    <input type="text"  name="member_site" autocomplete="off"  class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">
                    是否启用
                </label>
                <div class="layui-input-block">
                    <select lay-filter="aihao" name="member_status">
                        <option value="1">启用</option>
                        <option value="0">禁止</option>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">会员简介</label>
                <div class="layui-input-block">
                    <textarea  name="comment" style="height:80px;" class="layui-input"></textarea>
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