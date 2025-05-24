<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%machinery_type}}".
 *
 * @property int $id
 * @property int|null $mining_group_id
 * @property string $name
 * @property string|null $description
 * @property string|null $photo_base_url
 * @property string|null $photo_path
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Machinery[] $machineries
 * @property MiningGroup $miningGroup
 */
class MachineryType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%machinery_type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mining_group_id'], 'integer'],
            [['name'], 'required'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'photo_base_url', 'photo_path'], 'string', 'max' => 255],
            [['mining_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MiningGroup::class, 'targetAttribute' => ['mining_group_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'mining_group_id' => Yii::t('app', 'Mining Group ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'photo_base_url' => Yii::t('app', 'Photo Base Url'),
            'photo_path' => Yii::t('app', 'Photo Path'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Machineries]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\MachineryQuery
     */
    public function getMachineries()
    {
        return $this->hasMany(Machinery::class, ['machinery_type_id' => 'id']);
    }

    /**
     * Gets query for [[MiningGroup]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\MiningGroupQuery
     */
    public function getMiningGroup()
    {
        return $this->hasOne(MiningGroup::class, ['id' => 'mining_group_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\MachineryTypeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\MachineryTypeQuery(get_called_class());
    }
}
