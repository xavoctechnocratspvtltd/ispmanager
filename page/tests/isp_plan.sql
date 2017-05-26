/*
 Navicat Premium Backup

 Source Server         : localhost
 Source Server Type    : MariaDB
 Source Server Version : 100118
 Source Host           : localhost
 Source Database       : ispmanager

 Target Server Type    : MariaDB
 Target Server Version : 100118
 File Encoding         : utf-8

 Date: 05/16/2017 20:15:53 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `document`
-- ----------------------------
DROP TABLE IF EXISTS `document`;
CREATE TABLE `document` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `epan_id` int(11) NOT NULL,
  `type` varchar(45) DEFAULT NULL,
  `sub_type` varchar(45) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `search_string` text,
  PRIMARY KEY (`id`),
  KEY `fk_document_epan1_idx` (`epan_id`),
  FULLTEXT KEY `search_string` (`search_string`)
) ENGINE=InnoDB AUTO_INCREMENT=8459 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `document`
-- ----------------------------
BEGIN;
INSERT INTO `document` VALUES ('8418', '0', 'Item', null, null, '2017-05-06 18:17:33', null, '2017-05-06 18:17:33', 'Published', '8429'), ('8419', '0', 'Item', null, null, '2017-05-06 18:25:42', null, '2017-05-06 18:25:42', 'Published', null), ('8420', '0', 'Item', null, null, '2017-05-06 18:28:15', null, '2017-05-06 18:28:15', 'Published', null), ('8421', '0', 'Item', null, null, '2017-05-06 18:31:18', null, '2017-05-06 18:31:18', 'Published', null), ('8422', '0', 'Item', null, null, '2017-05-06 18:49:53', null, '2017-05-06 18:49:53', 'Published', null), ('8423', '0', 'Item', null, null, '2017-05-06 19:06:28', null, '2017-05-06 19:06:28', 'Published', null), ('8424', '0', 'Item', null, null, '2017-05-06 19:10:20', null, '2017-05-06 19:10:20', 'Published', null), ('8425', '0', 'Item', null, null, '2017-05-06 19:16:49', null, '2017-05-06 19:16:49', 'Published', null), ('8426', '0', 'Item', null, null, '2017-05-06 19:18:43', null, '2017-05-06 19:18:43', 'Published', null), ('8427', '0', 'Item', null, null, '2017-05-06 19:20:57', null, '2017-05-06 19:20:57', 'Published', null), ('8430', '0', 'Currency', null, '5', '2017-04-08 13:03:26', '5', '2017-05-08 13:03:26', 'Published', '  INR Currency Published'), ('8449', '0', 'Item', null, null, '2017-05-10 19:23:28', null, '2017-05-10 19:23:28', 'Published', null), ('8451', '0', 'Item', null, null, '2017-05-11 16:19:26', null, '2017-05-11 16:19:26', 'Published', null), ('8452', '0', 'Item', null, null, '2017-05-11 16:25:19', null, '2017-05-11 16:25:19', 'Published', null), ('8453', '0', 'Item', null, null, '2017-05-11 16:32:29', null, '2017-05-11 16:32:29', 'Published', null), ('8454', '0', 'Item', null, null, '2017-05-11 16:39:01', null, '2017-05-11 16:39:01', 'Published', null), ('8455', '0', 'Item', null, null, '2017-05-11 16:41:49', null, '2017-05-11 16:41:49', 'Published', null), ('8456', '0', 'Item', null, null, '2017-05-11 16:46:04', null, '2017-05-11 16:46:04', 'Published', null), ('8457', '0', 'Item', null, null, '2017-05-11 16:47:55', null, '2017-05-11 16:47:55', 'Published', null), ('8458', '0', 'Item', null, null, '2017-05-11 16:49:53', null, '2017-05-11 16:49:53', 'Published', null);
COMMIT;

-- ----------------------------
--  Table structure for `isp_condition`
-- ----------------------------
DROP TABLE IF EXISTS `isp_condition`;
CREATE TABLE `isp_condition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `data_limit` bigint(20) DEFAULT NULL,
  `download_limit` bigint(20) DEFAULT NULL,
  `upload_limit` bigint(20) DEFAULT NULL,
  `fup_download_limit` bigint(20) DEFAULT NULL,
  `fup_upload_limit` bigint(20) DEFAULT NULL,
  `accounting_download_ratio` int(11) DEFAULT NULL,
  `accounting_upload_ratio` int(11) DEFAULT NULL,
  `is_data_carry_forward` varchar(15) DEFAULT 'none',
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
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
  `remark` varchar(255) DEFAULT NULL,
  `treat_fup_as_dl_for_last_limit_row` tinyint(1) DEFAULT NULL,
  `is_pro_data_affected`  tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_plan_id` (`plan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `isp_condition`
-- ----------------------------
BEGIN;
INSERT INTO `isp_condition` VALUES ('1', '8418', '53687091200', '1048576', '1048576', null, null, '100', '100', 'none', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months', 'Main Plan', '0','0'), ('2', '8419', '536870912000', '1048576', '1048576', null, null, '100', '100', 'none', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '6', 'months', 'Main Plan', '0','0'), ('3', '8420', '536870912000', '2097152', '2097152', null, null, '100', '100', 'once', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '3', 'months', 'Main Plan', '0','0'), ('4', '8421', '107374182400', '2097152', '2097152', '524288', '524288', '100', '100', 'none', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months', 'Main Plan', '0','0'), ('5', '8422', '10737418240', '2097152', '2097152', null, null, '100', '100', 'none', '20:00:00', '08:00:00', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months', 'Night Plan', '0','0'), ('6', '8422', '10737418240', '2097152', '2097152', null, null, '100', '100', 'none', '08:00:00', '20:00:00', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months', 'Day Plan', '0', '0'), ('7', '8423', '107374182400', '2097152', '2097152', '524288', '524288', '100', '100', 'none', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months', 'All Day Plan', '0', '0'), ('8', '8423', null, null, null, null, null, '0', '0', 'none', null, null, '1', '0', '0', '0', '0', '0', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', null, null, 'Sunday Offer', '0', '0'), ('9', '8424', '107374182400', '2097152', '2097152', '524288', '524288', '100', '100', 'none', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months', 'Main Plan', '0', '0'), ('10', '8424', '2147483648', '2097152', '2097152', '1048576', '1048576', '100', '100', 'none', null, null, '1', '0', '0', '0', '0', '0', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'days', 'Sunday 2GB Extra', '1', '0'), ('11', '8425', '1073741824', '2097152', '2097152', '524288', '524288', '100', '100', 'none', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'days', 'Main Plan', '0', '0'), ('12', '8426', '107374182400', '2097152', '2097152', '524288', '524288', '100', '100', 'none', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months', 'Main Plan', '0', '0'), ('13', '8426', null, null, null, null, null, '0', '0', 'none', '22:00:00', '04:00:00', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', null, null, 'Night Unlimited', '0', '0'), ('14', '8427', '107374182400', '2097152', '2097152', '524288', '524288', '100', '100', 'none', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months', 'Main Plan', '0', '0'), ('15', '8427', null, '4194304', '4194304', '2097152', '2097152', '100', '100', 'none', '22:00:00', '04:00:00', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', null, null, 'Night High Speed', '0', '0'), ('16', '8450', null, '5242880', '5242880', '1048576', '1048576', '100', '100', 'none', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '7', 'days', 'main', '0', '0'), ('17', '8451', null, '10485760', '10485760', null, null, '100', '100', 'none', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', null, null, 'Main Topup', '0','0'), ('18', '8452', '2147483648', '5242880', '5242880', null, null, '100', '100', 'none', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', null, null, 'Main Topup', '0','0'), ('19', '8453', '5368709120', '4194304', '4194304', null, null, '100', '100', 'none', null, null, '1', '1', '1', '1', '1', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '1', '1', '1', '0', '0', '0', '4', 'days', 'Main Topup', '0','0'), ('20', '8454', null, '4194304', '4194304', '2097152', '2097152', '100', '100', 'none', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months', 'Main Topup', '0','0'), ('21', '8455', null, '5242880', '5242880', null, null, '100', '100', 'none', '20:00:00', null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'days', 'Main Topup', '0','0'), ('22', '8456', '21474836480', null, null, null, null, '100', '100', 'none', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months', 'Main Topup', '0','0'), ('23', '8457', '21474836480', null, null, null, null, '100', '100', 'none', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months', 'Main Topup', '0','0'), ('24', '8458', '107374182400', '4194304', '4194304', '1048576', '1048576', '100', '100', 'none', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months', 'Main Topup', '0','0'), ('25', '8458', null, '6291456', '6291456', '2097152', '2097152', '0', '0', 'none', '18:00:00', null, '1', '0', '0', '0', '0', '0', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'days', 'Sunday Special', '0','0'), ('26', '8458', '21474836480', '7340032', '7340032', '2097152', '2097152', '50', '50', 'none', null, null, '1', '1', '1', '1', '1', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '1', '1', '1', '1', '1', '1', '1', 'months', 'After 25th', '0','0'), ('27', '8458', null, '4194304', '4194304', '4194304', '4194304', '0', '0', 'none', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months', 'Night Unlimited', '0','0');
COMMIT;

-- ----------------------------
--  Table structure for `isp_plan`
-- ----------------------------
DROP TABLE IF EXISTS `isp_plan`;
CREATE TABLE `isp_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(255) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `sub_type` varchar(255) DEFAULT NULL,
  `search_string` text,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  `available_in_user_control_panel` tinyint(1) DEFAULT NULL,
  `document_id` int(11) NOT NULL,
  `is_topup` tinyint(4) DEFAULT NULL,
  `maintain_data_limit` tinyint(4) DEFAULT NULL,
  `is_auto_renew` tinyint(4) DEFAULT NULL,
  `item_id` int(11) NOT NULL,
  `tax_id` int(11) DEFAULT NULL,
  `plan_validity_value` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_created_by_id` (`created_by_id`),
  KEY `fk_updated_by_id` (`updated_by_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `isp_plan`
-- ----------------------------
BEGIN;
INSERT INTO `isp_plan` VALUES ('1', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '1', '8418', '5', '1'), ('2', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '0', '8419', '5', '6'), ('3', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '1', '8420', '5', '3'), ('4', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '1', '8421', '4', '1'), ('5', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '1', '8422', '5', '1'), ('6', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '1', '8423', '5', '1'), ('7', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '1', '8424', '5', '1'), ('8', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '1', '8425', '2', '1'), ('9', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '1', '8426', '5', '1'), ('10', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '1', '8427', '4', '1'), ('11', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '0', '8449', null, '1'), ('13', null, null, null, null, null, null, null, null, '1', '0', '1', '1', '0', '8451', '2', '7'), ('14', null, null, null, null, null, null, null, null, '0', '0', '1', '0', '0', '8452', '2', '1'), ('15', null, null, null, null, null, null, null, null, '0', '0', '1', '1', '0', '8453', '3', '4'), ('16', null, null, null, null, null, null, null, null, '0', '0', '1', '0', '0', '8454', '2', '1'), ('17', null, null, null, null, null, null, null, null, '0', '0', '1', '0', '0', '8455', '2', '1'), ('18', null, null, null, null, null, null, null, null, '0', '0', '1', '1', '1', '8456', '3', '1'), ('19', null, null, null, null, null, null, null, null, '0', '0', '1', '1', '1', '8457', '3', '1'), ('20', null, null, null, null, null, null, null, null, '0', '0', '1', '1', '1', '8458', '3', '1');
COMMIT;

-- ----------------------------
--  Table structure for `item`
-- ----------------------------
DROP TABLE IF EXISTS `item`;
CREATE TABLE `item` (
  `document_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `sku` varchar(255) NOT NULL,
  `original_price` decimal(14,2) DEFAULT NULL,
  `sale_price` decimal(14,2) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `description` text,
  `stock_availability` tinyint(4) DEFAULT NULL,
  `show_detail` tinyint(1) DEFAULT NULL,
  `show_price` tinyint(1) DEFAULT NULL,
  `display_sequence` int(11) DEFAULT NULL,
  `is_new` tinyint(1) DEFAULT NULL,
  `is_feature` tinyint(1) DEFAULT NULL,
  `is_mostviewed` tinyint(1) DEFAULT NULL,
  `Item_enquiry_auto_reply` tinyint(1) DEFAULT NULL,
  `is_comment_allow` tinyint(1) DEFAULT NULL,
  `comment_api` varchar(255) DEFAULT NULL,
  `add_custom_button` tinyint(1) DEFAULT NULL,
  `custom_button_url` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text,
  `tags` text,
  `is_designable` tinyint(1) DEFAULT NULL,
  `designs` longtext CHARACTER SET utf8,
  `is_party_publish` tinyint(1) DEFAULT NULL,
  `minimum_order_qty` int(11) DEFAULT NULL,
  `maximum_order_qty` int(11) DEFAULT NULL,
  `qty_unit_id` int(11) DEFAULT NULL,
  `is_attachment_allow` tinyint(1) DEFAULT NULL,
  `is_saleable` tinyint(1) DEFAULT NULL,
  `is_downloadable` tinyint(1) DEFAULT NULL,
  `is_rentable` tinyint(1) DEFAULT NULL,
  `is_enquiry_allow` tinyint(1) DEFAULT NULL,
  `is_template` tinyint(1) DEFAULT NULL,
  `negative_qty_allowed` varchar(255) DEFAULT NULL,
  `is_visible_sold` tinyint(1) DEFAULT NULL,
  `enquiry_send_to_admin` tinyint(1) DEFAULT NULL,
  `watermark_position` varchar(255) DEFAULT NULL,
  `watermark_opacity` varchar(255) DEFAULT NULL,
  `qty_from_set_only` tinyint(1) DEFAULT NULL,
  `custom_button_label` varchar(255) DEFAULT NULL,
  `is_servicable` tinyint(1) DEFAULT NULL,
  `is_purchasable` tinyint(1) DEFAULT NULL,
  `maintain_inventory` tinyint(1) DEFAULT NULL,
  `website_display` tinyint(1) DEFAULT NULL,
  `allow_negative_stock` tinyint(1) DEFAULT NULL,
  `is_productionable` tinyint(1) DEFAULT NULL,
  `warranty_days` int(11) DEFAULT NULL,
  `terms_and_conditions` text,
  `watermark_text` varchar(255) DEFAULT NULL,
  `duplicate_from_item_id` varchar(255) DEFAULT NULL,
  `is_allowuploadable` tinyint(1) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `designer_id` int(11) DEFAULT NULL,
  `is_dispatchable` tinyint(1) DEFAULT NULL,
  `item_specific_upload_hint` text,
  `upload_file_label` text,
  `to_customer_id` int(11) DEFAULT NULL,
  `weight` decimal(10,0) DEFAULT NULL,
  `quantity_group` varchar(255) DEFAULT NULL,
  `upload_file_group` varchar(255) DEFAULT NULL,
  `is_renewable` tinyint(4) DEFAULT NULL,
  `remind_to` varchar(255) DEFAULT NULL,
  `renewable_value` int(11) DEFAULT NULL,
  `renewable_unit` varchar(255) DEFAULT NULL,
  `is_teller_made_item` tinyint(4) DEFAULT NULL,
  `minimum_stock_limit` int(11) DEFAULT NULL,
  `is_serializable1` tinyint(4) DEFAULT NULL,
  `is_serializable` tinyint(4) DEFAULT NULL,
  `nominal_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_id` (`document_id`) USING BTREE,
  KEY `duplicate_from_item_id` (`duplicate_from_item_id`) USING BTREE,
  KEY `to_customer_id` (`to_customer_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `item`
-- ----------------------------
BEGIN;
INSERT INTO `item` VALUES ('8418', 'PL-50-M', 'PL-50-M', '100.00', '90.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, null, null, null, null, '1', 'Both', '1', 'MONTH', null, null, null, null,null), ('8419', 'PL-500 GB for 6 month', 'PL-500-6M', '1000.00', '900.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, null, null, null, null, '1', 'Both', '1', 'MONTH', null, null, null, null,null), ('8420', 'PL-500 GB for 3 month data carry', 'PL-500-3M-carry', '200.00', '2000.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '3', null, null, null, null, null, null, null, null, '1', 'Both', '1', 'MONTH', null, null, null, null,null), ('8421', 'unlimited 100GB-m', 'PL-100GB-2M-unlimited', '3000.00', '200.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, '1', 'Both', '1', 'MONTH', null, null, null, null,null), ('8422', 'Day Night plan', 'PL-20GB-2M-Day-Night', '400.00', '300.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '5', null, null, null, null, null, null, null, null, '1', 'Both', '1', 'MONTH', null, null, null, null,null), ('8423', 'SUNDAY EXCLUDED 100GB-1m', 'PL-100GB-1M-unlimited', '2000.00', '3000.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '6', null, null, null, null, null, null, null, null, '1', 'Both', '1', 'MONTH', null, null, null, null,null), ('8424', 'High Speed 100GB-2mb', 'PL-100GB-1M-high-speed', '100.00', '10.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '7', null, null, null, null, null, null, null, null, '1', 'Both', '1', 'MONTH', null, null, null, null,null), ('8425', 'jio plan', 'PL-1GB-1D-high-speed', '100.00', '200.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '8', null, null, null, null, null, null, null, null, '1', 'Both', '1', 'MONTH', null, null, null, null,null), ('8426', 'Night Unlimited', 'PL-10GB-1M-Bight-unlimited', '100.00', '2000.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '9', null, null, null, null, null, null, null, null, '1', 'Both', '1', 'MONTH', null, null, null, null,null), ('8427', 'Night highspeed', 'PL-100GB-1M-night-speed', '100.00', '200.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '10', null, null, null, null, null, null, null, null, '1', 'Both', '1', 'MONTH', null, null, null, null,null), ('8451', 'Top-7Days-10MB-HighSpeed', 'Top-7Days-10MB-HighSpeed', '1000.00', '1000.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '13', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null), ('8452', 'Top-1Days-5MB-HighSpeed', 'Top-1Days-5MB-HighSpeed', '200.00', '200.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '14', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null,null), ('8453', 'Top-After25-5GB-4MB-HighSpeed', 'Top-After25-5GB-4MB-HighSpeed', '70.00', '50.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '15', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null,null), ('8454', 'Top-1Month-4MB', 'Top-1Month-4MB', '100.00', '80.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '16', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null,null), ('8455', 'Top-Time-5MB-HighSpeed', 'Top-Time-5MB-HighSpeed', '200.00', '100.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '17', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null,null), ('8456', 'Top-1Month-20GB-Extra-CarryOnce', 'Top-1Month-20GB-Extra-CarryOnce', '500.00', '400.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '18', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null,null), ('8457', 'Top-1Month-20GB-Extra-CarryAlways', 'Top-1Month-20GB-Extra-CarryAlways', '500.00', '450.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '19', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null,null), ('8458', 'Top-Megha', 'Top-Megha', '1000.00', '700.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '20', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null,null);
COMMIT;

-- ----------------------------
--  Table structure for `taxation`
-- ----------------------------
DROP TABLE IF EXISTS `taxation`;
CREATE TABLE `taxation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `percentage` decimal(14,2) NOT NULL,
  `type` varchar(255) NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `sub_tax` text,
  PRIMARY KEY (`id`),
  KEY `created_by_id` (`created_by_id`) USING BTREE,
  FULLTEXT KEY `search_string` (`name`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `taxation`
-- ----------------------------
BEGIN;
INSERT INTO `taxation` VALUES ('1', 'Vat 05 %', '5.00', 'Taxation', '3', null), ('2', 'Vat 14.5 %', '14.50', 'Taxation', '3', null), ('3', 'CST 5 %', '5.00', 'Taxation', '3', null), ('4', 'CST 2 %  Against Form C', '2.00', 'Taxation', '3', null), ('5', 'CST 14.5 %', '14.50', 'Taxation', '3', null), ('6', 'VAT 5.5%', '5.50', 'Taxation', '3', null), ('7', 'CST 05.5%', '5.50', 'Taxation', '3', null);
COMMIT;

-- ----------------------------
--  Table structure for `unit`
-- ----------------------------
DROP TABLE IF EXISTS `unit`;
CREATE TABLE `unit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_group_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `unit`
-- ----------------------------
BEGIN;
INSERT INTO `unit` VALUES ('1', '1', 'hours'), ('2', '1', 'days'), ('3', '1', 'weeks'), ('4', '1', 'months'), ('5', '1', 'years');
COMMIT;

-- ----------------------------
--  Table structure for `unit_group`
-- ----------------------------
DROP TABLE IF EXISTS `unit_group`;
CREATE TABLE `unit_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `unit_group`
-- ----------------------------
BEGIN;
INSERT INTO `unit_group` VALUES ('1', 'Duration', '5');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
