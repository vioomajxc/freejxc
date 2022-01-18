<?php /*a:4:{s:61:"D:\wamp\www\vioomajxc\application\index\view\index\index.html";i:1577370539;s:58:"D:\wamp\www\vioomajxc\application\index\view\pub\base.html";i:1580363678;s:58:"D:\wamp\www\vioomajxc\application\index\view\pub\head.html";i:1577692251;s:58:"D:\wamp\www\vioomajxc\application\index\view\pub\foot.html";i:1566110242;}*/ ?>
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
<body class="layui-layout-body">
<!-- 顶部开始 -->
<div class="layui-layout layui-layout-admin" >
    <div class="layui-header">
    <div class="layui-logo shop"><?php echo htmlentities($shopname); ?></div>
    <ul class="layui-nav layui-layout-left" style="line-height: 200%;">
      <li class="layui-nav-item" style="color:#F00;"><span class="layui-icon layui-icon-rmb" style="margin-right:5px;"></span>收银</li>
      <li class="layui-nav-item"><a href="/index/member/index.html"><span class="layui-icon layui-icon-user" style="margin-right:5px;"></span>会员</a></li>
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
<div class="layui-side" style="width:30%;background:#FFF;border-right:1px dashed #999;">
    <div class="layui-row">
      <div class="layui-col-md12 orderid">
        <span class="layui-col-md4 layui-col-md-offset1" style="color:#FF5722;">No:<?php echo htmlentities($or_id); ?></span>
        <span class="layui-col-md4 layui-col-md-offset3"><?php echo htmlentities($thistime); ?></span>
    </div>
    <div class="layui-fluid">
        <div class="layui-row layui-col-md12 posTool">
            <span class="layui-col-md2">客单</span>
            <a class="delicon" title="删除订单产品列表" href="javascript:void();" onclick="delpositem('<?php echo htmlentities($or_id); ?>','/index/index/delposGoods')"><i class="layui-icon layui-icon-delete layui-col-md-offset8"></i></a>
        </div>
        <div class="layui-col-md12">
                    <table class="layui-table" id="posTable"></table>
                </div>
                <script type="text/html" id="actionTpl">
                        <a href="javascript:void();" onclick="delpositem_single('{{d.id}}','index/index/delposGoods')"><i class="layui-icon layui-icon-delete"></i></a>
                    </script>
                <div class="skuTpl">
                    <input type="text" class="layui-input layui-input-inline" style="width:80%;" name="sku" id="skuInput" autofocus="true" lay-verify="required">
                    <button type="button" class="layui-btn layui-btn-warm" onclick="xadmin.open('选择要添加的无码商品','/index/index/selectGoods.html?or_id=<?php echo htmlentities($or_id); ?>',800,600)">商品</button>
                </div>
    </div>
      
    </div>
  </div>

