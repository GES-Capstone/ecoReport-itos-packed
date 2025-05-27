<?php

namespace backend\models;

use common\models\User;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use backend\models\AuthAssignment;

/**
 * Create user form
 */
class UserForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $status;
    public $roles;
    public $mining_group_id;
    private $model;
    public $permissions = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => User::class, 'filter' => function ($query) {
                if (!$this->getModel()->isNewRecord) {
                    $query->andWhere(['not', ['id' => $this->getModel()->id]]);
                }
            }],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class, 'filter' => function ($query) {
                if (!$this->getModel()->isNewRecord) {
                    $query->andWhere(['not', ['id' => $this->getModel()->id]]);
                }
            }],

            ['password', 'required', 'on' => 'create'],
            ['password', 'string', 'min' => 6],
            [['status'], 'integer'],
            ['roles', 'required'],
            ['roles', 'string'],
            ['roles', 'in', 'range' => ArrayHelper::getColumn(Yii::$app->authManager->getRoles(), 'name')],
            [
                ['permissions'],
                'each',
                'rule' => ['in', 'range' => ArrayHelper::getColumn(
                    Yii::$app->authManager->getPermissions(),
                    'name'
                )]
            ],
            [['mining_group_id'], 'integer'],
            [['mining_group_id'], 'exist', 'targetClass' => \common\models\MiningGroup::class, 'targetAttribute' => ['mining_group_id' => 'id']],
        ];
    }

    /**
     * @return User
     */
    public function getModel()
    {
        if (!$this->model) {
            $this->model = new User();
        }
        return $this->model;
    }

    /**
     * @param User $model
     * @return User
     */
    public function setModel($model)
    {
        $this->username = $model->username;
        $this->email = $model->email;
        $this->status = $model->status;
        $this->mining_group_id = $model->mining_group_id;
        $this->model = $model;
        $this->roles = array_key_first(Yii::$app->authManager->getRolesByUser($model->getId()));

        $auth = Yii::$app->authManager;
        $rolePerms = array_keys($auth->getPermissionsByRole($this->roles));
        $allAssignments = ArrayHelper::getColumn(
            $auth->getPermissionsByUser($model->getId()),
            'name'
        );
        $directPerms = array_diff($allAssignments, $rolePerms);

        $this->permissions = $directPerms;

        return $this->model;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('backend', 'Username'),
            'email' => Yii::t('backend', 'Email'),
            'password' => Yii::t('backend', 'Password'),
            'status' => Yii::t('backend', 'Status'),
            'mining_group_id' => Yii::t('backend', 'Mining Group ID'),
            'roles' => Yii::t('backend', 'Roles'),
            'permissions' => Yii::t('backend', 'Permissions'),
        ];
    }

    /**
     * Signs user up.
     * @return User|null t
     * @throws Exception
     */
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
                throw new Exception('Model not saved');
            }

            if ($isNewRecord) {
                $model->afterSignup();
            }

            $auth = Yii::$app->authManager;
            $auth->revokeAll($model->getId());

            if (!empty($this->roles)) {
                $role = $auth->getRole($this->roles);
                if ($role) {
                    $auth->assign($role, $model->getId());
                }
            }
            $rolePermissions = [];
            if (!empty($this->roles)) {
                $rolePermissions = array_keys($auth->getPermissionsByRole($this->roles));
            }

            if (Yii::$app->user->can('changePermissions') && is_array($this->permissions)) {
                foreach ($this->permissions as $permName) {
                    if (!in_array($permName, $rolePermissions)) {
                        $permission = $auth->getPermission($permName);
                        if ($permission) {
                            $auth->assign($permission, $model->getId());
                        }
                    }
                }
            }

            return !$model->hasErrors();
        }
        return null;
    }
}
