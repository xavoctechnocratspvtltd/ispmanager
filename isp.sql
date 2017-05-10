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

 Date: 05/07/2017 11:07:32 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `isp_city`
-- ----------------------------
DROP TABLE IF EXISTS `isp_city`;
CREATE TABLE `isp_city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_state_id` (`state_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Table structure for `isp_condition`
-- ----------------------------
DROP TABLE IF EXISTS `isp_condition`;
CREATE TABLE `isp_condition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `data_limit` bigint(20) DEFAULT NULL,
  `download_limit` bigint(20) DEFAULT NULL,
  `upload_limit` bigint(20) DEFAULT NULL,
  `fup_download_limit` bigint(20) DEFAULT NULL,
  `fup_upload_limit` bigint(20) DEFAULT NULL,
  `accounting_download_ratio` int(11) DEFAULT NULL,
  `accounting_upload_ratio` int(11) DEFAULT NULL,
  `is_data_carry_forward` varchar(255),
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
  PRIMARY KEY (`id`),
  KEY `fk_plan_id` (`plan_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `isp_country`
-- ----------------------------
DROP TABLE IF EXISTS `isp_country`;
CREATE TABLE `isp_country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Table structure for `isp_policy`
-- ----------------------------
DROP TABLE IF EXISTS `isp_policy`;
CREATE TABLE `isp_policy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `data_dl` varchar(255) DEFAULT NULL,
  `data_ul` varchar(255) DEFAULT NULL,
  `data_accounting_dl` varchar(255) DEFAULT NULL,
  `data_accounting_ul` varchar(255) DEFAULT NULL,
  `reject` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_plan_id` (`plan_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Table structure for `isp_state`
-- ----------------------------
DROP TABLE IF EXISTS `isp_state`;
CREATE TABLE `isp_state` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_country_id` (`country_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Table structure for `isp_user`
-- ----------------------------
DROP TABLE IF EXISTS `isp_user`;
CREATE TABLE `isp_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `radius_username` varchar(255) DEFAULT NULL,
  `radius_password` varchar(255) DEFAULT NULL,
  `mac_address_cm` varchar(255) DEFAULT NULL,
  `cm_ip_pool` varchar(255) DEFAULT NULL,
  `cm_static_ip` varchar(255) DEFAULT NULL,
  `mac_address_cpe` varchar(255) DEFAULT NULL,
  `allow_mac_address_cpe_only` tinyint(1) DEFAULT NULL,
  `ip_address_mode_cpe` varchar(255) DEFAULT NULL,
  `simultaneous_use` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `email_id` varchar(255) DEFAULT NULL,
  `vat_id` varchar(255) DEFAULT NULL,
  `narration` text,
  `custom_radius_attributes` text,
  `is_verified` tinyint(1) DEFAULT NULL,
  `verified_by` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `ip_address_mode_cm` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `grace_period_in_days` varchar(255) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `create_invoice` tinyint(4) DEFAULT NULL,
  `include_pro_data_basis` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_plan_id` (`plan_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Table structure for `isp_user_plan_and_topup`
-- ----------------------------
DROP TABLE IF EXISTS `isp_user_plan_and_topup`;
CREATE TABLE `isp_user_plan_and_topup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `condition_id` int(11) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `is_topup` tinyint(1) DEFAULT NULL,
  `data_limit` bigint(20) DEFAULT NULL,
  `download_limit` bigint(20) DEFAULT NULL,
  `upload_limit` bigint(20) DEFAULT NULL,
  `fup_download_limit` bigint(20) DEFAULT NULL,
  `fup_upload_limit` bigint(20) DEFAULT NULL,
  `accounting_download_ratio` int(11) DEFAULT NULL,
  `accounting_upload_ratio` int(11) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `expire_date` datetime DEFAULT NULL,
  `is_expired` tinyint(1) DEFAULT NULL,
  `is_recurring` tinyint(1) DEFAULT NULL,
  `is_effective` tinyint(1) DEFAULT NULL,
  `download_data_consumed` bigint(20) DEFAULT NULL,
  `upload_data_consumed` bigint(20) DEFAULT NULL,
  `data_limit_row` varchar(255) DEFAULT NULL,
  `duplicated_from_record_id` int(255) DEFAULT NULL,
  `is_data_carry_forward` varchar(255),
  `carry_data` bigint(20) DEFAULT 0,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `sun` tinyint(1) DEFAULT NULL,
  `mon` tinyint(1) DEFAULT NULL,
  `tue` tinyint(1) DEFAULT NULL,
  `wed` tinyint(1) DEFAULT NULL,
  `thu` tinyint(1) DEFAULT NULL,
  `fri` tinyint(1) DEFAULT NULL,
  `sat` tinyint(1) DEFAULT NULL,
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
  `reset_date` datetime DEFAULT NULL,
  `data_reset_mode` varchar(255) DEFAULT NULL,
  `data_reset_value` varchar(255) DEFAULT NULL,
  `d01` tinyint(1) DEFAULT NULL,
  `d02` tinyint(1) DEFAULT NULL,
  `d03` tinyint(1) DEFAULT NULL,
  `d04` tinyint(1) DEFAULT NULL,
  `d05` tinyint(1) DEFAULT NULL,
  `d06` tinyint(1) DEFAULT NULL,
  `d07` tinyint(1) DEFAULT NULL,
  `d08` tinyint(1) DEFAULT NULL,
  `d09` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_id` (`user_id`),
  KEY `fk_plan_id` (`plan_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Table structure for `isp_user_topup`
-- ----------------------------
DROP TABLE IF EXISTS `isp_user_topup`;
CREATE TABLE `isp_user_topup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `topup_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_id` (`user_id`),
  KEY `fk_topup_id` (`topup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

SET FOREIGN_KEY_CHECKS = 1;
