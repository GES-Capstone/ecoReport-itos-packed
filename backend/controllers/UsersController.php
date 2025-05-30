<?php

namespace backend\controllers;

use common\models\UserProfile;
use common\models\User;
use Yii;
use yii\web\Controller;
use backend\models\UserForm;
use backend\models\UserCreateForm;
use yii\web\NotFoundHttpException;
use backend\models\GroupMiningCreateForm;
use common\models\Company;
use trntv\filekit\actions\UploadAction;
use common\models\MiningGroup;
use common\models\InitialConfiguration;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;

class UsersController extends Controller
{
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'activate' => ['post'],
                    'restore' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCreate()
    {
        $model = new UserCreateForm();
        $modelGM = new GroupMiningCreateForm();
        $rolesList = $this->getRolesWithDescriptions();

        $selectedGroupId = null;

        if (Yii::$app->request->post()) {
            $modelGM->load(Yii::$app->request->post());
            $model->load(Yii::$app->request->post());

            $randomPassword = Yii::$app->security->generateRandomString(10);
            $model->password = $randomPassword;

            $selectedGroupId = Yii::$app->request->post('selected_mining_group_id', null);

            if (empty($model->roles)) {
                Yii::$app->session->setFlash('error', Yii::t('backend', 'Please Select a Role.'));
                return $this->render('create', [
                    'model' => $model,
                    'modelGM' => $modelGM,
                    'roles' => $this->getRolesWithDescriptions(),
                    'miningGroups' => $this->getMiningGroupsList()
                ]);
            }

            if (empty($model->status)) {
                Yii::$app->session->setFlash('error', Yii::t('backend', 'Please Select a Status.'));
                return $this->render('create', [
                    'model' => $model,
                    'modelGM' => $modelGM,
                    'roles' => $this->getRolesWithDescriptions(),
                    'miningGroups' => $this->getMiningGroupsList()
                ]);
            }

            $miningGroupId = null;

            if (!empty($selectedGroupId)) {
                $miningGroupId = $selectedGroupId;
            } else if (!empty($modelGM->ges_name)) {
                $existingGroup = MiningGroup::find()
                    ->where([
                        'or',
                        ['name' => $modelGM->ges_name],
                        ['ges_name' => $modelGM->ges_name]
                    ])
                    ->one();

                if ($existingGroup) {
                    $miningGroupId = $existingGroup->id;
                    Yii::$app->session->setFlash('info', Yii::t('backend', 'User assigned to existing mining group.'));
                } else {
                    if ($modelGM->save()) {
                        $miningGroupId = $modelGM->miningGroup->id;

                        $initialConfig = new InitialConfiguration();
                        $initialConfig->step = 0;
                        $initialConfig->status = 'not started';
                        $initialConfig->mining_group_id = $miningGroupId;
                        if (!$initialConfig->save()) {
                            Yii::$app->session->setFlash('error', Yii::t('backend', 'Error creating initial configuration.'));
                            return $this->render('create', [
                                'model' => $model,
                                'modelGM' => $modelGM,
                                'roles' => $this->getRolesWithDescriptions(),
                                'miningGroups' => $this->getMiningGroupsList()
                            ]);
                        }
                    } else {
                        Yii::$app->session->setFlash('error', Yii::t('backend', 'Error creating mining group.'));
                        return $this->render('create', [
                            'model' => $model,
                            'modelGM' => $modelGM,
                            'roles' => $this->getRolesWithDescriptions(),
                            'miningGroups' => $this->getMiningGroupsList()
                        ]);
                    }
                }
            }

            $model->mining_group_id = $miningGroupId;

            if ($model->save()) {
                Yii::$app->mailer->compose()
                    ->setTo($model->email)
                    ->setFrom(['mauricie.seba@gmail.com' => 'EcoReportItos'])
                    ->setSubject('Credenciales de acceso - EcoReportItos')
                    ->setTextBody("Hola {$model->username},\n\nTu cuenta ha sido creada.\n\nUsuario: {$model->username}\nContraseña: {$randomPassword}\n\nPuedes acceder en: http://backend.yii2-starter-kit.localhost/\n\nSaludos,\nEquipo de Mi App")
                    ->send();
                Yii::$app->session->setFlash('success', Yii::t('backend', 'Usuario Creado Correctamente.'));
                return $this->redirect(['users/edit']);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('backend', 'Error creating user.'));
            }
        }



