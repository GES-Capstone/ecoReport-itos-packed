<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%area}}".
 *
 * @property int $id
 * @property int|null $mining_group_id
 * @property int|null $company_id
 * @property string $name
 * @property string|null $description
 *
 * @property Company $company
 * @property Fleet[] $fleets
 * @property MiningGroup $miningGroup
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
            [['mining_group_id', 'company_id'], 'integer'],
            [['name'], 'required'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
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
            'company_id' => Yii::t('app', 'Company ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
        ];
    }


    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id' => 'company_id']);
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


    public function getMiningGroup()
    {
        return $this->hasOne(MiningGroup::class, ['id' => 'mining_group_id']);
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
