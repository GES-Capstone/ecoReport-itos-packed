<?php
namespace common\models;

use Yii;

/**
 * This is the model class for table "fleet".
 *
 * @property int $id
 * @property int $mining_group_id
 * @property int $company_id
 * @property int $mining_process_id
 * @property int $area_id
 * @property int|null $location_id
 * @property string $name
 * @property string|null $description
 * @property string $created_at
 *
 * @property MiningGroup $miningGroup
 * @property Company $company
 * @property MiningProcess $miningProcess
 * @property Area $area
 * @property Location|null $location
 */
class Fleet extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%fleet}}';
    }

    public function rules()
    {
        return [
            [['mining_group_id', 'company_id', 'mining_process_id', 'area_id', 'name'], 'required'],
            [['mining_group_id', 'company_id', 'mining_process_id', 'area_id', 'location_id'], 'integer'],
            [['name', 'description'], 'string', 'max' => 255],
            [['created_at'], 'safe'],
            [['name'], 'validateUniqueNameInArea'],
            [['mining_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MiningGroup::class, 'targetAttribute' => ['mining_group_id' => 'id']],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
            [['mining_process_id'], 'exist', 'skipOnError' => true, 'targetClass' => MiningProcess::class, 'targetAttribute' => ['mining_process_id' => 'id']],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Area::class, 'targetAttribute' => ['area_id' => 'id']],
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
            'area_id' => Yii::t('backend', 'Area'),
            'location_id' => Yii::t('backend', 'Location'),
            'name' => Yii::t('backend', 'Fleet Name'),
            'description' => Yii::t('backend', 'Description'),
            'created_at' => Yii::t('backend', 'Created At'),
        ];
    }

    public function validateUniqueNameInArea($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $query = self::find()
                ->where(['area_id' => $this->area_id, 'name' => $this->name]);

            if (!$this->isNewRecord) {
                $query->andWhere(['<>', 'id', $this->id]);
            }

            if ($query->exists()) {
                $this->addError($attribute, Yii::t('backend', 'There is already a fleet with this name in the selected area.'));
            }
        }
    }

    public function getMiningGroup()
    {
        return $this->hasOne(MiningGroup::class, ['id' => 'mining_group_id']);
    }

    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id' => 'company_id']);
    }

    public function getMiningProcess()
    {
        return $this->hasOne(MiningProcess::class, ['id' => 'mining_process_id']);
    }

    public function getArea()
    {
        return $this->hasOne(Area::class, ['id' => 'area_id']);
    }

    public function getLocation()
    {
        return $this->hasOne(Location::class, ['id' => 'location_id']);
    }
}

