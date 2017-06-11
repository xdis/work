<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2016/12/27 19:26
 * description:
 */

namespace common\helpers;


class ModelHelper
{
    /**
     * 批量插入数据保存
     * 使用示例：
     *
     * $rows = [];
     * foreach ($items as $key => $value) {
     *     $rows[$key]['title'] = $value['title'];
     *     $rows[$key]['user_id'] = $userId;
     * }
     * if (!ModelHelper::saveAll(Post::tableName(), $rows)) {
     *     throw new Exception();
     * }
     *
     * @param $tableName
     * @param array $rows
     * @return int
     * @throws \yii\db\Exception
     */
    public static function saveAll($tableName, $rows = [])
    {
        if ($rows) {
            return \Yii::$app->db->createCommand()
                ->batchInsert($tableName, array_keys(array_values($rows)[0]), $rows)
                ->execute();
        }
        return false;
    }
}