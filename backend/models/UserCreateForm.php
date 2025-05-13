<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\UserProfile;

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
            [['firstname', 'middlename', 'lastname', 'email', 'password', 'status'], 'required'],
            [['username', 'firstname', 'middlename', 'lastname'], 'string', 'min' => 2, 'max' => 255],
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

            // Comenzar una transacción
            $transaction = Yii::$app->db->beginTransaction();

            try {
                // Crear y guardar el modelo de usuario
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

                // Crear el perfil de usuario y asociarlo al usuario
                $userProfile = new UserProfile();
                $userProfile->user_id = $model->id; // Asocia el perfil al usuario recién creado
                $userProfile->firstname = $this->firstname;
                $userProfile->middlename = $this->middlename;
                $userProfile->lastname = $this->lastname;

                if (!$userProfile->save()) {
                    $transaction->rollBack();
                    throw new \Exception('User profile not saved');
                }

                // Asignar roles al usuario
                $auth = Yii::$app->authManager;
                $auth->revokeAll($model->getId());

                if ($this->roles && is_array($this->roles)) {
                    foreach ($this->roles as $role) {
                        $auth->assign($auth->getRole($role), $model->getId());
                    }
                }

                // Confirmar la transacción
                $transaction->commit();

                return true;
            } catch (\Exception $e) {
                // Si algo falla, revertir la transacción
                $transaction->rollBack();
                Yii::error('Error creating user: ' . $e->getMessage());
                return false;
            }
        }

        return false;
    }
}
