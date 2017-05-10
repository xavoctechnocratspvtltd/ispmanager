/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : ispmanager

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-05-10 16:59:28
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `document`
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
) ENGINE=InnoDB AUTO_INCREMENT=8435 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of document
-- ----------------------------
INSERT INTO `document` VALUES ('8418', '0', 'Item', null, null, '2017-05-06 18:17:33', null, '2017-05-06 18:17:33', 'active', null);
INSERT INTO `document` VALUES ('8419', '0', 'Item', null, null, '2017-05-06 18:25:42', null, '2017-05-06 18:25:42', 'active', null);
INSERT INTO `document` VALUES ('8420', '0', 'Item', null, null, '2017-05-06 18:28:15', null, '2017-05-06 18:28:15', 'active', null);
INSERT INTO `document` VALUES ('8421', '0', 'Item', null, null, '2017-05-06 18:31:18', null, '2017-05-06 18:31:18', 'active', null);
INSERT INTO `document` VALUES ('8422', '0', 'Item', null, null, '2017-05-06 18:49:53', null, '2017-05-06 18:49:53', 'active', null);
INSERT INTO `document` VALUES ('8423', '0', 'Item', null, null, '2017-05-06 19:06:28', null, '2017-05-06 19:06:28', 'active', null);
INSERT INTO `document` VALUES ('8424', '0', 'Item', null, null, '2017-05-06 19:10:20', null, '2017-05-06 19:10:20', 'active', null);
INSERT INTO `document` VALUES ('8425', '0', 'Item', null, null, '2017-05-06 19:16:49', null, '2017-05-06 19:16:49', 'active', null);
INSERT INTO `document` VALUES ('8426', '0', 'Item', null, null, '2017-05-06 19:18:43', null, '2017-05-06 19:18:43', 'active', null);
INSERT INTO `document` VALUES ('8427', '0', 'Item', null, null, '2017-05-06 19:20:57', null, '2017-05-06 19:20:57', 'active', null);

