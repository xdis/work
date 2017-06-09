<?php

namespace company\models;

use common\helpers\ArrHelper;
use common\helpers\ModelHelper;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

/**
 * This is the model class for table "{{%auth_item}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property integer $company_id
 * @property integer $parent_id
 * @property integer $type
 * @property string $description
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthItem[] $permissions
 * @property AuthItem[] $roles
 * @property AuthItemChild[] $children
 */
class AuthItem extends \yii\db\ActiveRecord
{
    /**
     * @var integer 角色
     */
    const TYPE_ROLE = 1;
    /**
     * @var integer 权限
     */
    const TYPE_PERMISSION = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_item}}';
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_DELETE
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['company_id', 'parent_id', 'type', 'status', 'created_at', 'updated_at','visible','sort','industry_appid'], 'integer'],
            [['name','type'], 'unique', 'targetAttribute' => ['name', 'type'],'filter'=>['type'=>1,'company_id'=>Yii::$app->user->CompanyId], 'message' => '角色已存在'],
            [['description'], 'string'],
            [['name', 'url'], 'string', 'max' => 64],
        ];
    }

    /**
     * 自动更新created_at和updated_at时间
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键ID',
            'name' => '角色名',
            'url' => 'Url',
            'company_id' => '企业ID 0表示共用',
            'parent_id' => '父级',
            'type' => '类型',// 1角色 2权限,3 特殊权限,4 菜单
            'visible'=>'显示左菜单',
            'description' => '描述',
            'status' => '状态', // 1显示 0不显示
            'sort'  => '排序',
            'industry_appid'=>'所属应用',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
/*     public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $exists = AuthItem::find()
                ->where(['or', ['company_id' => Yii::$app->user->getCompanyId()], ['company_id' => 0]])
                ->andFilterWhere(['and', ['name' => $this->name, 'type' => AuthItem::TYPE_ROLE], ['<>', 'id', $this->id]])
                ->exists();
            if ($exists) {
                $this->addError('delete', '角色已经存在');
                return false;
            }
            return true;
        } else {
            return false;
        }
    } */

    /**
     * @return bool
     * @throws Exception
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if (AuthAssign::find()->where(['auth_item_id' => $this->id])->exists()) {
                throw new ForbiddenHttpException('角色已分配，不能删除');
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(AuthItem::className(), ['id' => 'child'])
            ->viaTable(AuthItemChild::tableName(), ['parent' => 'id']);
    }

    /**
     * @param array $conditions
     * @return AuthItem|object
     */
    public function getRole($conditions = [])
    {
        return AuthItem::find()
            ->where(['or', ['company_id' => Yii::$app->user->getCompanyId()], ['company_id' => 0]])
            ->andFilterWhere(['id' => $this->id, 'type' => AuthItem::TYPE_ROLE] + $conditions)
            ->one();
    }

    /**
     * @param array $conditions
     * @return array
     */
    public function getPermissionsTree($conditions = [])
    {
        $permissions = ArrayHelper::toArray($this->getPermissions($conditions));
        return ArrHelper::formatTree($permissions, 'id', 'parent_id', '');
    }

    public function getPermissions($conditions = [])
    {
        return self::findAll(['type' => self::TYPE_PERMISSION] + $conditions);
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getRoles()
    {
        return self::find()
            ->where(['or', ['company_id' => Yii::$app->user->getCompanyId()], ['company_id' => 0]])
            ->andWhere(['type' => AuthItem::TYPE_ROLE])
            ->all();
    }

    /**
     * Get items
     * @return array
     */
    public function getRolePermissions()
    {
        // 所有的路由权限
        $available = ArrayHelper::map($this->getPermissions(), 'url', 'name');

        $role = $this->getRole();
        // 该角色拥有的路由权限
        $assigned = ArrayHelper::map($role->children, 'url', 'name');
        return [
            'available' => array_diff($available, $assigned),
            'assigned' => $assigned,
        ];
    }

    /**
     * 批量插入权限
     * @param $routes
     * @return bool
     * @throws Exception
     */
    public static function savePermissions($routes)
    {
        if (!$routes) {
            return true;
        }
        $rows = [];
        $time = time();
        foreach ($routes as $key => $value) {
            $rows[$key]['name'] = $value;
            $rows[$key]['url'] = $value;
            $rows[$key]['type'] = self::TYPE_PERMISSION;
            $rows[$key]['created_at'] = $time;
            $rows[$key]['updated_at'] = $time;
        }
        if (!ModelHelper::saveAll(self::tableName(), $rows)) {
            Yii::error($rows, '批量插入权限失败');
            throw new Exception('批量插入权限失败');
        }
        return true;
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return ['不显示', '显示'];
    }
    
    
    /**
     * 编辑角色与权限
     * @param int       $type       1:添加    2：修改
     * @param string    $purview    权限，多个权限则逗号隔开
     * @param int       $id         角色id，只有type=2的时候才用上
     * @return bool
     */
    public function editAuth ($type = 1, $purview = '', $id = '') {
        if ($type == 1) {
            try {
                $trans = Yii::$app->db->beginTransaction();
                //第一步：保存角色基本信息
                if (!$this->save()) {
                    $this->addError(array_keys($this->errors)[0],current($this->errors)[0]);
                    throw new Exception(current($this->errors)[0]);
                }
                
                //第二步：为该角色添加新的权限
                if (!empty($purview)) {
                    $arr_purview = explode(',', $purview);
                    $rows = array();
                    foreach ($arr_purview as $k=>$v) {
                        $rows[$k]['parent'] = $this->primaryKey;
                        $rows[$k]['child']  = $v;
                    }
                    //批量添加
                    if (!ModelHelper::saveAll('auth_item_child', $rows)) {
                        $this->addError('add','操作失败');
                        throw new Exception('操作失败');
                    }
                }
                
                $trans->commit();
                return true;
            } catch (\Exception $e) {
                $trans->rollback();
                return false;
            }
            
            
        } elseif ($type == 2) {
            try {
                $trans = Yii::$app->db->beginTransaction();
                //第一步：保存角色基本信息
                if (!$this->save()) {
                    $this->addError(array_keys($this->errors)[0],current($this->errors)[0]);
                    throw new Exception(current($this->errors)[0]);
                }
                
                //第二步：删除改角色原来所有的权限
                if (!AuthItemChild::deleteAll(['parent'=>$id])) {
                    $this->addError('update','操作失败');
                    throw new Exception('操作失败');
                }
                
                //第三步：为该角色添加新的权限
                if (!empty($purview)) {
                    $arr_purview = explode(',', $purview);
                    $rows = array();
                    foreach ($arr_purview as $k=>$v) {
                        $rows[$k]['parent'] = $id;
                        $rows[$k]['child']  = $v;
                    }
                    //批量添加
                    if (!ModelHelper::saveAll('auth_item_child', $rows)) {
                        $this->addError('update','操作失败');
                        throw new Exception('操作失败');
                    }
                }
                
                $trans->commit();
                return true;
            } catch (\Exception $e) {
                $trans->rollback();
                return false;
            }

        }
    }
}
