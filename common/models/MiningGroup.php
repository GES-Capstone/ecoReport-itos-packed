<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "mining_group".
 *
 * @property int $id
 * @property int|null $location_id
 * @property string $name
 * @property string $ges_name
 * @property string|null $description
 * @property string|null $commercial_address
 * @property string|null $operational_address
 * @property string|null $phone
 * @property string|null $email
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $logo_path
 * @property string|null $logo_base_url
 *
 * @property Company[] $companies
 * @property Location $location
 * @property User[] $users
 */
class MiningGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mining_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['location_id'], 'integer'],
            [['name', 'ges_name'], 'required'],
            [['description', 'commercial_address', 'operational_address'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'ges_name', 'logo_path', 'logo_base_url'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 100],
            [['name'], 'unique'],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::class, 'targetAttribute' => ['location_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'location_id' => 'Location ID',
            'name' => 'Nombre del Grupo Minero',
            'ges_name' => 'Nombre del Grupo Minero',
            'description' => 'Description',
            'commercial_address' => 'Commercial Address',
            'operational_address' => 'Operational Address',
            'phone' => 'Phone',
            'email' => 'Email',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'logo_path' => 'Logo Path',
            'logo_base_url' => 'Logo Base Url',
        ];
    }

    /**
     * Gets query for [[Companies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Company::class, ['mining_group_id' => 'id']);
    }

    /**
     * Gets query for [[Location]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Location::class, ['id' => 'location_id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['mining_group_id' => 'id']);
    }
}
