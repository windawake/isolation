<?php
/**
 * Created by PhpStorm.
 * User: zhangzibin
 * Date: 2017/6/20
 * Time: 16:47
 */
use Workerman\Worker;
use Workerman\Lib\Timer;

require_once __DIR__ . '/../Autoloader.php';

$task = new Worker();
try{
    $con = new \PDO("mysql:host=localhost;dbname=test;charset=utf8;port=3306","root","");
}catch(PDOException $e)
{
    echo $e->getMessage();
}

$redisClient = (new \Redis());
$redisClient->pconnect('127.0.0.1', '6379');
$task->onWorkerStart = function($task)
{
    global $con, $redisClient;
    //同步订单
    Timer::add(0.01, function() use($con, $redisClient){
        if($redisClient->lLen("buy:order:list")){
            $orderNo = $redisClient->lPop("buy:order:list");
            $orderInfo = $redisClient->hGetAll("buy:order:{$orderNo}");
            if($orderInfo){
                $sql = "INSERT INTO buy_order(order_no,user_id,goods_id,sold) values ({$orderInfo['order_no']}, {$orderInfo['user_id']}, {$orderInfo['goods_id']}, {$orderInfo['sold']})";
                $res = $con->exec($sql);
                if($res){
                    $redisClient->del("buy:order:{$orderNo}");
                }

            }else{
                $redisClient->rPush("buy:order:list",$orderNo);
            }
        }
    });

};

Worker::runAll();