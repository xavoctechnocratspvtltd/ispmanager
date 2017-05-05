/*
Navicat MySQL Data Transfer

Source Server         : Localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : printonclick

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-05-05 18:58:41
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `isp_plan`
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
-- Records of isp_plan
-- ----------------------------
INSERT INTO `isp_plan` VALUES ('1', 'PL-50-M', '50 GB for 1 Month', '1', '1000', 'active', '0', '1', '1', '1', 'month');
INSERT INTO `isp_plan` VALUES ('2', 'Topup1', '', '0', '', 'active', '1', null, null, null, null);
INSERT INTO `isp_plan` VALUES ('3', 'PL-500-GB-6', '', '0', '5000', 'active', '0', '1', '0', '6', 'month');
INSERT INTO `isp_plan` VALUES ('4', 'PL-Day-Night-10GB', '', '0', '3000', 'active', '0', '1', '1', '1', 'month');
INSERT INTO `isp_plan` VALUES ('5', 'PL-50-GB-sunday-50%', '', '0', '600', 'active', '0', '1', '1', '1', 'month');
INSERT INTO `isp_plan` VALUES ('6', 'PL-1GB-Daily', 'zj', '0', '200', 'active', '0', '1', '1', '1', 'month');
INSERT INTO `isp_plan` VALUES ('7', 'PL-1GB-Night-Unlimited', '', '0', '', 'active', '0', '1', '1', '1', 'month');