<!-- 右侧主体开始 -->
<div class="layui-body right layui-bg-gray" style="left:30%;padding:10px;">
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
                            <input type="hidden" name="hideMoney" id="hideMoney" value="0">
                        </div>
                            <button type="button" class="layui-btn layui-btn-fluid" name="readcard" id="readcard">读取会员卡</button>
                    </div>
                </div>
                <hr style="color:#000;">
        <!--支付按钮-->
        <div class="layui-card">
            <div class="layui-card-body">
                <div class="layui-row">
                    <div class="money" id="Money">0</div>
                </div>
            </div>
        </div>
                <div class="layui-card">
                    <div class="layui-card-body">
                        <div class="layui-row layui-col-space10 pay-btn">
                        <div class="layui-col-sm6">
                          <div class="layui-bg-gray pay-coupon">
                              <a href="javascript:void();" id="pay-coupon" title="优惠券付款">优惠券</a>
                          </div>
                        </div>

                        <div class="layui-col-sm6">
                          <div class="layui-bg-gray pay-card">
                              <a href="javascript:void();" id="pay-card" title="会员卡扣款">卡&nbsp;&nbsp;扣</a>
                          </div>
                        </div>

                        <div class="layui-col-sm6">
                          <div class="layui-bg-gray pay-cash">
                              <a href="javascript:void();" id="pay-cash" title="使用现金收款">现&nbsp;&nbsp;金</a>
                          </div>
                        </div>

                        <div class="layui-col-sm6">
                          <div class="layui-bg-gray pay-alipay">
                              <a href="javascript:void();" id="pay-alipay" title="使用支付宝收款">支付宝</a>
                          </div>
                        </div>

                        <div class="layui-col-sm6">
                          <div class="layui-bg-gray pay-wechat">
                              <a href="javascript:void();" id="pay-wechat" title="使用微信收款">微&nbsp;&nbsp;信</a>
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
                    <div class="layui-card-header goods_pal_title" id="goods_pal_title">热卖商品（TOP20）</div>
                    <div class="layui-card-body layui-row goods_pal_body" id="goods_pal_body" style="background:#F2F2F2;">
                        <?php if(is_array($goods) || $goods instanceof \think\Collection || $goods instanceof \think\Paginator): $i = 0; $__LIST__ = $goods;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>                    
                        <div class="layui-col-md3 layui-fluid layui-col-space50">
                            <div class="layui-card">
                                <a href="javascript:void();" title="<?php echo htmlentities($vo['goodsname']); ?>库存数量为<?php echo htmlentities($vo['numbers']); ?>" onclick="manualToPos(<?php echo htmlentities($vo['id']); ?>,'<?php echo htmlentities($or_id); ?>',<?php echo htmlentities($vo['price']); ?>);">
                        <div class="layui-card-header goods_title"><?php echo htmlentities($vo['goodsname']); ?></div>
                        <div class="layui-card-body goods_pal">
                            <span class="price">￥<?php echo htmlentities(number_format($vo['price'],2)); ?></span>
                    <span class="layui-badge" style="float:right;"><?php echo htmlentities($vo['numbers']); ?></span>        
                        </div>
                    </a>
                    </div>
                       </div>
                <?php endforeach; endif; else: echo "" ;endif; ?> 
                    </div>
                </div>
            </div>

            <div class="toolbar layui-col-md1">
                <!--分类列表-->
                <?php if(is_array($categorys) || $categorys instanceof \think\Collection || $categorys instanceof \think\Paginator): $i = 0; $__LIST__ = $categorys;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <a href="javascript:void();" onclick="openCategory(<?php echo htmlentities($vo['id']); ?>,'<?php echo htmlentities($or_id); ?>')" class="layui-badge" title="<?php echo htmlentities($vo['category_name']); ?>"><?php echo htmlentities($vo['category_name']); ?></a>        
                <?php endforeach; endif; else: echo "" ;endif; ?>                
        </div>
        
    </div>
</div>
<div class="layui-footer footer" style="left:30%;">
    <div class="layui-row layui-col-md12">
    <button type="button" class="layui-btn" onclick="OpenPort();">开钱箱(F2)</button>
    <button type="button" class="layui-btn" onclick="hangUp('<?php echo htmlentities($or_id); ?>');">挂单(F3)</button>
    <button type="button" class="layui-btn" onclick="xadmin.open('订单取出','/index/index/getOutList');">取单(F4)</button>
