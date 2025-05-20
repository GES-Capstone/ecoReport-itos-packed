<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%fleet}}".
 *
 * @property int $id
 * @property int|null $mining_group_id
 * @property int|null $area_id
 * @property int|null $location_id
 * @property string $name
 * @property string|null $description
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Area $area
 * @property Location $location
 * @property Machinery[] $machineries
 * @property MiningGroup $miningGroup
 */
class Fleet extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%fleet}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mining_group_id', 'area_id', 'location_id', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'required'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Area::class, 'targetAttribute' => ['area_id' => 'id']],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::class, 'targetAttribute' => ['location_id' => 'id']],
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
            'area_id' => Yii::t('app', 'Area ID'),
            'location_id' => Yii::t('app', 'Location ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Area]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\AreaQuery
     */
    public function getArea()
    {
        return $this->hasOne(Area::class, ['id' => 'area_id']);
    }

    /**
     * Gets query for [[Location]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\LocationQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Location::class, ['id' => 'location_id']);
    }

    /**
     * Gets query for [[Machineries]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\MachineryQuery
     */
    public function getMachineries()
    {
        return $this->hasMany(Machinery::class, ['fleet_id' => 'id']);
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
     * @return \common\models\query\FleetQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\FleetQuery(get_called_class());
    }
}
