<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "location".
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
    public $location_url;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%location}}';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['default'] = ['location_url', 'latitude', 'longitude'];
        $scenarios['optional'] = ['location_url', 'latitude', 'longitude'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['location_url'], 'required', 'on' => self::SCENARIO_DEFAULT, 'message' => Yii::t('backend', '{attribute} cannot be blank.')],
            ['location_url', 'match', 'pattern' => '/^\s*-?\d+(\.\d+)?\s*,\s*-?\d+(\.\d+)?\s*$/', 'message' => Yii::t('backend', 'Incorrect format. Use: latitude,longitude')],
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
            'location_url' => Yii::t('backend', 'Coordinates (latitude,longitude)'),
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

    public function afterFind()
    {
        parent::afterFind();
        $this->location_url = rtrim(rtrim($this->latitude, '0'), '.') . ',' . rtrim(rtrim($this->longitude, '0'), '.');
    }

    public function beforeValidate()
    {
        if (!empty($this->location_url) && strpos($this->location_url, ',') !== false) {
            list($lat, $lng) = explode(',', $this->location_url);
            $this->latitude = trim($lat);
            $this->longitude = trim($lng);
        }
        return parent::beforeValidate();
    }

   
}
