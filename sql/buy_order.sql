/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50505
Source Host           : 127.0.0.1:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-08-05 17:24:33
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for buy_order
-- ----------------------------
DROP TABLE IF EXISTS `buy_order`;
CREATE TABLE `buy_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` bigint(20) unsigned DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `goods_id` int(11) unsigned DEFAULT NULL,
  `sold` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_order_no` (`order_no`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=latin1;
