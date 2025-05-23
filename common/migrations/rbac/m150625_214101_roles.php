<?php

use common\models\User;
use common\rbac\Migration;

class m150625_214101_roles extends Migration
{
    /**
     * @return bool|void
     * @throws \yii\base\Exception
     */
    public function up()
    {
        $this->auth->removeAll();

        $user = $this->auth->createRole(User::ROLE_USER);
        $this->auth->add($user);

        $inspector = $this->auth->createRole(User::ROLE_INSPECTOR);
        $this->auth->add($inspector);
        $this->auth->addChild($inspector, $user);

        $reviewer = $this->auth->createRole(User::ROLE_REVIEWER);
        $this->auth->add($reviewer);
        $this->auth->addChild($reviewer, $inspector);
        $this->auth->addChild($reviewer, $user);

        $manager = $this->auth->createRole(User::ROLE_MANAGER);
        $this->auth->add($manager);
        $this->auth->addChild($manager, $reviewer);
        $this->auth->addChild($manager, $inspector);
        $this->auth->addChild($manager, $user);

        $admin = $this->auth->createRole(User::ROLE_ADMINISTRATOR);
        $this->auth->add($admin);
        $this->auth->addChild($admin, $manager);
        $this->auth->addChild($admin, $reviewer);
        $this->auth->addChild($admin, $inspector);
        $this->auth->addChild($admin, $user);

        $superAdmin = $this->auth->createRole(User::ROLE_SUPER_ADMINISTRATOR);
        $this->auth->add($superAdmin);
        $this->auth->addChild($superAdmin, $admin);
        $this->auth->addChild($superAdmin, $manager);
        $this->auth->addChild($superAdmin, $reviewer);
        $this->auth->addChild($superAdmin, $inspector);
        $this->auth->addChild($superAdmin, $user);

        $this->auth->assign($superAdmin, 1);
        $this->auth->assign($admin, 2);
        $this->auth->assign($manager, 3);
        $this->auth->assign($reviewer, 4);
        $this->auth->assign($inspector, 5);
        $this->auth->assign($user, 6);
    }

    /**
     * @return bool|void
     */
    public function down()
    {
        $this->auth->remove($this->auth->getRole(User::ROLE_SUPER_ADMINISTRATOR));
        $this->auth->remove($this->auth->getRole(User::ROLE_ADMINISTRATOR));
        $this->auth->remove($this->auth->getRole(User::ROLE_MANAGER));
        $this->auth->remove($this->auth->getRole(User::ROLE_REVIEWER));
        $this->auth->remove($this->auth->getRole(User::ROLE_INSPECTOR));
        $this->auth->remove($this->auth->getRole(User::ROLE_USER));
    }
}
