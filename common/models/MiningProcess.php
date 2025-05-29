<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "mining_process".
 *
 * @property int $id
 * @property int $mining_group_id
 * @property int $company_id
 * @property int|null $location_id
 * @property string $name
 * @property string|null $description
 * @property string $created_at
 *
 * @property Area[] $areas
 * @property Company $company
 * @property Location $location
 * @property MiningGroup $miningGroup
 */
class MiningProcess extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mining_process';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mining_group_id', 'company_id', 'name'], 'required'],
            [['mining_group_id', 'company_id', 'location_id'], 'integer'],
            [['name'], 'validateUniqueNameInCompany'],
            [['mining_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MiningGroup::class, 'targetAttribute' => ['mining_group_id' => 'id']],
            [['created_at'], 'safe'],
            [['name', 'description'], 'string', 'max' => 255],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
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
            'company_id' => Yii::t('app', 'Company ID'),
            'location_id' => Yii::t('app', 'Location ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
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
     * Gets query for [[Location]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\LocationQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Location::class, ['id' => 'location_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\MiningProcessQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\MiningProcessQuery(get_called_class());
    }

    /**
     * Gets query for [[Areas]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\AreaQuery
     */
    public function getAreas()
    {
        return $this->hasMany(Area::class, ['mining_process_id' => 'id']);
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
}
