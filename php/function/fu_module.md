# 功能模块

## 获取当前时间戳_精确到毫秒
```php
function microtime_float($flag = 0) {
    list($usec, $sec) = explode(" ", microtime());
    if ($flag == 0) {
        return ((float) $usec + (float) $sec);
    }
    return substr((float) $usec, strpos((float) $usec, '.') + 1, 4);
}
```



## 需要反序列化的-反序列化
```php
/**
 * 需要反序列化的-反序列化
 * @param type 需要序列化的数据
 * @return type
 */
function un_serialize($data){
	$data = trim( $data );
	if ( 'N;' == $data )
		return unserialize($data);
	if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
		return $data;
	switch ( $badions[1] ) {
		case 'a' :
		case 'O' :
		case 's' :
			if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
				return unserialize($data);
			break;
		case 'b' :
		case 'i' :
		case 'd' :
			if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
				return unserialize($data);
			break;
	}
	return $data;
}
```



## 时间戳相差输出
```php
function timediff($begin_time, $end_time) {
	if ($begin_time < $end_time) {
		$starttime = $begin_time;
		$endtime = $end_time;
	}
	$timediff = $endtime - $starttime;

	/* Rming()函数，即舍去法取整 */
	$d = floor($timediff / 3600 / 24);
	$h = floor(($timediff % (3600 * 24)) / 3600);  //%取余
	$m = floor(($timediff % (3600 * 24)) % 3600 / 60);
	$s = floor(($timediff % (3600 * 24)) % 60);
	$res = array("day" => $d, "hour" => $h, "min" => $m, "sec" => $s);
	return $res;
}
```

## 打印输出_pt
```php
function pt($data, $is_die = true) {
    echo "<pre>";
    print_r($data);
    if ($is_die) {
        exit;
    }
}
```

## 打印输出_dp
```php
function dp($data, $is_die = true) {
    echo "<pre>";
    var_dump($data);
    if ($is_die) {
        exit;
    }
}
```

## 字符串截取_单字节截取模式
```php
/**
 *  字符串截取，单字节截取模式
 *
 * @param     string		$str  需要截取的字符串
 * @param     int			$length  截取的长度
 * @param     int			$start  开始截取的位置     ps:负数无效
 * @param     boole			$omission  是否要在后面加上省略号， false:不加  true:加
 * @return    string
 */
function cn_substr_utf8($str, $length, $start = 0, $omission = false) {
    //判断变量是否为空
    if (strlen($str) < $start + 1) {
        return '';
    }
    preg_match_all("/./su", $str, $ar);
    $str = '';
    $tstr = '';
    //为了兼容mysql4.1以下版本,与数据库varchar一致,这里使用按字节截取
    for ($i = 0; isset($ar[0][$i]); $i++) {
        //这里是把起始位置之前的字段过滤掉
        if (strlen($tstr) < $start) {
            $tstr .= $ar[0][$i];
        } else {
            //strlen($ar[0][$i] 如果是中文就3个字符，如果是别的就1个字符
            if (strlen($str) <= $length && $length - strlen($str) >= strlen($ar[0][$i])) {
                $str .= $ar[0][$i];
            } else {
                break;
            }
        }
    }
    if (strlen($str) > $length && $omission) {
        $str .= '...';
    }
    return $str;
}
```


## 用于生成随机的数字和字母组合
```php
/**
 *  用于生成随机的数字和字母组合
 * @Param $len 生成长度
 * @Param $is_strtoupper   是否转换成大写
 * @Param $chars   			     自定义随机字符串
 * @Return string
 */
function randStr($len, $is_strtoupper = false, $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
    $string = '';
    for (; $len >= 1; $len--) {
        $position = rand() % strlen($chars);
        $string .= substr($chars, $position, 1);
    }

    if ($is_strtoupper) {
        strtoupper($string);
    }
    return $string;
}

```

## 随机数字数字
```php
/**
 * 随机数字数字
 * @param type $num
 * @return string
 * @author  cmk
 */
function getRangNUm($num) {
    $arr = array();
    while (count($arr) < $num - 1) {
        $arr[] = rand(1, $num);
        $arr = array_unique($arr);
        
    }
    return  implode("",$arr);;
}

```

## 验证手机号码
```php
function is_mobile($mobile) {
    $pattern = '/^1(?:3[0-9]|4[0-9]|5[0-9]|8[0-9]|7[0678])\d{8}$/';
    $is_mobile = (bool) preg_match($pattern, $mobile);
    return $is_mobile;
}
```


