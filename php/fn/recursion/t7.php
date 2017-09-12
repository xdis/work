<?php
header("Content-Type:text/html; charset=utf-8");

$str = "2013-08至2013-09——武汉地质大学
钻石4C的分级其他
从钻石的4C进行分级，颜色、净度、切工、重量。
********************
2009-01至2011-12——华南师范大学
人力资源管理本科


********************
2008-09至2011-06——广东新安职业技术学院
工商管理大专
";

function ex_education($str)
{
    $rtn = array();
    if (is_array($str)) {

        foreach ($str as $k => $v) {
            $explode = explode(chr(13), trim($v));

            //毕业时间与学校名称
            $school = explode('——', $explode[0]);
            //时间切割
            $school_date = explode('至', $school[0]);
            //var_dump($school_date);exit;
            $education_array['start_date'] = $school_date[0] . '-1';
            $education_array['end_date'] = $school_date[1] . '-1';
            $education_array['school_name'] = $school[1];
            //专业与学历
            $education_array['major_name'] = mb_substr($explode[1], 1, -2, 'utf-8');
            $education_array['degree_name'] = mb_substr($explode[1], -2, 2, 'utf-8');

            //专业描述
            $education_array['education_detail'] = (isset($explode[2])) ? $explode[2] : '';
            $rtn['education'][] = $education_array;

            $result = $rtn;
        }
    } else {
        $temp_arr[] = $str;
        $result = ex_education($temp_arr);

    }

    //var_dump($temp);exit;
    return $result;

}


//var_dump($result);exit;


//if(!is_array())


$result = (stristr($str, '********************')) ? explode('********************', $str) : $str;


$rtn = ex_education($result);
//echo count($rtn['education']);exit;
var_dump($rtn);



