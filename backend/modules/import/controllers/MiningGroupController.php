<?php


namespace backend\modules\import\controllers;

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
                        throw new \Exception('Error saving location');
                    }
                }
              
                $model->location_id = $location->id;
                // Fechas de creación y actualización
                $model->created_at = date('Y-m-d H:i:s');
                $model->updated_at = date('Y-m-d H:i:s');

                // Guardar el grupo minero
                if (!$model->save()) {
                    throw new \Exception('Error saving the mining group');
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
    
        $users = ArrayHelper::map(User::find()->all(), 'id', 'username');
        $groups = ArrayHelper::map(MiningGroup::find()->all(), 'id', 'name');
    
        if (Yii::$app->request->isPost) {
            $userId = Yii::$app->request->post('user_id');
            $groupId = Yii::$app->request->post('group_id');
    
            if (!is_numeric($userId) || !is_numeric($groupId)) {
                Yii::$app->session->setFlash('error', 'User and group IDs must be numeric values');
                return $this->render('assign', [
                    'users' => $users,
                    'groups' => $groups,
                    'userId' => $userId,
                    'groupId' => $groupId,
                ]);
            }
    
            $user = User::findOne($userId);
            if (!$user) {
                Yii::$app->session->setFlash('error', 'Selected user does not exist');
                return $this->render('assign', [
                    'users' => $users,
                    'groups' => $groups,
                    'userId' => $userId,
                    'groupId' => $groupId,
                ]);
            }
    
            $group = MiningGroup::findOne($groupId);
            if (!$group) {
                Yii::$app->session->setFlash('error', 'Selected mining group does not exist');
                return $this->render('assign', [
                    'users' => $users,
                    'groups' => $groups,
                    'userId' => $userId,
                    'groupId' => $groupId,
                ]);
            }
    
            if ($user->mining_group_id) {
                $currentGroup = MiningGroup::findOne($user->mining_group_id);
                $currentGroupName = $currentGroup ? $currentGroup->name : 'unknown';
                
                Yii::$app->session->setFlash('error', 
                    "User already belongs to group '$currentGroupName'. " .
                    "Cannot reassign a user who already has a mining group."
                );
                
                return $this->render('assign', [
                    'users' => $users,
                    'groups' => $groups,
                    'userId' => $userId,
                    'groupId' => $groupId,
                ]);
            }
    
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $user->mining_group_id = $groupId;
                $user->updated_at = date('Y-m-d H:i:s');
    
                if (!$user->save()) {
                    throw new \Exception('Error saving changes: ' . implode(', ', $user->getErrorSummary(true)));
                }
    
                Yii::info("User {$user->username} (ID: {$user->id}) assigned to mining group {$group->name} (ID: {$group->id}) by " . Yii::$app->user->identity->username, 'mining');
    
                $transaction->commit();
                Yii::$app->session->setFlash('success', "User successfully assigned to group '{$group->name}'");
                
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
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