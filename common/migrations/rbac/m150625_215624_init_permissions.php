<?php

use common\models\User;
use common\rbac\Migration;

class m150625_215624_init_permissions extends Migration
{
    public function up()
    {
        $userRole = $this->auth->getRole(User::ROLE_USER);
        $inspectorRole = $this->auth->getRole(User::ROLE_INSPECTOR);
        $reviewerRole = $this->auth->getRole(User::ROLE_REVIEWER);
        $managerRole = $this->auth->getRole(User::ROLE_MANAGER);
        $administratorRole = $this->auth->getRole(User::ROLE_ADMINISTRATOR);
        $superAdminRole = $this->auth->getRole(User::ROLE_SUPER_ADMINISTRATOR);

        $loginToBackend = $this->auth->createPermission('loginToBackend');
        $loginToBackend->description = Yii::t('backend', 'Allow to login');
        $this->auth->add($loginToBackend);

        $this->auth->addChild($userRole, $loginToBackend);
        $this->auth->addChild($inspectorRole, $loginToBackend);
        $this->auth->addChild($reviewerRole, $loginToBackend);
        $this->auth->addChild($managerRole, $loginToBackend);
        $this->auth->addChild($administratorRole, $loginToBackend);
        $this->auth->addChild($superAdminRole, $loginToBackend);

        $createUser = $this->auth->createPermission('createUser');
        $createUser->description = Yii::t('backend', 'Allow to create users');
        $this->auth->add($createUser);

        $this->auth->addChild($administratorRole, $createUser);
        $this->auth->addChild($superAdminRole, $createUser);

        $changePermissions = $this->auth->createPermission('changePermissions');
        $changePermissions->description = Yii::t('backend', 'Allow to change user permissions');
        $this->auth->add($changePermissions);

        $this->auth->addChild($administratorRole, $changePermissions);
        $this->auth->addChild($superAdminRole, $changePermissions);
    }

    public function down()
    {
        $this->auth->remove($this->auth->getPermission('loginToBackend'));
    }
}
