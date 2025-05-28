<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%fleet}}".
 *
 * @property int $id
 * @property int|null $area_id
 * @property int|null $location_id
 * @property string $name
 * @property string|null $description
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Area $area
 * @property Location $location
 * @property Machinery[] $machineries
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
            [['area_id', 'location_id'], 'integer'],
            [['name'], 'required'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Area::class, 'targetAttribute' => ['area_id' => 'id']],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::class, 'targetAttribute' => ['location_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
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
     * {@inheritdoc}
     * @return \common\models\query\FleetQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\FleetQuery(get_called_class());
    }
}
