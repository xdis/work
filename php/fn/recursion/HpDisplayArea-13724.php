<?php

/**
 * 显示地区
 *
 * @copyright  2011 Baidajob.com
 * @version    Release: @package_version@
 * @author baidajob01
 */
class Zend_View_Helper_HpDisplayArea extends BD_View_Helper_HpBase
{

    function hpDisplayArea($area_id = '', $length = 20)
    {
        $html = '';
        if (is_numeric($area_id)) {
            $area_row = BD_Utility::getRegionRow($area_id);
            $region_name = $area_row['region']['area_name'];
            $city_name = $area_row['city']['area_name'];
            $province_name = $area_row['province']['area_name'];
            if ($province_name) {
                $html .= $province_name;
            }
            if ($city_name && $province_name != $city_name) {
                $html .= '-' . $city_name;
            }

            if ($region_name) {
                $html = $city_name . '-' . $region_name;
            }
            if (is_numeric($length)) {
                return BD_Utility::substr($html, 0, $length);
            } else {
                return $html;
            }
        }
        //输出多个地区 
        if (is_array($area_id) && count($area_id) > 0) {
            $area_row = array();
            foreach ($area_id as $v) {
                $area_row[] = $this->hpDisplayArea($v);
            }
            $html = implode(', ', $area_row);
            return $html;
        }
    }

}
