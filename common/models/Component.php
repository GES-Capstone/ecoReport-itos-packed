<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%component}}".
 *
 * @property int $id
 * @property int $machinery_id
 * @property int|null $location_id
 * @property string $name
 * @property string|null $tag
 * @property string|null $model
 * @property float|null $useful_life_years
 * @property int|null $useful_life_hours
 * @property string|null $supplier
 * @property float|null $cost
 * @property string|null $started_operations
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ComponentDocument[] $componentDocuments
 * @property Location $location
 * @property Machinery $machinery
 */
class Component extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%component}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['machinery_id', 'name'], 'required'],
            [['machinery_id', 'location_id', 'useful_life_hours'], 'integer'],
            [['useful_life_years', 'cost'], 'number'],
            [['started_operations', 'created_at', 'updated_at'], 'safe'],
            [['name', 'tag', 'model', 'supplier'], 'string', 'max' => 255],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::class, 'targetAttribute' => ['location_id' => 'id']],
            [['machinery_id'], 'exist', 'skipOnError' => true, 'targetClass' => Machinery::class, 'targetAttribute' => ['machinery_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'machinery_id' => Yii::t('app', 'Machinery ID'),
            'location_id' => Yii::t('app', 'Location ID'),
            'name' => Yii::t('app', 'Name'),
            'tag' => Yii::t('app', 'Tag'),
            'model' => Yii::t('app', 'Model'),
            'useful_life_years' => Yii::t('app', 'Useful Life Years'),
            'useful_life_hours' => Yii::t('app', 'Useful Life Hours'),
            'supplier' => Yii::t('app', 'Supplier'),
            'cost' => Yii::t('app', 'Cost'),
            'started_operations' => Yii::t('app', 'Started Operations'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[ComponentDocuments]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\ComponentDocumentQuery
     */
    public function getComponentDocuments()
    {
        return $this->hasMany(ComponentDocument::class, ['component_id' => 'id']);
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
     * Gets query for [[Machinery]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\MachineryQuery
     */
    public function getMachinery()
    {
        return $this->hasOne(Machinery::class, ['id' => 'machinery_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\ComponentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ComponentQuery(get_called_class());
    }
}
