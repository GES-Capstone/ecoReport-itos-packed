<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%machinery}}".
 *
 * @property int $id
 * @property int|null $fleet_id
 * @property int|null $functional_status_id
 * @property int|null $machinery_type_id
 * @property int|null $location_id
 * @property string $tag
 * @property string $unique_tag
 * @property string|null $brand
 * @property string|null $model
 * @property string|null $start_operation
 * @property int|null $lifespan_years
 * @property string|null $supplier
 * @property float|null $cost
 * @property string|null $sap_code
 * @property string|null $description
 * @property string|null $photo_base_url
 * @property string|null $photo_path
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $inspection_type
 * @property string|null $family
 *
 * @property Component[] $components
 * @property Fleet $fleet
 * @property FunctionalStatus $functionalStatus
 * @property Location $location
 * @property MachineryDocument[] $machineryDocuments
 * @property MachineryType $machineryType
 */
class Machinery extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%machinery}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fleet_id', 'functional_status_id', 'machinery_type_id', 'location_id', 'lifespan_years'], 'integer'],
            [['tag', 'unique_tag'], 'required'],
            [['start_operation', 'created_at', 'updated_at'], 'safe'],
            [['cost'], 'number'],
            [['description', 'inspection_type', 'family'], 'string'],
            [['tag', 'brand', 'model', 'supplier', 'sap_code', 'photo_base_url', 'photo_path'], 'string', 'max' => 255],
            [['unique_tag'], 'string', 'max' => 512],
            [['fleet_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fleet::class, 'targetAttribute' => ['fleet_id' => 'id']],
            [['functional_status_id'], 'exist', 'skipOnError' => true, 'targetClass' => FunctionalStatus::class, 'targetAttribute' => ['functional_status_id' => 'id']],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::class, 'targetAttribute' => ['location_id' => 'id']],
            [['machinery_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => MachineryType::class, 'targetAttribute' => ['machinery_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fleet_id' => Yii::t('app', 'Fleet ID'),
            'functional_status_id' => Yii::t('app', 'Functional Status ID'),
            'machinery_type_id' => Yii::t('app', 'Machinery Type ID'),
            'location_id' => Yii::t('app', 'Location ID'),
            'tag' => Yii::t('app', 'Tag'),
            'unique_tag' => Yii::t('app', 'Unique Tag'),
            'brand' => Yii::t('app', 'Brand'),
            'model' => Yii::t('app', 'Model'),
            'start_operation' => Yii::t('app', 'Start Operation'),
            'lifespan_years' => Yii::t('app', 'Lifespan Years'),
            'supplier' => Yii::t('app', 'Supplier'),
            'cost' => Yii::t('app', 'Cost'),
            'sap_code' => Yii::t('app', 'Sap Code'),
            'description' => Yii::t('app', 'Description'),
            'photo_base_url' => Yii::t('app', 'Photo Base Url'),
            'photo_path' => Yii::t('app', 'Photo Path'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'inspection_type' => Yii::t('app', 'Inspection Type'),
            'family' => Yii::t('app', 'Family'),
        ];
    }

    /**
     * Gets query for [[Components]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\ComponentQuery
     */
    public function getComponents()
    {
        return $this->hasMany(Component::class, ['machinery_id' => 'id']);
    }

    /**
     * Gets query for [[Fleet]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\FleetQuery
     */
    public function getFleet()
    {
        return $this->hasOne(Fleet::class, ['id' => 'fleet_id']);
    }

    /**
     * Gets query for [[FunctionalStatus]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\FunctionalStatusQuery
     */
    public function getFunctionalStatus()
    {
        return $this->hasOne(FunctionalStatus::class, ['id' => 'functional_status_id']);
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
     * Gets query for [[MachineryDocuments]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\MachineryDocumentQuery
     */
    public function getMachineryDocuments()
    {
        return $this->hasMany(MachineryDocument::class, ['machinery_id' => 'id']);
    }

    /**
     * Gets query for [[MachineryType]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\MachineryTypeQuery
     */
    public function getMachineryType()
    {
        return $this->hasOne(MachineryType::class, ['id' => 'machinery_type_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\MachineryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\MachineryQuery(get_called_class());
    }
}