        if (! Yii::$app->user->can('super-administrator')) {
            unset(
                $rolesList['super-administrator'],
                $rolesList['administrator']
            );
        }

        return $this->render('create', [
            'model' => $model,
            'modelGM' => $modelGM,
            'roles' => $this->getRolesWithDescriptions(),
            'miningGroups' => $this->getMiningGroupsList()
        ]);
    }

    /**
     * 
     * @return array
     */
    protected function getMiningGroupsList()
    {
        $groups = MiningGroup::find()->all();
        return \yii\helpers\ArrayHelper::map($groups, 'id', function ($model) {
            return $model->ges_name;
        });
    }

    /**
     * 
     * @param string $term El término de búsqueda
     * @return array Resultados en formato JSON
     */
    public function actionSearchGroups($term)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $query = MiningGroup::find()
            ->where([
                'or',
                ['like', 'name', $term],
                ['like', 'ges_name', $term]
            ])
            ->limit(10);

        $groups = $query->all();

        $results = [];
        foreach ($groups as $group) {
            $name = !empty($group->name) ? $group->name : $group->ges_name;
            $results[] = [
                'id' => $group->id,
                'value' => $name,
                'label' => $name
            ];
        }

        return $results;
    }
    public function actionEdit()
    {
        if (!Yii::$app->user->can('administrator')) {
            throw new \yii\web\NotFoundHttpException('No tienes permiso para acceder a esta página.');
        }

        $group_user_mining = Yii::$app->request->get('group_user_mining');
        $company_filter = Yii::$app->request->get('companies');
        $role_filter = Yii::$app->request->get('role_filter');
        $status_filter = Yii::$app->request->get('status_filter', User::STATUS_ACTIVE); // Por defecto muestra activos

        $query      = User::find();
        $isAdmin    = Yii::$app->user->can('administrator');
        $isSuper    = Yii::$app->user->can('super-administrator');
        $userGroupId = Yii::$app->user->identity->mining_group_id;

        if (!$isAdmin) {
            $userGroupId = Yii::$app->user->identity->mining_group_id;
            $query->andWhere(['mining_group_id' => $userGroupId]);
        }

        if ($group_user_mining !== null && $group_user_mining !== '') {
            if ($group_user_mining === 'no_group') {
                $query->andWhere(['mining_group_id' => null]);
            } else {
                $query->andWhere(['mining_group_id' => $group_user_mining]);
            }
        }

        if ($company_filter !== null && $company_filter !== '') {
            // “no company” special case
            if ($company_filter === 'no_company') {
                if ($isSuper) {
                    $groupIdsNoCompany = User::find()
                        ->select('mining_group_id')
                        ->where(['company_id' => null])
                        ->distinct()
                        ->column();

                    $query->joinWith('authAssignments aa', false)
                        ->andWhere([
                            'or',
                            ['company_id' => null],
                            [
                                'and',
                                ['aa.item_name'     => User::ROLE_ADMINISTRATOR],
                                ['in', 'mining_group_id', $groupIdsNoCompany],
                            ],
                        ]);
                } else {
                    $query->andWhere([
                        'company_id'      => null,
                        'mining_group_id' => $userGroupId,
                    ]);
                }
            } else {
                if ($isSuper) {
                    $groupIds = User::find()
                        ->select('mining_group_id')
                        ->where(['company_id' => $company_filter])
                        ->distinct()
                        ->column();
                    $query->joinWith('authAssignments aa', false)
                        ->andWhere([
                            'or',
                            ['company_id' => $company_filter],
                            [
                                'and',
                                ['aa.item_name'     => User::ROLE_ADMINISTRATOR],
                                ['in', 'mining_group_id', $groupIds],
                            ],
                        ]);
                } else {
                    $query->andWhere([
                        'company_id'      => $company_filter,
                        'mining_group_id' => $userGroupId,
                    ]);
                }
            }
        }

        if ($role_filter) {
            $query->joinWith('authAssignments as auth_assignment')
                ->andWhere(['auth_assignment.item_name' => $role_filter]);
        }

        $query->andWhere(['status' => $status_filter]);

        if ($isSuper) {
            $users = $query->all();
        } else if ($isAdmin) {
            $users = $query->andWhere(['mining_group_id' => $userGroupId])->all();
        } else {
            $users = [$query->andWhere(['id' => Yii::$app->user->id])->one()];
        }

        $groups = MiningGroup::find()->all();
        $groupOptions = \yii\helpers\ArrayHelper::map($groups, 'id', 'name');
        $groupOptions = ['no_group' => Yii::t('backend', 'Without a Group')] + $groupOptions;

        $companyQuery = Company::find();

        if ($isAdmin && ! $isSuper) {
            $companyQuery->innerJoin(
                User::tableName(),
                User::tableName() . '.company_id = ' . Company::tableName() . '.id'
            )
                ->andWhere([
                    User::tableName() . '.mining_group_id' => $userGroupId
                ]);
        }

        $companies = $companyQuery->all();
        $companyOptions = ['no_company' => Yii::t('backend', 'Without a Company'),] + \yii\helpers\ArrayHelper::map($companies, 'id', 'name');

        $auth = Yii::$app->authManager;
        $rolesList = \yii\helpers\ArrayHelper::map($auth->getRoles(), 'name', 'name');

        $statusOptions = [
            User::STATUS_ACTIVE => Yii::t('backend', 'Active Users'),
            User::STATUS_NOT_ACTIVE => Yii::t('backend', 'Inactive Users'),
            User::STATUS_DELETED => Yii::t('backend', 'Deleted Users'),
        ];

        return $this->render('edit', [
            'users' => $users,
            'groupOptions' => $groupOptions,
            'companyOptions' => $companyOptions,
            'rolesList' => $rolesList,
            'isAdmin' => $isAdmin,
            'statusOptions' => $statusOptions,
        ]);
    }

    /**
     * 
     */
    public function actionActivate($id)
    {
        if (!Yii::$app->user->can('administrator')) {
            throw new \yii\web\NotFoundHttpException('No tienes permiso para acceder a esta página.');
        }

        $user = User::findOne($id);
        if (!$user) {
            throw new \yii\web\NotFoundHttpException('Usuario no encontrado.');
        }

        $model = new UserForm();
        $model->setModel($user);

        if ($user->status == User::STATUS_NOT_ACTIVE) {
            $model->status = User::STATUS_ACTIVE;
            if ($model->save()) {
                Yii::$app->session->setFlash(
                    'success',
                    Yii::t('backend', 'User activated successfully.')
                );
            } else {
                Yii::$app->session->setFlash(
                    'error',
                    Yii::t('backend', 'Error activating user.')
                );
            }
        } else {
            Yii::$app->session->setFlash(
                'warning',
                Yii::t('backend', 'The user is not in inactive status.')
            );
        }

        return $this->redirect(['users/edit', 'status_filter' => User::STATUS_NOT_ACTIVE]);
    }

    /**
     * Restore a deleted user
     */
    public function actionRestore($id)
    {
        if (!Yii::$app->user->can('administrator')) {
            throw new \yii\web\NotFoundHttpException('No tienes permiso para acceder a esta página.');
        }

        $user = User::findOne($id);
        if (!$user) {
            throw new \yii\web\NotFoundHttpException('Usuario no encontrado.');
        }

        $model = new UserForm();
        $model->setModel($user);

        if ($user->status == User::STATUS_DELETED) {
            $model->status = User::STATUS_ACTIVE;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Usuario restaurado correctamente.');
            } else {
                Yii::$app->session->setFlash('error', 'Error al restaurar el usuario.');
            }
        } else {
            Yii::$app->session->setFlash('error', 'El usuario no está eliminado.');
        }

        return $this->redirect(['users/edit', 'status_filter' => User::STATUS_DELETED]);
    }


    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('administrator')) {
            throw new \yii\web\NotFoundHttpException('No tienes permiso para acceder a esta página.');
        }

        $user = User::findOne($id);
        if (!$user) {
            throw new NotFoundHttpException('El usuario no existe.');
        }

        $model = new UserForm();
        $model->setModel($user);
        $modelProfile = $user->userProfile;
        $gm = MiningGroup::findOne($user->mining_group_id);

        if ($modelProfile->load(Yii::$app->request->post()) && $modelProfile->save()) {
            Yii::$app->session->setFlash('success', 'Imagen actualizada correctamente.');
        }

        if (Yii::$app->request->post('change_password') !== null) {
            $newPassword = Yii::$app->security->generateRandomString(10);
            $model->password = $newPassword;

            if ($model->save()) {
                Yii::$app->mailer->compose()
                    ->setTo($user->email)
                    ->setFrom(['mauricie.seba@gmail.com' => 'EcoReportItos'])
                    ->setSubject('Tu nueva contraseña')
                    ->setTextBody("Hola {$user->username},\n\nTu nueva contraseña es: {$newPassword}\n\nPor favor, cámbiala después de ingresar.")
                    ->send();
                Yii::$app->session->setFlash('success', 'Contraseña generada y enviada por correo.');
                return $this->redirect(['update', 'id' => $id]);
            } else {
                Yii::$app->session->setFlash('error', 'Error al guardar la nueva contraseña.');
            }
        }

        if (Yii::$app->request->post('view_password')) {
            if (!Yii::$app->user->can('super-administrator')) {
                throw new ForbiddenHttpException('No tienes permiso para hacer eso.');
            }
            $superAdminPass = Yii::$app->request->post('admin_password');
            $admin = Yii::$app->user->identity;

            if (!$admin->validatePassword($superAdminPass)) {
                Yii::$app->session->setFlash('error', 'Contraseña de super-administrador incorrecta.');
            } else {
                $plain = Yii::$app->security->decryptByKey(
                    $user->password_encrypted,
                    Yii::$app->params['passwordEncryptionKey']
                );
                Yii::$app->session->setFlash('view_password', $plain);
            }

            return $this->redirect(['update', 'id' => $id]);
        }

        $viewPassword = Yii::$app->session->getFlash('view_password');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Usuario actualizado correctamente.');
            return $this->redirect(['update', 'id' => $id]);
        }

        if (Yii::$app->request->isGet) {
            $auth = Yii::$app->authManager;
            $allUserPermissions = array_keys($auth->getPermissionsByUser($user->id));
            $model->permissions = $allUserPermissions;
        }

        $permissionsRaw = Yii::$app->authManager->getPermissions();
        $permissions = [];
        foreach ($permissionsRaw as $permission) {
            $permissions[$permission->name] = $permission->description ?: $permission->name;
        }

        return $this->render('update', [
            'model' => $model,
            'user' => $user,
            'gm' => $gm,
            'modelProfile' => $modelProfile,
            'roles' => $this->getRolesWithDescriptions(),
            'permissions' => $permissions,
            'viewPassword' => $viewPassword,
        ]);
    }

    public function actionDelete($id)
    {
        $user = User::findOne($id);
        $model = new UserForm();
        $model->setModel($user);

        if ($model) {
            if ($model->status == 3) {
                Yii::$app->session->setFlash('error', 'El usuario ya está eliminado.');
            } else {

                $model->status = 3;
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Usuario marcado como eliminado correctamente.');
                } else {
                    Yii::$app->session->setFlash('error', 'Error al cambiar el estado del usuario.');
                }
            }
        } else {
            Yii::$app->session->setFlash('error', 'Usuario no encontrado.');
        }

        return $this->redirect(['users/edit']);
    }

    private function getRolesWithDescriptions()
    {
        $rolesWithDescription = [];
        $allRoles = Yii::$app->authManager->getRoles();

        $isSuperAdmin = Yii::$app->user->can('super-administrator');
        $isAdmin = Yii::$app->user->can('administrator');

        foreach ($allRoles as $role) {
            if (!$isSuperAdmin && $role->name === 'super-administrator') {
                continue;
            }

            if ($isAdmin && !$isSuperAdmin && $role->name === 'administrator') {
                continue;
            }

            $rolesWithDescription[$role->name] = $role->description ?: $role->name;
        }

        return $rolesWithDescription;
    }

    public function actions()
    {
        return [
            'avatar-upload' => [
                'class'       => UploadAction::class,
                'deleteRoute' => false,
                'on afterSave'   => function ($event) {
                    $profileId = Yii::$app->request->get('id');
                    $model = UserProfile::findOne(['user_id' => $profileId]);
                    if (!$model) {
                        throw new NotFoundHttpException('Profile not found.');
                    }
                    $model->picture = $event->file->getPath();
                    if (!$model->save()) {
                        Yii::$app->session->setFlash('error', Yii::t('backend', 'Error saving profile picture.'));
                    }
                },
                'on afterDelete' => function ($event) {
                    $profileId = Yii::$app->request->get('id');
                    $model = UserProfile::findOne(['user_id' => $profileId]);
                    if ($model && $model->picture !== null) {
                        $model->picture = null;
                        if (!$model->save()) {
                            Yii::$app->session->setFlash('error', Yii::t('backend', 'Error deleting profile picture.'));
                        }
                    }
                },
            ],
        ];
    }
}
