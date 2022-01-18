<?php
/**
 * 支付宝当面付服务
 * @author Jena
 */
namespace AlipayF2F;

require_once "model/builder/AlipayTradePayContentBuilder.php";
require_once "service/AlipayTradeService.php";

class Alipay
{

    /**
     * 条形码当面付
     *
     * @param mixed $sn 自定义订单编号          
     * @param number $auth_code 用户支付授权码<即当面付条形码>
     * @param string $subject 订单标题
     * @param string $body 订单正文
     * @param number $total_amount 支付金额
     * @return NULL[]
     */
    public static function doBarCodeTrade($config,$sn, $auth_code, $subject, $body, $total_amount)
    {
        $barPayRequestBuilder = new \AlipayTradePayContentBuilder();
        $barPayRequestBuilder->setOutTradeNo($sn);
        $barPayRequestBuilder->setTotalAmount($total_amount);
        $barPayRequestBuilder->setAuthCode($auth_code);
        $barPayRequestBuilder->setTimeExpress("5m");
        $barPayRequestBuilder->setSubject($subject);
        $barPayRequestBuilder->setBody($body);
        
        // 调用barPay方法获取当面付应答
        //$config = config('service.alipay');
        $barPay = new \AlipayTradeService($config);
        $barPayResult = $barPay->barPay($barPayRequestBuilder);
        
        $data = [];
        $data['status'] = $barPayResult->getTradeStatus(); // SUCCESS/FAILED/UNKNOWN
        $response = json_decode(json_encode($barPayResult->getResponse()), true);
        trace('alipay:' . json_encode($response), 'log');
        $data['message'] = $response['msg'];
        if ($data['status'] == 'SUCCESS') {
            $data['outer_sn'] = $response['trade_no'];
        } else {
            $data['message'] .= $response['sub_msg'];
        }
        
        return $data;
    }
}