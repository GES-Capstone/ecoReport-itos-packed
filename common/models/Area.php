<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%area}}".
 *
 * @property int $id
 * @property int|null $mining_group_id
 * @property int|null $mining_process_id
 * @property string $name
 * @property string|null $description
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Fleet[] $fleets
 * @property MiningGroup $miningGroup
 * @property MiningProcess $miningProcess
 */
class Area extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%area}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mining_group_id', 'mining_process_id'], 'integer'],
            [['name'], 'required'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['mining_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MiningGroup::class, 'targetAttribute' => ['mining_group_id' => 'id']],
            [['mining_process_id'], 'exist', 'skipOnError' => true, 'targetClass' => MiningProcess::class, 'targetAttribute' => ['mining_process_id' => 'id']],
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
            'mining_process_id' => Yii::t('app', 'Mining Process ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Fleets]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\FleetQuery
     */
    public function getFleets()
    {
        return $this->hasMany(Fleet::class, ['area_id' => 'id']);
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
     * Gets query for [[MiningProcess]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\MiningProcessQuery
     */
    public function getMiningProcess()
    {
        return $this->hasOne(MiningProcess::class, ['id' => 'mining_process_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\AreaQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\AreaQuery(get_called_class());
    }
}
