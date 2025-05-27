<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "area".
 *
 * @property int $id
 * @property int $mining_group_id
 * @property int $company_id
 * @property int $mining_process_id
 * @property int $location_id
 * @property string $name
 * @property string|null $description
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $created_at
 *
 * @property Location $location
 * @property Company $company
 * @property MiningGroup $miningGroup
 * @property MiningProcess $miningProcess
 */
class Area extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%area}}';
    }

    public function rules()
    {
        return [
            [['mining_group_id', 'company_id', 'mining_process_id', 'location_id'], 'integer'],
            [['mining_group_id', 'company_id', 'mining_process_id', 'name'], 'required'],
            [['name', 'description'], 'string', 'max' => 255],
            [['created_at'], 'safe'],
            [['name'], 'validateUniqueNameInProcess'],
            [['mining_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MiningGroup::class, 'targetAttribute' => ['mining_group_id' => 'id']],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
            [['mining_process_id'], 'exist', 'skipOnError' => true, 'targetClass' => MiningProcess::class, 'targetAttribute' => ['mining_process_id' => 'id']],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::class, 'targetAttribute' => ['location_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'mining_group_id' => Yii::t('backend', 'Mining Group'),
            'company_id' => Yii::t('backend', 'Company'),
            'mining_process_id' => Yii::t('backend', 'Mining Process'),
            'location_id' => Yii::t('backend', 'Location'),
            'name' => Yii::t('backend', 'Area Name'),
            'description' => Yii::t('backend', 'Description'),
            'created_at' => Yii::t('backend', 'Created At'),
        ];
    }

    public function getLocation()
    {
        return $this->hasOne(Location::class, ['id' => 'location_id']);
    }

    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id' => 'company_id']);
    }

    public function getMiningGroup()
    {
        return $this->hasOne(MiningGroup::class, ['id' => 'mining_group_id']);
    }

    public function getMiningProcess()
    {
        return $this->hasOne(MiningProcess::class, ['id' => 'mining_process_id']);
    }

    public function validateUniqueNameInProcess($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $query = self::find()
                ->where([
                    'mining_process_id' => $this->mining_process_id,
                    'name' => $this->name,
                ]);

            if (!$this->isNewRecord) {
                $query->andWhere(['<>', 'id', $this->id]);
            }

            if ($query->exists()) {
                $this->addError($attribute, Yii::t('backend', 'There is already an area with this name in the selected mining process.'));
            }
        }
    }
}
