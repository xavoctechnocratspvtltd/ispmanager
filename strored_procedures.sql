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

 Date: 06/06/2017 09:15:13 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Procedure structure for `getApplicableRow`
-- ----------------------------
DROP PROCEDURE IF EXISTS `getApplicableRow`;
delimiter ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `getApplicableRow`(username varchar (255),now datetime, with_data_limit boolean,less_then_this_id integer)
BEGIN

SET @now = now;
SET @day = LOWER(DATE_FORMAT(@now,"%a"));
SET @date = CONCAT("d",DATE_FORMAT(@now,"%d"));
SET @current_time = TIME(@now);
SET @today = DATE(@now);
SET @username = username;

SELECT 
	isp_user_plan_and_topup.id id,
	data_limit + carry_data AS net_data_limit,
	user.last_dl_limit last_dl_limit,
	user.last_ul_limit last_ul_limit,
	user.last_accounting_dl_ratio,
	user.last_accounting_ul_ratio,
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
	IFNULL( (select radacct.acctinputoctets from radacct where radacct.username = @username and acctstoptime is null) , 0 ) SessionInputOctets ,
	IFNULL( (select radacct.acctoutputoctets  from radacct where radacct.username = @username and acctstoptime is null), 0 ) SessionOutputOctets ,
	IFNULL( (select radacct.acctsessiontime  from radacct where username = @username and acctstoptime is null), 0 ) SessionTime
	into @t_applicable_row_id, @t_net_data_limit, @t_last_dl_limit, @t_last_ul_limit, @t_last_accounting_dl_ratio, @t_last_accounting_ul_ratio,@t_download_data_consumed, @t_upload_data_consumed, @t_download_limit, @t_upload_limit, @t_fup_download_limit, @t_fup_upload_limit, @t_accounting_download_ratio, @t_accounting_upload_ratio, @t_burst_dl_limit, @t_burst_ul_limit, @t_burst_threshold_dl_limit, @t_burst_threshold_ul_limit, @t_burst_dl_time, @t_burst_ul_time, @t_priority, @t_time_limit, @t_time_consumed, @t_treat_fup_as_dl_for_last_limit_row,@t_SessionInputOctate, @t_SessionOutputOctate, @t_SessionTime
FROM
	isp_user_plan_and_topup 
JOIN
	isp_user user on isp_user_plan_and_topup.user_id=user.customer_id
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

END
 ;;
delimiter ;

-- ----------------------------
--  Function structure for `checkAuthentication`
-- ----------------------------
DROP FUNCTION IF EXISTS `checkAuthentication`;
delimiter ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `checkAuthentication`(now datetime, username varchar(255)) RETURNS text CHARSET utf8
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
	RETURN CONCAT(@access,',', 0,',', 0,',', 0);
	LEAVE P;
END IF;

IF @bw_net_data_limit is null THEN
	CALL getApplicableRow(username,now,TRUE,null);
	SET @data_applicable_row_id = @t_applicable_row_id;
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
SET	@burst_ul_limit = null;
SET @burst_threshold_dl_limit = null;
SET	@burst_threshold_ul_limit = null;
SET	@burst_dl_time = null;
SET	@burst_ul_time = null;
SET @priority = null;

IF @fup is NULL THEN
	IF dl_from_row = "bw" THEN
		SET	@burst_dl_limit = @bw_burst_dl_limit;
		SET	@burst_threshold_dl_limit = @bw_burst_threshold_dl_limit;
		SET	@burst_dl_time = @bw_burst_dl_time;
	END IF;
 	IF dl_from_row = "data" THEN
 		SET	@burst_dl_limit = @data_burst_dl_limit;
 		SET	@burst_threshold_dl_limit = @data_burst_threshold_dl_limit;
 		SET	@burst_dl_time = @data_burst_dl_time;
 	END IF;
 
  	IF ul_from_row = "bw" THEN
  			SET @burst_ul_limit = @bw_burst_ul_limit;
  			SET @burst_threshold_ul_limit = @bw_burst_threshold_ul_limit;
  			SET @burst_ul_time = @bw_burst_ul_time;
  	END IF;
 	IF ul_from_row = "data" THEN
 			SET @burst_ul_limit = @data_burst_ul_limit;
 			SET @burst_threshold_ul_limit = @data_burst_threshold_ul_limit;
 			SET	@burst_ul_time = @data_burst_ul_time;
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

RETURN CONCAT(@access,',', @coa,',', @ul_limit,'/', @dl_limit,',',@burst_string);

END
 ;;
delimiter ;

-- ----------------------------
--  Function structure for `updateAccountingData`
-- ----------------------------
DROP FUNCTION IF EXISTS `updateAccountingData`;
delimiter ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `updateAccountingData`(dl_data bigint, ul_data bigint, now datetime, username varchar(255), session_time_consumed bigint) RETURNS text CHARSET utf8
BEGIN

	IF(now is NULL) THEN 
		SET now = now();
	END IF;

	SELECT last_accounting_dl_ratio, last_accounting_ul_ratio into @last_accounting_dl_ratio, @last_accounting_ul_ratio FROM isp_user WHERE radius_username = username;

	UPDATE 
		isp_user_plan_and_topup 
	SET 
		download_data_consumed = IFNULL(download_data_consumed,0) + ((dl_data*@last_accounting_dl_ratio) /100) ,
		upload_data_consumed = IFNULL(upload_data_consumed,0) + ((ul_data*@last_accounting_ul_ratio) /100),
		time_consumed = IFNULL(time_consumed,0) + session_time_consumed
	WHERE 
		is_effective = 1 AND user_id = (SELECT customer_id from isp_user where radius_username = username)
	;

	select checkAuthentication(now, username) into @temp;
	RETURN @temp;

END
 ;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
