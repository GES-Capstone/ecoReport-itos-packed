<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\UserProfile;
use yii\helpers\ArrayHelper;

class UserCreateForm extends Model
{
    public $firstname;
    public $middlename;
    public $lastname;
    public $profession;
    public $username;
    public $email;
    public $password;
    public $status;
    public $roles;
    public $model;
    public $mining_group_id;

    public function rules()
    {
        return [
            [['firstname', 'middlename', 'lastname', 'email', 'status'], 'required'],
            [['username', 'firstname', 'middlename', 'lastname', 'profession'], 'string', 'min' => 2, 'max' => 255],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'This email has already been taken.'],
            ['password', 'string', 'min' => 6],
            [['status'], 'integer'],
            ['roles', 'required'],
            ['roles', 'required'],
            ['roles', 'string'],
            ['roles', 'in', 'range' => ArrayHelper::getColumn(
                Yii::$app->authManager->getRoles(),
                'name'
            )],
            [['mining_group_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('backend', 'Username'),
            'email' => Yii::t('backend', 'Email'),
            'password' => Yii::t('backend', 'Password'),
            'firstname' => Yii::t('common', 'Firstname'),
            'middlename' => Yii::t('common', 'Middlename'),
            'lastname' => Yii::t('common', 'Lastname'),
            'profession' => Yii::t('common', 'Profession'),
            'status' => Yii::t('backend', 'Status'),
            'roles' => Yii::t('backend', 'Roles'),
            'mining_group_id' => Yii::t('backend', 'Mining Group ID'),
        ];
    }

    public function setModel()
    {
        $this->model->username = $this->username;
        $this->model->email = $this->email;
        $this->model->password = Yii::$app->security->generatePasswordHash($this->password);
        $this->model->status = $this->status;
        $this->model->roles = $this->roles;
        $this->model->firstname = $this->firstname;
        $this->model->middlename = $this->middlename;
        $this->model->lastname = $this->lastname;
        $this->model->profession = $this->profession;
    }

    public function getModel()
    {
        if ($this->model === null) {
            $this->model = new User();
        }
        return $this->model;
    }

    public function save()
    {
        if ($this->validate()) {
            $model = $this->getModel();
            $isNewRecord = $model->getIsNewRecord();

            $transaction = Yii::$app->db->beginTransaction();

            try {
                $model->username = $this->username;
                $model->email = $this->email;
                $model->status = $this->status;
                $model->mining_group_id = $this->mining_group_id;
                if ($this->password) {
                    $model->setPassword($this->password);
                }
                if (!$model->save()) {
                    $transaction->rollBack();
                    throw new \Exception('User model not saved');
                }

                $userProfile = new UserProfile();
                $userProfile->user_id = $model->id;
                $userProfile->firstname = $this->firstname;
                $userProfile->middlename = $this->middlename;
                $userProfile->lastname = $this->lastname;
                $userProfile->profession = $this->profession;

                if (!$userProfile->save()) {
                    $transaction->rollBack();
                    throw new \Exception('User profile not saved');
                }

                $auth = Yii::$app->authManager;
                $auth->revokeAll($model->getId());

                if (!empty($this->roles)) {
                    $role = $auth->getRole($this->roles);
                    if ($role) {
                        $auth->assign($role, $model->getId());
                    }
                }

                $transaction->commit();

                return true;
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error('Error creating user: ' . $e->getMessage());
                return false;
            }
        }

        return false;
    }
}
