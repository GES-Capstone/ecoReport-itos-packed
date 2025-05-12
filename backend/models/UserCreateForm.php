<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\UserProfile;

class UserCreateForm extends Model
{
    public $firstname;
    public $middlename;
    public $lastname;
    public $username;
    public $email;
    public $password;
    public $status;
    public $roles;
    public $mining_group_id;
    
    // Para almacenar errores específicos
    private $_specificErrors = [];

    public function rules()
    {
        return [
            [['firstname','middlename','lastname', 'email', 'password', 'status'], 'required'],
            [['username','firstname','middlename','lastname'], 'string', 'min' => 2, 'max' => 255],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Este correo electrónico ya está en uso.'],
            ['password', 'string', 'min' => 6],
            [['status'], 'integer'],
            [['roles'], 'each', 'rule' => ['in', 'range' => array_keys(Yii::$app->authManager->getRoles())]],
            [['mining_group_id'], 'integer'],
        ];
    }   

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('backend', 'Username'),
            'email' => Yii::t('backend', 'Email'),
            'password' => Yii::t('backend', 'Password'),
            'status' => Yii::t('backend', 'Status'),
            'roles' => Yii::t('backend', 'Roles'),
            'mining_group_id' => Yii::t('backend', 'Mining Group ID'),
            'firstname' => Yii::t('backend', 'First Name'),
            'middlename' => Yii::t('backend', 'Middle Name'),
            'lastname' => Yii::t('backend', 'Last Name'),
        ];
    }
    
    /**
     * Devuelve errores específicos capturados durante el proceso de guardado
     */
    public function getSpecificErrors()
    {
        return $this->_specificErrors;
    }
    
    /**
     * Guarda un nuevo usuario con su perfil y roles
     * @return boolean si el usuario se creó correctamente
     */
    public function save()
    {
        // Limpiar errores anteriores
        $this->_specificErrors = [];
        
        if (!$this->validate()) {
            $this->_specificErrors[] = 'El formulario contiene errores de validación. Por favor verifique los campos.';
            return false;
        }
        
        // Inicia transacción para asegurar la integridad de los datos
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            // Crea nuevo usuario
            $user = new User();
            $user->username = $this->username ?: $this->email; // Usa email como username si no se proporciona
            $user->email = $this->email;
            $user->status = $this->status;
            $user->mining_group_id = $this->mining_group_id;
            $user->setPassword($this->password);
            
            if (!$user->save()) {
                $errors = $this->formatModelErrors($user->getErrors());
                $this->_specificErrors[] = "Error al guardar los datos del usuario: $errors";
                $transaction->rollBack();
                return false;
            }
            
            // Crea el perfil de usuario con los datos personales
            $profile = new UserProfile();
            $profile->user_id = $user->id;
            $profile->firstname = $this->firstname;
            $profile->middlename = $this->middlename;
            $profile->lastname = $this->lastname;
            
            // Asegurarse de que el locale tenga un valor válido
            if (!isset($profile->locale)) {
                $profile->locale = Yii::$app->language;
            }
            
            if (!$profile->save()) {
                $errors = $this->formatModelErrors($profile->getErrors());
                $this->_specificErrors[] = "Error al guardar el perfil del usuario: $errors";
                $transaction->rollBack();
                return false;
            }
            
            // Asigna roles
            $auth = Yii::$app->authManager;
            $rolesAssigned = false;
            
            if (!empty($this->roles)) {
                foreach ($this->roles as $roleName) {
                    $role = $auth->getRole($roleName);
                    if ($role) {
                        $auth->assign($role, $user->id);
                        $rolesAssigned = true;
                    } else {
                        $this->_specificErrors[] = "El rol '$roleName' no existe";
                    }
                }
                
                if (!$rolesAssigned) {
                    $this->_specificErrors[] = "No se pudo asignar ningún rol al usuario";
                    $transaction->rollBack();
                    return false;
                }
            }
            
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->_specificErrors[] = "Error del sistema: " . $e->getMessage();
            Yii::error('Error al crear usuario: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return false;
        }
    }
    
    /**
     * Formatea errores de modelo en un string para mostrar
     */
    private function formatModelErrors($errors)
    {
        $formattedErrors = [];
        foreach ($errors as $attribute => $attributeErrors) {
            $formattedErrors[] = "$attribute: " . implode(", ", $attributeErrors);
        }
        return implode("; ", $formattedErrors);
    }
}