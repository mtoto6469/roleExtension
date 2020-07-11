<?php

namespace mroro\role\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property int $type
 * @property string|null $description
 * @property string|null $rule_name
 * @property resource|null $data
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthRule $ruleName
 * @property AuthItemChild[] $authItemChildren
 * @property AuthItemChild[] $authItemChildren0
 * @property Role[] $children
 * @property Role[] $parents
 */
class Role extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['name'], 'unique'],
            [['rule_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthorRule::className(), 'targetAttribute' => ['rule_name' => 'name']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'type' => Yii::t('app', 'Type'),
            'description' => Yii::t('app', 'Description'),
            'rule_name' => Yii::t('app', 'Rule Name'),
            'data' => Yii::t('app', 'Data'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[AuthAssignments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['item_name' => 'name']);
    }

    /**
     * Gets query for [[RuleName]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRuleName()
    {
        return $this->hasOne(AuthRule::className(), ['name' => 'rule_name']);
    }

    /**
     * Gets query for [[AuthItemChildren]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren()
    {
        return $this->hasMany(AuthItemChild::className(), ['parent' => 'name']);
    }

    /**
     * Gets query for [[AuthItemChildren0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren0()
    {
        return $this->hasMany(AuthItemChild::className(), ['child' => 'name']);
    }

    /**
     * Gets query for [[Children]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Role::className(), ['name' => 'child'])->viaTable('auth_item_child', ['parent' => 'name']);
    }

    /**
     * Gets query for [[Parents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParents()
    {
        return $this->hasMany(Role::className(), ['name' => 'parent'])->viaTable('auth_item_child', ['child' => 'name']);
    }
    private function all_roles(){

        return[

            'user'=>[
                ['name'=>'create_user','label'=>'create','checked'=>0],
                ['name'=>'update_user','label'=>'update','checked'=>0],
                ['name'=>'delete_user','label'=>'delete','checked'=>0],
            ],
        ];
    }
    public function getAllRoles()
    {
        $roles=$this->all_roles();
        if (!$this->isNewRecord){
            $db_all_rules=(new Query())
                ->select(['child'])
                ->from('auth_item_child')
                ->where(['parent'=>$this->name])
                ->all();
            $db_roles=[];
            foreach ($db_all_rules as $key=>$value)
            {
                array_push($db_roles,$value['child']);
            }
            foreach ($roles as $key=>$value)
            {
                foreach ($value as $keyItem=>$valueItem)
                {
                    if(in_array($valueItem['name'],$db_roles))
                    {
                        $roles[$key][$keyItem]['checked']=1;
                    }
                }

            }
        }
        return $roles;

    }
    public function save($runValidation = true, $attributeNames = NULL)
    {
        $auth=Yii::$app->authManager;
        $time=time();
        $sql="DELETE FROM `auth_item_child` WHERE `parent`='{$this->name}'";
        Yii::$app->db->createCommand($sql)->query();

        $items=Yii::$app->request->post('Items');

        $sql="INSERT IGNORE INTO `auth_item` (`name`,`type`,`description`,`rule_name`,`data`,`created_at`,`updated_at`) VALUES ('{$this->name}',1,'{$this->description}',null ,null , $time, $time)";
        Yii::$app->db->createCommand($sql)->query();

        if ($items != null){
            foreach ($items as $key=>$value ){

                $sql="INSERT IGNORE INTO `auth_item` (`name`,`type`,`description`,`rule_name`,`data`,`created_at`,`updated_at`) VALUES ('{$key}',2,'{$key}',null ,null , $time, $time)";//type= TYPE_ROLE =1
                Yii::$app->db->createCommand($sql)->query();

                $sql="INSERT INTO `auth_item_child`(`parent`, `child`) VALUES ('{$this->name}','{$key}')";
                Yii::$app->db->createCommand($sql)->query();


            }
        }

        return true;

    }

}
