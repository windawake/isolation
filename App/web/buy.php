<?php
/**
 * Created by PhpStorm.
 * User: zhangzibin
 * Date: 2017/8/5
 * Time: 10:24
 */

require __DIR__.'/../../vendor/autoload.php';

$client = new Predis\Client('tcp://127.0.0.1:6379');
//随机用户
$userId = rand(1,200);
$goodsId = 1;

$arrNum = array(1,1,1,2,3,4,5,6,1,1);
$key = array_rand($arrNum);
//随机库存
$stock = $arrNum[$key];

try{
    $con = new PDO("mysql:host=localhost;dbname=test;charset=utf8;port=3306","root","");
}catch(PDOException $e)
{
    echo $e->getMessage();
}

//$sql = "update buy_goods set click_times = click_times+1 where id={$goodsId}";
//$con->exec($sql);

$client->hincrby("buy:goods:{$goodsId}","click_times",1);

//$sql = "SELECT stock_left FROM buy_stock WHERE goods_id={$goodsId}";
//$stock_left = $con->query($sql)->fetchColumn();

//$con->beginTransaction();//开始事务定义

$stock_left = $client->get("buy:stock:{$goodsId}");

if($stock_left > 0){
    //$sql = "UPDATE buy_stock set stock_left = stock_left - {$stock} where  stock_left>0 and goods_id={$goodsId}";
    //$res = $con->exec($sql);
    $res = $client->decrby("buy:stock:{$goodsId}",$stock);
    if($res >= 0){
        $ma = explode(' ',microtime());
        $mi = $ma[1].substr($ma[0],2,6);
        $mt = mt_rand(1000,9999);
        $orderNo = $mi.$mt;

        $client->rpush("buy:order:list",$orderNo);

        $data = array(
            'order_no' => $orderNo,
            'user_id' => $userId,
            'goods_id' => $goodsId,
            'sold' => $stock
        );

        $client->hmset("buy:order:{$orderNo}",$data);

        //$sql = "INSERT INTO buy_order(order_no,user_id,goods_id,sold) values ({$orderNo}, {$userId}, {$goodsId}, {$stock})";
        //$res2 = $con->exec($sql);

        //$sql = "update buy_goods set sales_volume = sales_volume + {$stock} where id={$goodsId}";
        //$res3 = $con->exec($sql);
        $client->hincrby("buy:goods:{$goodsId}","sales_volume",$stock);
    }else{
        $res = $client->incrby("buy:stock:{$goodsId}",$stock);
    }
}else{
    echo "已经没剩余了";
}
