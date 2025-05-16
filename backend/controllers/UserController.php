<?php

namespace backend\controllers;

use backend\models\search\UserSearch;
use backend\models\UserForm;
use common\models\User;
use common\models\UserToken;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use trntv\filekit\actions\UploadAction;
use backend\models\AccountForm;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public $layout = 'main';
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws \yii\base\Exception
     * @throws NotFoundHttpException
     */
    public function actionLogin($id)
    {
        $model = $this->findModel($id);
        $tokenModel = UserToken::create(
            $model->getId(),
            UserToken::TYPE_LOGIN_PASS,
            60
        );

        return $this->redirect(
            Yii::$app->urlManagerFrontend->createAbsoluteUrl(['user/sign-in/login-by-pass', 'token' => $tokenModel->token])
        );
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserForm();
        $model->setScenario('create');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'roles' => ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name')
        ]);
    }

    /**
     * Updates an existing User model.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = new UserForm();
        $model->setModel($this->findModel($id));
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'roles' => ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name')
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        Yii::$app->authManager->revokeAll($id);
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
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

    public function actionProfile()
    {
        $model = Yii::$app->user->identity->userProfile;
        $user = Yii::$app->user->identity;
        $modelAccount = new AccountForm();
        $modelAccount->username = $user->username;
        $modelAccount->email = $user->email;
        $modelProfile = new UserForm();
        $modelProfile->setModel($user);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Perfil actualizado correctamente.');
            return $this->redirect(['profile']);
        }

        if ($modelAccount->load(Yii::$app->request->post()) && $modelAccount->validate()) {
            if ($modelAccount->password !== $modelAccount->password_confirm) {
                Yii::$app->session->setFlash('error', 'Las contraseñas no coinciden.');
            } else {
                $user->setPassword($modelAccount->password);
                if ($user->save()) {
                    Yii::$app->session->setFlash('success', 'Contraseña cambiada correctamente.');
                    return $this->redirect(['profile']);
                } else {
                    Yii::$app->session->setFlash('error', 'Error al cambiar la contraseña.');
                }
            }
        }
        if ($modelProfile->load(Yii::$app->request->post()) && $modelProfile->save()) {
            Yii::$app->session->setFlash('success', 'Nombre de usuario cambiado correctamente.');
            return $this->redirect(['profile']);
        }

        return $this->render('profile', ['model' => $model, 'modelAccount' => $modelAccount, 'modelProfile' => $modelProfile]);
    }
}
