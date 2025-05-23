<?php

namespace backend\controllers;

use Yii;
use yii\web\ForbiddenHttpException;

/**
 * Site controller
 */
class SiteController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [];
    }

    public function beforeAction($action)
    {
        $this->layout = Yii::$app->user->isGuest || !Yii::$app->user->can('loginToBackend') ? 'base' : 'common';

        return parent::beforeAction($action);
    }

    public function actionError()
    {
        Yii::$app->controller->layout = 'main';
        $exception = Yii::$app->errorHandler->exception;

        if ($exception !== null) {
            if ($exception instanceof ForbiddenHttpException) {
                return $this->render('error403', ['exception' => $exception]);
            }

            return $this->render('error', ['exception' => $exception]);
        }

        return $this->goHome();
    }
}
