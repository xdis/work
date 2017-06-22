DELIMITER $$

USE `qian100`$$

DROP FUNCTION IF EXISTS `activity_kuaiche_jifen`$$

CREATE DEFINER=`root`@`localhost` FUNCTION `activity_kuaiche_jifen`(
      _loan_id INTEGER                	-- 投标的ID  
) RETURNS VARCHAR(4) CHARSET utf8
BEGIN
  /* 理财快车积分兑换  第一步 */
  DECLARE _IsRecash INTEGER;
  DECLARE _LoanTerm INTEGER;     -- 标的多少期 
  DECLARE _invest_id INTEGER;
  DECLARE _invest_amount DECIMAL(18,2);
  DECLARE _member_id INTEGER;
  DECLARE activity_kuaiche_jifen_insert INTEGER;
  
  -- 游标定义
  DECLARE finished INT DEFAULT 0;     
     DECLARE cur_1 CURSOR 
       FOR 
         --  SELECT invest_id,invest_amount,member_id from investkuaiche where loan_id = _loan_id and STATUS = 0;
           SELECT investkuaiche.invest_id,investkuaiche.invest_amount,investkuaiche.member_id 
		FROM investkuaiche INNER JOIN invest ON invest.id = investkuaiche.invest_id 
		WHERE investkuaiche.loan_id = _loan_id AND investkuaiche.STATUS = 0 AND invest.IsValid = 1; 
           
      DECLARE CONTINUE HANDLER FOR NOT FOUND SET finished = 1;   
      
   -- 查询理财快车投标的记录
    SELECT LoanTerm INTO _LoanTerm FROM loan WHERE id = _loan_id;
      -- 游标开始                    
       OPEN cur_1;  
       curl:REPEAT  
	FETCH cur_1 INTO _invest_id,_invest_amount,_member_id;  
	
	 IF NOT finished THEN 
	 
	    SELECT activity_kuaiche_jifen_insert(_member_id,_invest_id,_LoanTerm,_invest_amount,_loan_id) INTO activity_kuaiche_jifen_insert; 
             -- INSERT INTO sysstoragelog (log_name,log_step) VALUES('积分兑换',CONCAT('activity_kuaiche_jifen_insert(',_member_id,',',_invest_id,',',_LoanTerm,',',_invest_amount,',',_loan_id,')'));
        END IF;         
          UNTIL finished  
        -- 循环结束 
        END REPEAT curl;  
       -- 关闭游标 
       CLOSE cur_1;
          
     RETURN '1';
    END$$

DELIMITER ;