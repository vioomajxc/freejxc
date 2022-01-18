<?php /*a:2:{s:62:"D:\wamp\www\vioomajxc\application\index\view\member\index.html";i:1580363668;s:59:"D:\wamp\www\vioomajxc\application\index\view\pub\modal.html";i:1579079993;}*/ ?>
<!doctype html>
<html class="x-admin-sm">
<head>
    <meta charset="UTF-8">
    <title>唯马POS收银系统</title>
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="stylesheet" href="/static/css/font.css">
    <link rel="stylesheet" href="/static/css/pos.css">
    <link rel="stylesheet" href="/static/css/xadmin.css">
    <script src="/static/lib/layui/css/layui.css" charset="utf-8"></script>
    <script src="/static/lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="/static/js/xadmin.js"></script>
    <script type="text/javascript" src="/static/js/jquery.min.js"></script>
    <script type="text/javascript" src="/static/js/jquery.form.js"></script>
    <script type="text/javascript" src="/static/js/lotus.js"></script>
    
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

<!-- 顶部开始 -->
<div class="layui-layout layui-layout-admin" >
    <div class="layui-header">
    <div class="layui-logo shop"><?php echo htmlentities($shopname); ?></div>
    <ul class="layui-nav layui-layout-left" style="line-height: 200%;">
      <li class="layui-nav-item"><a href="/index/index" title="进入收银界面"><span class="layui-icon layui-icon-rmb" style="margin-right:5px;"></span>收银</a></li>
      <li class="layui-nav-item"><a style="color:#F00;"><span class="layui-icon layui-icon-user" style="margin-right:5px;"></span>会员</a></li>
      <li class="layui-nav-item layui-bg-cyan"><a href="javascript:void();" onclick="xadmin.open('交接班对账单','/index/index/handover/')"><span class="layui-icon layui-icon-list" style="margin-right:5px;"></span>交接班</a></li>   
      <li class="layui-nav-item">
        <a href="javascript:;">更多<span class="layui-icon layui-icon-more" style="margin-right:5px;"></span></a>
        <dl class="layui-nav-child">
          <dd><a onclick="xadmin.open('修改密码','<?php echo url('index/login/editPasswd'); ?>',400,350)">修改密码</a>
                </dd>
          <dd><a  href="javascript:logout()">注销账号</a>
                </dd>
        </dl>
      </li>
    </ul>

    <ul class="layui-nav layui-layout-right" lay-filter="">
        <li class="layui-nav-item layui-hide-xs" lay-unselect="" title="全屏">
            <a  href="javascript:" id="fullscreen">
                <i class="layui-icon layui-icon-screen-full"></i>
            </a>
        </li>
        <li class="layui-nav-item" lay-unselect>
            <a href="javascript:;" class="layui-btn layui-bg-gray"><i class="layui-icon layui-icon-username" style="margin-right:5px;"></i><?php echo htmlentities($usinfo); ?></a>
        </li>
        <li class="layui-nav-item"><a style="margin-right:5px;">&nbsp;</a></li>
    </ul>
</div>
<!-- 顶部结束 -->
<!-- 中部开始 -->
<div class="layui-side" style="width:25%;background:#FFF;border-right:1px dashed #999;padding:10px;">
    <div class="layui-row">
    	<fieldset class="layui-elem-field layui-field-title" >
                <legend style="color:#FF5722">快速添加店铺会员</legend>
            </fieldset>
        <form class="layui-form layui-form-pane" id="lotus-add-form" action="addMember" method="post">
            <div class="layui-form-item">
                <label class="layui-form-label">会员名称</label>
                <div class="layui-input-block">
                    <input type="text"  name="member_name" lay-verify="required" autocomplete="off"  class="layui-input" placeholder="企业名称或个人姓名">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">会员简称</label>
                <div class="layui-input-block">
                    <input type="text"  name="member_sname" autocomplete="off"  class="layui-input">
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
                <label class="layui-form-label">分&nbsp;&nbsp;&nbsp;类</label>
                <div class="layui-input-block">
                    <select lay-filter="aihao" name="member_category">
                        <option value="0">顶级分类</option>
                        <?php if(is_array($mcategory) || $mcategory instanceof \think\Collection || $mcategory instanceof \think\Paginator): $i = 0; $__LIST__ = $mcategory;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities(str_repeat('丨--',$vo['level']-1)); ?><?php echo htmlentities($vo['mcategory_name']); ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">会员地址</label>
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
                    <button class="layui-btn" lay-submit="" lay-filter="toSubmit">提交</button>
                    <button id="reset" type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>
    </div>
  </div>

