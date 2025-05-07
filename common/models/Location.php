<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "location" .
 *
 * @property int $id
 * @property float $latitude Latitude coordinate
 * @property float $longitude Longitude coordinate
 *
 * @property Company[] $companies
 * @property MiningGroup[] $miningGroups
 */
class Location extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%location}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['latitude', 'longitude'], 'required'],
            [['latitude', 'longitude'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
        ];
    }

    /**
     * Gets query for [[Companies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Company::class, ['location_id' => 'id']);
    }

    /**
     * Gets query for [[MiningGroups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMiningGroups()
    {
        return $this->hasMany(MiningGroup::class, ['location_id' => 'id']);
    }
}
