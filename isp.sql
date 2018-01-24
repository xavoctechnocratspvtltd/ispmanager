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

 Date: 07/01/2017 12:27:29 PM
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Table structure for `isp_condition`
-- ----------------------------
DROP TABLE IF EXISTS `isp_condition`;
CREATE TABLE `isp_condition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `data_limit` bigint(20) DEFAULT NULL,
  `time_limit` bigint(20) DEFAULT NULL,
  `download_limit` bigint(20) DEFAULT NULL,
  `upload_limit` bigint(20) DEFAULT NULL,
  `fup_download_limit` bigint(20) DEFAULT NULL,
  `fup_upload_limit` bigint(20) DEFAULT NULL,
  `burst_dl_limit` bigint(20) DEFAULT NULL,
  `burst_ul_limit` bigint(20) DEFAULT NULL,
  `burst_threshold_dl_limit` bigint(20) DEFAULT NULL,
  `burst_threshold_ul_limit` bigint(20) DEFAULT NULL,
  `burst_dl_time` bigint(20) DEFAULT NULL,
  `burst_ul_time` bigint(20) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `accounting_download_ratio` int(11) DEFAULT NULL,
  `accounting_upload_ratio` int(11) DEFAULT NULL,
  `is_data_carry_forward` varchar(255) DEFAULT NULL,
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
  `treat_fup_as_dl_for_last_limit_row` tinyint(1) DEFAULT NULL,
  `is_pro_data_affected` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_plan_id` (`plan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `isp_country`
-- ----------------------------
DROP TABLE IF EXISTS `isp_country`;
CREATE TABLE `isp_country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

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
  `otp_verified` tinyint(1) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `verified_by` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `ip_address_mode_cm` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `grace_period_in_days` varchar(255) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `create_invoice` tinyint(4) DEFAULT NULL,
  `include_pro_data_basis` varchar(255) DEFAULT NULL,
  `mac_address` varchar(255) DEFAULT NULL,
  `is_invoice_date_first_to_first` tinyint(4) DEFAULT NULL,
  `last_dl_limit` bigint(20) DEFAULT NULL,
  `last_ul_limit` bigint(20) DEFAULT NULL,
  `last_accounting_dl_ratio` int(11) DEFAULT NULL,
  `last_accounting_ul_ratio` int(11) DEFAULT NULL,
  `otp_send_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_plan_id` (`plan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=latin1;

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
  `time_limit` bigint(20) DEFAULT NULL,
  `download_limit` bigint(20) DEFAULT NULL,
  `upload_limit` bigint(20) DEFAULT NULL,
  `fup_download_limit` bigint(20) DEFAULT NULL,
  `fup_upload_limit` bigint(20) DEFAULT NULL,
  `burst_dl_limit` bigint(20) DEFAULT NULL,
  `burst_ul_limit` bigint(20) DEFAULT NULL,
  `burst_threshold_dl_limit` bigint(20) DEFAULT NULL,
  `burst_threshold_ul_limit` bigint(20) DEFAULT NULL,
  `burst_dl_time` bigint(20) DEFAULT NULL,
  `burst_ul_time` bigint(20) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
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
  `time_consumed` bigint(20) DEFAULT NULL,
  `data_limit_row` varchar(255) DEFAULT NULL,
  `duplicated_from_record_id` int(11) DEFAULT NULL,
  `is_data_carry_forward` varchar(255) DEFAULT NULL,
  `carry_data` bigint(20) DEFAULT '0',
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
  `treat_fup_as_dl_for_last_limit_row` tinyint(1) DEFAULT NULL,
  `is_pro_data_affected` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_id` (`user_id`),
  KEY `fk_plan_id` (`plan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : 127.0.0.1:3306
Source Database       : prompt

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-07-10 15:26:25
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `isp_payment_transactions`
-- ----------------------------
DROP TABLE IF EXISTS `isp_payment_transactions`;
CREATE TABLE `isp_payment_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `payment_mode` varchar(255) DEFAULT NULL,
  `cheque_no` varchar(255) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  `dd_no` varchar(255) DEFAULT NULL,
  `dd_date` date DEFAULT NULL,
  `bank_detail` text,
  `amount` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of isp_payment_transactions
-- ----------------------------


SET FOREIGN_KEY_CHECKS = 1;
