<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%functional_status}}".
 *
 * @property int $id
 * @property int $mining_group_id
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Machinery[] $machineries
 * @property MiningGroup $miningGroup
 */
class FunctionalStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%functional_status}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mining_group_id', 'status', 'created_at', 'updated_at'], 'required'],
            [['mining_group_id', 'created_at', 'updated_at'], 'integer'],
            [['status'], 'string', 'max' => 255],
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
            'status' => Yii::t('app', 'Status'),
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
        return $this->hasMany(Machinery::class, ['functional_status_id' => 'id']);
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
     * @return \common\models\query\FunctionalStatusQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\FunctionalStatusQuery(get_called_class());
    }
}