</div>
</div>
</div>
<script type="text/javascript">
layui.use('table', function(){
  var table = layui.table;
  
  //第一个实例
  table.render({
    elem: '#posTable'
    ,url: '/index/index/posListJson?or_id=<?php echo htmlentities($or_id); ?>' //数据接口
    ,page: false //开启分页
    ,totalRow:true
    ,done:function(res){
var sumTotal=0;//统计金额
layui.each(res.data,function(index,d){
sumTotal+=d.it_number * d.it_price;
})
this.elem.next().find('.layui-table td[data-field=sum] .layui-table-cell:last').text('￥'+sumTotal.toFixed(2))
$("#Money").text(sumTotal.toFixed(2))
$("#hideMoney").val(sumTotal);
}
    ,cols: [[ //表头
      {field: 'id', title: '项', width:60, sort: true, fixed: 'left',totalRowText:'合计'}
      ,{field: 'goodsname', title: '品名', width:160}
      ,{field: 'it_number', title: '数量', width:80, sort: true,totalRow:true,align:'center'}
      ,{field: 'sum', title: '金额', width:100,totalRow:true,templet:function(d){
        return '<span style="color:#FF5722">￥'+(d.it_number*d.it_price).toFixed(2)+'</span>';
                            },align:'center'} 
    ]]
  });
  
});
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
                            $('#skuInput').focus();
                    }
                })
    });

    //优惠券付款
    $('#pay-coupon').click(function(){
        layer.msg('优惠券付款功能暂未启用！',{icon:5,time:1000});
    })

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

    //会员卡付款
    $('#pay-card').click(function(){
        cardno = $('#cardNo').val();
        if(cardno == ""){
            layer.msg("会员卡/身份证号/手机号不能为空！",{icon:5,time:1000});
            return false;
        }
        xadmin.open('会员卡结算','/index/index/payMent.html?or_id=<?php echo htmlentities($or_id); ?>&pay_type=card&cardno='+cardno,300,400);
    })
    //现金付款
    $('#pay-cash').click(function(){
        hideMoney = $('#hideMoney').val();
        cardno = $('#cardNo').val();
        if(hideMoney == 0){
            layer.msg("此订单无产品，请先添加产品！",{icon:5,time:1000});
            return false;
        }
        xadmin.open('现金结算','/index/index/payMent.html?or_id=<?php echo htmlentities($or_id); ?>&pay_type=cash&cardno='+cardno,300,400);
    })
    //微信付款
    $('#pay-wechat').click(function(){
        hideMoney = $('#hideMoney').val();
        if(hideMoney == 0){
            layer.msg("此订单无产品，请先添加产品！",{icon:5,time:1000});
            return false;
        }
        xadmin.open('微信结算','/index/index/payMent.html?or_id=<?php echo htmlentities($or_id); ?>&pay_type=wechat&cardno=',300,400);
    })

    //支付宝付款
    $('#pay-alipay').click(function(){
        hideMoney = $('#hideMoney').val();
        if(hideMoney == 0){
            layer.msg("此订单无产品，请先添加产品！",{icon:5,time:1000});
            return false;
        }
        xadmin.open('支付宝结算','/index/index/payMent.html?or_id=<?php echo htmlentities($or_id); ?>&pay_type=alipay&cardno=',300,400);
    })
 
        //处理扫描枪
        $('#skuInput').bind('keypress',function(event){
            if(event.keyCode == "13"){
                sku =  $('#skuInput').val();
                or_id = '<?php echo htmlentities($or_id); ?>';
                if(sku==""){
                    layer.msg("条码不能为空",{icon:5,time:1000});
                    return false;
                }
            $.ajax({
            url: "<?php echo url('index/index/posItem'); ?>",
            type: 'post',
            dataType: 'json',
            data:{sku:sku,or_id:or_id},
        })
            .done(function(data){
                    if(data.code==0){
                        layer.msg(data.msg,{icon:5,time:500});
                    }else{
                            //lotus.parentTableReload();
                            layui.table.reload('posTable');
                            $('#skuInput').val("");
                            $('#skuInput').focus();
                    }
                })
            }
        });
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
    //删除项目
    function delpositem(id,rote){
        $.ajax({
            url:rote,
            type:'post',
            dataType:'json',
            data:{id:id},
        })
        .done(function(data){
                    if(data.code==0){
                        layer.msg(data.msg,{icon:5,time:500});
                    }else{
                            layui.table.reload('posTable');
                            $('#skuInput').focus();
                    }
                })
    }
    //手动写入POS订单
    function manualToPos(id,or_id,price){
        $.ajax({
            url:'/index/index/manualToPos',
            type:'post',
            dataType:'json',
            data:{id:id,or_id:or_id,price:price},
        })
        .done(function(data){
                    if(data.code==0){
                        layer.msg(data.msg,{icon:5,time:500});
                    }else{
                            layui.table.reload('posTable');
                            $('#skuInput').focus();
                    }
                })
    }
    //读取分类产品
    function openCategory(id,or_id){
        $(this).addClass("layui-bg-orange");
        $.ajax({
            url:'/index/index/openCategory',
            type:'post',
            dataType:'json',
            data:{id:id,or_id:or_id},
        })
        .done(function(data){
                    if(data.code==0){
                        layer.msg(data.msg,{icon:5,time:500});
                    }else{
                        if(data.title!="")
                            $('#goods_pal_title').html(data.title);
                            $('#goods_pal_body').html(data.body);
                            $('#skuInput').focus();
                    }
                })
    }
    //订单挂起事件
    function hangUp(id,route){
        $.ajax({
            url:'/index/index/hangUp',
            type:'post',
            dataType:'json',
            data:{id:id},
        })
        .done(function(data){
                    if(data.code==0){
                        layer.msg(data.msg,{icon:5,time:1000});
                    }else{
                        layer.msg(data.msg,{icon:1,time:1000},function(){
                        location.reload();
                    });
                    }
                })
    }

    //全局键盘事件
  $('*').keydown(function(event){
    switch(event.keyCode){
        case "113"://F2
        alert(event.KeyCode);
        break;
        case "114"://F3
        alert(event.KeyCode);
        break;
        case "115"://F4
        alert(event.KeyCode);
        break;
    }
 }); 

  //处理会员卡读卡
    $('#cardNo').keypress(function(e){
        if(e.KeyCode=="13"){
            $("#readcard").click();
        }
    });
</script>
</body>
<script>
     var is_remember = false;
</script>
<script src="/static/js/addon.js"></script>
</html>