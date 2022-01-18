    var lotus = function () {
        this.v = '1.0';
    }
    function getUrlParam(name) {
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
            var r = window.location.search.substr(1).match(reg);  //匹配目标参数
            if (r != null) return unescape(r[2]); return null; //返回参数值
        }
    lotus.addForm = function () {
        layui.use(['form', 'layer','jquery'], function () {
            var form = layui.form;
            var layer = layui.layer;
            var $ = layui.jquery;
            //监听提交
            var options = {
                type: 'post',           //post提交
                dataType: "json",        //json格式
                data: {},    //如果需要提交附加参数，视情况添加
                clearForm: false,        //成功提交后，清除所有表单元素的值
                resetForm: false,        //成功提交后，重置所有表单元素的值
                cache: false,
                async: false,          //同步返回
                success: function (data) {
                    if (data.code == 0) {
                        layer.msg(data.msg);
                    }
                 
                    if(data.code==1) {
                        layer.msg(data.msg, {icon: 1, time: 500}, function () {
                            lotus.reload();
                        });
                    }

                    if(data.code==2){
                        layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                            location.reload();
                        });
                    }
                    //服务器端返回处理逻辑
                },
                error: function (XmlHttpRequest, textStatus, errorThrown) {
                    layer.msg('操作失败:服务器处理失败（添加）');
                }
            };
            // bind form using 'ajaxForm'
            $('#lotus-add-form').ajaxForm(options).submit(function (data) {
                //无逻辑
            });

        })
    }
    lotus.editForm = function (lotusFormVal) {
        layui.use(['table','form', 'layer','jquery'], function () {
            var form  = layui.form;
            var layer = layui.layer;
            var $ = layui.jquery;
            form.val("lotus-form-filter", lotusFormVal);
            //监听提交
            var options = {
                type: 'post',           //post提交
                dataType: "json",        //json格式
                data: {},    //如果需要提交附加参数，视情况添加
                clearForm: false,        //成功提交后，清除所有表单元素的值
                resetForm: false,        //成功提交后，重置所有表单元素的值
                cache: false,
                async: false,          //同步返回
                success: function (data) {
                    console.log(data);
                    if (data.code == 0) {
                        layer.msg(data.msg);
                    }
                    if(data.code==1){
                        layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                                lotus.parentTableReload();
                        });
                    }
                    //在顶级iframe操作
                    if(data.code==2){
                        layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                            parent.location.href = data.url;
                        });
                    }
                    //服务器端返回处理逻辑
                },
                error: function (XmlHttpRequest, textStatus, errorThrown) {
                    layer.msg('操作失败:服务器处理失败（编辑）');
                }
            };
            // bind form using 'ajaxForm'
            $('#lotus-edit-form').ajaxForm(options).submit(function (data) {
                //无逻辑
            });

        })
    }
    lotus.editSingleForm = function (lotusFormVal) {
        layui.use(['table','form', 'layer','jquery'], function () {
            var form  = layui.form;
            var layer = layui.layer;
            var $ = layui.jquery;
            form.val("lotus-form-filter", lotusFormVal);
            //监听提交
            var options = {
                type: 'post',           //post提交
                dataType: "json",        //json格式
                data: {},    //如果需要提交附加参数，视情况添加
                clearForm: false,        //成功提交后，清除所有表单元素的值
                resetForm: false,        //成功提交后，重置所有表单元素的值
                cache: false,
                async: false,          //同步返回
                success: function (data) {
                    console.log(data);
                    if (data.code == 0) {
                        layer.msg(data.msg);
                    }
                    if(data.code==1){
                        layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                            location.reload();
                            //layui.table.reload('lotus-table');
                        });
                    }
                    //在顶级iframe操作
                    if(data.code==2){
                        layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                            parent.location.href = data.url;
                        });
                    }
                },
                error: function (XmlHttpRequest, textStatus, errorThrown) {
                    layer.msg('操作失败:服务器处理失败（单个）');
                }
            };
            // bind form using 'ajaxForm'
            $('#lotus-sigle-edit-form').ajaxForm(options).submit(function (data) {
                //无逻辑
            });

        })
    }
    //刷新
    lotus.reload = function () {
        var index = parent.layer.getFrameIndex(window.name);
        parent.layer.close(index);
        parent.location.reload();
    }
    lotus.table = function (where = null) {
        layui.use(['laydate','form','table'], function(){
            var table = layui.table;
            var laydate = layui.laydate;
            var  form = layui.form;
            var laypage = layui.laypage;

            // 监听全选
            form.on('checkbox(checkall)', function(data){

                if(data.elem.checked){
                    $('tbody input').prop('checked',true);
                }else{
                    $('tbody input').prop('checked',false);
                }
                form.render('checkbox');
            });
            // 监听switch
            

            //执行一个laydate实例
            laydate.render({
                elem: '#start', //指定元素
　　            trigger: 'click'
            });

            //执行一个laydate实例
            laydate.render({
                elem: '#end', //指定元素
                trigger: 'click'
            });

            //监听单元格编辑
            table.on('edit(lotus-table)', function(obj) {
                    var value = obj.value //得到修改后的值
                        ,data = obj.data //得到所在行所有键值
                        ,field = obj.field; //得到字段
                    //layer.msg('[ID: ' + data.id + '] ' + field + ' 字段更改为：' + value);
                    $.ajax({
                url: '/admin/pub/changeItemValue',
                type: 'post',
                dataType: 'json',
                data: {id:data.id,val:value,field:field},
            })
                .done(function(data){
                    if(data.code==0){
                        layer.msg(data.msg,{icon:5,time:500});
                    }else{
                        layer.msg(data.msg,{icon:1,time:500},function(){
                            //lotus.parentTableReload();
                            layui.table.reload('lotus-table');
                        })
                    }
                })

            });

            //头工具栏事件
            table.on('toolbar(lotus-table)', function(obj) {
                console.log(obj);
                    var checkStatus = table.checkStatus(obj.config.id);
                    switch (obj.event) {
                        case 'addBatchGoods':
                            var data = checkStatus.data;
                            var or_id = getUrlParam('or_id');
                            var ordertype = getUrlParam('ordertype');
                            $.ajax({
                                url:'/admin/pub/selectBatchGoods?or_id='+or_id+'&ordertype='+ordertype,
                                type:'post',
                                dataType:'json',
                                data:{data:data},
                            })
                            .done(function(data){
                    if(data.code==0){
                        layer.msg(data.msg,{icon:5,time:1500});
                    }else{
                        layer.msg(data.msg,{icon:1,time:1500},function(){
                            lotus.parentTableReload();
                            //layui.table.reload('lotus-table');
                        })
                    }
                })
                            //layer.alert(JSON.stringify(data));
                            break;
                        case 'getCheckData':
                            var data = checkStatus.data;
                            var or_id = getUrlParam('or_id');
                            $.ajax({
                                url:'/admin/storage/selectBatchGoods?or_id='+or_id,
                                type:'post',
                                dataType:'json',
                                data:{data:data},
                            })
                            .done(function(data){
                    if(data.code==0){
                        layer.msg(data.msg,{icon:5,time:1500});
                    }else{
                        layer.msg(data.msg,{icon:1,time:1500},function(){
                            lotus.parentTableReload();
                            //layui.table.reload('lotus-table');
                        })
                    }
                })
                            //layer.alert(JSON.stringify(data));
                            break;
                            case 'getCheckPay':
                            var data = checkStatus.data;
                            //layer.alert(JSON.stringify(data));
                            layer.confirm('确定要为选择的订单组合付款吗?',{btn: ['确定','取消']}, function(index){
                            $.ajax({
                                url:'/admin/financial/unionPay/',
                                type:'post',
                                dataType:'json',
                                data:{data:data},
                            })
                            .done(function(data){
                                if(data.code == 0)
                                layer.msg(data.msg,{icon:5,time:1500});
                            else{
                                layer.msg(data.msg,{icon:1,time:1500},function(){
                                    layui.table.reload('lotus-table');
                                })
                            }
                            })
                            layer.close(index);
                        })
                            break;
                            case 'getCheckCollection':
                            var data = checkStatus.data;
                            layer.confirm('确定要为选择的订单组合收款吗?',{btn: ['确定','取消']}, function(index){
                            $.ajax({
                                url:'/admin/financial/unionCollection/',
                                type:'post',
                                dataType:'json',
                                data:{data:data},
                            })
                            .done(function(data){
                                if(data.code == 0)
                                layer.msg(data.msg,{icon:5,time:1500});
                            else{
                                layer.msg(data.msg,{icon:1,time:1500},function(){
                                    layui.table.reload('lotus-table');
                                })
                            }
                            })
                            layer.close(index);
                        })
                            break;
                        case 'getCheckLength':
                            var data = checkStatus.data;
                            layer.msg('选中了：' + data.length + ' 个');
                            break;
                        case 'isAll':
                            layer.msg(checkStatus.isAll ? '全选': '未全选');
                            break;
                    };
                });

            //执行一个laypage实例
            laypage.render({
                elem: 'layui-table-page1' //注意，这里的 test1 是 ID，不用加 # 号
                ,count: 50 //数据总数，从服务端得到
                ,hash:true
            });

            if(where!==null){
                table.reload('lotus-table',{
                    where:where
                })
            }

        });
    }

    //父级table重载
    lotus.parentTableReload = function () {
        parent.layui.table.reload('lotus-table');
        //关闭弹层
        var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
        parent.layer.close(index); //再执行关闭
    }

    lotus.del = function (id,or_id,route) {
        layer.confirm('确定要删除吗?',{
            btn: ['确定','取消'] //按钮
        }, function(index){
            $.ajax({
                url: route,
                type: 'post',
                dataType: 'json',
                data: {id:id,or_id:or_id},
            })
                .done(function(data){
                    if(data.code==0){
                        layer.msg(data.msg,{icon:5,time:500});
                    }else{
                        layer.msg(data.msg,{icon:1,time:500},function(){
                            layui.table.reload('lotus-table');
                        })
                    }
                })
                layer.close(index);
        });
    }

    lotus.selectSingle = function (id,or_id,route) {
            $.ajax({
                url: route,
                type: 'post',
                dataType: 'json',
                data: {id:id,or_id:or_id},
            })
                .done(function(data){
                    if(data.code==0){
                        layer.msg(data.msg,{icon:5,time:500});
                    }else{
                        layer.msg(data.msg,{icon:1,time:500},function(){
                            //lotus.parentTableReload();
                            parent.layui.table.reload('lotus-table');
                        })
                    }
                })
    }

//审核订单
    lotus.verifyOrder = function (id,route) {
        layer.confirm('确定要审核/完成此单据吗，这会触发库存或财务的变动?',{
            btn: ['确定','取消'] //按钮
        }, function(){
            $.ajax({
                url: route,
                type: 'post',
                dataType: 'json',
                data: {id:id},
            })
                .done(function(data){
                    if(data.code==0){
                        layer.msg(data.msg,{icon:5,time:1000});
                    }else{
                        layer.msg(data.msg,{icon:1,time:1000},function(){
                            //lotus.parentTableReload();
                            layui.table.reload('lotus-table');
                        })
                    }
                })
        });
    }

    //智能加载CURD对象
    if($("#lotus-table")){
        lotus.table();
    }
    if($("#lotus-add-form")){
        lotus.addForm();
    }
    if($("#lotus-edit-form")){
        lotus.editForm();
    }
    if($("#lotus-sigel-edit-form")){
        lotus.editSingleForm();
    }





    




