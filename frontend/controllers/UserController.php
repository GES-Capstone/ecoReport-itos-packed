<?php

namespace frontend\controllers;

use yii\web\Controller;

class UserController extends Controller{

    public function actionHello(): string
    {
        $mensaje = "hola que tal gente";
        $fecha = date("Y-m-d");
        return $this->render('hello',['mensaje'=>$mensaje,'fecha'=>$fecha]);
    }
}
