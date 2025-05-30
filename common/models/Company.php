<?php

namespace common\models;

use Yii;
use trntv\filekit\behaviors\UploadBehavior;

/**
 * This is the model class for table "{{%company}}".
 *
 * @property int $id
 * @property int|null $mining_group_id
 * @property int|null $location_id
 * @property string $name
 * @property string|null $description
 * @property string|null $commercial_address
 * @property string|null $operational_address
 * @property string|null $phone
 * @property string|null $email
 * @property string $picture
 * @property string|null $logo_path Path to logo image
 * @property string|null $logo_base_url Base URL for logo image
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Area[] $areas
 * @property Location $location
 * @property MiningGroup $miningGroup
 * @property UserScope[] $userScopes
 * @property User[] $users
 */
class Company extends \yii\db\ActiveRecord
{
    /** @var \yii\web\UploadedFile|string|null */
    public $picture;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%company}}';
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
            [['mining_group_id', 'location_id'], 'integer'],
            [['name'], 'required'],
            [['name'], 'validateUniqueNameInGroup'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'commercial_address', 'operational_address', 'logo_path', 'logo_base_url'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 100],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::class, 'targetAttribute' => ['location_id' => 'id']],
            [['mining_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MiningGroup::class, 'targetAttribute' => ['mining_group_id' => 'id']],
            ['picture', 'safe']
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
            'location_id' => Yii::t('backend', 'Location ID'),
            'name' => Yii::t('backend', 'Name'),
            'description' => Yii::t('backend', 'Description'),
            'commercial_address' => Yii::t('backend', 'Commercial Address'),
            'operational_address' => Yii::t('backend', 'Operational Address'),
            'phone' => Yii::t('backend', 'Phone'),
            'email' => Yii::t('backend', 'Email'),
            'logo_path' => Yii::t('backend', 'Logo Path'),
            'logo_base_url' => Yii::t('backend', 'Logo Base Url'),
            'picture' => Yii::t('backend', 'Picture'),
        ];
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
     * Gets query for [[MiningGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMiningGroup()
    {
        return $this->hasOne(MiningGroup::class, ['id' => 'mining_group_id']);
    }


    public function getUserScopes()
    {
        return $this->hasMany(UserScope::class, ['company_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('{{%user_scope}}', ['company_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\CompanyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CompanyQuery(get_called_class());
    }

    public function getLogo($default = null)
    {
        return $this->logo_path
            ? Yii::getAlias($this->logo_base_url . '/' . $this->logo_path)
            : $default;
    }

    public function validateUniqueNameInGroup($attribute, $params)
    {
        $query = Company::find()
            ->where(['name' => $this->name, 'mining_group_id' => $this->mining_group_id]);

        if (!$this->isNewRecord) {
            $query->andWhere(['<>', 'id', $this->id]);
        }

        if ($query->exists()) {
            $this->addError($attribute, Yii::t('backend', 'There is already a company with this name in the mining group.'));
        }
    }
}
