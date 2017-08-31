//Toast提示框函数
function Toast(msg, duration) {
    duration = isNaN(duration) ? 3000 : duration;
    var m = document.createElement('div');
    m.innerHTML = msg;
    m.style.cssText = "width: 60%;min-width: 150px;opacity: 0.7;height: 30px;color: rgb(255, 0, 0);line-height: 30px;text-align: center;border-radius: 15px;position: fixed;top: 40%;left: 20%;z-index: 999999;background: rgb(0, 0, 0);font-weight:blod;font-size:16px;";
    document.body.appendChild(m);
    setTimeout(function () {
        var d = 0.8;
        m.style.webkitTransition = '-webkit-transition ' + d + 's ease-in, opacity ' + d + 's ease-in';
        m.style.opacity = '0';
        setTimeout(function () {
            document.body.removeChild(m)
        }, d * 1000);
    }, duration);
}

//考试倒计时函数
function examCountdown(time, spanid, callback) {
    window.cd_time = time; //定义全局变量
    window.used_time = 0;  //答题所用时间
    window.cflag = false;  //已经执行回调函数的标记
    setInterval(function () {
        var hour = parseInt(cd_time / 60 / 60 % 24);
        hour = hour < 10 ? "0" + hour : hour;
        var minute = parseInt(cd_time / 60 % 60);
        minute = minute < 10 ? "0" + minute : minute;
        var seconds = parseInt(cd_time % 60);
        seconds = seconds < 10 ? "0" + seconds : seconds;
        $("#"+spanid).html(hour + ":" + minute + ":" + seconds);
        cd_time--;
        used_time++;
        cd_time = cd_time < 0 ? 0 : cd_time;
        if(cd_time == 0 && !cflag){  //倒计时到达0，执行回调函数
            callback();
            cflag = true;  //标记回调函数为已经执行状态，防止回调函数多次执行
        }
    }, 1000);
}