<!-- 右侧主体开始 -->
<div class="layui-body right layui-bg-gray" style="left:26%;padding:10px;">
    <div class="layui-fluid layui-anim layui-anim-upbit">
        <div class="layui-row layui-col-space10">
            <div class="layui-col-md4">
                <!--会员及支付版块-->
                <div class="layui-card">
                    <div class="layui-card-body" id="member_info_pal">
                        <table class="member-table">
                            <tbody>
                                <tr>
                                    <th>会员姓名：</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>会员卡号：</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>电话号码：</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>会员级别：</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>现金余额：</th>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        
                </div>
                <div class="layui-card-body">
                <div class="layui-form-item">
                            <input type="text" class="layui-input" name="cardno" placeholder="会员卡号/身份证号/手机号" id="cardNo">
                        </div>
                            <button type="button" class="layui-btn layui-btn-fluid" name="readcard" id="readcard">输入/读取会员卡充值</button>
                    </div>
                </div>
                <hr style="color:#000;">
        <!--支付按钮-->
        <div class="layui-card">
            <div class="layui-card-body">
                    <div class="money layui-inline">
                    	<label class="layui-form-label">充值:</label>
                    	<div class="layui-inline"><input type="text" name="cardMoney" id="cardMoney" value="0" class="layui-input" style="width:200px;" autofocus="autofocus"></div>
                    </div>
                    <div class="money layui-inline">
                    	<label class="layui-form-label">赠送:</label>
                    	<div class="layui-inline"><input type="text" name="addMoney" id="addMoney" value="0" class="layui-input" style="width:200px;" autofocus="autofocus"></div>
                    </div>
            </div>
        </div>
                <div class="layui-card">
                    <div class="layui-card-body">
                        <div class="layui-row layui-col-space10 pay-btn">
                        
                        <div class="layui-col-sm6">
                          <div class="layui-bg-gray pay-cash">
                              <a href="javascript:void();" id="pay-cash" title="使用现金收款">现金充值</a>
                          </div>
                        </div>

                        <div class="layui-col-sm6">
                          <div class="layui-bg-gray pay-alipay">
                              <a href="javascript:void();" id="pay-alipay" title="使用支付宝收款">支付宝充值</a>
                          </div>
                        </div>

                        <div class="layui-col-sm6">
                          <div class="layui-bg-gray pay-wechat">
                              <a href="javascript:void();" id="pay-wechat" title="使用微信收款">微信充值</a>
                          </div>
                        </div>

                        <div class="layui-col-sm6">
                          <div class="layui-bg-gray pay-bank">
                              <a href="javascript:void();" id="pay-bank" title="使用银联刷卡收款">银行卡</a>
                          </div>
                        </div>

                        <div class="layui-col-sm6">
                          <div class="layui-bg-gray pay-baidu">
                              <a href="javascript:void();" id="pay-baidu" title="使用百度钱包收款">百度钱包</a>
                          </div>
                        </div>

                        <div class="layui-col-sm6">
                          <div class="layui-bg-gray pay-tuan">
                              <a href="javascript:void();" id="pay-tuan" title="团购收款">团&nbsp;&nbsp;购</a>
                          </div>
                        </div>
                        </div>
            </div>
        </div>
            </div>

            <div class="layui-col-md7">
                <!--收款版块-->
                <div class="layui-card">
                    <div class="layui-card-header goods_pal_title" id="goods_pal_title">会员列表</div>
                    <div class="layui-card-body ">
                    <form class="layui-form layui-col-space10" id="ajaxForm" method="get" >
                        <div class="layui-inline layui-show-xs-block">
                            <input class="layui-input" style="width:120px;" autocomplete="off" placeholder="开始日" name="start" id="start"></div>
                        <div class="layui-inline layui-show-xs-block">
                            <input class="layui-input" style="width:120px;" autocomplete="off" placeholder="截止日" name="end" id="end"></div>
                        <div class="layui-inline layui-show-xs-block">
                            <input type="text" name="smember_name" placeholder="请输入名称" autocomplete="off" class="layui-input"></div>
                        <div class="layui-inline layui-show-xs-block">
                            <a class="layui-btn lotus-search-btn"  lay-filter="search">
                                <i class="layui-icon">&#xe615;</i></a>
                        </div>
                    </form>
                </div>
                    <div class="layui-card-body layui-row goods_pal_body" id="goods_pal_body">
                        <table id="lotus-table"  lay-data="{page:true,limits:[10,20,50,100],loading:true,url:'/index/member/memberListJson?category=<?php echo htmlentities($category); ?>',toolbar:true,hash:''}"  class="layui-table layui-hide" >
                        <thead>
                        <tr>
                            <th lay-data="{field:'id',sort: true}">ID</th>
                            <th lay-data="{field:'member_sname',width:150,sort:true}">会员简称</th>
                            <th lay-data="{field:'member_phone'}">电话</th>
                            <th lay-data="{field:'mcategory_name',sort:true}">分类</th>
                            <th lay-data="{field:'member_regtime',templet:'<div>{{ layui.util.toDateString(d.member_regtime * 1000) }}</div>'}">注册日期</th>
                            <th lay-data="{field:'id',templet:'#actionTpl'}">操作</th>
                        </tr>
                        </thead>
                    </table>
                    <script type="text/html" id="actionTpl">
                        <button onclick="addMemberCard('{{d.id}}')" class="layui-btn  layui-btn-xs layui-btn-normal">开卡</button>
                    </script>
                    </div>
                </div>
            </div>

            <div class="toolbar layui-col-md1">
                <!--分类列表-->
                <a href="/index/member/" class="layui-badge">所有分类</a>
                <?php if(is_array($categorys) || $categorys instanceof \think\Collection || $categorys instanceof \think\Paginator): $i = 0; $__LIST__ = $categorys;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <a href="?category=<?php echo htmlentities($vo['id']); ?>" class="layui-badge" title="<?php echo htmlentities($vo['mcategory_name']); ?>"><?php echo htmlentities($vo['mcategory_name']); ?></a>        
                <?php endforeach; endif; else: echo "" ;endif; ?>                
        </div>
        
    </div>
