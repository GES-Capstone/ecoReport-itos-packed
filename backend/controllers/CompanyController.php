<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Company;
use common\models\Location;
use common\models\MiningGroup;
use trntv\filekit\actions\UploadAction;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class CompanyController extends Controller
{

    public $layout = 'main';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update'],
                        'allow' => true,
                        'roles' => ['administrator', 'super-administrator'],
                    ],
                    [
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['super-administrator', 'administrator'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'company-logo-upload' => [
                'class' => UploadAction::class,
                'deleteRoute' => false,
                'on afterSave' => function ($event) {
                    $file  = $event->file;
                    $id    = Yii::$app->request->get('id');
                    $model = Company::findOne($id);
                    if ($model) {
                        $model->logo_path = $file->getPath();
                        if (!$model->save()) {
                            Yii::$app->session->setFlash('error', 'Error saving company logo.');
                        }
                    }
                },
                'on afterDelete' => function ($event) {
                    $id = Yii::$app->request->get('id');
                    $model = Company::findOne($id);
                    if ($model->Picture) {
                        $model->picture = null;
                        if (!$model->save()) {
                            Yii::$app->session->setFlash('error', 'Error deleting company logo.');
                        }
                    }
                },
            ],
        ];
    }

    /**
     * If you want a separate endpoint to handle deletes via POST/deleteRoute:
     */
    public function actionDeleteLogo($id, $key = null)
    {
        $model = Company::findOne($id);
        if ($model) {
            $model->logo_path     = null;
            $model->logo_base_url = null;
            $model->save(false, ['logo_path', 'logo_base_url']);
        }
        return $this->asJson(['success' => true]);
    }

    /**
     * Lists all companies.
     */
    public function actionIndex()
    {
        if (
            !Yii::$app->user->can('administrator')
            && !Yii::$app->user->can('super-administrator')
        ) {
            throw new NotFoundHttpException('No tienes permiso para acceder a esta página.');
        }

        $miningGroupFilter    = Yii::$app->request->get('mining_group_filter');

        $query = Company::find()
            ->alias('c')
            ->joinWith(['miningGroup mg']);

        if (
            Yii::$app->user->can('administrator')
            && !Yii::$app->user->can('super-administrator')
        ) {
            $query->andWhere(['c.mining_group_id' => Yii::$app->user->identity->mining_group_id]);
        }

        if ($miningGroupFilter !== null && $miningGroupFilter !== '') {
            $query->andWhere(['c.mining_group_id' => $miningGroupFilter]);
        }

        $companies = $query->all();

        $allGroups    = MiningGroup::find()->all();
        $groupOptions = ['' => Yii::t('backend', 'All Groups')]
            + ArrayHelper::map($allGroups, 'id', 'name');

        return $this->render('index', [
            'companies' => $companies,
            'groupOptions' => $groupOptions,
            'currentFilters' => [
                'mining_group_filter' => $miningGroupFilter,
            ],
        ]);
    }

    /**
     * Displays the edit Company form and propagates mining_group_id changes to users.
     *
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = Company::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('backend', 'The requested company does not exist.'));
        }

        // Build dropdown options
        $groupOptions = ArrayHelper::map(MiningGroup::find()->all(), 'id', 'name');

        // Capture the old group before loading POST
        $oldGroupId = $model->mining_group_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // If the mining_group_id changed, update all users in this company
            if ($model->mining_group_id !== $oldGroupId) {
                \common\models\User::updateAll(
                    ['mining_group_id' => $model->mining_group_id],
                    ['company_id'      => $model->id]
                );
            }

            Yii::$app->session->setFlash(
                'success',
                Yii::t('backend', 'Company and its users updated successfully.')
            );
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model'        => $model,
            'groupOptions' => $groupOptions,
        ]);
    }


    /**
     * Deletes an existing Company.
     * @param integer $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = Company::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('The company does not exist.');
        }
        $model->delete();

        Yii::$app->session->setFlash('success', 'Company deleted.');
        return $this->redirect(['index']);
    }
}
