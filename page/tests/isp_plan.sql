/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MariaDB
 Source Server Version : 100118
 Source Host           : localhost
 Source Database       : ispmanager

 Target Server Type    : MariaDB
 Target Server Version : 100118
 File Encoding         : utf-8

 Date: 05/05/2017 23:59:50 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `isp_condition`
-- ----------------------------
DROP TABLE IF EXISTS `isp_condition`;
CREATE TABLE `isp_condition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `data_limit` varchar(255) DEFAULT NULL,
  `download_limit` varchar(255) DEFAULT NULL,
  `upload_limit` varchar(255) DEFAULT NULL,
  `fup_download_limit` varchar(255) DEFAULT NULL,
  `fup_upload_limit` varchar(255) DEFAULT NULL,
  `accounting_download_ratio` varchar(255) DEFAULT NULL,
  `accounting_upload_ratio` varchar(255) DEFAULT NULL,
  `is_data_carry_forward` tinyint(1) DEFAULT NULL,
  `start_time` varchar(255) DEFAULT NULL,
  `end_time` varchar(255) DEFAULT NULL,
  `sun` tinyint(1) DEFAULT NULL,
  `mon` tinyint(1) DEFAULT NULL,
  `tue` tinyint(1) DEFAULT NULL,
  `wed` tinyint(1) DEFAULT NULL,
  `thu` tinyint(1) DEFAULT NULL,
  `fri` tinyint(1) DEFAULT NULL,
  `sat` tinyint(1) DEFAULT NULL,
  `d01` tinyint(1) DEFAULT NULL,
  `d02` tinyint(1) DEFAULT NULL,
  `d03` tinyint(1) DEFAULT NULL,
  `d04` tinyint(1) DEFAULT NULL,
  `d05` tinyint(1) DEFAULT NULL,
  `d06` tinyint(1) DEFAULT NULL,
  `d07` tinyint(1) DEFAULT NULL,
  `d08` tinyint(1) DEFAULT NULL,
  `d09` tinyint(1) DEFAULT NULL,
  `d10` tinyint(1) DEFAULT NULL,
  `d11` tinyint(1) DEFAULT NULL,
  `d12` tinyint(1) DEFAULT NULL,
  `d13` tinyint(1) DEFAULT NULL,
  `d14` tinyint(1) DEFAULT NULL,
  `d15` tinyint(1) DEFAULT NULL,
  `d16` tinyint(1) DEFAULT NULL,
  `d17` tinyint(1) DEFAULT NULL,
  `d18` tinyint(1) DEFAULT NULL,
  `d19` tinyint(1) DEFAULT NULL,
  `d20` tinyint(1) DEFAULT NULL,
  `d21` tinyint(1) DEFAULT NULL,
  `d22` tinyint(1) DEFAULT NULL,
  `d23` tinyint(1) DEFAULT NULL,
  `d24` tinyint(1) DEFAULT NULL,
  `d25` tinyint(1) DEFAULT NULL,
  `d26` tinyint(1) DEFAULT NULL,
  `d27` tinyint(1) DEFAULT NULL,
  `d28` tinyint(1) DEFAULT NULL,
  `d29` tinyint(1) DEFAULT NULL,
  `d30` tinyint(1) DEFAULT NULL,
  `d31` tinyint(1) DEFAULT NULL,
  `data_reset_value` varchar(255) DEFAULT NULL,
  `data_reset_mode` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_plan_id` (`plan_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `isp_condition`
-- ----------------------------
BEGIN;
INSERT INTO `isp_condition` VALUES ('1', '1', '10gb', '2mbps', '2mbps', '1mbps', '1mbps', '100', '100', '0', '08:00:00', '20:00:00', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months'), ('2', '1', '20gb', '2mbps', '2mbps', '1mbps', '1mbps', '100', '100', '0', '20:00:00', '08:00:00', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months');
COMMIT;

-- ----------------------------
--  Table structure for `isp_plan`
-- ----------------------------
DROP TABLE IF EXISTS `isp_plan`;
CREATE TABLE `isp_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `available_in_user_control_panel` tinyint(1) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `is_topup` tinyint(1) DEFAULT NULL,
  `maintain_data_limit` tinyint(1) DEFAULT NULL,
  `is_auto_renew` tinyint(1) DEFAULT NULL,
  `period` varchar(255) DEFAULT NULL,
  `period_unit` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `isp_plan`
-- ----------------------------
BEGIN;
INSERT INTO `isp_plan` VALUES ('1', 'PL-50-M', '50 GB for 1 Month', '1', '1000', 'active', '0', '1', '1', '1', 'month'), ('2', 'Topup1', '', '0', '', 'active', '1', null, null, null, null), ('3', 'PL-500-GB-6', '', '0', '5000', 'active', '0', '1', '0', '6', 'month'), ('4', 'PL-Day-Night-10GB', '', '0', '3000', 'active', '0', '1', '1', '1', 'month'), ('5', 'PL-50-GB-sunday-50%', '', '0', '600', 'active', '0', '1', '1', '1', 'month'), ('6', 'PL-1GB-Daily', 'zj', '0', '200', 'active', '0', '1', '1', '1', 'month'), ('7', 'PL-1GB-Night-Unlimited', '', '0', '', 'active', '0', '1', '1', '1', 'month');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
