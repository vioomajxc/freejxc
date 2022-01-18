<?php /*a:5:{s:61:"D:\wamp\www\vioomajxc\application\admin\view\index\index.html";i:1566110242;s:58:"D:\wamp\www\vioomajxc\application\admin\view\pub\base.html";i:1581269863;s:58:"D:\wamp\www\vioomajxc\application\admin\view\pub\head.html";i:1580744803;s:58:"D:\wamp\www\vioomajxc\application\admin\view\pub\left.html";i:1566110242;s:58:"D:\wamp\www\vioomajxc\application\admin\view\pub\foot.html";i:1566110242;}*/ ?>
<!doctype html>
<html class="x-admin-sm">
<head>
    <meta charset="UTF-8">
    <title>唯马进销存通用后台</title>
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="stylesheet" href="/static/css/font.css">
    <link rel="stylesheet" href="/static/css/xadmin.css">
    <link rel="stylesheet" href="<?php echo htmlentities($theme); ?>">
    <script src="/static/lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="/static/js/xadmin.js"></script>
    <script type="text/javascript" src="/static/js/jquery.min.js"></script>
    <script type="text/javascript" src="/static/js/jquery.form.js"></script>
    <!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="index">
<!-- 顶部开始 -->
<div class="container" >
    <div class="logo">
        <a href="/index.php/admin"><?php echo htmlentities($site_config['site_name']); ?></a></div>
    <div class="left_open">
        <a><i title="展开左侧栏" class="layui-icon">&#xe668;</i></a>
    </div>

    <div class="shuaxin" style="margin-left: 25px;">
        <a class="top-refresh" ><i style="background: rgba(0,0,0,0);" title="刷新" class="layui-icon layui-icon-refresh"></i></a>
    </div>

    <ul class="layui-nav right" lay-filter="">
        <li class="layui-nav-item layui-hide-xs" lay-unselect="">
            <a href="/index/index" target="_blank" title="POS收银前台">
                <i class="layui-icon layui-icon-website"></i>
            </a>
        </li>
        <li class="layui-nav-item layui-hide-xs" lay-unselect="" title="全屏">
            <a  href="javascript:" id="fullscreen">
                <i class="layui-icon layui-icon-screen-full"></i>
            </a>
        </li>
        <li class="layui-nav-item" lay-unselect >
            <a href="javascript:;"><i class="layui-icon layui-icon-theme"></i>风格</a>
            <dl class="layui-nav-child" >
                <!-- 二级菜单 -->
                <dd>
                    <a href="javascript:setTheme('white-black')">阿修罗</a>
                </dd>
                <dd>
                    <a href="javascript:setTheme('black')">黑武士</a>
                </dd>
                <dd>
                    <a  href="javascript:setTheme('girl')">紫霞仙子</a>
                </dd>
            </dl>
        </li>
        <li class="layui-nav-item" lay-unselect>

            <a href="javascript:;"><i class="layui-icon layui-icon-username"></i><?php echo session('username'); ?></a>
            <dl class="layui-nav-child" >
                <!-- 二级菜单 -->
                <dd>
                    <a onclick="xadmin.open('修改密码','<?php echo url('admin/user/editPasswd'); ?>')">修改密码</a>
                </dd>
                <dd>
                    <a  href="javascript:logout()">注销账号</a>
                </dd>
            </dl>
        </li>
        <li class="layui-nav-item layui-hide-xs" style="position:relative;right: 1px;">
            <a href="/index/index" target="_blank" title="更多">
                <i class="layui-icon layui-icon-more-vertical"></i>
            </a>
        </li>

    </ul>
