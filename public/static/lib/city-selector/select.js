layui.use('form', function() {
	var form = layui.form;
		
	var province = $("#province"),
		city = $("#city"),
		area = $("#area");
	
	//初始将省份数据赋予
	for (var i = 0; i < provinceList.length; i++) {
		addEle(province, provinceList[i].name);
	}
	
	//赋予完成 重新渲染select
	form.render('select');
	
	//向select中 追加内容
	function addEle(ele, value) {
		var optionStr = "";
		optionStr = "<option value=" + value + " >" + value + "</option>";
		ele.append(optionStr);
	}

	//移除select中所有项 赋予初始值
	function removeEle(ele) {
		ele.find("option").remove();
		var optionStar = "<option value=" + "0" + ">" + "请选择" + "</option>";
		ele.append(optionStar);
	}

	var provinceText,
		cityText,
		cityItem;
	
	//选定省份后 将该省份的数据读取追加上
	form.on('select(province)', function(data) {
		provinceText = data.value;
		$.each(provinceList, function(i, item) {
			if (provinceText == item.name) {
				cityItem = i;
				return cityItem;
			}
		});
		removeEle(city);
		removeEle(area);
		$.each(provinceList[cityItem].cityList, function(i, item) {
			addEle(city, item.name);
		})
		//重新渲染select 
		form.render('select');
	})

	////选定市或直辖县后 将对应的数据读取追加上
	form.on('select(city)', function(data) {
		cityText = data.value;
		removeEle(area);
		$.each(provinceList, function(i, item) {
			if (provinceText == item.name) {
				cityItem = i;
				return cityItem;
			}
		});
		$.each(provinceList[cityItem].cityList, function(i, item) {
			if (cityText == item.name) {
				for (var n = 0; n < item.areaList.length; n++) {
					addEle(area, item.areaList[n]);
				}
			}
		})
		//重新渲染select 
		form.render('select');
	})



})