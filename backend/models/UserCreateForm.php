<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;

class UserCreateForm extends Model
{
    public $firstname;
    public $middlename;
    public $lastname;
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
            [['firstname','middlename','lastname', 'email', 'password', 'status'], 'required'],
            [['username','firstname','middlename','lastname'], 'string', 'min' => 2, 'max' => 255],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'This email has already been taken.'],
            ['password', 'string', 'min' => 6],
            [['status'], 'integer'],
            [['roles'], 'each', 'rule' => ['in', 'range' => array_keys(Yii::$app->authManager->getRoles())]],
            [['mining_group_id'], 'integer'],
        ];
    }   

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('backend', 'Username'),
            'email' => Yii::t('backend', 'Email'),
            'password' => Yii::t('backend', 'Password'),
            'status' => Yii::t('backend', 'Status'),
            'roles' => Yii::t('backend', 'Roles'),
            'mining_group_id' => Yii::t('backend', 'Mining Group ID'),
        ];
    }
    
    public function setModel(){

        $this->model->username = $this->username;
        $this->model->email = $this->email;
        $this->model->password = Yii::$app->security->generatePasswordHash($this->password);
        $this->model->status = $this->status;
        $this->model->roles = $this->roles;
        $this->model->firstname = $this->firstname;
        $this->model->middlename = $this->middlename;
        $this->model->lastname = $this->lastname;
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
            $model->username = $this->username;
            $model->email = $this->email;
            $model->status = $this->status;
            $model->mining_group_id = $this->mining_group_id;
            if ($this->password) {
                $model->setPassword($this->password);
            }
            if (!$model->save()) {
                throw new \Exception('Model not saved');
            }
            if ($isNewRecord) {
                $model->afterSignup();
            }
            $auth = Yii::$app->authManager;
            $auth->revokeAll($model->getId());

            if ($this->roles && is_array($this->roles)) {
                foreach ($this->roles as $role) {
                    $auth->assign($auth->getRole($role), $model->getId());
                }
            }

            return !$model->hasErrors();
        }
        return null;
        return false;
    }

}