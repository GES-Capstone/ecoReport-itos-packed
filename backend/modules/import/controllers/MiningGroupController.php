<?php


namespace backend\controllers;

use Yii;
use common\models\MiningGroup;
use common\models\Location;
use common\models\User;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * MiningGroupController para la gestión simple de grupos mineros
 */
class MiningGroupController extends Controller
{
    /**
     * Acción para crear un nuevo grupo minero
     */
    public function actionCreate()
    {
        $model = new MiningGroup();
        $location = new Location();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $location->load(Yii::$app->request->post());

            // Iniciar transacción
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Guardar primero la ubicación si hay coordenadas
                if (!empty($location->latitude) && !empty($location->longitude)) {
                    if (!$location->save()) {
                        throw new \Exception('Error al guardar la ubicación');
                    }
                    $model->location_id = $location->id;
                }

                // Fechas de creación y actualización
                $model->created_at = date('Y-m-d H:i:s');
                $model->updated_at = date('Y-m-d H:i:s');

                // Guardar el grupo minero
                if (!$model->save()) {
                    throw new \Exception('Error al guardar el grupo minero');
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Grupo minero creado correctamente');
                return $this->redirect(['assign']);

            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('create', [
            'model' => $model,
            'location' => $location,
        ]);
    }

    /**
     * Acción para asignar un grupo minero a un usuario
     */
    public function actionAssign()
    {
        $userId = null;
        $groupId = null;

        // Lista de usuarios y grupos mineros para los dropdowns
        $users = ArrayHelper::map(User::find()->all(), 'id', 'username');
        $groups = ArrayHelper::map(MiningGroup::find()->all(), 'id', 'name');

        if (Yii::$app->request->isPost) {
            $userId = Yii::$app->request->post('user_id');
            $groupId = Yii::$app->request->post('group_id');

            if ($userId && $groupId) {
                // Obtener el usuario
                $user = User::findOne($userId);

                if ($user) {
                    // Asignar el grupo minero al usuario
                    $user->mining_group_id = $groupId;

                    if ($user->save()) {
                        Yii::$app->session->setFlash('success', 'Grupo minero asignado correctamente al usuario');
                    } else {
                        Yii::$app->session->setFlash('error', 'Error al asignar el grupo minero al usuario');
                    }
                } else {
                    Yii::$app->session->setFlash('error', 'Usuario no encontrado');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Debes seleccionar un usuario y un grupo minero');
            }
        }

        return $this->render('assign', [
            'users' => $users,
            'groups' => $groups,
            'userId' => $userId,
            'groupId' => $groupId,
        ]);
    }
}