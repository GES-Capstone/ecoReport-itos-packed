<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property int|null $mining_group_id
 * @property int|null $location_id
 * @property string $name
 * @property string|null $description
 * @property string|null $commercial_address
 * @property string|null $operational_address
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $logo_path Path to logo image
 * @property string|null $logo_base_url Base URL for logo image
 *
 * @property Location $location
 * @property MiningGroup $miningGroup
 * @property UserScope[] $userScopes
 * @property User[] $users
 */
class Company extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mining_group_id', 'location_id'], 'integer'],
            [['name'], 'required'],
            [['description'], 'string'],
            [['name', 'commercial_address', 'operational_address', 'logo_path', 'logo_base_url'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 100],
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
            'id' => 'ID',
            'mining_group_id' => 'Mining Group ID',
            'location_id' => 'Location ID',
            'name' => 'Name',
            'description' => 'Description',
            'commercial_address' => 'Commercial Address',
            'operational_address' => 'Operational Address',
            'phone' => 'Phone',
            'email' => 'Email',
            'logo_path' => 'Logo Path',
            'logo_base_url' => 'Logo Base Url',
        ];
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
     * Gets query for [[MiningGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMiningGroup()
    {
        return $this->hasOne(MiningGroup::class, ['id' => 'mining_group_id']);
    }

    /**
     * Gets query for [[UserScopes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserScopes()
    {
        return $this->hasMany(UserScope::class, ['company_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('user_scope', ['company_id' => 'id']);
    }
}
