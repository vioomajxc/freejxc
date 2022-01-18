$(".top-refresh").bind('click',function () {
    $(".layui-show").find("iframe")[0].contentWindow.location.reload();
})
$('#fullscreen').bind('click',function () {
    if (document.fullscreenElement) {
        document.exitFullscreen();
    }
    document.body.requestFullscreen();
})