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
 * @property int|null $location_id
 * @property string $name
 * @property string|null $description
 * @property string $created_at
 *
 * @property Company $company
 * @property Fleet[] $fleets
 * @property Location $location
 * @property MiningGroup $miningGroup
 * @property MiningProcess $miningProcess
 */
class Area extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'area';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mining_group_id', 'company_id', 'mining_process_id', 'name'], 'required'],
            [['mining_group_id', 'company_id', 'mining_process_id', 'location_id'], 'integer'],
            [['created_at'], 'safe'],
            [['name'], 'validateUniqueNameInProcess'],
            [['name', 'description'], 'string', 'max' => 255],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::class, 'targetAttribute' => ['location_id' => 'id']],
            [['mining_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MiningGroup::class, 'targetAttribute' => ['mining_group_id' => 'id']],
            [['mining_process_id'], 'exist', 'skipOnError' => true, 'targetClass' => MiningProcess::class, 'targetAttribute' => ['mining_process_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'mining_group_id' => Yii::t('backend', 'Mining Group ID'),
            'company_id' => Yii::t('backend', 'Company ID'),
            'mining_process_id' => Yii::t('backend', 'Mining Process ID'),
            'location_id' => Yii::t('backend', 'Location ID'),
            'name' => Yii::t('backend', 'Name'),
            'description' => Yii::t('backend', 'Description'),
            'created_at' => Yii::t('backend', 'Created At'),
        ];
    }

    /**
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\CompanyQuery
     */
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
     * Gets query for [[MiningGroup]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\MiningGroupQuery
     */
    public function getMiningGroup()
    {
        return $this->hasOne(MiningGroup::class, ['id' => 'mining_group_id']);
    }

    /**
     * Gets query for [[MiningProcess]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\MiningProcessQuery
     */
    public function getMiningProcess()
    {
        return $this->hasOne(MiningProcess::class, ['id' => 'mining_process_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\AreaQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\AreaQuery(get_called_class());
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