## 导出excel封装方法
```php
/**
 * 导出excel封装方法
 * @param $title      表格名称
 * @param $headNames  表格头部列名称
 * @param $data       表格数据（二维数组）
 * @param $mode       1:浏览器输出模式    2：文件保存但不输出模式
 * @param $path       文件名称  包含：文件保存的路径，必须是绝对路径     例如：    d:\myword\index.html   格式
 */
function exportExcel($title,$headNames,$data,$mode=1,$path=''){
    $maxColumn = array('A','B','C','D','E','F','G','H','I','J','K','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    $objPHPExcel = new \PHPExcel();
    $objSheet=$objPHPExcel->getActiveSheet();//获取当前活动sheet
    $objSheet->setTitle($title);//给当前活动sheet起个名称
    $headStyle = array();
    $columnSizes =  array();

    for($i = 0;$i< count($headNames);$i++){
        $objSheet->setCellValue($maxColumn[$i].'1',$headNames[$i]);
        $headStyle[$i] = $maxColumn[$i].'1';
        $columnSizes[$i] = $maxColumn[$i];
    }
    $styleArray1 = array(
        'font' => array('bold' => true,'size'=>12, 'color'=>array('argb' => '00000000'),),
        'alignment' => array(
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ),
    );
    foreach($headStyle as $val){
        $column = substr($val,0,1);
        $objSheet->getColumnDimension($column)->setWidth(20);
        $objSheet->getStyle($val)->applyFromArray($styleArray1);
    }
    $j=2;

    foreach($data as $dVal){
        $i = 0;
        foreach($dVal as $val){
            for($i;$i<$columnSizes;){
                $objSheet->setCellValue($columnSizes[$i].$j,' '."".$val);
                $objSheet->getStyle($columnSizes[$i].$j)->getAlignment()->setWrapText(true);
                break;
            }
            $i++;
        }

        $j++;
    }
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');//生成excel文件
    
    if ($mode == 1) {
        browser_export('Excel5',$title.'.xls');//输出到浏览器
        $objWriter->save('php://output');
    } elseif ($mode == 2) {
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path));
        }
        $objWriter->save($path);
    }
}


function browser_export($type,$filename){
    if($type=="Excel5"){
        header('Content-Type: application/vnd.ms-excel');//告诉浏览器将要输出excel03文件
    }else{
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器数据excel07文件
    }
    header('Content-Disposition: attachment;filename="'.$filename.'"');//告诉浏览器将输出文件的名称
    header('Cache-Control: max-age=0');//禁止缓存
}
```


## 身份证号验证
```php
/**
 * @Description 身份证号验证
 * @Param $id_card string 身份证号码字符串
 * @Return bool TRUE 通过  FALSE 不通过
 */
function validate_id_card($id_card) {
    if(strlen($id_card) == 18) {
        return idcard_checksum18($id_card);
    } elseif((strlen($id_card) == 15)) {
        $id_card = idcard_15to18($id_card);
        return idcard_checksum18($id_card);
    }else{
        return FALSE;
    }
}

// 将15位身份证升级到18位
function idcard_15to18($idcard){
    if (strlen($idcard) != 15){
        return false;
    }else{
        // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
        if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false){
            $idcard = substr($idcard, 0, 6) . '18'. substr($idcard, 6, 9);
        }else{
            $idcard = substr($idcard, 0, 6) . '19'. substr($idcard, 6, 9);
        }
    }
    $idcard = $idcard . _idcard_verify_number($idcard);
    return $idcard;
}

// 18位身份证校验码有效性检查
function idcard_checksum18($idcard){
    if (strlen($idcard) != 18){ return false; }
    $idcard_base = substr($idcard, 0, 17);
    if (_idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))){
        return false;
    }else{
        return true;
    }
}

/**
 * 计算身份证校验码，根据国家标准GB 11643-1999
 * 主要用于内部调用
 * @param $idcard_base
 * @return bool | string
 */
function _idcard_verify_number($idcard_base) {
    if(strlen($idcard_base) != 17) {
        return FALSE;
    }
    //加权因子
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
    //校验码对应值
    $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
    $checksum = 0;
    for ($i = 0; $i < strlen($idcard_base); $i++) {
        $checksum += substr($idcard_base, $i, 1) * $factor[$i];
    }
    $mod = $checksum % 11;
    $verify_number = $verify_number_list[$mod];
    return $verify_number;
}
```



## 出月份的第一天最后一天及当月有多少天
```php
/**
 * 输出月份的  第一天 最后一天  及当月有多少天
 * @param $date 格式  年-月 如 2017-03
 * @author cmk
 * @return mixed
 */
 function generalDateList($date){
    //计算当月的第一天和最后一天
    $beginDate = $date . '-01';
    $endDate = date('Y-m-d', strtotime("$beginDate +1 month -1 day"));

    //获取插入的天数
    $arr['insert_start_day'] = strtotime($beginDate);
    $arr['insert_end_day'] = strtotime($endDate);
    $arr['insert_day'] = ceil(abs($arr['insert_start_day'] -  $arr['insert_end_day']) / 86400);
    return $arr;

}
```



## 获取当前时间戳_精确到毫秒
```php

```