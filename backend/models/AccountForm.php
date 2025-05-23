<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Account form
 */
class AccountForm extends Model
{
    public $username;
    public $email;
    public $current_password;
    public $password;
    public $password_confirm;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            [
                'username',
                'unique',
                'targetClass' => '\common\models\User',
                'message' => Yii::t('backend', 'This username has already been taken.'),
                'filter' => function ($query) {
                    $query->andWhere(['not', ['id' => Yii::$app->user->id]]);
                }
            ],
            ['username', 'string', 'min' => 1, 'max' => 255],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            [
                'email',
                'unique',
                'targetClass' => '\common\models\User',
                'message' => Yii::t('backend', 'This email has already been taken.'),
                'filter' => function ($query) {
                    $query->andWhere(['not', ['id' => Yii::$app->user->getId()]]);
                }
            ],
            ['current_password', 'required', 'when' => function ($model) {
                return !empty($model->password);
            }],
            ['current_password', 'string', 'min' => 6],
            ['current_password', 'validateCurrentPassword'],
            ['current_password', 'required', 'when' => function ($model) {
                return !empty($model->password);
            }],
            ['password', 'string', 'min' => 6],
            [['password_confirm'], 'compare', 'compareAttribute' => 'password']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('backend', 'Username'),
            'email' => Yii::t('backend', 'Email'),
            'current_password' => Yii::t('backend', 'Current Password'),
            'password' => Yii::t('backend', 'Password'),
            'password_confirm' => Yii::t('backend', 'Password Confirm')
        ];
    }

    public function validateCurrentPassword($attribute, $params)
    {
        $user = User::findOne(Yii::$app->user->id);
        if (!$user || !$user->validatePassword($this->current_password)) {
            $this->addError($attribute, Yii::t('backend', 'The current password is incorrect.'));
        }
    }
}
