<?php

namespace common\helpers;

/**
 * 通用的树型类，可以生成任何树型结构
 */
class TreeHelper {

    static public $treeList = array();

    /**
     * 查找之类
     * @param type $data
     * @param type $pid
     * @return type 
     */
    public function findChild($data, $pid=0) {
        self::$treeList = '';
        foreach ($data as $key => $value) {
            if ($value['pid'] == $pid) {
                self::$treeList[] = $value; //用类名或者self在非静态方法中访问静态成员
                unset($data[$key]);
            }
        }
        return self::$treeList;
    }

    /**
     * 生成树型  - 递归
     * @param type $data
     * @param type $pid
     * @return type 
     */
    static public function getTree(&$data, $pid = 0) {
        $childs = self::findChild($data, $pid);
        if (empty($childs)) {
            return null;
        }
        foreach ($childs as $key => $val) {
            $treeList = self::getTree($data, $val['id']);
            if ($treeList !== null) {
                $childs[$key]['children'] = $treeList;
            }
        }
        return $childs;
    }

    /** 后台 共用
     *  无限极分类 ————重组数组
     * @param  array  	$cate        数组源
     * @param  str		$name  	 	 自定义键名
     * @param  int		$pid  		 父ID
     */
    public static function getSubs($cate, $name = 'children', $pid = 0) {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['parent_id'] == $pid) {
                $temp = self::getSubs($cate, $name, $v['id']);
                if ($temp) {
                    $v[$name] = $temp;
                }
                $arr[] = $v;
            }
        }
        return $arr;
    }

    /** 后台 左边栏目专用
     *  无限极分类2 ———— 自定义属性
     * @param  array  	$arr   数组源
     */
    public static function changeSubs($arr) {
        $jsonArr = array();
        foreach ($arr as $key => $val) {
            $jsonArr[$key]['id'] = $val['menu_id'];
            $jsonArr[$key]['text'] = $val['menu_name'];
            $jsonArr[$key]['attributes'] = array('type' => $val['type'], 'href' => U(MODULE_NAME . $val['href']));

            if ($val['children']) {
                $jsonArr[$key]['children'] = self::changeSubs($val['children']);
            }
        }
        return $jsonArr;
    }

}

?>