</div>
<div class="layui-footer footer" style="left:25%;">
    
</div>
</div>
<script>
    $('.lotus-search-btn').on('click',function () {
        var where = {
            start:$('input[name=start]').val(),
            smember_name:$('input[name=smember_name]').val(),
            end:$('input[name=end]').val(),
        };
        lotus.table(where);
    })
</script>
<script type="text/javascript">
$(function(){
    //会员卡信息读取
    $('#readcard').click(function(event){
            cardno = $('#cardNo').val();
            if(cardno == ""){
            layer.msg("会员卡/身份证号/手机号不能为空！",{icon:5,time:1000});
            return false;
        }
            $.ajax({
                url:"<?php echo url('index/index/readCard'); ?>",
                type:'post',
                dataType:'json',
                data:{cardno:cardno},
            })
            .done(function(data){
                    if(data.code==0){
                        layer.msg(data.msg,{icon:5,time:500});
                    }else{
                            $('#member_info_pal').html(data.html);
                            $('#cardMoney').focus();
                    }
                })
    });



    //银联付款
    $('#pay-bank').click(function(){
        layer.msg('银联付款功能暂未启用！',{icon:5,time:1000});
    })

    //百度钱包付款
    $('#pay-baidu').click(function(){
        layer.msg('百度钱包付款功能暂未启用！',{icon:5,time:1000});
    })

    //团购付款
    $('#pay-tuan').click(function(){
        layer.msg('团购功能暂未启用！',{icon:5,time:1000});
    })


    //现金付款
    $('#pay-cash').click(function(){
        cardMoney = $('#cardMoney').val();
        addmoney = $('#addMoney').val();
        cardno = $('#cardNo').val();
        memberid = $("input[name='memberid']").val();
        if(cardMoney == 0 || memberid == 0 || cardno == ""){
            layer.msg("必须选择会员、录入卡号和充值金额！",{icon:5,time:1000});
            return false;
        }
        xadmin.open('现金结算','/index/member/payMent.html?pay_type=cash&cardno='+cardno+'&cardmoney='+cardMoney+'&memberid='+memberid+'&addmoney='+addmoney,300,400);
    })
    //微信付款
    $('#pay-wechat').click(function(){
        cardMoney = $('#cardMoney').val();
        addmoney = $('#addMoney').val();
        cardno = $('#cardNo').val();
        memberid = $("input[name='memberid']").val();
        if(cardMoney == 0 || memberid == 0 || cardno == ""){
            layer.msg("必须选择会员、录入卡号和充值金额！",{icon:5,time:1000});
            return false;
        }
        xadmin.open('微信结算','/index/member/payMent.html?pay_type=wechat&cardno='+cardno+'&cardmoney='+cardMoney+'&memberid='+memberid+'&addmoney='+addmoney,300,400);
    })

    //支付宝付款
    $('#pay-alipay').click(function(){
        cardMoney = $('#cardMoney').val();
        addmoney = $('#addMoney').val();
        cardno = $('#cardNo').val();
        memberid = $("input[name='memberid']").val();
        if(cardMoney == 0 || memberid == 0 || cardno == ""){
            layer.msg("必须选择会员、录入卡号和充值金额！",{icon:5,time:1000});
            return false;
        }
        xadmin.open('支付宝结算','/index/member/payMent.html?pay_type=alipay&cardno='+cardno+'&cardmoney='+cardMoney+'&memberid='+memberid+'&addmoney='+addmoney,300,400);
    })
 
    });

    //注销方法
    function logout() {
        $.ajax({
            url: "<?php echo url('index/login/logout'); ?>",
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
 
//读取会员信息
    function addMemberCard(id){
        $.ajax({
            url:'/index/member/addMemberCard',
            type:'post',
            dataType:'json',
            data:{id:id},
        })
        .done(function(data){
                    if(data.code==0){
                        layer.msg(data.msg,{icon:5,time:500});
                    }else{
                        if(data.title!="")
                            $('#member_info_pal').html(data.html);
                            $('#cardMoney').focus();
                    }
                })
    }
  //处理会员卡读卡
    $('#cardNo').keypress(function(e){
        if(e.KeyCode=="13"){
            $("#readcard").click();
        }
    });
</script>

</body>
</html>