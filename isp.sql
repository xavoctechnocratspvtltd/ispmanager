/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MariaDB
 Source Server Version : 100214
 Source Host           : localhost
 Source Database       : ispmanager

 Target Server Type    : MariaDB
 Target Server Version : 100214
 File Encoding         : utf-8

 Date: 10/02/2018 12:19:11 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `isp_channel`
-- ----------------------------
DROP TABLE IF EXISTS `isp_channel`;
CREATE TABLE `isp_channel` (
  `contact_id` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permitted_bandwidth` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `isp_channel_association`
-- ----------------------------
DROP TABLE IF EXISTS `isp_channel_association`;
CREATE TABLE `isp_channel_association` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_id` int(11) DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `isp_user_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `lead_id` int(11) DEFAULT NULL,
  `payment_transaction_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_chanel_id` (`channel_id`) USING BTREE,
  KEY `fk_plan_id` (`plan_id`) USING BTREE,
  KEY `fk_user_id` (`isp_user_id`) USING BTREE,
  KEY `fk_invoice_id` (`invoice_id`) USING BTREE,
  KEY `fk_lead_id` (`lead_id`) USING BTREE,
  KEY `fk_payment_transaction_id` (`payment_transaction_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=FIXED;

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
--  Table structure for `isp_client`
-- ----------------------------
DROP TABLE IF EXISTS `isp_client`;
CREATE TABLE `isp_client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `ipaddr` varchar(255) DEFAULT NULL,
  `secret` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB AUTO_INCREMENT=553 DEFAULT CHARSET=utf8;

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
--  Table structure for `isp_devices`
-- ----------------------------
DROP TABLE IF EXISTS `isp_devices`;
CREATE TABLE `isp_devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `monitor` varchar(255) DEFAULT NULL,
  `failed_action` text DEFAULT NULL,
  `port` varchar(255) DEFAULT NULL,
  `allowed_fail_cycle` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `protocol` varchar(255) DEFAULT NULL,
  `override_check_line` varchar(255) DEFAULT NULL,
  `override_failed_action` varchar(255) DEFAULT NULL,
  `secret` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `isp_payment_transactions`
-- ----------------------------
DROP TABLE IF EXISTS `isp_payment_transactions`;
CREATE TABLE `isp_payment_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `payment_mode` varchar(255) DEFAULT NULL,
  `cheque_no` varchar(255) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  `dd_no` varchar(255) DEFAULT NULL,
  `dd_date` date DEFAULT NULL,
  `bank_detail` text DEFAULT NULL,
  `amount` varchar(255) DEFAULT NULL,
  `is_submitted_to_company` tinyint(4) DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `narration` text DEFAULT NULL,
  `submitted_by_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `isp_plan`
-- ----------------------------
DROP TABLE IF EXISTS `isp_plan`;
CREATE TABLE `isp_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `sub_type` varchar(255) DEFAULT NULL,
  `search_string` text DEFAULT NULL,
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
  `free_tenure` int(11) NOT NULL DEFAULT 0,
  `free_tenure_unit` varchar(255) DEFAULT NULL,
  `is_surrenderable` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_created_by_id` (`created_by_id`),
  KEY `fk_updated_by_id` (`updated_by_id`),
  KEY `fk_document_id` (`document_id`) USING BTREE,
  KEY `fk_item_id` (`item_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=395 DEFAULT CHARSET=latin1;

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
  `narration` text DEFAULT NULL,
  `custom_radius_attributes` text DEFAULT NULL,
  `otp_verified` tinyint(1) DEFAULT NULL,
  `verified_by` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `ip_address_mode_cm` varchar(255) DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `grace_period_in_days` varchar(255) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `create_invoice` tinyint(4) DEFAULT NULL,
  `include_pro_data_basis` varchar(255) DEFAULT NULL,
  `mac_address` varchar(255) DEFAULT NULL,
  `is_invoice_date_first_to_first` tinyint(4) DEFAULT NULL,
  `last_dl_limit` bigint(20) NOT NULL,
  `last_ul_limit` bigint(20) NOT NULL,
  `last_accounting_dl_ratio` int(11) DEFAULT NULL,
  `last_accounting_ul_ratio` int(11) DEFAULT NULL,
  `otp_send_time` datetime DEFAULT NULL,
  `installation_assign_to_id` int(11) DEFAULT NULL,
  `installed_at` datetime DEFAULT NULL,
  `installed_narration` text DEFAULT NULL,
  `installation_assign_at` datetime DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `connection_type` varchar(255) DEFAULT NULL,
  `demo_plan_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_plan_id` (`plan_id`),
  KEY `fk_id` (`id`) USING BTREE,
  KEY `fk_radius_username` (`radius_username`) USING BTREE,
  KEY `fk_customer_id` (`customer_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3701 DEFAULT CHARSET=latin1;

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
  `session_download_data_consumed` bigint(20) DEFAULT 0,
  `session_upload_data_consumed` bigint(20) DEFAULT 0,
  `session_download_data_consumed_on_reset` bigint(20) DEFAULT 0,
  `session_upload_data_consumed_on_reset` bigint(20) DEFAULT 0,
  `session_time_consumed` bigint(20) DEFAULT 0,
  `data_limit_row` varchar(255) DEFAULT NULL,
  `duplicated_from_record_id` int(11) DEFAULT NULL,
  `is_data_carry_forward` varchar(255) DEFAULT NULL,
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
  `treat_fup_as_dl_for_last_limit_row` tinyint(1) DEFAULT NULL,
  `is_pro_data_affected` tinyint(1) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_id` (`user_id`),
  KEY `fk_plan_id` (`plan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2948 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Table structure for `isp_user_plan_and_topup_copy`
-- ----------------------------
DROP TABLE IF EXISTS `isp_user_plan_and_topup_copy`;
CREATE TABLE `isp_user_plan_and_topup_copy` (
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
  `session_download_data_consumed` bigint(20) DEFAULT 0,
  `session_upload_data_consumed` bigint(20) DEFAULT 0,
  `session_time_consumed` bigint(20) DEFAULT 0,
  `data_limit_row` varchar(255) DEFAULT NULL,
  `duplicated_from_record_id` int(11) DEFAULT NULL,
  `is_data_carry_forward` varchar(255) DEFAULT NULL,
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
  `treat_fup_as_dl_for_last_limit_row` tinyint(1) DEFAULT NULL,
  `is_pro_data_affected` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_id` (`user_id`),
  KEY `fk_plan_id` (`plan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2305 DEFAULT CHARSET=latin1;

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

-- ----------------------------
--  Procedure structure for `getApplicableRow`
-- ----------------------------
DROP PROCEDURE IF EXISTS `getApplicableRow`;
delimiter ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `getApplicableRow`(username varchar (255),now datetime, with_data_limit boolean,less_then_this_id integer)
    READS SQL DATA
BEGIN

if now is null THEN 
  SET now = now();
END IF;

SET @now = now;
SET @day = LOWER(DATE_FORMAT(@now,"%a"));
SET @date = CONCAT("d",DATE_FORMAT(@now,"%d"));
SET @current_time = TIME(@now);
SET @today = DATE(@now);
SET @username = username;

SET @t_applicable_row_id =null;
SET @t_applicable_row_name = null;
SET @t_net_data_limit = null;
SET @t_last_dl_limit=null;
SET @t_last_ul_limit=null;
SET @t_last_accounting_dl_ratio=null;
SET @t_last_accounting_ul_ratio=null;
SET @t_download_data_consumed=null;
SET @t_upload_data_consumed=null;
SET @t_download_limit=null;
SET @t_upload_limit=null;
SET @t_fup_download_limit=null;
SET @t_fup_upload_limit=null;
SET @t_accounting_download_ratio=null;
SET @t_accounting_upload_ratio=null;
SET @t_burst_dl_limit=null;
SET @t_burst_ul_limit=null;
SET @t_burst_threshold_dl_limit=null;
SET @t_burst_threshold_ul_limit=null;
SET @t_burst_dl_time=null;
SET @t_burst_ul_time=null;
SET @t_priority=null;
SET @t_time_limit=null;
SET @t_time_consumed=null;
SET @t_treat_fup_as_dl_for_last_limit_row=null;
SET @t_SessionInputOctate =null;
SET @t_SessionOutputOctate=null;
SET @t_SessionTime=null;


SELECT 
  isp_user_plan_and_topup.id id,
  isp_user_plan_and_topup.remark row_name,
  data_limit + carry_data AS net_data_limit,
  u.last_dl_limit last_dl_limit,
  u.last_ul_limit last_ul_limit,
  u.last_accounting_dl_ratio,
  u.last_accounting_ul_ratio,
  isp_user_plan_and_topup.download_data_consumed,
  isp_user_plan_and_topup.upload_data_consumed,
  isp_user_plan_and_topup.download_limit,
  isp_user_plan_and_topup.upload_limit,
  isp_user_plan_and_topup.fup_download_limit,
  isp_user_plan_and_topup.fup_upload_limit,
  isp_user_plan_and_topup.accounting_download_ratio,
  isp_user_plan_and_topup.accounting_upload_ratio,
  isp_user_plan_and_topup.burst_dl_limit,
  isp_user_plan_and_topup.burst_ul_limit,
  isp_user_plan_and_topup.burst_threshold_dl_limit,
  isp_user_plan_and_topup.burst_threshold_ul_limit,
  isp_user_plan_and_topup.burst_dl_time,
  isp_user_plan_and_topup.burst_ul_time,
  isp_user_plan_and_topup.priority,
  isp_user_plan_and_topup.time_limit,
  isp_user_plan_and_topup.time_consumed,
  `treat_fup_as_dl_for_last_limit_row`,
  (IFNULL( isp_user_plan_and_topup.session_download_data_consumed , 0 ) - IFNULL( isp_user_plan_and_topup.session_download_data_consumed_on_reset , 0 )) SessionInputOctets ,
  (IFNULL( isp_user_plan_and_topup.session_upload_data_consumed, 0 ) - IFNULL( isp_user_plan_and_topup.session_upload_data_consumed_on_reset, 0 )) SessionOutputOctets ,
  IFNULL( isp_user_plan_and_topup.session_time_consumed, 0 ) SessionTime
  into @t_applicable_row_id,@t_applicable_row_name, @t_net_data_limit, @t_last_dl_limit, @t_last_ul_limit, @t_last_accounting_dl_ratio, @t_last_accounting_ul_ratio,@t_download_data_consumed, @t_upload_data_consumed, @t_download_limit, @t_upload_limit, @t_fup_download_limit, @t_fup_upload_limit, @t_accounting_download_ratio, @t_accounting_upload_ratio, @t_burst_dl_limit, @t_burst_ul_limit, @t_burst_threshold_dl_limit, @t_burst_threshold_ul_limit, @t_burst_dl_time, @t_burst_ul_time, @t_priority, @t_time_limit, @t_time_consumed, @t_treat_fup_as_dl_for_last_limit_row,@t_SessionInputOctate, @t_SessionOutputOctate, @t_SessionTime
FROM
  isp_user_plan_and_topup 
JOIN
  isp_user u on isp_user_plan_and_topup.user_id=u.customer_id
WHERE
            (
              (
                (
                  CAST(@current_time AS time) BETWEEN `start_time` AND `end_time` 
                  OR 
                  (
                    NOT CAST(@current_time AS time) BETWEEN `end_time` AND `start_time` 
                    AND `start_time` > `end_time`
                  )
                ) 
                AND
                (is_expired=0 or is_expired is null)
              )
              OR
              (
                `start_time` is null
              )
              OR (`start_time`='00:00:00' and `end_time`='00:00:00')
            )
            AND
            (
              @now >= start_date
              AND
              @now <= expire_date
            )
            AND
              (is_expired=0 or is_expired is null)

            AND
            `user_id`= (SELECT customer_id from isp_user where radius_username = @username and is_active = 1)
            AND (
              (IF('sun'=@day,1,0)=1 AND sun = 1) OR
              (IF('mon'=@day,1,0)=1 AND mon = 1) OR
              (IF('tue'=@day,1,0)=1 AND tue = 1) OR
              (IF('wed'=@day,1,0)=1 AND wed = 1) OR
              (IF('thu'=@day,1,0)=1 AND thu = 1) OR
              (IF('fri'=@day,1,0)=1 AND fri = 1) OR
              (IF('sat'=@day,1,0)=1 AND sat = 1)
            )
            AND (
              (IF('d01'=@date,1,0)=1 AND d01 = 1) OR
              (IF('d02'=@date,1,0)=1 AND d02 = 1) OR 
              (IF('d03'=@date,1,0)=1 AND d03 = 1) OR 
              (IF('d04'=@date,1,0)=1 AND d04 = 1) OR 
              (IF('d05'=@date,1,0)=1 AND d05 = 1) OR 
              (IF('d06'=@date,1,0)=1 AND d06 = 1) OR 
              (IF('d07'=@date,1,0)=1 AND d07 = 1) OR 
              (IF('d08'=@date,1,0)=1 AND d08 = 1) OR 
              (IF('d09'=@date,1,0)=1 AND d09 = 1) OR 
              (IF('d10'=@date,1,0)=1 AND d10 = 1) OR 
              (IF('d11'=@date,1,0)=1 AND d11 = 1) OR 
              (IF('d12'=@date,1,0)=1 AND d12 = 1) OR 
              (IF('d13'=@date,1,0)=1 AND d13 = 1) OR 
              (IF('d14'=@date,1,0)=1 AND d14 = 1) OR 
              (IF('d15'=@date,1,0)=1 AND d15 = 1) OR 
              (IF('d16'=@date,1,0)=1 AND d16 = 1) OR 
              (IF('d17'=@date,1,0)=1 AND d17 = 1) OR 
              (IF('d18'=@date,1,0)=1 AND d18 = 1) OR 
              (IF('d19'=@date,1,0)=1 AND d19 = 1) OR 
              (IF('d20'=@date,1,0)=1 AND d20 = 1) OR 
              (IF('d21'=@date,1,0)=1 AND d21 = 1) OR 
              (IF('d22'=@date,1,0)=1 AND d22 = 1) OR 
              (IF('d23'=@date,1,0)=1 AND d23 = 1) OR 
              (IF('d24'=@date,1,0)=1 AND d24 = 1) OR 
              (IF('d25'=@date,1,0)=1 AND d25 = 1) OR 
              (IF('d26'=@date,1,0)=1 AND d26 = 1) OR
              (IF('d27'=@date,1,0)=1 AND d27 = 1) OR 
              (IF('d28'=@date,1,0)=1 AND d28 = 1) OR 
              (IF('d29'=@date,1,0)=1 AND d29 = 1) OR 
              (IF('d30'=@date,1,0)=1 AND d30 = 1) OR 
              (IF('d31'=@date,1,0)=1 AND d31 = 1) 

            )
            AND(
              with_data_limit = false OR 
              (
                data_limit is not null AND data_limit >0
              )
            )
            AND(
              less_then_this_id is null OR
              (
                isp_user_plan_and_topup.id < less_then_this_id
              )
            )
            order by is_topup desc, isp_user_plan_and_topup.id desc
            limit 1;

#SELECT @t_applicable_row_id;

END
 ;;
delimiter ;

-- ----------------------------
--  Procedure structure for `_b4_read_write_getApplicableRow`
-- ----------------------------
DROP PROCEDURE IF EXISTS `_b4_read_write_getApplicableRow`;
delimiter ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `_b4_read_write_getApplicableRow`(username varchar (255),now datetime, with_data_limit boolean,less_then_this_id integer)
    READS SQL DATA
BEGIN

if now is null THEN 
  SET now = now();
END IF;

SET @now = now;
SET @day = LOWER(DATE_FORMAT(@now,"%a"));
SET @date = CONCAT("d",DATE_FORMAT(@now,"%d"));
SET @current_time = TIME(@now);
SET @today = DATE(@now);
SET @username = username;

SET @t_applicable_row_id =null;
SET @t_applicable_row_name = null;
SET @t_net_data_limit = null;
SET @t_last_dl_limit=null;
SET @t_last_ul_limit=null;
SET @t_last_accounting_dl_ratio=null;
SET @t_last_accounting_ul_ratio=null;
SET @t_download_data_consumed=null;
SET @t_upload_data_consumed=null;
SET @t_download_limit=null;
SET @t_upload_limit=null;
SET @t_fup_download_limit=null;
SET @t_fup_upload_limit=null;
SET @t_accounting_download_ratio=null;
SET @t_accounting_upload_ratio=null;
SET @t_burst_dl_limit=null;
SET @t_burst_ul_limit=null;
SET @t_burst_threshold_dl_limit=null;
SET @t_burst_threshold_ul_limit=null;
SET @t_burst_dl_time=null;
SET @t_burst_ul_time=null;
SET @t_priority=null;
SET @t_time_limit=null;
SET @t_time_consumed=null;
SET @t_treat_fup_as_dl_for_last_limit_row=null;
SET @t_SessionInputOctate =null;
SET @t_SessionOutputOctate=null;
SET @t_SessionTime=null;


SELECT 
  isp_user_plan_and_topup.id id,
  isp_user_plan_and_topup.remark row_name,
  data_limit + carry_data AS net_data_limit,
  u.last_dl_limit last_dl_limit,
  u.last_ul_limit last_ul_limit,
  u.last_accounting_dl_ratio,
  u.last_accounting_ul_ratio,
  isp_user_plan_and_topup.download_data_consumed,
  isp_user_plan_and_topup.upload_data_consumed,
  isp_user_plan_and_topup.download_limit,
  isp_user_plan_and_topup.upload_limit,
  isp_user_plan_and_topup.fup_download_limit,
  isp_user_plan_and_topup.fup_upload_limit,
  isp_user_plan_and_topup.accounting_download_ratio,
  isp_user_plan_and_topup.accounting_upload_ratio,
  isp_user_plan_and_topup.burst_dl_limit,
  isp_user_plan_and_topup.burst_ul_limit,
  isp_user_plan_and_topup.burst_threshold_dl_limit,
  isp_user_plan_and_topup.burst_threshold_ul_limit,
  isp_user_plan_and_topup.burst_dl_time,
  isp_user_plan_and_topup.burst_ul_time,
  isp_user_plan_and_topup.priority,
  isp_user_plan_and_topup.time_limit,
  isp_user_plan_and_topup.time_consumed,
  `treat_fup_as_dl_for_last_limit_row`,
  IFNULL( isp_user_plan_and_topup.session_download_data_consumed , 0 ) SessionInputOctets ,
  IFNULL( isp_user_plan_and_topup.session_upload_data_consumed, 0 ) SessionOutputOctets ,
  IFNULL( isp_user_plan_and_topup.session_time_consumed, 0 ) SessionTime
  into @t_applicable_row_id,@t_applicable_row_name, @t_net_data_limit, @t_last_dl_limit, @t_last_ul_limit, @t_last_accounting_dl_ratio, @t_last_accounting_ul_ratio,@t_download_data_consumed, @t_upload_data_consumed, @t_download_limit, @t_upload_limit, @t_fup_download_limit, @t_fup_upload_limit, @t_accounting_download_ratio, @t_accounting_upload_ratio, @t_burst_dl_limit, @t_burst_ul_limit, @t_burst_threshold_dl_limit, @t_burst_threshold_ul_limit, @t_burst_dl_time, @t_burst_ul_time, @t_priority, @t_time_limit, @t_time_consumed, @t_treat_fup_as_dl_for_last_limit_row,@t_SessionInputOctate, @t_SessionOutputOctate, @t_SessionTime
FROM
  isp_user_plan_and_topup 
JOIN
  isp_user u on isp_user_plan_and_topup.user_id=u.customer_id
WHERE
            (
              (
                (
                  CAST(@current_time AS time) BETWEEN `start_time` AND `end_time` 
                  OR 
                  (
                    NOT CAST(@current_time AS time) BETWEEN `end_time` AND `start_time` 
                    AND `start_time` > `end_time`
                  )
                ) 
                AND
                (is_expired=0 or is_expired is null)
              )
              OR
              (
                `start_time` is null
              )
              OR (`start_time`='00:00:00' and `end_time`='00:00:00')
            )
            AND
            (
              @now >= start_date
              AND
              @now <= end_date
            )
            AND
              (is_expired=0 or is_expired is null)

            AND
            `user_id`= (SELECT customer_id from isp_user where radius_username = @username)
            AND (
              (IF('sun'=@day,1,0)=1 AND sun = 1) OR
              (IF('mon'=@day,1,0)=1 AND mon = 1) OR
              (IF('tue'=@day,1,0)=1 AND tue = 1) OR
              (IF('wed'=@day,1,0)=1 AND wed = 1) OR
              (IF('thu'=@day,1,0)=1 AND thu = 1) OR
              (IF('fri'=@day,1,0)=1 AND fri = 1) OR
              (IF('sat'=@day,1,0)=1 AND sat = 1)
            )
            AND (
              (IF('d01'=@date,1,0)=1 AND d01 = 1) OR
              (IF('d02'=@date,1,0)=1 AND d02 = 1) OR 
              (IF('d03'=@date,1,0)=1 AND d03 = 1) OR 
              (IF('d04'=@date,1,0)=1 AND d04 = 1) OR 
              (IF('d05'=@date,1,0)=1 AND d05 = 1) OR 
              (IF('d06'=@date,1,0)=1 AND d06 = 1) OR 
              (IF('d07'=@date,1,0)=1 AND d07 = 1) OR 
              (IF('d08'=@date,1,0)=1 AND d08 = 1) OR 
              (IF('d09'=@date,1,0)=1 AND d09 = 1) OR 
              (IF('d10'=@date,1,0)=1 AND d10 = 1) OR 
              (IF('d11'=@date,1,0)=1 AND d11 = 1) OR 
              (IF('d12'=@date,1,0)=1 AND d12 = 1) OR 
              (IF('d13'=@date,1,0)=1 AND d13 = 1) OR 
              (IF('d14'=@date,1,0)=1 AND d14 = 1) OR 
              (IF('d15'=@date,1,0)=1 AND d15 = 1) OR 
              (IF('d16'=@date,1,0)=1 AND d16 = 1) OR 
              (IF('d17'=@date,1,0)=1 AND d17 = 1) OR 
              (IF('d18'=@date,1,0)=1 AND d18 = 1) OR 
              (IF('d19'=@date,1,0)=1 AND d19 = 1) OR 
              (IF('d20'=@date,1,0)=1 AND d20 = 1) OR 
              (IF('d21'=@date,1,0)=1 AND d21 = 1) OR 
              (IF('d22'=@date,1,0)=1 AND d22 = 1) OR 
              (IF('d23'=@date,1,0)=1 AND d23 = 1) OR 
              (IF('d24'=@date,1,0)=1 AND d24 = 1) OR 
              (IF('d25'=@date,1,0)=1 AND d25 = 1) OR 
              (IF('d26'=@date,1,0)=1 AND d26 = 1) OR
              (IF('d27'=@date,1,0)=1 AND d27 = 1) OR 
              (IF('d28'=@date,1,0)=1 AND d28 = 1) OR 
              (IF('d29'=@date,1,0)=1 AND d29 = 1) OR 
              (IF('d30'=@date,1,0)=1 AND d30 = 1) OR 
              (IF('d31'=@date,1,0)=1 AND d31 = 1) 

            )
            AND(
              with_data_limit = false OR 
              (
                data_limit is not null AND data_limit >0
              )
            )
            AND(
              less_then_this_id is null OR
              (
                isp_user_plan_and_topup.id < less_then_this_id
              )
            )
            order by is_topup desc, isp_user_plan_and_topup.id desc
            limit 1;

#SELECT @t_applicable_row_id;

END
 ;;
delimiter ;

-- ----------------------------
--  Function structure for `checkAuthentication`
-- ----------------------------
DROP FUNCTION IF EXISTS `checkAuthentication`;
delimiter ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `checkAuthentication`(now datetime, username varchar(255)) RETURNS text CHARSET utf8mb4
    MODIFIES SQL DATA
P:BEGIN

if now is null THEN 
  SET now = now();
END IF;

CALL getApplicableRow(username,now,false,null);

SET @user_last_dl_limit = @t_last_dl_limit;
SET @user_last_ul_limit = @t_last_ul_limit;

SET @user_last_accounting_dl_ratio = @t_last_accounting_dl_ratio;
SET @user_last_accounting_ul_ratio = @t_last_accounting_ul_ratio;

SET @user_SessionInputOctate = @t_SessionInputOctate;
SET @user_SessionOutputOctate = @t_SessionOutputOctate;
SET @user_SessionTime = @t_SessionTime;

SET @bw_applicable_row_id = @t_applicable_row_id;
SET @bw_applicable_row_name = @t_applicable_row_name;

SET @bw_download_limit = @t_download_limit;
SET @bw_upload_limit = @t_upload_limit;
SET @bw_fup_download_limit = @t_fup_download_limit;
SET @bw_fup_upload_limit = @t_fup_upload_limit;
SET @bw_accounting_download_ratio = @t_accounting_download_ratio;
SET @bw_accounting_upload_ratio = @t_accounting_upload_ratio;

SET @bw_net_data_limit = @t_net_data_limit;
SET @bw_download_data_consumed = @t_download_data_consumed;
SET @bw_upload_data_consumed= @t_upload_data_consumed;

SET @bw_time_limit = @t_time_limit;
SET @bw_time_consumed = @t_time_consumed;
SET @bw_burst_dl_limit = @t_burst_dl_limit;
SET @bw_burst_ul_limit = @t_burst_ul_limit;
SET @bw_burst_threshold_dl_limit= @t_burst_threshold_dl_limit;
SET @bw_burst_threshold_ul_limit= @t_burst_threshold_ul_limit;
SET @bw_burst_dl_time = @t_burst_dl_time;
SET @bw_burst_ul_time = @t_burst_ul_time;
SET @bw_priority = @t_priority;

SET @treat_fup_as_dl_for_last_limit_row = @t_treat_fup_as_dl_for_last_limit_row;

SET @data_applicable_row_id = @t_applicable_row_id;

SET @data_download_limit = @t_download_limit;
SET @data_upload_limit = @t_upload_limit;
SET @data_fup_download_limit = @t_fup_download_limit;
SET @data_fup_upload_limit = @t_fup_upload_limit;

SET @data_net_data_limit = @t_net_data_limit;
SET @data_download_data_consumed = @t_download_data_consumed;
SET @data_upload_data_consumed= @t_upload_data_consumed;

SET @data_time_limit = @t_time_limit;
SET @data_time_consumed = @t_time_consumed;
SET @data_burst_dl_limit = @t_burst_dl_limit;
SET @data_burst_ul_limit = @t_burst_ul_limit;
SET @data_burst_threshold_dl_limit= @t_burst_threshold_dl_limit;
SET @data_burst_threshold_ul_limit= @t_burst_threshold_ul_limit;
SET @data_burst_dl_time = @t_burst_dl_time;
SET @data_burst_ul_time = @t_burst_ul_time;
SET @data_priority = @t_priority;

SET @access= true;

IF @bw_applicable_row_id is null THEN
  SET @access= false;
  RETURN CONCAT(@access,',', 0,',', '0/0',',', 0);
  LEAVE P;
END IF;

IF @bw_net_data_limit is null THEN
  CALL getApplicableRow(username,now,TRUE,null);
  SET @data_applicable_row_id = @t_applicable_row_id;
  SET @data_applicable_row_name = @t_applicable_row_name;

  SET @data_net_data_limit = @t_net_data_limit;
  SET @data_download_data_consumed = @t_download_data_consumed;
  SET @data_upload_data_consumed= @t_upload_data_consumed;
  SET @data_download_limit = @t_download_limit;
  SET @data_upload_limit = @t_upload_limit;
  SET @data_fup_download_limit = @t_fup_download_limit;
  SET @data_fup_upload_limit = @t_fup_upload_limit;
  SET @data_accounting_download_ratio = @t_accounting_download_ratio;
  SET @data_accounting_upload_ratio = @t_accounting_upload_ratio;
  SET @data_time_limit = @t_time_limit;
  SET @data_time_consumed = @t_time_consumed;
  SET @data_burst_dl_limit = @t_burst_dl_limit;
  SET @data_burst_ul_limit = @t_burst_ul_limit;
  SET @data_burst_threshold_dl_limit= @t_burst_threshold_dl_limit;
  SET @data_burst_threshold_ul_limit= @t_burst_threshold_ul_limit;
  SET @data_burst_dl_time = @t_burst_dl_time;
  SET @data_burst_ul_time = @t_burst_ul_time;
  SET @data_priority = @t_priority;
END IF;

SET @fup = false;

IF ( (@data_download_data_consumed + @data_upload_data_consumed + @user_SessionInputOctate + @user_SessionOutputOctate) > @data_net_data_limit) THEN
  SET @fup = true;
  IF @treat_fup_as_dl_for_last_limit_row THEN 
    CALL getApplicableRow(username,now,false,@data_applicable_row_id);
    SET @nxt_data_applicable_row_id = @t_applicable_row_id;
    SET @data_applicable_row_name = @t_applicable_row_name;
    SET @nxt_net_data_limit = @t_net_data_limit;
    SET @nxt_download_data_consumed = @t_download_data_consumed;
    SET @nxt_upload_data_consumed= @t_upload_data_consumed;
    SET @nxt_download_limit = @t_download_limit;
    SET @nxt_upload_limit = @t_upload_limit;
    SET @nxt_fup_download_limit = @t_fup_download_limit;
    SET @nxt_fup_upload_limit = @t_fup_upload_limit;
    SET @nxt_accounting_download_ratio = @t_accounting_download_ratio;
    SET @nxt_accounting_upload_ratio = @t_accounting_upload_ratio;

    IF @nxt_download_data_consumed + @nxt_upload_data_consumed + @user_SessionInputOctate + @user_SessionOutputOctate > @nxt_net_data_limit THEN
      SET @data_download_limit = @nxt_fup_download_limit;
      SET @data_upload_limit = @nxt_fup_upload_limit;
    ELSE
      SET @data_download_limit = @bw_fup_download_limit;
      SET @data_upload_limit = @bw_fup_upload_limit;
    END IF;
  END IF;
END IF;

SET @dl_limit = null;
SET @ul_limit = null;
SET @coa =  false;

IF @fup THEN
  SET @dl_limit = @bw_fup_download_limit;
  SET @ul_limit = @bw_fup_upload_limit;
ELSE
  SET @dl_limit = @bw_download_limit;
  SET @ul_limit = @bw_upload_limit;
END IF;

IF ((@bw_time_consumed  + @user_SessionTime) >= @bw_time_limit AND @bw_time_limit > 0 )THEN 
  SET @coa = true;
END IF;

SET @dl_from_row = "bw";
SET @ul_from_row = "bw";

IF @dl_limit is null THEN
  SET @dl_from_row = "data";
  IF @fup THEN
    SET @dl_limit = @data_fup_download_limit;
  ELSE
    SET @dl_limit = @data_download_limit;
  END IF; 
  
  IF ((@data_time_consumed  + @user_SessionTime) >= @data_time_limit AND @data_time_limit > 0 )  THEN
    SET @coa = true;
  END IF;
END IF;

IF @ul_limit is null THEN
  IF @fup THEN
    SET @ul_limit = @data_fup_upload_limit;
  ELSE
    SET @ul_limit = @data_upload_limit;
  END IF;
  SET @ul_from_row = "data";
  IF ((@data_time_consumed  + @user_SessionTime) >= @data_time_limit AND @data_time_limit > 0 ) THEN 
   SET @coa = true;
  END IF;
END IF;

SET @burst_dl_limit = null;
SET @burst_ul_limit = null;
SET @burst_threshold_dl_limit = null;
SET @burst_threshold_ul_limit = null;
SET @burst_dl_time = null;
SET @burst_ul_time = null;
SET @priority = null;

IF @fup is NULL THEN
  IF dl_from_row = "bw" THEN
    SET @burst_dl_limit = @bw_burst_dl_limit;
    SET @burst_threshold_dl_limit = @bw_burst_threshold_dl_limit;
    SET @burst_dl_time = @bw_burst_dl_time;
  END IF;
  IF dl_from_row = "data" THEN
    SET @burst_dl_limit = @data_burst_dl_limit;
    SET @burst_threshold_dl_limit = @data_burst_threshold_dl_limit;
    SET @burst_dl_time = @data_burst_dl_time;
  END IF;
 
    IF ul_from_row = "bw" THEN
        SET @burst_ul_limit = @bw_burst_ul_limit;
        SET @burst_threshold_ul_limit = @bw_burst_threshold_ul_limit;
        SET @burst_ul_time = @bw_burst_ul_time;
    END IF;
  IF ul_from_row = "data" THEN
      SET @burst_ul_limit = @data_burst_ul_limit;
      SET @burst_threshold_ul_limit = @data_burst_threshold_ul_limit;
      SET @burst_ul_time = @data_burst_ul_time;
  END IF;
  
 SET @priority = @bw_priority;
 IF ( @data_priority > @bw_priority) THEN
  SET @priority = @data_priority;
 END IF;

END IF;


SET @speed_change =false;
SET @accounting_change = false;

IF @dl_limit != @user_last_dl_limit OR @ul_limit != @user_last_ul_limit THEN
  SET @speed_change = true;
        SET @coa = true;
  UPDATE isp_user SET last_dl_limit = @dl_limit, last_ul_limit = @ul_limit WHERE radius_username = username;
END IF;

IF @user_last_accounting_dl_ratio != @bw_accounting_download_ratio OR @user_last_accounting_ul_ratio != @bw_accounting_upload_ratio  THEN
  SET @accounting_change= true;
  SET @coa = true;
  UPDATE isp_user SET last_accounting_dl_ratio = @bw_accounting_download_ratio, last_accounting_ul_ratio = @bw_accounting_upload_ratio WHERE radius_username = username;
END IF;


IF (@dl_limit is null AND @ul_limit is null) OR ( (@data_time_consumed + @user_SessionTime)  > @data_time_limit AND @data_time_limit > 0)  THEN
  SET @access = false;
END IF;

UPDATE isp_user_plan_and_topup set is_effective= 0 where user_id= (SELECT customer_id from isp_user where radius_username = username);

IF @data_applicable_row_id is not null THEN
  UPDATE isp_user_plan_and_topup set is_effective=1 where id=@data_applicable_row_id;
END IF;

SET @burst_string =false;

IF @burst_dl_limit is not null or @burst_dl_limit != "" THEN
  SET @burst_string= CONCAT(@burst_ul_limit,'/',@burst_threshold_dl_limit,' ',@burst_threshold_ul_limit,'/',@burst_dl_time,' ',@burst_ul_time,' ',@priority);
END IF;

RETURN CONCAT(@access,',', @coa,',', IFNULL(@ul_limit,0),'/', IFNULL(@dl_limit,0),',',@burst_string);

END
 ;;
delimiter ;

-- ----------------------------
--  Function structure for `checkAuthenticationReadOnly`
-- ----------------------------
DROP FUNCTION IF EXISTS `checkAuthenticationReadOnly`;
delimiter ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `checkAuthenticationReadOnly`(now datetime, username varchar(255)) RETURNS text CHARSET utf8mb4
    READS SQL DATA
P:BEGIN

if now is null THEN 
  SET now = now();
END IF;

CALL getApplicableRow(username,now,false,null);

SET @user_last_dl_limit = @t_last_dl_limit;
SET @user_last_ul_limit = @t_last_ul_limit;

SET @user_last_accounting_dl_ratio = @t_last_accounting_dl_ratio;
SET @user_last_accounting_ul_ratio = @t_last_accounting_ul_ratio;

SET @user_SessionInputOctate = @t_SessionInputOctate;
SET @user_SessionOutputOctate = @t_SessionOutputOctate;
SET @user_SessionTime = @t_SessionTime;

SET @bw_applicable_row_id = @t_applicable_row_id;
SET @bw_applicable_row_name = @t_applicable_row_name;

SET @bw_download_limit = @t_download_limit;
SET @bw_upload_limit = @t_upload_limit;
SET @bw_fup_download_limit = @t_fup_download_limit;
SET @bw_fup_upload_limit = @t_fup_upload_limit;
SET @bw_accounting_download_ratio = @t_accounting_download_ratio;
SET @bw_accounting_upload_ratio = @t_accounting_upload_ratio;

SET @bw_net_data_limit = @t_net_data_limit;
SET @bw_download_data_consumed = @t_download_data_consumed;
SET @bw_upload_data_consumed= @t_upload_data_consumed;

SET @bw_time_limit = @t_time_limit;
SET @bw_time_consumed = @t_time_consumed;
SET @bw_burst_dl_limit = @t_burst_dl_limit;
SET @bw_burst_ul_limit = @t_burst_ul_limit;
SET @bw_burst_threshold_dl_limit= @t_burst_threshold_dl_limit;
SET @bw_burst_threshold_ul_limit= @t_burst_threshold_ul_limit;
SET @bw_burst_dl_time = @t_burst_dl_time;
SET @bw_burst_ul_time = @t_burst_ul_time;
SET @bw_priority = @t_priority;

SET @treat_fup_as_dl_for_last_limit_row = @t_treat_fup_as_dl_for_last_limit_row;

SET @data_applicable_row_id = @t_applicable_row_id;

SET @data_download_limit = @t_download_limit;
SET @data_upload_limit = @t_upload_limit;
SET @data_fup_download_limit = @t_fup_download_limit;
SET @data_fup_upload_limit = @t_fup_upload_limit;

SET @data_net_data_limit = @t_net_data_limit;
SET @data_download_data_consumed = @t_download_data_consumed;
SET @data_upload_data_consumed= @t_upload_data_consumed;

SET @data_time_limit = @t_time_limit;
SET @data_time_consumed = @t_time_consumed;
SET @data_burst_dl_limit = @t_burst_dl_limit;
SET @data_burst_ul_limit = @t_burst_ul_limit;
SET @data_burst_threshold_dl_limit= @t_burst_threshold_dl_limit;
SET @data_burst_threshold_ul_limit= @t_burst_threshold_ul_limit;
SET @data_burst_dl_time = @t_burst_dl_time;
SET @data_burst_ul_time = @t_burst_ul_time;
SET @data_priority = @t_priority;

SET @access= true;

IF @bw_applicable_row_id is null THEN
  SET @access= false;
  RETURN CONCAT(@access,',', 0,',', '0/0',',', 0);
  LEAVE P;
END IF;

IF @bw_net_data_limit is null THEN
  CALL getApplicableRow(username,now,TRUE,null);
  SET @data_applicable_row_id = @t_applicable_row_id;
  SET @data_applicable_row_name = @t_applicable_row_name;

  SET @data_net_data_limit = @t_net_data_limit;
  SET @data_download_data_consumed = @t_download_data_consumed;
  SET @data_upload_data_consumed= @t_upload_data_consumed;
  SET @data_download_limit = @t_download_limit;
  SET @data_upload_limit = @t_upload_limit;
  SET @data_fup_download_limit = @t_fup_download_limit;
  SET @data_fup_upload_limit = @t_fup_upload_limit;
  SET @data_accounting_download_ratio = @t_accounting_download_ratio;
  SET @data_accounting_upload_ratio = @t_accounting_upload_ratio;
  SET @data_time_limit = @t_time_limit;
  SET @data_time_consumed = @t_time_consumed;
  SET @data_burst_dl_limit = @t_burst_dl_limit;
  SET @data_burst_ul_limit = @t_burst_ul_limit;
  SET @data_burst_threshold_dl_limit= @t_burst_threshold_dl_limit;
  SET @data_burst_threshold_ul_limit= @t_burst_threshold_ul_limit;
  SET @data_burst_dl_time = @t_burst_dl_time;
  SET @data_burst_ul_time = @t_burst_ul_time;
  SET @data_priority = @t_priority;
END IF;

SET @fup = false;

IF ( (@data_download_data_consumed + @data_upload_data_consumed + @user_SessionInputOctate + @user_SessionOutputOctate) > @data_net_data_limit) THEN
  SET @fup = true;
  IF @treat_fup_as_dl_for_last_limit_row THEN 
    CALL getApplicableRow(username,now,false,@data_applicable_row_id);
    SET @nxt_data_applicable_row_id = @t_applicable_row_id;
    SET @data_applicable_row_name = @t_applicable_row_name;
    SET @nxt_net_data_limit = @t_net_data_limit;
    SET @nxt_download_data_consumed = @t_download_data_consumed;
    SET @nxt_upload_data_consumed= @t_upload_data_consumed;
    SET @nxt_download_limit = @t_download_limit;
    SET @nxt_upload_limit = @t_upload_limit;
    SET @nxt_fup_download_limit = @t_fup_download_limit;
    SET @nxt_fup_upload_limit = @t_fup_upload_limit;
    SET @nxt_accounting_download_ratio = @t_accounting_download_ratio;
    SET @nxt_accounting_upload_ratio = @t_accounting_upload_ratio;

    IF @nxt_download_data_consumed + @nxt_upload_data_consumed + @user_SessionInputOctate + @user_SessionOutputOctate > @nxt_net_data_limit THEN
      SET @data_download_limit = @nxt_fup_download_limit;
      SET @data_upload_limit = @nxt_fup_upload_limit;
    ELSE
      SET @data_download_limit = @bw_fup_download_limit;
      SET @data_upload_limit = @bw_fup_upload_limit;
    END IF;
  END IF;
END IF;

SET @dl_limit = null;
SET @ul_limit = null;
SET @coa =  false;

IF @fup THEN
  SET @dl_limit = @bw_fup_download_limit;
  SET @ul_limit = @bw_fup_upload_limit;
ELSE
  SET @dl_limit = @bw_download_limit;
  SET @ul_limit = @bw_upload_limit;
END IF;

IF ((@bw_time_consumed  + @user_SessionTime) >= @bw_time_limit AND @bw_time_limit > 0 )THEN 
  SET @coa = true;
END IF;

SET @dl_from_row = "bw";
SET @ul_from_row = "bw";

IF @dl_limit is null THEN
  SET @dl_from_row = "data";
  IF @fup THEN
    SET @dl_limit = @data_fup_download_limit;
  ELSE
    SET @dl_limit = @data_download_limit;
  END IF; 
  
  IF ((@data_time_consumed  + @user_SessionTime) >= @data_time_limit AND @data_time_limit > 0 )  THEN
    SET @coa = true;
  END IF;
END IF;

IF @ul_limit is null THEN
  IF @fup THEN
    SET @ul_limit = @data_fup_upload_limit;
  ELSE
    SET @ul_limit = @data_upload_limit;
  END IF;
  SET @ul_from_row = "data";
  IF ((@data_time_consumed  + @user_SessionTime) >= @data_time_limit AND @data_time_limit > 0 ) THEN 
   SET @coa = true;
  END IF;
END IF;

SET @burst_dl_limit = null;
SET @burst_ul_limit = null;
SET @burst_threshold_dl_limit = null;
SET @burst_threshold_ul_limit = null;
SET @burst_dl_time = null;
SET @burst_ul_time = null;
SET @priority = null;

IF @fup is NULL THEN
  IF dl_from_row = "bw" THEN
    SET @burst_dl_limit = @bw_burst_dl_limit;
    SET @burst_threshold_dl_limit = @bw_burst_threshold_dl_limit;
    SET @burst_dl_time = @bw_burst_dl_time;
  END IF;
  IF dl_from_row = "data" THEN
    SET @burst_dl_limit = @data_burst_dl_limit;
    SET @burst_threshold_dl_limit = @data_burst_threshold_dl_limit;
    SET @burst_dl_time = @data_burst_dl_time;
  END IF;
 
    IF ul_from_row = "bw" THEN
        SET @burst_ul_limit = @bw_burst_ul_limit;
        SET @burst_threshold_ul_limit = @bw_burst_threshold_ul_limit;
        SET @burst_ul_time = @bw_burst_ul_time;
    END IF;
  IF ul_from_row = "data" THEN
      SET @burst_ul_limit = @data_burst_ul_limit;
      SET @burst_threshold_ul_limit = @data_burst_threshold_ul_limit;
      SET @burst_ul_time = @data_burst_ul_time;
  END IF;
  
 SET @priority = @bw_priority;
 IF ( @data_priority > @bw_priority) THEN
  SET @priority = @data_priority;
 END IF;

END IF;


SET @speed_change =false;
SET @accounting_change = false;

IF @dl_limit != @user_last_dl_limit OR @ul_limit != @user_last_ul_limit THEN
  SET @speed_change = true;
        SET @coa = true;
  -- UPDATE isp_user SET last_dl_limit = @dl_limit, last_ul_limit = @ul_limit WHERE radius_username = username;
END IF;

IF @user_last_accounting_dl_ratio != @bw_accounting_download_ratio OR @user_last_accounting_ul_ratio != @bw_accounting_upload_ratio  THEN
  SET @accounting_change= true;
  SET @coa = true;
  -- UPDATE isp_user SET last_accounting_dl_ratio = @bw_accounting_download_ratio, last_accounting_ul_ratio = @bw_accounting_upload_ratio WHERE radius_username = username;
END IF;


IF (@dl_limit is null AND @ul_limit is null) OR ( (@data_time_consumed + @user_SessionTime)  > @data_time_limit AND @data_time_limit > 0)  THEN
  SET @access = false;
END IF;

-- UPDATE isp_user_plan_and_topup set is_effective= 0 where user_id= (SELECT customer_id from isp_user where radius_username = username);

-- IF @data_applicable_row_id is not null THEN
  -- UPDATE isp_user_plan_and_topup set is_effective=1 where id=@data_applicable_row_id;
-- END IF;

SET @burst_string =false;

IF @burst_dl_limit is not null or @burst_dl_limit != "" THEN
  SET @burst_string= CONCAT(@burst_ul_limit,'/',@burst_threshold_dl_limit,' ',@burst_threshold_ul_limit,'/',@burst_dl_time,' ',@burst_ul_time,' ',@priority);
END IF;

RETURN CONCAT(@access,',', @coa,',', IFNULL(@ul_limit,0),'/', IFNULL(@dl_limit,0),',',@burst_string);

END
 ;;
delimiter ;

-- ----------------------------
--  Function structure for `sessionClose`
-- ----------------------------
DROP FUNCTION IF EXISTS `sessionClose`;
delimiter ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `sessionClose`(username varchar(255)) RETURNS text CHARSET utf8mb4
    MODIFIES SQL DATA
BEGIN
  UPDATE 
    isp_user_plan_and_topup   
  SET
    download_data_consumed = IFNULL(download_data_consumed,0) + IFNULL(session_download_data_consumed,0) - IFNULL(session_download_data_consumed_on_reset,0),
    upload_data_consumed = IFNULL(upload_data_consumed,0) + IFNULL(session_upload_data_consumed,0) - IFNULL(session_upload_data_consumed_on_reset,0),
    time_consumed = IFNULL(time_consumed,0) + IFNULL(session_time_consumed,0),
    session_download_data_consumed=0,
    session_upload_data_consumed=0,
    session_download_data_consumed_on_reset = 0,
    session_upload_data_consumed_on_reset = 0,
    session_time_consumed=0
  WHERE
    user_id = (SELECT customer_id from isp_user where radius_username = username);
  RETURN "done";
END
 ;;
delimiter ;

-- ----------------------------
--  Function structure for `updateAccountingData`
-- ----------------------------
DROP FUNCTION IF EXISTS `updateAccountingData`;
delimiter ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `updateAccountingData`(dl_data bigint, ul_data bigint,input_gigawords int, output_gigawords int, now datetime, username varchar(255), session_time_consumed bigint) RETURNS text CHARSET utf8mb4
    MODIFIES SQL DATA
BEGIN

  IF(now is NULL) THEN 
    SET now = now();
  END IF;

  SELECT IFNULL(last_accounting_dl_ratio,100), IFNULL(last_accounting_ul_ratio,100) into @last_accounting_dl_ratio, @last_accounting_ul_ratio FROM isp_user WHERE radius_username = username;

  UPDATE 
    isp_user_plan_and_topup 
  SET 
    isp_user_plan_and_topup.session_download_data_consumed = (((input_gigawords << 32 | dl_data)*@last_accounting_dl_ratio) /100) ,
    isp_user_plan_and_topup.session_upload_data_consumed = (((output_gigawords << 32 | ul_data)*@last_accounting_ul_ratio) /100),
    isp_user_plan_and_topup.session_time_consumed = session_time_consumed
  WHERE 
    is_effective = 1 AND user_id = (SELECT customer_id from isp_user where radius_username = username)
  ;

  select checkAuthentication(now, username) into @temp;
  RETURN @temp;

END
 ;;
delimiter ;

-- ----------------------------
--  Function structure for `_b4_read_write_checkAuthentication`
-- ----------------------------
DROP FUNCTION IF EXISTS `_b4_read_write_checkAuthentication`;
delimiter ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `_b4_read_write_checkAuthentication`(now datetime, username varchar(255)) RETURNS text CHARSET utf8mb4
    MODIFIES SQL DATA
    DETERMINISTIC
P:BEGIN

if now is null THEN 
  SET now = now();
END IF;

CALL getApplicableRow(username,now,false,null);

SET @user_last_dl_limit = @t_last_dl_limit;
SET @user_last_ul_limit = @t_last_ul_limit;

SET @user_last_accounting_dl_ratio = @t_last_accounting_dl_ratio;
SET @user_last_accounting_ul_ratio = @t_last_accounting_ul_ratio;

SET @user_SessionInputOctate = @t_SessionInputOctate;
SET @user_SessionOutputOctate = @t_SessionOutputOctate;
SET @user_SessionTime = @t_SessionTime;

SET @bw_applicable_row_id = @t_applicable_row_id;
SET @bw_applicable_row_name = @t_applicable_row_name;

SET @bw_download_limit = @t_download_limit;
SET @bw_upload_limit = @t_upload_limit;
SET @bw_fup_download_limit = @t_fup_download_limit;
SET @bw_fup_upload_limit = @t_fup_upload_limit;
SET @bw_accounting_download_ratio = @t_accounting_download_ratio;
SET @bw_accounting_upload_ratio = @t_accounting_upload_ratio;

SET @bw_net_data_limit = @t_net_data_limit;
SET @bw_download_data_consumed = @t_download_data_consumed;
SET @bw_upload_data_consumed= @t_upload_data_consumed;

SET @bw_time_limit = @t_time_limit;
SET @bw_time_consumed = @t_time_consumed;
SET @bw_burst_dl_limit = @t_burst_dl_limit;
SET @bw_burst_ul_limit = @t_burst_ul_limit;
SET @bw_burst_threshold_dl_limit= @t_burst_threshold_dl_limit;
SET @bw_burst_threshold_ul_limit= @t_burst_threshold_ul_limit;
SET @bw_burst_dl_time = @t_burst_dl_time;
SET @bw_burst_ul_time = @t_burst_ul_time;
SET @bw_priority = @t_priority;

SET @treat_fup_as_dl_for_last_limit_row = @t_treat_fup_as_dl_for_last_limit_row;

SET @data_applicable_row_id = @t_applicable_row_id;

SET @data_download_limit = @t_download_limit;
SET @data_upload_limit = @t_upload_limit;
SET @data_fup_download_limit = @t_fup_download_limit;
SET @data_fup_upload_limit = @t_fup_upload_limit;

SET @data_net_data_limit = @t_net_data_limit;
SET @data_download_data_consumed = @t_download_data_consumed;
SET @data_upload_data_consumed= @t_upload_data_consumed;

SET @data_time_limit = @t_time_limit;
SET @data_time_consumed = @t_time_consumed;
SET @data_burst_dl_limit = @t_burst_dl_limit;
SET @data_burst_ul_limit = @t_burst_ul_limit;
SET @data_burst_threshold_dl_limit= @t_burst_threshold_dl_limit;
SET @data_burst_threshold_ul_limit= @t_burst_threshold_ul_limit;
SET @data_burst_dl_time = @t_burst_dl_time;
SET @data_burst_ul_time = @t_burst_ul_time;
SET @data_priority = @t_priority;

SET @access= true;

IF @bw_applicable_row_id is null THEN
  SET @access= false;
  RETURN CONCAT(@access,',', 0,',', '0/0',',', 0);
  LEAVE P;
END IF;

IF @bw_net_data_limit is null THEN
  CALL getApplicableRow(username,now,TRUE,null);
  SET @data_applicable_row_id = @t_applicable_row_id;
  SET @data_applicable_row_name = @t_applicable_row_name;

  SET @data_net_data_limit = @t_net_data_limit;
  SET @data_download_data_consumed = @t_download_data_consumed;
  SET @data_upload_data_consumed= @t_upload_data_consumed;
  SET @data_download_limit = @t_download_limit;
  SET @data_upload_limit = @t_upload_limit;
  SET @data_fup_download_limit = @t_fup_download_limit;
  SET @data_fup_upload_limit = @t_fup_upload_limit;
  SET @data_accounting_download_ratio = @t_accounting_download_ratio;
  SET @data_accounting_upload_ratio = @t_accounting_upload_ratio;
  SET @data_time_limit = @t_time_limit;
  SET @data_time_consumed = @t_time_consumed;
  SET @data_burst_dl_limit = @t_burst_dl_limit;
  SET @data_burst_ul_limit = @t_burst_ul_limit;
  SET @data_burst_threshold_dl_limit= @t_burst_threshold_dl_limit;
  SET @data_burst_threshold_ul_limit= @t_burst_threshold_ul_limit;
  SET @data_burst_dl_time = @t_burst_dl_time;
  SET @data_burst_ul_time = @t_burst_ul_time;
  SET @data_priority = @t_priority;
END IF;

SET @fup = false;

IF ( (@data_download_data_consumed + @data_upload_data_consumed + @user_SessionInputOctate + @user_SessionOutputOctate) > @data_net_data_limit) THEN
  SET @fup = true;
  IF @treat_fup_as_dl_for_last_limit_row THEN 
    CALL getApplicableRow(username,now,false,@data_applicable_row_id);
    SET @nxt_data_applicable_row_id = @t_applicable_row_id;
    SET @data_applicable_row_name = @t_applicable_row_name;
    SET @nxt_net_data_limit = @t_net_data_limit;
    SET @nxt_download_data_consumed = @t_download_data_consumed;
    SET @nxt_upload_data_consumed= @t_upload_data_consumed;
    SET @nxt_download_limit = @t_download_limit;
    SET @nxt_upload_limit = @t_upload_limit;
    SET @nxt_fup_download_limit = @t_fup_download_limit;
    SET @nxt_fup_upload_limit = @t_fup_upload_limit;
    SET @nxt_accounting_download_ratio = @t_accounting_download_ratio;
    SET @nxt_accounting_upload_ratio = @t_accounting_upload_ratio;

    IF @nxt_download_data_consumed + @nxt_upload_data_consumed + @user_SessionInputOctate + @user_SessionOutputOctate > @nxt_net_data_limit THEN
      SET @data_download_limit = @nxt_fup_download_limit;
      SET @data_upload_limit = @nxt_fup_upload_limit;
    ELSE
      SET @data_download_limit = @bw_fup_download_limit;
      SET @data_upload_limit = @bw_fup_upload_limit;
    END IF;
  END IF;
END IF;

SET @dl_limit = null;
SET @ul_limit = null;
SET @coa =  false;

IF @fup THEN
  SET @dl_limit = @bw_fup_download_limit;
  SET @ul_limit = @bw_fup_upload_limit;
ELSE
  SET @dl_limit = @bw_download_limit;
  SET @ul_limit = @bw_upload_limit;
END IF;

IF ((@bw_time_consumed  + @user_SessionTime) >= @bw_time_limit AND @bw_time_limit > 0 )THEN 
  SET @coa = true;
END IF;

SET @dl_from_row = "bw";
SET @ul_from_row = "bw";

IF @dl_limit is null THEN
  SET @dl_from_row = "data";
  IF @fup THEN
    SET @dl_limit = @data_fup_download_limit;
  ELSE
    SET @dl_limit = @data_download_limit;
  END IF; 
  
  IF ((@data_time_consumed  + @user_SessionTime) >= @data_time_limit AND @data_time_limit > 0 )  THEN
    SET @coa = true;
  END IF;
END IF;

IF @ul_limit is null THEN
  IF @fup THEN
    SET @ul_limit = @data_fup_upload_limit;
  ELSE
    SET @ul_limit = @data_upload_limit;
  END IF;
  SET @ul_from_row = "data";
  IF ((@data_time_consumed  + @user_SessionTime) >= @data_time_limit AND @data_time_limit > 0 ) THEN 
   SET @coa = true;
  END IF;
END IF;

SET @burst_dl_limit = null;
SET @burst_ul_limit = null;
SET @burst_threshold_dl_limit = null;
SET @burst_threshold_ul_limit = null;
SET @burst_dl_time = null;
SET @burst_ul_time = null;
SET @priority = null;

IF @fup is NULL THEN
  IF dl_from_row = "bw" THEN
    SET @burst_dl_limit = @bw_burst_dl_limit;
    SET @burst_threshold_dl_limit = @bw_burst_threshold_dl_limit;
    SET @burst_dl_time = @bw_burst_dl_time;
  END IF;
  IF dl_from_row = "data" THEN
    SET @burst_dl_limit = @data_burst_dl_limit;
    SET @burst_threshold_dl_limit = @data_burst_threshold_dl_limit;
    SET @burst_dl_time = @data_burst_dl_time;
  END IF;
 
    IF ul_from_row = "bw" THEN
        SET @burst_ul_limit = @bw_burst_ul_limit;
        SET @burst_threshold_ul_limit = @bw_burst_threshold_ul_limit;
        SET @burst_ul_time = @bw_burst_ul_time;
    END IF;
  IF ul_from_row = "data" THEN
      SET @burst_ul_limit = @data_burst_ul_limit;
      SET @burst_threshold_ul_limit = @data_burst_threshold_ul_limit;
      SET @burst_ul_time = @data_burst_ul_time;
  END IF;
  
 SET @priority = @bw_priority;
 IF ( @data_priority > @bw_priority) THEN
  SET @priority = @data_priority;
 END IF;

END IF;


SET @speed_change =false;
SET @accounting_change = false;

IF @dl_limit != @user_last_dl_limit OR @ul_limit != @user_last_ul_limit THEN
  SET @speed_change = true;
        SET @coa = true;
  UPDATE isp_user SET last_dl_limit = @dl_limit, last_ul_limit = @ul_limit WHERE radius_username = username;
END IF;

IF @user_last_accounting_dl_ratio != @bw_accounting_download_ratio OR @user_last_accounting_ul_ratio != @bw_accounting_upload_ratio  THEN
  SET @accounting_change= true;
  SET @coa = true;
  UPDATE isp_user SET last_accounting_dl_ratio = @bw_accounting_download_ratio, last_accounting_ul_ratio = @bw_accounting_upload_ratio WHERE radius_username = username;
END IF;


IF (@dl_limit is null AND @ul_limit is null) OR ( (@data_time_consumed + @user_SessionTime)  > @data_time_limit AND @data_time_limit > 0)  THEN
  SET @access = false;
END IF;

UPDATE isp_user_plan_and_topup set is_effective= 0 where user_id= (SELECT customer_id from isp_user where radius_username = username);

IF @data_applicable_row_id is not null THEN
  UPDATE isp_user_plan_and_topup set is_effective=1 where id=@data_applicable_row_id;
END IF;

SET @burst_string =false;

IF @burst_dl_limit is not null or @burst_dl_limit != "" THEN
  SET @burst_string= CONCAT(@burst_ul_limit,'/',@burst_threshold_dl_limit,' ',@burst_threshold_ul_limit,'/',@burst_dl_time,' ',@burst_ul_time,' ',@priority);
END IF;

RETURN CONCAT(@access,',', @coa,',', IFNULL(@ul_limit,0),'/', IFNULL(@dl_limit,0),',',@burst_string);

END
 ;;
delimiter ;

-- ----------------------------
--  Function structure for `_b4_read_write_checkAuthenticationReadOnly`
-- ----------------------------
DROP FUNCTION IF EXISTS `_b4_read_write_checkAuthenticationReadOnly`;
delimiter ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `_b4_read_write_checkAuthenticationReadOnly`(now datetime, username varchar(255)) RETURNS text CHARSET utf8mb4
    READS SQL DATA
    DETERMINISTIC
P:BEGIN

if now is null THEN 
  SET now = now();
END IF;

CALL getApplicableRow(username,now,false,null);

SET @user_last_dl_limit = @t_last_dl_limit;
SET @user_last_ul_limit = @t_last_ul_limit;

SET @user_last_accounting_dl_ratio = @t_last_accounting_dl_ratio;
SET @user_last_accounting_ul_ratio = @t_last_accounting_ul_ratio;

SET @user_SessionInputOctate = @t_SessionInputOctate;
SET @user_SessionOutputOctate = @t_SessionOutputOctate;
SET @user_SessionTime = @t_SessionTime;

SET @bw_applicable_row_id = @t_applicable_row_id;
SET @bw_applicable_row_name = @t_applicable_row_name;

SET @bw_download_limit = @t_download_limit;
SET @bw_upload_limit = @t_upload_limit;
SET @bw_fup_download_limit = @t_fup_download_limit;
SET @bw_fup_upload_limit = @t_fup_upload_limit;
SET @bw_accounting_download_ratio = @t_accounting_download_ratio;
SET @bw_accounting_upload_ratio = @t_accounting_upload_ratio;

SET @bw_net_data_limit = @t_net_data_limit;
SET @bw_download_data_consumed = @t_download_data_consumed;
SET @bw_upload_data_consumed= @t_upload_data_consumed;

SET @bw_time_limit = @t_time_limit;
SET @bw_time_consumed = @t_time_consumed;
SET @bw_burst_dl_limit = @t_burst_dl_limit;
SET @bw_burst_ul_limit = @t_burst_ul_limit;
SET @bw_burst_threshold_dl_limit= @t_burst_threshold_dl_limit;
SET @bw_burst_threshold_ul_limit= @t_burst_threshold_ul_limit;
SET @bw_burst_dl_time = @t_burst_dl_time;
SET @bw_burst_ul_time = @t_burst_ul_time;
SET @bw_priority = @t_priority;

SET @treat_fup_as_dl_for_last_limit_row = @t_treat_fup_as_dl_for_last_limit_row;

SET @data_applicable_row_id = @t_applicable_row_id;

SET @data_download_limit = @t_download_limit;
SET @data_upload_limit = @t_upload_limit;
SET @data_fup_download_limit = @t_fup_download_limit;
SET @data_fup_upload_limit = @t_fup_upload_limit;

SET @data_net_data_limit = @t_net_data_limit;
SET @data_download_data_consumed = @t_download_data_consumed;
SET @data_upload_data_consumed= @t_upload_data_consumed;

SET @data_time_limit = @t_time_limit;
SET @data_time_consumed = @t_time_consumed;
SET @data_burst_dl_limit = @t_burst_dl_limit;
SET @data_burst_ul_limit = @t_burst_ul_limit;
SET @data_burst_threshold_dl_limit= @t_burst_threshold_dl_limit;
SET @data_burst_threshold_ul_limit= @t_burst_threshold_ul_limit;
SET @data_burst_dl_time = @t_burst_dl_time;
SET @data_burst_ul_time = @t_burst_ul_time;
SET @data_priority = @t_priority;

SET @access= true;

IF @bw_applicable_row_id is null THEN
  SET @access= false;
  RETURN CONCAT(@access,',', 0,',', '0/0',',', 0);
  LEAVE P;
END IF;

IF @bw_net_data_limit is null THEN
  CALL getApplicableRow(username,now,TRUE,null);
  SET @data_applicable_row_id = @t_applicable_row_id;
  SET @data_applicable_row_name = @t_applicable_row_name;

  SET @data_net_data_limit = @t_net_data_limit;
  SET @data_download_data_consumed = @t_download_data_consumed;
  SET @data_upload_data_consumed= @t_upload_data_consumed;
  SET @data_download_limit = @t_download_limit;
  SET @data_upload_limit = @t_upload_limit;
  SET @data_fup_download_limit = @t_fup_download_limit;
  SET @data_fup_upload_limit = @t_fup_upload_limit;
  SET @data_accounting_download_ratio = @t_accounting_download_ratio;
  SET @data_accounting_upload_ratio = @t_accounting_upload_ratio;
  SET @data_time_limit = @t_time_limit;
  SET @data_time_consumed = @t_time_consumed;
  SET @data_burst_dl_limit = @t_burst_dl_limit;
  SET @data_burst_ul_limit = @t_burst_ul_limit;
  SET @data_burst_threshold_dl_limit= @t_burst_threshold_dl_limit;
  SET @data_burst_threshold_ul_limit= @t_burst_threshold_ul_limit;
  SET @data_burst_dl_time = @t_burst_dl_time;
  SET @data_burst_ul_time = @t_burst_ul_time;
  SET @data_priority = @t_priority;
END IF;

SET @fup = false;

IF ( (@data_download_data_consumed + @data_upload_data_consumed + @user_SessionInputOctate + @user_SessionOutputOctate) > @data_net_data_limit) THEN
  SET @fup = true;
  IF @treat_fup_as_dl_for_last_limit_row THEN 
    CALL getApplicableRow(username,now,false,@data_applicable_row_id);
    SET @nxt_data_applicable_row_id = @t_applicable_row_id;
    SET @data_applicable_row_name = @t_applicable_row_name;
    SET @nxt_net_data_limit = @t_net_data_limit;
    SET @nxt_download_data_consumed = @t_download_data_consumed;
    SET @nxt_upload_data_consumed= @t_upload_data_consumed;
    SET @nxt_download_limit = @t_download_limit;
    SET @nxt_upload_limit = @t_upload_limit;
    SET @nxt_fup_download_limit = @t_fup_download_limit;
    SET @nxt_fup_upload_limit = @t_fup_upload_limit;
    SET @nxt_accounting_download_ratio = @t_accounting_download_ratio;
    SET @nxt_accounting_upload_ratio = @t_accounting_upload_ratio;

    IF @nxt_download_data_consumed + @nxt_upload_data_consumed + @user_SessionInputOctate + @user_SessionOutputOctate > @nxt_net_data_limit THEN
      SET @data_download_limit = @nxt_fup_download_limit;
      SET @data_upload_limit = @nxt_fup_upload_limit;
    ELSE
      SET @data_download_limit = @bw_fup_download_limit;
      SET @data_upload_limit = @bw_fup_upload_limit;
    END IF;
  END IF;
END IF;

SET @dl_limit = null;
SET @ul_limit = null;
SET @coa =  false;

IF @fup THEN
  SET @dl_limit = @bw_fup_download_limit;
  SET @ul_limit = @bw_fup_upload_limit;
ELSE
  SET @dl_limit = @bw_download_limit;
  SET @ul_limit = @bw_upload_limit;
END IF;

IF ((@bw_time_consumed  + @user_SessionTime) >= @bw_time_limit AND @bw_time_limit > 0 )THEN 
  SET @coa = true;
END IF;

SET @dl_from_row = "bw";
SET @ul_from_row = "bw";

IF @dl_limit is null THEN
  SET @dl_from_row = "data";
  IF @fup THEN
    SET @dl_limit = @data_fup_download_limit;
  ELSE
    SET @dl_limit = @data_download_limit;
  END IF; 
  
  IF ((@data_time_consumed  + @user_SessionTime) >= @data_time_limit AND @data_time_limit > 0 )  THEN
    SET @coa = true;
  END IF;
END IF;

IF @ul_limit is null THEN
  IF @fup THEN
    SET @ul_limit = @data_fup_upload_limit;
  ELSE
    SET @ul_limit = @data_upload_limit;
  END IF;
  SET @ul_from_row = "data";
  IF ((@data_time_consumed  + @user_SessionTime) >= @data_time_limit AND @data_time_limit > 0 ) THEN 
   SET @coa = true;
  END IF;
END IF;

SET @burst_dl_limit = null;
SET @burst_ul_limit = null;
SET @burst_threshold_dl_limit = null;
SET @burst_threshold_ul_limit = null;
SET @burst_dl_time = null;
SET @burst_ul_time = null;
SET @priority = null;

IF @fup is NULL THEN
  IF dl_from_row = "bw" THEN
    SET @burst_dl_limit = @bw_burst_dl_limit;
    SET @burst_threshold_dl_limit = @bw_burst_threshold_dl_limit;
    SET @burst_dl_time = @bw_burst_dl_time;
  END IF;
  IF dl_from_row = "data" THEN
    SET @burst_dl_limit = @data_burst_dl_limit;
    SET @burst_threshold_dl_limit = @data_burst_threshold_dl_limit;
    SET @burst_dl_time = @data_burst_dl_time;
  END IF;
 
    IF ul_from_row = "bw" THEN
        SET @burst_ul_limit = @bw_burst_ul_limit;
        SET @burst_threshold_ul_limit = @bw_burst_threshold_ul_limit;
        SET @burst_ul_time = @bw_burst_ul_time;
    END IF;
  IF ul_from_row = "data" THEN
      SET @burst_ul_limit = @data_burst_ul_limit;
      SET @burst_threshold_ul_limit = @data_burst_threshold_ul_limit;
      SET @burst_ul_time = @data_burst_ul_time;
  END IF;
  
 SET @priority = @bw_priority;
 IF ( @data_priority > @bw_priority) THEN
  SET @priority = @data_priority;
 END IF;

END IF;


SET @speed_change =false;
SET @accounting_change = false;

IF @dl_limit != @user_last_dl_limit OR @ul_limit != @user_last_ul_limit THEN
  SET @speed_change = true;
        SET @coa = true;
  -- UPDATE isp_user SET last_dl_limit = @dl_limit, last_ul_limit = @ul_limit WHERE radius_username = username;
END IF;

IF @user_last_accounting_dl_ratio != @bw_accounting_download_ratio OR @user_last_accounting_ul_ratio != @bw_accounting_upload_ratio  THEN
  SET @accounting_change= true;
  SET @coa = true;
  -- UPDATE isp_user SET last_accounting_dl_ratio = @bw_accounting_download_ratio, last_accounting_ul_ratio = @bw_accounting_upload_ratio WHERE radius_username = username;
END IF;


IF (@dl_limit is null AND @ul_limit is null) OR ( (@data_time_consumed + @user_SessionTime)  > @data_time_limit AND @data_time_limit > 0)  THEN
  SET @access = false;
END IF;

-- UPDATE isp_user_plan_and_topup set is_effective= 0 where user_id= (SELECT customer_id from isp_user where radius_username = username);

-- IF @data_applicable_row_id is not null THEN
  -- UPDATE isp_user_plan_and_topup set is_effective=1 where id=@data_applicable_row_id;
-- END IF;

SET @burst_string =false;

IF @burst_dl_limit is not null or @burst_dl_limit != "" THEN
  SET @burst_string= CONCAT(@burst_ul_limit,'/',@burst_threshold_dl_limit,' ',@burst_threshold_ul_limit,'/',@burst_dl_time,' ',@burst_ul_time,' ',@priority);
END IF;

RETURN CONCAT(@access,',', @coa,',', IFNULL(@ul_limit,0),'/', IFNULL(@dl_limit,0),',',@burst_string);

END
 ;;
delimiter ;

-- ----------------------------
--  Function structure for `_b4_read_write_sessionClose`
-- ----------------------------
DROP FUNCTION IF EXISTS `_b4_read_write_sessionClose`;
delimiter ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `_b4_read_write_sessionClose`(username varchar(255)) RETURNS text CHARSET utf8mb4
    MODIFIES SQL DATA
BEGIN
  UPDATE 
    isp_user_plan_and_topup   
  SET
    download_data_consumed = IFNULL(download_data_consumed,0) + IFNULL(session_download_data_consumed,0),
    upload_data_consumed = IFNULL(upload_data_consumed,0) + IFNULL(session_upload_data_consumed,0),
    time_consumed = IFNULL(time_consumed,0) + IFNULL(session_time_consumed,0),
    session_download_data_consumed=0,
    session_upload_data_consumed=0,
    session_time_consumed=0
  WHERE
    user_id = (SELECT customer_id from isp_user where radius_username = username);
  RETURN "done";
END
 ;;
delimiter ;

-- ----------------------------
--  Function structure for `_b4_read_write_updateAccountingData`
-- ----------------------------
DROP FUNCTION IF EXISTS `_b4_read_write_updateAccountingData`;
delimiter ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `_b4_read_write_updateAccountingData`(dl_data bigint, ul_data bigint, now datetime, username varchar(255), session_time_consumed bigint) RETURNS text CHARSET utf8mb4
    MODIFIES SQL DATA
BEGIN

  IF(now is NULL) THEN 
    SET now = now();
  END IF;

  SELECT IFNULL(last_accounting_dl_ratio,100), IFNULL(last_accounting_ul_ratio,100) into @last_accounting_dl_ratio, @last_accounting_ul_ratio FROM isp_user WHERE radius_username = username;

  UPDATE 
    isp_user_plan_and_topup 
  SET 
    isp_user_plan_and_topup.session_download_data_consumed = ((dl_data*@last_accounting_dl_ratio) /100) ,
    isp_user_plan_and_topup.session_upload_data_consumed = ((ul_data*@last_accounting_ul_ratio) /100),
    isp_user_plan_and_topup.session_time_consumed = session_time_consumed
  WHERE 
    is_effective = 1 AND user_id = (SELECT customer_id from isp_user where radius_username = username)
  ;

  select checkAuthentication(now, username) into @temp;
  RETURN @temp;

END
 ;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
