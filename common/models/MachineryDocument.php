<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%machinery_document}}".
 *
 * @property int $id
 * @property int $machinery_id
 * @property string|null $type
 * @property string $name
 * @property string|null $base_url
 * @property string|null $file_path
 * @property int|null $file_size
 * @property string|null $mime_type
 * @property string|null $description
 * @property string|null $upload_date
 * @property string|null $expiry_date
 * @property string|null $version
 * @property int|null $created_by
 *
 * @property Machinery $machinery
 */
class MachineryDocument extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%machinery_document}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['machinery_id', 'name'], 'required'],
            [['machinery_id', 'file_size', 'created_by'], 'integer'],
            [['type', 'description'], 'string'],
            [['upload_date', 'expiry_date'], 'safe'],
            [['name', 'base_url', 'file_path', 'mime_type', 'version'], 'string', 'max' => 255],
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
            'type' => Yii::t('app', 'Type'),
            'name' => Yii::t('app', 'Name'),
            'base_url' => Yii::t('app', 'Base Url'),
            'file_path' => Yii::t('app', 'File Path'),
            'file_size' => Yii::t('app', 'File Size'),
            'mime_type' => Yii::t('app', 'Mime Type'),
            'description' => Yii::t('app', 'Description'),
            'upload_date' => Yii::t('app', 'Upload Date'),
            'expiry_date' => Yii::t('app', 'Expiry Date'),
            'version' => Yii::t('app', 'Version'),
            'created_by' => Yii::t('app', 'Created By'),
        ];
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
     * @return \common\models\query\MachineryDocumentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\MachineryDocumentQuery(get_called_class());
    }
}