-- ----------------------------
-- Table structure for `isp_condition`
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
  PRIMARY KEY (`id`),
  KEY `fk_plan_id` (`plan_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of isp_condition
-- ----------------------------
INSERT INTO `isp_condition` VALUES ('1', '8418', 'Main Plan', '53687091200', '1048576', '1048576', null, null, '100', '100', '0', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months');
INSERT INTO `isp_condition` VALUES ('2', '8419', 'Main Plan', '536870912000', '1048576', '1048576', null, null, '100', '100', '0', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '6', 'months');
INSERT INTO `isp_condition` VALUES ('3', '8420', 'Main Plan', '536870912000', '2097152', '2097152', null, null, '100', '100', '1', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '3', 'months');
INSERT INTO `isp_condition` VALUES ('4', '8421', 'Main Plan', '107374182400', '2097152', '2097152', '524288', '524288', '100', '100', '0', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months');
INSERT INTO `isp_condition` VALUES ('5', '8422', 'Day Plan', '10737418240', '2097152', '2097152', null, null, '100', '100', '0', '08:00:00', '20:00:00', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months');
INSERT INTO `isp_condition` VALUES ('6', '8422', 'Night Plan', '10737418240', '2097152', '2097152', null, null, '100', '100', '0', '20:00:00', '08:00:00', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months');
INSERT INTO `isp_condition` VALUES ('7', '8423', 'All Day Plan', '107374182400', '2097152', '2097152', '524288', '524288', '100', '100', '0', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months');
INSERT INTO `isp_condition` VALUES ('8', '8423', 'Sunday Offer', null, null, null, null, null, '0', '0', '0', null, null, '1', '0', '0', '0', '0', '0', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', null, null);
INSERT INTO `isp_condition` VALUES ('9', '8424', 'All Days', '107374182400', '2097152', '2097152', '524288', '524288', '100', '100', '0', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months');
INSERT INTO `isp_condition` VALUES ('10', '8424', 'Sunday offer', '2147483648', '2097152', '2097152', '1048576', '1048576', '100', '100', '0', null, null, '1', '0', '0', '0', '0', '0', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'days');
INSERT INTO `isp_condition` VALUES ('11', '8425', 'Daily Reset Plan', '1073741824', '2097152', '2097152', '524288', '524288', '100', '100', '0', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'days');
INSERT INTO `isp_condition` VALUES ('12', '8426', 'Main Plan', '107374182400', '2097152', '2097152', '524288', '524288', '100', '100', '0', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months');
INSERT INTO `isp_condition` VALUES ('13', '8426', 'Night Offer', null, null, null, null, null, '0', '0', '0', '22:00:00', '04:00:00', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', null, null);
INSERT INTO `isp_condition` VALUES ('14', '8427', 'Main Plan', '107374182400', '2097152', '2097152', '524288', '524288', '100', '100', '0', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months');
INSERT INTO `isp_condition` VALUES ('15', '8427', 'Night Offer', null, '4194304', '4194304', '2097152', '2097152', '100', '100', '0', '22:00:00', '04:00:00', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', null, null);
INSERT INTO `isp_condition` VALUES ('19', '8418', '', null, null, null, null, null, '100', '100', '0', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', null);
INSERT INTO `isp_condition` VALUES ('20', '8432', 'DAY PLAN', '53687091200', '2097152', '2097152', '524288', '524288', '100', '100', '0', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months');
INSERT INTO `isp_condition` VALUES ('21', '8432', 'UNLIM', null, '2097152', '2097152', '1048576', '1048576', '0', '0', '0', null, null, '1', '0', '0', '0', '0', '0', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months');
INSERT INTO `isp_condition` VALUES ('22', '8433', 'Day Plan', '1073741824', '4194304', '4194304', '131072', '131072', '100', '100', '0', null, null, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'days');
INSERT INTO `isp_condition` VALUES ('23', '8434', 'Day Plan', '10737418240', '2097152', '2097152', null, null, '100', '100', '0', '08:00:00', '20:00:00', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months');
INSERT INTO `isp_condition` VALUES ('24', '8434', 'Night Plan', '10737418240', '2097152', '2097152', null, null, '100', '100', '0', '20:00:00', '08:00:00', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'months');

-- ----------------------------
-- Table structure for `isp_plan`
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
  PRIMARY KEY (`id`),
  KEY `fk_created_by_id` (`created_by_id`),
  KEY `fk_updated_by_id` (`updated_by_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of isp_plan
-- ----------------------------
INSERT INTO `isp_plan` VALUES ('1', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '1', '8418', '5');
INSERT INTO `isp_plan` VALUES ('2', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '0', '8419', '5');
INSERT INTO `isp_plan` VALUES ('3', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '1', '8420', '5');
INSERT INTO `isp_plan` VALUES ('4', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '1', '8421', '4');
INSERT INTO `isp_plan` VALUES ('5', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '1', '8422', '5');
INSERT INTO `isp_plan` VALUES ('6', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '1', '8423', '5');
INSERT INTO `isp_plan` VALUES ('7', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '1', '8424', '5');
INSERT INTO `isp_plan` VALUES ('8', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '1', '8425', '2');
INSERT INTO `isp_plan` VALUES ('9', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '1', '8426', '5');
INSERT INTO `isp_plan` VALUES ('10', null, null, null, null, null, null, null, null, '0', '0', '0', '1', '1', '8427', '4');

-- ----------------------------
-- Table structure for `item`
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
  PRIMARY KEY (`id`),
  KEY `document_id` (`document_id`) USING BTREE,
  KEY `duplicate_from_item_id` (`duplicate_from_item_id`) USING BTREE,
  KEY `to_customer_id` (`to_customer_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of item
-- ----------------------------
INSERT INTO `item` VALUES ('8418', 'PL-50-M', 'PL-50-M', '100.00', '90.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `item` VALUES ('8419', 'PL-500 GB for 6 month', 'PL-500-6M', '1000.00', '900.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `item` VALUES ('8420', 'PL-500 GB for 3 month data carry', 'PL-500-3M-carry', '200.00', '2000.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '3', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `item` VALUES ('8421', 'unlimited 100GB-m', 'PL-100GB-2M-unlimited', '3000.00', '200.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `item` VALUES ('8422', 'Day Night plan', 'PL-20GB-2M-Day-Night', '400.00', '300.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '5', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `item` VALUES ('8423', 'SUNDAY EXCLUDED 100GB-1m', 'PL-100GB-1M-unlimited', '2000.00', '3000.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '6', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `item` VALUES ('8424', '100GB-2mb-HighSpeed on sunday', 'PL-100GB-1M-high-speed', '100.00', '10.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '7', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `item` VALUES ('8425', 'jio plan', 'PL-1GB-1D-high-speed', '100.00', '200.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '8', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `item` VALUES ('8426', 'Night Unlimited', 'PL-10GB-1M-Bight-unlimited', '100.00', '2000.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '9', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `item` VALUES ('8427', 'Night highspeed', 'PL-100GB-1M-night-speed', '100.00', '200.00', null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '4', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '10', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);

-- ----------------------------
-- Table structure for `taxation`
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
-- Records of taxation
-- ----------------------------
INSERT INTO `taxation` VALUES ('1', 'Vat 05 %', '5.00', 'Taxation', '3', null);
INSERT INTO `taxation` VALUES ('2', 'Vat 14.5 %', '14.50', 'Taxation', '3', null);
INSERT INTO `taxation` VALUES ('3', 'CST 5 %', '5.00', 'Taxation', '3', null);
INSERT INTO `taxation` VALUES ('4', 'CST 2 %  Against Form C', '2.00', 'Taxation', '3', null);
INSERT INTO `taxation` VALUES ('5', 'CST 14.5 %', '14.50', 'Taxation', '3', null);
INSERT INTO `taxation` VALUES ('6', 'VAT 5.5%', '5.50', 'Taxation', '3', null);
INSERT INTO `taxation` VALUES ('7', 'CST 05.5%', '5.50', 'Taxation', '3', null);

-- ----------------------------
-- Table structure for `unit`
-- ----------------------------
DROP TABLE IF EXISTS `unit`;
CREATE TABLE `unit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_group_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of unit
-- ----------------------------
INSERT INTO `unit` VALUES ('1', '1', 'hours');
INSERT INTO `unit` VALUES ('2', '1', 'days');
INSERT INTO `unit` VALUES ('3', '1', 'weeks');
INSERT INTO `unit` VALUES ('4', '1', 'months');
INSERT INTO `unit` VALUES ('5', '1', 'years');

-- ----------------------------
-- Table structure for `unit_group`
-- ----------------------------
DROP TABLE IF EXISTS `unit_group`;
CREATE TABLE `unit_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of unit_group
-- ----------------------------
INSERT INTO `unit_group` VALUES ('1', 'Duration', '5');
