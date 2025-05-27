<?php

namespace backend\controllers;

use yii\web\Controller;

use yii\filters\VerbFilter;

class HomeController extends Controller
{
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}
