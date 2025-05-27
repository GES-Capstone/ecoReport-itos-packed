<?php

namespace common\models;

use Yii;
use trntv\filekit\behaviors\UploadBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "mining_group".
 *
 * @property int $id
 * @property int|null $location_id
 * @property string $name
 * @property string $ges_name
 * @property string|null $description
 * @property string|null $commercial_address
 * @property string|null $operational_address
 * @property string|null $phone
 * @property string|null $email
 * @property string $created_at
 * @property string $updated_at
 * @property string $picture
 * @property string|null $logo_path
 * @property string|null $logo_base_url
 *
 * @property Company[] $companies
 * @property Location $location
 * @property User[] $users
 */
class MiningGroup extends ActiveRecord
{
    /**
     * @var 
     */
    public $picture;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%mining_group}}';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'picture' => [
                'class' => UploadBehavior::class,
                'attribute' => 'picture',
                'pathAttribute' => 'logo_path',
                'baseUrlAttribute' => 'logo_base_url'
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['location_id'], 'integer'],
            [['name', 'ges_name'], 'required'],
            [['description', 'commercial_address', 'operational_address'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'ges_name', 'logo_path', 'logo_base_url'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 100],
            [['name'], 'unique'],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::class, 'targetAttribute' => ['location_id' => 'id']],
            ['picture', 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'location_id' => 'Location ID',
            'name' => 'Nombre del Grupo Minero',
            'ges_name' => 'Nombre  GES del Grupo Minero',
            'description' => 'Description',
            'commercial_address' => 'Commercial Address',
            'operational_address' => 'Operational Address',
            'phone' => 'Phone',
            'email' => 'Email',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'picture' => Yii::t('common', 'Picture'),
        ];
    }

    /**
     * Gets query for [[Companies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Company::class, ['mining_group_id' => 'id']);
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
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['mining_group_id' => 'id']);
    }

    /**
     * @param null $default
     * @return bool|null|string
     */
    public function getLogo($default = null)
    {
        return $this->logo_path
            ? Yii::getAlias($this->logo_base_url . '/' . $this->logo_path)
            : $default;
    }
}
