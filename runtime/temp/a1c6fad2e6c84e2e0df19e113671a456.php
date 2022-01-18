<?php /*a:3:{s:56:"D:\wamp\www\freejxc\application\install\view\\step5.html";i:1581263028;s:61:"D:\wamp\www\freejxc\application\install\view\public\head.html";i:1581255115;s:63:"D:\wamp\www\freejxc\application\install\view\public\footer.html";i:1571831384;}*/ ?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8" />
<title>ViooMa收银系统安装</title>
<link rel="stylesheet" href="/static/install/simpleboot/themes/flat/theme.min.css" />
<link rel="stylesheet" href="/static/install/css/install.css" />
<link rel="stylesheet" href="/static/font-awesome/css/font-awesome.min.css" type="text/css">


	<script src="/static/install/js/jquery.js"></script>
</head>
<body>
	<div class="wrap">
		<include file="public/header" />
		<section class="section">
			<div style="padding: 40px 20px;">
				<div class="text-center">
					<a style="font-size: 18px;">恭喜您，唯马进销存安装完成！</a>
					<a style="font-size: 18px;">用户名: <span id="username"></span>  密码:<span class="password"></span>  </a>
					<br>
					<br>
					<div class="alert alert-danger" style="width: 350px;display: inline-block;">
						为了您站点的安全，安装完成后即可将网站app目录下的“install”文件夹删除!
						另请对data/database.php文件做好备份，以防丢失！
					</div>
					<br>
				<a class="btn btn-success" href="<?php echo lotus_get_root(); ?>/">进入前台</a>
					<a class="btn btn-success" href="<?php echo lotus_get_root(); ?>/index.php?s=admin">进入后台</a>
					<a class="btn btn-primary"  href="http://www.viooma.com" target="_blank" class="site-star">官方网站</a>
				</div>
			</div>
		</section>
	</div>
	<div class="footer">
	&copy; 2017-<?php echo date('Y'); ?> <a href="https://www.viooma.com" target="_blank">唯马网络</a>
</div>
</body>
</html>