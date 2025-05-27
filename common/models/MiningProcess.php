<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "mining_process".
 *
 * @property int $id
 * @property int $mining_group_id
 * @property int $company_id
 * @property int $location_id
 * @property string $name
 * @property string|null $description
 * @property string|null $created_at
 *
 * @property Location $location
 * @property Company $company
 * @property MiningGroup $miningGroup
 */
class MiningProcess extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%mining_process}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mining_group_id', 'company_id', 'location_id'], 'integer'],
            [['mining_group_id', 'company_id','name'], 'required'],
            [['name', 'description'], 'string', 'max' => 255],
            [['name'], 'validateUniqueNameInCompany'],
            [['created_at'], 'safe'],
            [['mining_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MiningGroup::class, 'targetAttribute' => ['mining_group_id' => 'id']],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::class, 'targetAttribute' => ['location_id' => 'id']],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'mining_group_id' => Yii::t('backend', 'Mining Group'),
            'company_id' => Yii::t('backend', 'Company'),
            'location_id' => Yii::t('backend', 'Location'),
            'name' => Yii::t('backend', 'Process Name'),
            'description' => Yii::t('backend', 'Description'),
            'created_at' => Yii::t('backend', 'Created At'),
        ];
    }

    /**
     * Gets query for [[Location]].
     */
    public function getLocation()
    {
        return $this->hasOne(Location::class, ['id' => 'location_id']);
    }

    /**
     * Gets query for [[Company]].
     */
    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id' => 'company_id']);
    }

    /**
     * Gets query for [[MiningGroup]].
     */
    public function getMiningGroup()
    {
        return $this->hasOne(MiningGroup::class, ['id' => 'mining_group_id']);
    }

    public function validateUniqueNameInCompany($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $query = MiningProcess::find()
                ->where([
                    'company_id' => $this->company_id,
                    'name' => $this->name,
                ]);

            if (!$this->isNewRecord) {
                $query->andWhere(['<>', 'id', $this->id]);
            }

            if ($query->exists()) {
                 $this->addError($attribute, Yii::t('backend', 'There is already a process with this name in the selected company.'));
            }
        }
    }
}