</div>
<!-- 顶部结束 -->
<!-- 中部开始 -->
<!-- 左侧菜单开始 -->
<div class="left-nav">
    <div id="side-nav">
        <ul id="nav">
            <?php if(is_array($menu) || $menu instanceof \think\Collection || $menu instanceof \think\Paginator): if( count($menu)==0 ) : echo "" ;else: foreach($menu as $key=>$vo): if(isset($vo['children'])): ?>
            <li>
                <a href="javascript:;">
                    <?php if(!empty($vo['icon'])): ?>
                    <i class="layui-icon <?php echo htmlentities($vo['icon']); ?>"></i>
                    <?php else: ?>
                    <i class="layui-icon"></i>
                    <?php endif; ?>
                    <!-- <i class="iconfont">&#xe620;</i> -->
                    <cite><?php echo htmlentities($vo['title']); ?></cite>
                    <i class="iconfont nav_right">&#xe697;</i>
                </a>
                <ul class="sub-menu">
                    <?php if(is_array($vo['children']) || $vo['children'] instanceof \think\Collection || $vo['children'] instanceof \think\Paginator): if( count($vo['children'])==0 ) : echo "" ;else: foreach($vo['children'] as $key=>$v): if(isset($v['children'])): ?>
                    <li>
                        <a  onclick="xadmin.add_tab('<?php echo htmlentities($v['title']); ?>','<?php echo url($v['name']); ?>')">
                            <?php if(empty($v['icon'])): ?>
                            <i class="layui-icon">&#xe602;</i>
                            <?php else: ?>
                            <i class="layui-icon <?php echo htmlentities($vo['icon']); ?>"></i>
                            <?php endif; ?>
                            <cite> <?php echo htmlentities($v['title']); ?></cite>
                            <i class="iconfont nav_right">&#xe697;</i>
                        </a>
                        <ul class="sub-menu">
                            <?php if(is_array($v['children']) || $v['children'] instanceof \think\Collection || $v['children'] instanceof \think\Paginator): if( count($v['children'])==0 ) : echo "" ;else: foreach($v['children'] as $key=>$vs): ?>
                            <li>
                                <a _href="<?php echo url($vs['name']); ?>">
                                    <i class="layui-icon">&#xe63f;</i>
                                    <cite> <?php echo htmlentities($vs['title']); ?></cite>

                                </a>
                            </li >
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li>
                        <a  onclick="xadmin.add_tab('<?php echo htmlentities($v['title']); ?>','<?php echo url($v['name']); ?>')">
                            <i class="layui-icon">&#xe63f;</i>
                            <cite> <?php echo htmlentities($v['title']); ?></cite>
                        </a>
                    </li>
                    <?php endif; ?>
                    </li >
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
            </li>
            <?php else: ?>
            <li>
                <a   onclick="xadmin.add_tab('<?php echo htmlentities($vo['title']); ?>','<?php echo url($vo['name']); ?>')"_>
                    <?php if(!empty($vo['icon'])): ?>
                    <i class="layui-icon <?php echo htmlentities($vo['icon']); ?>"></i>
                    <?php else: ?>
                    <i class="layui-icon"></i>
                    <?php endif; ?>
                    <cite><?php echo htmlentities($vo['title']); ?></cite>
                    <!--<i class="iconfont nav_right">&#xe697;</i>-->
                </a>
            </li >
            <?php endif; ?>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div>
</div>
<!-- <div class="x-slide_left"></div> -->
<!-- 左侧菜单结束 -->
<!-- 右侧主体开始 -->
<div class="page-content">
    <div class="layui-tab tab tab" lay-filter="xbs_tab" lay-allowclose="false">
        <ul class="layui-tab-title">
            <li class="home layui-this" lay-id="home">
                <i class="layui-icon">&#xe68e;</i>我的桌面</li></ul>
        <div class="layui-unselect layui-form-select layui-form-selected" id="tab_right">
            <dl>
                <dd data-type="this">关闭当前</dd>
                <dd data-type="other">关闭其它</dd>
                <dd data-type="all">关闭全部</dd></dl>
        </div>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <iframe  src="<?php echo url('admin/index/welcome'); ?>" frameborder="0" scrolling="yes" class="x-iframe"></iframe>
            </div>
        </div>
        <div id="tab_show"></div>
    </div>
</div>
<div class="page-content-bg"></div>
<style id="theme_style"></style>
</body>
<script>
    //注销方法
    function logout() {
        $.ajax({
            url: "<?php echo url('/admin/login/logout'); ?>",
            type: 'post',
            dataType: 'json',
            data:{},
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
    }
    //切换主题
    function setTheme(key) {
        $.ajax({
            url: "<?php echo url('/admin/index/setTheme'); ?>",
            type: 'get',
            dataType: 'json',
            data:{'theme':key},
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
    }
</script>
<script>
     var is_remember = false;
</script>
<script src="/static/js/addon.js"></script>
</html>