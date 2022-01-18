<?php
// +----------------------------------------------------------------------
// | LotusAdmin
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://www.lotusadmin.top/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: wenhainan <qq 610176732>
// +----------------------------------------------------------------------
use org\HttpClient;
// 应用公共文件
function isMobile()
{
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    if (isset($_SERVER['HTTP_VIA'])) {
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}
/**
 * 子元素计数器
 * @param array $array
 * @param int   $pid
 * @return array
 */
function array_children_count($array, $pid)
{
    $counter = [];
    foreach ($array as $item) {
        $count = isset($counter[$item[$pid]]) ? $counter[$item[$pid]] : 0;
        $count++;
        $counter[$item[$pid]] = $count;
    }
    return $counter;
}
/**
 * 数组层级缩进转换
 * @param array $array 源数组
 * @param int   $pid
 * @param int   $level
 * @return array
 */
function array2level($array, $pid = 0, $level = 1)
{
    static $list = [];
    foreach ($array as $v) {
        if ($v['pid'] == $pid) {
            $v['level'] = $level;
            $list[]     = $v;
            array2level($array, $v['id'], $level + 1);
        }
    }
    return $list;
}
/**
 * 构建层级（树状）数组
 * @param array  $array          要进行处理的一维数组，经过该函数处理后，该数组自动转为树状数组
 * @param string $pid_name       父级ID的字段名
 * @param string $child_key_name 子元素键名
 * @return array|bool
 */
function array2tree(&$array, $pid_name = 'pid', $child_key_name = 'children')
{
    $counter = array_children_count($array, $pid_name);
    if (!isset($counter[0]) || $counter[0] == 0) {
        return $array;
    }
    $tree = [];
    while (isset($counter[0]) && $counter[0] > 0) {
        $temp = array_shift($array);
        if (isset($counter[$temp['id']]) && $counter[$temp['id']] > 0) {
            array_push($array, $temp);
        } else {
            if ($temp[$pid_name] == 0) {
                $tree[] = $temp;
            } else {
                $array = array_child_append($array, $temp[$pid_name], $temp, $child_key_name);
            }
        }
        $counter = array_children_count($array, $pid_name);
    }
    return $tree;
}
/**
 * 把元素插入到对应的父元素$child_key_name字段
 * @param        $parent
 * @param        $pid
 * @param        $child
 * @param string $child_key_name 子元素键名
 * @return mixed
 */
function array_child_append($parent, $pid, $child, $child_key_name)
{
    foreach ($parent as &$item) {
        if ($item['id'] == $pid) {
            if (!isset($item[$child_key_name])) {
                $item[$child_key_name] = [];
            }

            $item[$child_key_name][] = $child;
        }
    }
    return $parent;
}
/**
 * 手机号格式检查
 * @param string $mobile
 * @return bool
 */
function check_mobile_number($mobile)
{
    if (!is_numeric($mobile)) {
        return false;
    }
    $reg = '#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#';

    return preg_match($reg, $mobile) ? true : false;
}
//获取客户端真实IP
function getClientIP()
{
    global $ip;
    if (getenv("HTTP_CLIENT_IP")) {
        $ip = getenv("HTTP_CLIENT_IP");
    } else if (getenv("HTTP_X_FORWARDED_FOR")) {
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    } else if (getenv("REMOTE_ADDR")) {
        $ip = getenv("REMOTE_ADDR");
    } else {
        $ip = "Unknow";
    }

    return $ip;
}
/**
 * 获取 IP  地理位置
 * 淘宝IP接口
 * @Return: array
 */
function getCity($ip = '')
{
    if($ip == ''){
        $url = "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json";
        $ip=json_decode(file_get_contents($url),true);
        $data = $ip;
    }else{
        $url="http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
        $ip=json_decode(file_get_contents($url));
        if((string)$ip->code=='1'){
            return false;
        }
        $data = (array)$ip->data;
    }

    return $data;
}
/**
 * 判断 LotusAdmin 核心是否安装
 * @return bool
 */
function lotus_is_installed()
{
    static $lotusIsInstalled;
    if (empty($lotusIsInstalled)) {
        $lotusIsInstalled = file_exists(LOTUS_ROOT.'data/install.lock');
    }
    return $lotusIsInstalled;
}
/**
 * 切分SQL文件成多个可以单独执行的sql语句
 * @param $file sql文件路径
 * @param $tablePre 表前缀
 * @param string $charset 字符集
 * @param string $defaultTablePre 默认表前缀
 * @param string $defaultCharset 默认字符集
 * @return array
 */
function lotus_split_sql($file, $tablePre, $charset = 'utf8mb4', $defaultTablePre = 'lotus_', $defaultCharset = 'utf8mb4')
{
    if (file_exists($file)) {
        //读取SQL文件
        $sql = file_get_contents($file);
        $sql = str_replace("\r", "\n", $sql);
        $sql = str_replace("BEGIN;\n", '', $sql);//兼容 navicat 导出的 insert 语句
        $sql = str_replace("COMMIT;\n", '', $sql);//兼容 navicat 导出的 insert 语句
        $sql = str_replace($defaultCharset, $charset, $sql);
        $sql = trim($sql);
        //替换表前缀
        $sql  = str_replace(" `{$defaultTablePre}", " `{$tablePre}", $sql);
        $sqls = explode(";\n", $sql);
        return $sqls;
    }

    return [];
}
/**
 * 随机字符串生成
 * @param int $len 生成的字符串长度
 * @return string
 */
function lotus_random_string($len = 6)
{
    $chars    = [
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
        "3", "4", "5", "6", "7", "8", "9"
    ];
    $charsLen = count($chars) - 1;
    shuffle($chars);    // 将数组打乱
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}
/**
 * 获取网站根目录
 * @return string 网站根目录
 */
function lotus_get_root()
{
    $request = new \think\Request();
    $root    = $request->root();
    $root    = str_replace('/index.php', '', $root);
    if (defined('APP_NAMESPACE') && APP_NAMESPACE == 'api') {
        $root = preg_replace('/\/api$/', '', $root);
        $root = rtrim($root, '/');
    }

    return $root;
}

/**
 * 设置系统配置，通用
 * @param string $key 配置键值,都小写
 * @param array $data 配置值，数组
 * @param bool $replace 是否完全替换
 * @return bool 是否成功
 */
function lotus_set_option($key, $data, $replace = false)
{
    if (!is_array($data) || empty($data) || !is_string($key) || empty($key)) {
        return false;
    }

    $key        = strtolower($key);
    $option     = [];
    $findOption = \think\Db::name('option')->where('option_name', $key)->find();
    if ($findOption) {
        if (!$replace) {
            $oldOptionValue = json_decode($findOption['option_value'], true);
            if (!empty($oldOptionValue)) {
                $data = array_merge($oldOptionValue, $data);
            }
        }

        $option['option_value'] = json_encode($data);
        \think\Db::name('option')->where('option_name', $key)->update($option);
        \think\Db::name('option')->getLastSql();
    } else {
        $option['option_name']  = $key;
        $option['option_value'] = json_encode($data);
        \think\Db::name('option')->insert($option);
    }
    cache('cmf_options_' . $key, null);//删除缓存
    return true;
}

/**
 * 格式化字节大小
 *
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}
//异常字符串处理
function checkStr($str){
    switch ($str) {
        case strspn($str,'&#;')>0:
            $str =  ltrim($str,'&#');
            $str =  rtrim($str,';');
            return $str;
        default:
            return $str;
            break;
    }
}

//get请求测试
function doGet($url)
{
    //初始化
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    // 执行后不直接打印出来
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    // 跳过证书检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // 不从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //执行并获取HTML文档内容
    $output = curl_exec($ch);
    //释放curl句柄
    curl_close($ch);
    return  $output;
}
//post请求测试
function doPost($url,$post_data,$header)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    // 执行后不直接打印出来
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // 设置请求方式为post
    curl_setopt($ch, CURLOPT_POST, true);
    // post的变量
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    // 请求头，可以传数组
    curl_setopt($ch, CURLOPT_HEADER, $header);
    // 跳过证书检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // 不从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    try {
        $output = curl_exec($ch);
    } catch (Exception $e) {
        echo $e->getMessage();
    }


    curl_close($ch);
    return $output;
}

function array_merge_recursive_simple() {

    if (func_num_args() < 2) {
        trigger_error(__FUNCTION__ .' needs two or more array arguments', E_USER_WARNING);
        return;
    }
    $arrays = func_get_args();
    $merged = array();
    while ($arrays) {
        $array = array_shift($arrays);
        if (!is_array($array)) {
            trigger_error(__FUNCTION__ .' encountered a non array argument', E_USER_WARNING);
            return;
        }
        if (!$array)
            continue;
        foreach ($array as $key => $value)
            if (is_string($key))
                if (is_array($value) && array_key_exists($key, $merged) && is_array($merged[$key]))
                    $merged[$key] = call_user_func(__FUNCTION__, $merged[$key], $value);
                else
                    $merged[$key] = $value;
            else
                $merged[] = $value;
    }
    return $merged;
}
/**
 * 验证码检查，验证完后销毁验证码增加安全性 ,<br>返回true验证码正确，false验证码错误
 * @return boolean <br>true：验证码正确，false：验证码错误
 */
function check_verify_code($verify){
    $verify = new \think\Verify();
    return $verify->check($verify, "");
}

/**
 * 取汉字的第一个字的首字母
 * @param string $str
 * @return string|null
 */
function getFirstChar($str) {
    if (empty($str)) {
        return '';
    }
 
    $fir = $fchar = ord($str[0]);
    if ($fchar >= ord('A') && $fchar <= ord('z')) {
        return strtoupper($str[0]);
    }
 
    $s1 = @iconv('UTF-8', 'gb2312//IGNORE', $str);
    $s2 = @iconv('gb2312', 'UTF-8', $s1);
    $s = $s2 == $str ? $s1 : $str;
    if (!isset($s[0]) || !isset($s[1])) {
        return '';
    }
 
    $asc = ord($s[0]) * 256 + ord($s[1]) - 65536;
 
    if (is_numeric($str)) {
        return $str;
    }
 
    if (($asc >= -20319 && $asc <= -20284) || $fir == 'A') {
        return 'A';
    }
    if (($asc >= -20283 && $asc <= -19776) || $fir == 'B') {
        return 'B';
    }
    if (($asc >= -19775 && $asc <= -19219) || $fir == 'C') {
        return 'C';
    }
    if (($asc >= -19218 && $asc <= -18711) || $fir == 'D') {
        return 'D';
    }
    if (($asc >= -18710 && $asc <= -18527) || $fir == 'E') {
        return 'E';
    }
    if (($asc >= -18526 && $asc <= -18240) || $fir == 'F') {
        return 'F';
    }
    if (($asc >= -18239 && $asc <= -17923) || $fir == 'G') {
        return 'G';
    }
    if (($asc >= -17922 && $asc <= -17418) || $fir == 'H') {
        return 'H';
    }
    if (($asc >= -17417 && $asc <= -16475) || $fir == 'J') {
        return 'J';
    }
    if (($asc >= -16474 && $asc <= -16213) || $fir == 'K') {
        return 'K';
    }
    if (($asc >= -16212 && $asc <= -15641) || $fir == 'L') {
        return 'L';
    }
    if (($asc >= -15640 && $asc <= -15166) || $fir == 'M') {
        return 'M';
    }
    if (($asc >= -15165 && $asc <= -14923) || $fir == 'N') {
        return 'N';
    }
    if (($asc >= -14922 && $asc <= -14915) || $fir == 'O') {
        return 'O';
    }
    if (($asc >= -14914 && $asc <= -14631) || $fir == 'P') {
        return 'P';
    }
    if (($asc >= -14630 && $asc <= -14150) || $fir == 'Q') {
        return 'Q';
    }
    if (($asc >= -14149 && $asc <= -14091) || $fir == 'R') {
        return 'R';
    }
    if (($asc >= -14090 && $asc <= -13319) || $fir == 'S') {
        return 'S';
    }
    if (($asc >= -13318 && $asc <= -12839) || $fir == 'T') {
        return 'T';
    }
    if (($asc >= -12838 && $asc <= -12557) || $fir == 'W') {
        return 'W';
    }
    if (($asc >= -12556 && $asc <= -11848) || $fir == 'X') {
        return 'X';
    }
    if (($asc >= -11847 && $asc <= -11056) || $fir == 'Y') {
        return 'Y';
    }
    if (($asc >= -11055 && $asc <= -10247) || $fir == 'Z') {
        return 'Z';
    }
 
    return '';
}

//获取整条字符串所有汉字拼音首字母
    function pinyin_long($zh){
        $ret = "";
        $s1 = iconv("UTF-8","GBK//IGNORE", $zh);
        $s2 = iconv("GBK","UTF-8", $s1);
        if($s2 == $zh){$zh = $s1;}
        for($i = 0; $i < strlen($zh); $i++){
            $s1 = substr($zh,$i,1);
            $p = ord($s1);
            if($p > 160){
                $s2 = substr($zh,$i++,2);
                $ret .= getFirstChar($s2);
            }else{
                $ret .= $s1;
            }
        }
        return $ret;
    }

    /**
 * 格式化月份
 * @param string $time
 * @param int $ceil
 * @return array
 */
function getMonth($time='',$ceil=0){
    if(empty($time)){
        $firstday = date("Y-m-01",time());
        $lastday = date("Y-m-d",strtotime("$firstday +1 month -1 day"));
    }else if($time=='n'){
        if($ceil!=0)
            $season = ceil(date('n') /3)-$ceil;
        else
            $season = ceil(date('n') /3);
        $firstday=date('Y-m-01',mktime(0,0,0,($season - 1) *3 +1,1,date('Y')));
        $lastday=date('Y-m-t',mktime(0,0,0,$season * 3,1,date('Y')));
    }else if($time=='y'){
        $firstday=date('Y-01-01');
        $lastday=date('Y-12-31');
    }else if($time=='h'){
        $firstday = date('Y-m-d', strtotime('this week +'.$ceil.' day')) . ' 00:00:00';
        $lastday = date('Y-m-d', strtotime('this week +'.($ceil+1).' day')) . ' 23:59:59';
    }
    return array($firstday,$lastday);
}


  define('USER', '12612019@qq.com');  //*必填*：飞鹅云后台注册账号
  define('UKEY', '3GbJjWpdBbmfbEpB');  //*必填*: 飞鹅云后台注册账号后生成的UKEY 【备注：这不是填打印机的KEY】
  define('PRINT_SERVER','https://api.feieyun.cn/Api/Open/');//飞鹅API服务器
  define('IP','api.feieyun.cn');
  define('PATH','/Api/Open/');
  define('API_SERVER','http://www.viooma.com/plugin/');
  /**
   * [批量添加打印机接口 Open_printerAddlist]
   * @param  [string] $printerContent [打印机的sn#key]
   * @return [string]                 [接口返回值]
   */
  function printerAddlist($printerContent){
    $time = time();         //请求时间
    $msgInfo = array(
      'user'=>USER,
      'stime'=>$time,
      'sig'=>signature($time),
      'apiname'=>'Open_printerAddlist',
      'printerContent'=>$printerContent
    );
    
      $result = doPost(PRINT_SERVER,$msgInfo,'application/x-www-form-urlencoded');
      return $result;
  }


  /**
   * [打印订单接口 Open_printMsg]
   * @param  [string] $sn      [打印机编号sn]
   * @param  [string] $content [打印内容]
   * @param  [string] $times   [打印联数]
   * @return [string]          [接口返回值]
   */
  function printMsg($sn,$content,$times){
    $time = time();         //请求时间
    $msgInfo = array(
      'user'=>USER,
      'stime'=>$time,
      'sig'=>signature($time),
      'apiname'=>'Open_printMsg',
      'sn'=>$sn,
      'content'=>$content,
      'times'=>$times//打印次数
    );
      //服务器返回的JSON字符串，建议要当做日志记录起来
      $result = doPost(PRINT_SERVER,$msgInfo,'application/x-www-form-urlencoded');
      return $result;
  }

  /**
   * [标签机打印订单接口 Open_printLabelMsg]
   * @param  [string] $sn      [打印机编号sn]
   * @param  [string] $content [打印内容]
   * @param  [string] $times   [打印联数]
   * @return [string]          [接口返回值]
   */
  function printLabelMsg($sn,$content,$times){
    $time = time();         //请求时间
    $msgInfo = array(
      'user'=>USER,
      'stime'=>$time,
      'sig'=>signature($time),
      'apiname'=>'Open_printLabelMsg',
      'sn'=>$sn,
      'content'=>$content,
      'times'=>$times//打印次数
    );
      //服务器返回的JSON字符串，建议要当做日志记录起来
      $result = doPost(PRINT_SERVER,$msgInfo,'application/x-www-form-urlencoded');
      echo $result;
  }

  /**
   * [批量删除打印机 Open_printerDelList]
   * @param  [string] $snlist [打印机编号，多台打印机请用减号“-”连接起来]
   * @return [string]         [接口返回值]
   */
  function printerDelList($snlist){
    $time = time();         //请求时间
    $msgInfo = array(
      'user'=>USER,
      'stime'=>$time,
      'sig'=>signature($time),
      'apiname'=>'Open_printerDelList',
      'snlist'=>$snlist
    );
      $result = doPost(PRINT_SERVER,$msgInfo,'application/x-www-form-urlencoded');
      echo $result;
  }

  /**
   * [修改打印机信息接口 Open_printerEdit]
   * @param  [string] $sn       [打印机编号]
   * @param  [string] $name     [打印机备注名称]
   * @param  [string] $phonenum [打印机流量卡号码,可以不传参,但是不能为空字符串]
   * @return [string]           [接口返回值]
   */
  function printerEdit($sn,$name,$phonenum){
    $time = time();         //请求时间
    $msgInfo = array(
      'user'=>USER,
      'stime'=>$time,
      'sig'=>signature($time),
      'apiname'=>'Open_printerEdit',
      'sn'=>$sn,
      'name'=>$name,
      'phonenum'=>$phonenum
    );
      $result = doPost(PRINT_SERVER,$msgInfo,'application/x-www-form-urlencoded');
      echo $result;
  }


  /**
   * [清空待打印订单接口 Open_delPrinterSqs]
   * @param  [string] $sn [打印机编号]
   * @return [string]     [接口返回值]
   */
  function delPrinterSqs($sn){
    $time = time();         //请求时间
    $msgInfo = array(
      'user'=>USER,
      'stime'=>$time,
      'sig'=>signature($time),
      'apiname'=>'Open_delPrinterSqs',
      'sn'=>$sn
    );
      $result = doPost(PRINT_SERVER,$msgInfo,'application/x-www-form-urlencoded');
      echo $result;
  }

  /**
   * [查询订单是否打印成功接口 Open_queryOrderState]
   * @param  [string] $orderid [调用打印机接口成功后,服务器返回的JSON中的编号 例如：123456789_20190919163739_95385649]
   * @return [string]          [接口返回值]
   */
  function queryOrderState($orderid){
    $time = time();         //请求时间
    $msgInfo = array(
      'user'=>USER,
      'stime'=>$time,
      'sig'=>signature($time),
      'apiname'=>'Open_queryOrderState',
      'orderid'=>$orderid
    );
      $result = doPost(PRINT_SERVER,$msgInfo,'application/x-www-form-urlencoded');
      echo $result;
  }

  /**
   * [查询指定打印机某天的订单统计数接口 Open_queryOrderInfoByDate]
   * @param  [string] $sn   [打印机的编号]
   * @param  [string] $date [查询日期，格式YY-MM-DD，如：2019-09-20]
   * @return [string]       [接口返回值]
   */
  function queryOrderInfoByDate($sn,$date){
    $time = time();         //请求时间
    $msgInfo = array(
      'user'=>USER,
      'stime'=>$time,
      'sig'=>signature($time),
      'apiname'=>'Open_queryOrderInfoByDate',
      'sn'=>$sn,
      'date'=>$date
    );
      $result = doPost(PRINT_SERVER,$msgInfo,'application/x-www-form-urlencoded');
      echo $result;
  }

  /**
   * [获取某台打印机状态接口 Open_queryPrinterStatus]
   * @param  [string] $sn [打印机编号]
   * @return [string]     [接口返回值]
   */
  function queryPrinterStatus($sn){
    $time = time();         //请求时间
    $msgInfo = array(
      'user'=>USER,
      'stime'=>$time,
      'sig'=>signature($time),
      'apiname'=>'Open_queryPrinterStatus',
      'sn'=>$sn
    );
      $result = doPost(PRINT_SERVER,$msgInfo,'application/x-www-form-urlencoded');
      echo $result;
  }

  /**
   * [signature 生成签名]
   * @param  [string] $time [当前UNIX时间戳，10位，精确到秒]
   * @return [string]       [接口返回值]
   */
  function signature($time){
    return sha1(USER.UKEY.$time);//公共参数，请求公钥
  }

  /*打印内容排版*/
  function print_setting($arr,$A,$B,$C,$D)
    {
        $orderInfo = '';
      foreach ($arr as $k5 => $v5) {
        $name = isset($v5['goodsname'])?$v5['goodsname']:'';
        $price = isset($v5['it_price'])?$v5['it_price']:'0';
        $num = isset($v5['it_number'])?$v5['it_number']:'1';
        $prices = round($price*$num,2);
        $kw3 = '';
        $kw1 = '';
        $kw2 = '';
        $kw4 = '';
        $tail = '';
        $str = $name;
        $blankNum = $A;//名称控制为14个字节
        $lan = mb_strlen($str,'utf-8');
        $m = 0;
        $j=1;
        $blankNum++;
        $result = array();
        if(strlen($price) < $B){
              $k1 = $B - strlen($price);
              for($q=0;$q<$k1;$q++){
                    $kw1 .= ' ';
              }
              $price = $price.$kw1;
        }
        if(strlen($num) < $C){
              $k2 = $C - strlen($num);
              for($q=0;$q<$k2;$q++){
                    $kw2 .= ' ';
              }
              $num = $num.$kw2;
        }
        if(strlen($prices) < $D){
              $k3 = $D - strlen($prices);
              for($q=0;$q<$k3;$q++){
                    $kw4 .= ' ';
              }
              $prices = $prices.$kw4;
        }
        for ($i=0;$i<$lan;$i++){
          $new = mb_substr($str,$m,$j,'utf-8');
          $j++;
          if(mb_strwidth($new,'utf-8')<$blankNum) {
            if($m+$j>$lan) {
              $m = $m+$j;
              $tail = $new;
              $lenght = iconv("UTF-8", "GBK//IGNORE", $new);
              $k = $A - strlen($lenght);
              for($q=0;$q<$k;$q++){
                $kw3 .= ' ';
              }
              if($m==$j){
                $tail .= $kw3.' '.$price.' '.$num.' '.$prices;
              }else{
                $tail .= $kw3.'<BR>';
              }
              break;
            }else{
              $next_new = mb_substr($str,$m,$j,'utf-8');
              if(mb_strwidth($next_new,'utf-8')<$blankNum) continue;
              else{
                $m = $i+1;
                $result[] = $new;
                $j=1;
              }
            }
          }
        }
        $head = '';
        
        foreach ($result as $key=>$value) {
          if($key < 1){
            $v_lenght = iconv("UTF-8", "GBK//IGNORE", $value);
            $v_lenght = strlen($v_lenght);
            if($v_lenght == 13) $value = $value." ";
            $head .= $value.' '.$price.' '.$num.' '.$prices;
          }else{
            $head .= $value.'<BR>';
          } 
        }
        $orderInfo .= $head.$tail;
        @$nums += $prices;
      }
      return $orderInfo;
    }
/**
 * 根据数组指定键名排序数组
 * @param $array array  被排序数组
 * @param $key_name string 数组键名
 * @param $sort   string  desc|asc  升序或者降序
 * @return array 返回排序后的数组
 * 例子:传入数组 $array=array(array('id'=>1,'sort'=>20),array('id'=>2,'sort'=>10),array('id'=>3,'sort'=>30));
 * gw_sort($array,'sort','asc');
 * 伪代码结果: sort:10,sort:20,sort:30
 */
 function gw_sort($array,$key_name,$sort){
    $key_name_array = array();//保存被排序数组键名
    foreach($array as $key=>$val){
        $key_name_array[] = $val[$key_name];
    }
    if($sort=="desc"){
        rsort($key_name_array);
    }else if($sort=="asc"){
        sort($key_name_array);
    }
     $key_name_array = array_flip($key_name_array);//反转键名和值得到数组排序后的位置
    $result = array();
    foreach($array as $k=>$v){
        $this_key_name_value = $v[$key_name];//当前数组键名值依次是20,10,30
        $save_position = $key_name_array[$this_key_name_value];//获取20,10,30排序后存储位置
        $result[$save_position] = $v;//当前项存储到数组指定位置
    }
    ksort($result);
 
    return $result;
}

?>
