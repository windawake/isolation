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
    //商品点击量
    Timer::add(10, function() use($con, $redisClient){
        $clickTimes = $redisClient->hget("buy:goods:1",'click_times');
        $sql = "select click_times from buy_goods where id=1";
        $item = $con->query($sql)->fetch();
        if(isset($item['click_times']) && !is_null($clickTimes) && $item['click_times'] != $clickTimes){
            $sql = "update buy_goods set click_times = {$clickTimes} where id=1 limit 1";
            $con->exec($sql);
        }

    });

    //商品销量
    Timer::add(10, function() use ($con, $redisClient){
        $salesVolume = $redisClient->hget("buy:goods:1",'sales_volume');
        $sql = "select sales_volume from buy_goods where id=1";
        $item = $con->query($sql)->fetch();
        if(isset($item['sales_volume']) && !is_null($salesVolume) && $item['sales_volume'] != $salesVolume){
            $sql = "update buy_goods set sales_volume = {$salesVolume} where id=1 limit 1";
            $con->exec($sql);
        }
    });

    //库存数量
    Timer::add(3, function() use ($con, $redisClient){
        $stockLeft = $redisClient->get("buy:stock:1");
        $sql = "select stock_left from buy_stock where goods_id=1";
        $item = $con->query($sql)->fetch();
        if(isset($item['stock_left']) && !is_null($stockLeft) && $item['stock_left'] != $stockLeft){
            $sql = "update buy_stock set stock_left = {$stockLeft} where goods_id=1 limit 1";
            $con->exec($sql);
        }
    });
};

Worker::runAll();