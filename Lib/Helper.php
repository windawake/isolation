<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Workerman\Lib;

class Helper
{
    public static function log($message, $mark = '' , $file = '' ,$logPath='', $fileSize = 1048576){
        if (empty($logPath))
            $logPath = __DIR__.'/../logs/';
        $datetime = date('[Y-m-d H:i:s]');
        $mark .= empty($mark) ? '' : ' ';
        $message = is_array($message) ? var_export($message, true) : $message;
        $message = $datetime.' '.$mark.$message . PHP_EOL;
        $file = $logPath. (empty($file) ? 'running.log' : $file);
        //日志文件大于1M，重命名
        if(is_file($file) && filesize($file) > $fileSize){
            rename($file, $file.'_'.date('YmdHis'));
        }
        //文件不存在，则创建
        if(!file_exists($file)){
            $dir = dirname($file);
            !is_dir($dir) && mkdir($dir,0755,true);
            fclose(fopen($file,'x'));
        }
        error_log($message,3,$file);
    }
}
