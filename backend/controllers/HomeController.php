<?php

namespace backend\controllers;

use common\models\UserProfile;
use common\models\User;
use Yii;
use yii\web\Controller;
use backend\models\UserForm;
use backend\models\AccountForm;
use backend\models\UserCreateForm;
use yii\web\NotFoundHttpException;
use backend\models\GroupMiningCreateForm;
use trntv\filekit\actions\UploadAction;
use common\models\MiningGroup;
use common\models\InitialConfiguration;

class HomeController extends Controller
{
    public $layout = 'homeBase';

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCreate()
{
    $model = new UserCreateForm();
    $modelGM = new GroupMiningCreateForm();

    if (Yii::$app->request->post()) {
        $modelGM->load(Yii::$app->request->post());
        $model->load(Yii::$app->request->post());


        if (empty($model->password)) {
            Yii::$app->session->setFlash('error', Yii::t('backend', 'Por favor ingrese una Contraseña.'));
            return $this->render('create', [
                'model' => $model,
                'modelGM' => $modelGM,
                'roles' => $this->getRolesWithDescriptions()
            ]);
        }

        if (empty($model->roles)) {
            Yii::$app->session->setFlash('error', Yii::t('backend', 'Por favor seleccione al menos un rol.'));
            return $this->render('create', [
                'model' => $model,
                'modelGM' => $modelGM,
                'roles' => $this->getRolesWithDescriptions()
            ]);
        }

        if (empty($model->status)) {
            Yii::$app->session->setFlash('error', Yii::t('backend', 'Por favor seleccione un estado.'));
            return $this->render('create', [
                'model' => $model,
                'modelGM' => $modelGM,
                'roles' => $this->getRolesWithDescriptions()
            ]);
        }
        $miningGroupId = null;

        if (!empty($modelGM->ges_name)) {
            if ($modelGM->save()) {
                $miningGroupId = $modelGM->miningGroup->id;

                $initialConfig = new InitialConfiguration();
                $initialConfig->step = 0;
                $initialConfig->status = 'not started';
                $initialConfig->mining_group_id = $miningGroupId;
                if (!$initialConfig->save()) {
                    Yii::$app->session->setFlash('error', Yii::t('backend', 'Error Creando Configuración Inicial.'));
                    return $this->render('create', [
                        'model' => $model,
                        'modelGM' => $modelGM,
                        'roles' => $this->getRolesWithDescriptions()
                    ]);
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('backend', 'Error Creando Grupo Minero.'));
                return $this->render('create', [
                    'model' => $model,
                    'modelGM' => $modelGM,
                    'roles' => $this->getRolesWithDescriptions()
                ]);
            }
        }

        $model->mining_group_id = $miningGroupId;

        if ($model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'Usuario Creado Correctamente.'));
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('error', Yii::t('backend', 'Error Creando Usuario.'));
        }
    }

    return $this->render('create', [
        'model' => $model,
        'modelGM' => $modelGM,
        'roles' => $this->getRolesWithDescriptions()
    ]);
}



public function actionEdit()
{
    if (!Yii::$app->user->can('administrator')) {
        throw new \yii\web\NotFoundHttpException('No tienes permiso para acceder a esta página.');
    }

    $group_user_mining = Yii::$app->request->get('group_user_mining');
    $role_filter = Yii::$app->request->get('role_filter');

    $query = User::find();
    $isAdmin = Yii::$app->user->can('administrator');

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

    if ($role_filter) {
        $query->joinWith('authAssignments as auth_assignment')
              ->andWhere(['auth_assignment.item_name' => $role_filter]);
    }

    $query->andWhere(['status' => 2]);
    $users = $query->all();


    $groups = MiningGroup::find()->all();
    $groupOptions = \yii\helpers\ArrayHelper::map($groups, 'id', 'name');
    $groupOptions = ['no_group' => 'Sin grupo'] + $groupOptions;

    $auth = Yii::$app->authManager;
    $rolesList = \yii\helpers\ArrayHelper::map($auth->getRoles(), 'name', 'name');

    return $this->render('edit', [
        'users' => $users,
        'groupOptions' => $groupOptions,
        'rolesList' => $rolesList,
        'isAdmin' => $isAdmin,
    ]);
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

    if ($model->load(Yii::$app->request->post())) {
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Usuario actualizado correctamente.');
        } 
        else {
            Yii::$app->session->setFlash('error', 'Error al guardar el usuario.');
        }
    }

    return $this->render('update', [
        'model' => $model,
        'gm' => $gm,
        'modelProfile' => $modelProfile,
        'roles' => $this->getRolesWithDescriptions(),
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

    return $this->redirect(['home/edit']);
}


private function getRolesWithDescriptions()
{   
    $rolesWithDescription = [];

    if (!Yii::$app->user->can('administrator')) {
        foreach (Yii::$app->authManager->getRoles() as $role) {
            if ($role->name == 'administrator') {
                continue; 
            }
            $rolesWithDescription[$role->name] = $role->description ?: $role->name;
        }
    } else {
        foreach (Yii::$app->authManager->getRoles() as $role) {
            $rolesWithDescription[$role->name] = $role->description ?: $role->name;
        }
    }

    return $rolesWithDescription; 
}
public function actions()
{
    return [
        'avatar-upload' => [
            'class' => UploadAction::class,
            'deleteRoute' => false,
            'on afterSave' => function ($event) {
                $file = $event->file;
                $model = Yii::$app->user->identity->userProfile;
                $model->picture = $file->getPath();
                if (!$model->save()) {
                    Yii::$app->session->setFlash('error', 'Error al guardar la imagen de perfil.');
                }
            },
            'on afterDelete' => function ($event) {
                $model = Yii::$app->user->identity->userProfile;
                if ($model->picture) {
                    $model->picture = null;
                    if (!$model->save()) {
                        Yii::$app->session->setFlash('error', 'Error al eliminar la imagen de perfil.');
                    }
                }
            },
        ],
    ];
}

}

