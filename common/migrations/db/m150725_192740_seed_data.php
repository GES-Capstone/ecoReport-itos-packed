<?php

use common\models\User;
use yii\db\Migration;

class m150725_192740_seed_data extends Migration
{
    /**
     * @return bool|void
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $this->insert('{{%location}}', [
            'id' => 1,
            'latitude' => -29.45151,
            'longitude' => -71.23921,
        ]);
        $this->insert('{{%location}}', [
            'id' => 2,
            'latitude' => -28.973558,
            'longitude' => -70.154134,
        ]);
        $this->insert('{{%mining_group}}', [
            'id' => 1,
            'location_id' => 1,
            'name' => 'El Tofo',
            'ges_name' => 'El Tofo',
            'description' => 'Webmaster group',
            'commercial_address' => '1234 Main St, Suite 100, Cityville, ST 12345',
            'operational_address' => '5678 Industrial Rd, Cityville, ST 12345',
        ]);
        $this->insert('{{%mining_group}}', [
            'id' => 2,
            'location_id' => 2,
            'name' => 'Alpha Minerals',
            'ges_name' => 'Alpha Minerals',
            'description' => 'Unearthing tomorrow\'s riches with relentless precision, from deep-shaft gold to open-pit coal, powered by innovation and grit.',
            'commercial_address' => '2500 Mineral Peak Blvd, Suite 1200',
            'operational_address' => '4500 Quarry Access Road',
        ]);
        $this->insert('{{%initial_configuration}}', [
            'id' => 1,
            'step' => 1,
            'status' => 'in progress',
            'mining_group_id' => 1,
        ]);
        $this->insert('{{%initial_configuration}}', [
            'id' => 2,
            'step' => 0,
            'status' => 'not started',
            'mining_group_id' => 2,
        ]);
        $this->insert('{{%company}}', [
            'id' => 1,
            'mining_group_id' => 1,
            'name' => 'GES Mining',
            'description' => 'GES Mining is a leading mining company specializing in the extraction of precious metals and minerals.',
            'commercial_address' => '1234 Main St, Suite 100, Cityville, ST 12345',
            'operational_address' => '5678 Industrial Rd, Cityville, ST 12345',
            'phone' => '+1 (555) 123-4567',
            'email' => 'ges@company.cl',
        ]);
        $this->insert('{{%company}}', [
            'id' => 2,
            'mining_group_id' => 2,
            'name' => 'Alpha Mining',
            'description' => 'Alpha Mining is a global leader in the mining industry, dedicated to sustainable practices and innovative technologies.',
            'commercial_address' => '2500 Mineral Peak Blvd, Suite 1200',
            'operational_address' => '4500 Quarry Access Road',
            'phone' => '+1 (555) 987-6543',
            'email' => 'alpha@company.cl',
        ]);
        $this->insert('{{%user}}', [
            'id' => 1,
            'mining_group_id' => 1,
            'company_id' => 1,
            'username' => 'ges',
            'email' => 'ges@example.com',
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('ges'),
            'password_encrypted' => Yii::$app->security->encryptByKey(
                'ges',
                Yii::$app->params['passwordEncryptionKey']
            ),
            'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
            'access_token' => Yii::$app->getSecurity()->generateRandomString(40),
            'status' => User::STATUS_ACTIVE,
            'created_at' => time(),
            'updated_at' => time()
        ]);
        $this->insert('{{%user}}', [
            'id' => 2,
            'mining_group_id' => 2,
            'username' => 'webmaster',
            'email' => 'webmaster@example.com',
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('webmaster'),
            'password_encrypted' => Yii::$app->security->encryptByKey(
                'webmaster',
                Yii::$app->params['passwordEncryptionKey']
            ),
            'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
            'access_token' => Yii::$app->getSecurity()->generateRandomString(40),
            'status' => User::STATUS_ACTIVE,
            'created_at' => time(),
            'updated_at' => time()
        ]);
        $this->insert('{{%user}}', [
            'id' => 3,
            'mining_group_id' => 2,
            'company_id' => 2,
            'username' => 'manager',
            'email' => 'manager@example.com',
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('manager'),
            'password_encrypted' => Yii::$app->security->encryptByKey(
                'manager',
                Yii::$app->params['passwordEncryptionKey']
            ),
            'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
            'access_token' => Yii::$app->getSecurity()->generateRandomString(40),
            'status' => User::STATUS_ACTIVE,
            'created_at' => time(),
            'updated_at' => time()
        ]);
        $this->insert('{{%user}}', [
            'id' => 4,
            'mining_group_id' => 2,
            'company_id' => 2,
            'username' => 'reviewer',
            'email' => 'user1@example.com',
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('reviewer'),
            'password_encrypted' => Yii::$app->security->encryptByKey(
                'reviewer',
                Yii::$app->params['passwordEncryptionKey']
            ),
            'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
            'access_token' => Yii::$app->getSecurity()->generateRandomString(40),
            'status' => User::STATUS_ACTIVE,
            'created_at' => time(),
            'updated_at' => time()
        ]);
        $this->insert('{{%user}}', [
            'id' => 5,
            'mining_group_id' => 2,
            'company_id' => 2,
            'username' => 'inspector',
            'email' => 'user2@example.com',
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('inspector'),
            'password_encrypted' => Yii::$app->security->encryptByKey(
                'inspector',
                Yii::$app->params['passwordEncryptionKey']
            ),
            'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
            'access_token' => Yii::$app->getSecurity()->generateRandomString(40),
            'status' => User::STATUS_ACTIVE,
            'created_at' => time(),
            'updated_at' => time()
        ]);
        $this->insert('{{%user}}', [
            'id' => 6,
            'mining_group_id' => 2,
            'company_id' => 2,
            'username' => 'user',
            'email' => 'user3@example.com',
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('user'),
            'password_encrypted' => Yii::$app->security->encryptByKey(
                'user',
                Yii::$app->params['passwordEncryptionKey']
            ),
            'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
            'access_token' => Yii::$app->getSecurity()->generateRandomString(40),
            'status' => User::STATUS_ACTIVE,
            'created_at' => time(),
            'updated_at' => time()
        ]);

        $this->insert('{{%user_profile}}', [
            'user_id' => 1,
            'locale' => Yii::$app->sourceLanguage,
            'profession' => 'Super Admin',
            'firstname' => 'John',
            'lastname' => 'Doe'
        ]);
        $this->insert('{{%user_profile}}', [
            'user_id' => 2,
            'locale' => Yii::$app->sourceLanguage,
            'profession' => 'Webmaster',
            'firstname' => 'Jane',
            'lastname' => 'Smith'
        ]);
        $this->insert('{{%user_profile}}', [
            'user_id' => 3,
            'locale' => Yii::$app->sourceLanguage,
            'profession' => 'Manager',
            'firstname' => 'Alice',
            'lastname' => 'Johnson'
        ]);
        $this->insert('{{%user_profile}}', [
            'user_id' => 4,
            'locale' => Yii::$app->sourceLanguage,
            'profession' => 'Reviewer',
            'firstname' => 'Bob',
            'lastname' => 'Brown'
        ]);
        $this->insert('{{%user_profile}}', [
            'user_id' => 5,
            'locale' => Yii::$app->sourceLanguage,
            'profession' => 'Inspector',
            'firstname' => 'Charlie',
            'lastname' => 'Davis'
        ]);
        $this->insert('{{%user_profile}}', [
            'user_id' => 6,
            'locale' => Yii::$app->sourceLanguage,
            'profession' => 'User',
            'firstname' => 'Eve',
            'lastname' => 'Wilson'
        ]);

        $this->insert('{{%page}}', [
            'slug' => 'about',
            'title' => 'About',
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'status' => \common\models\Page::STATUS_PUBLISHED,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%article_category}}', [
            'id' => 1,
            'slug' => 'news',
            'title' => 'News',
            'status' => \common\models\ArticleCategory::STATUS_ACTIVE,
            'created_at' => time()
        ]);

        $this->insert('{{%widget_menu}}', [
            'key' => 'frontend-index',
            'title' => 'Frontend index menu',
            'items' => json_encode([
                [
                    'label' => 'Get started with Yii2',
                    'url' => 'http://www.yiiframework.com',
                    'options' => ['tag' => 'span'],
                    'template' => '<a href="{url}" class="btn btn-lg btn-success">{label}</a>'
                ],
                [
                    'label' => 'Yii2 Starter Kit on GitHub',
                    'url' => 'https://github.com/yii2-starter-kit/yii2-starter-kit',
                    'options' => ['tag' => 'span'],
                    'template' => '<a href="{url}" class="btn btn-lg btn-primary">{label}</a>'
                ],
                [
                    'label' => 'Find a bug?',
                    'url' => 'https://github.com/yii2-starter-kit/yii2-starter-kit/issues',
                    'options' => ['tag' => 'span'],
                    'template' => '<a href="{url}" class="btn btn-lg btn-danger">{label}</a>'
                ]

            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'status' => \common\models\WidgetMenu::STATUS_ACTIVE
        ]);

        $this->insert('{{%widget_text}}', [
            'key' => 'backend_welcome',
            'title' => 'Welcome to backend',
            'body' => '<p>Welcome to Yii2 Starter Kit Dashboard</p>',
            'status' => 1,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%widget_text}}', [
            'key' => 'ads-example',
            'title' => 'Google Ads Example Block',
            'body' => '<div class="lead">
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-9505937224921657"
                     data-ad-slot="2264361777"
                     data-ad-format="auto"></ins>
                <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>',
            'status' => 0,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%widget_carousel}}', [
            'id' => 1,
            'key' => 'index',
            'status' => \common\models\WidgetCarousel::STATUS_ACTIVE
        ]);

        $this->insert('{{%widget_carousel_item}}', [
            'carousel_id' => 1,
            'base_url' => Yii::getAlias('@frontendUrl'),
            'path' => 'img/yii2-starter-kit.gif',
            'type' => 'image/gif',
            'url' => '/',
            'status' => 1
        ]);

        $this->insert('{{%key_storage_item}}', [
            'key' => 'backend.theme-skin',
            'value' => 'skin-blue',
            'comment' => 'skin-blue, skin-black, skin-purple, skin-green, skin-red, skin-yellow'
        ]);

        $this->insert('{{%key_storage_item}}', [
            'key' => 'backend.layout-fixed',
            'value' => 0
        ]);

        $this->insert('{{%key_storage_item}}', [
            'key' => 'backend.layout-boxed',
            'value' => 0
        ]);

        $this->insert('{{%key_storage_item}}', [
            'key' => 'backend.layout-collapsed-sidebar',
            'value' => 0
        ]);

        $this->insert('{{%key_storage_item}}', [
            'key' => 'frontend.maintenance',
            'value' => 'disabled',
            'comment' => 'Set it to "enabled" to turn on maintenance mode'
        ]);
    }

    /**
     * @return bool|void
     */
    public function safeDown()
    {
        $this->delete('{{%key_storage_item}}', [
            'key' => 'frontend.maintenance'
        ]);

        $this->delete('{{%key_storage_item}}', [
            'key' => [
                'backend.theme-skin',
                'backend.layout-fixed',
                'backend.layout-boxed',
                'backend.layout-collapsed-sidebar',
            ],
        ]);

        $this->delete('{{%widget_carousel_item}}', [
            'carousel_id' => 1
        ]);

        $this->delete('{{%widget_carousel}}', [
            'id' => 1
        ]);

        $this->delete('{{%widget_text}}', [
            'key' => 'backend_welcome'
        ]);

        $this->delete('{{%widget_menu}}', [
            'key' => 'frontend-index'
        ]);

        $this->delete('{{%article_category}}', [
            'id' => 1
        ]);

        $this->delete('{{%page}}', [
            'slug' => 'about'
        ]);

        $this->delete('{{%user_profile}}', [
            'user_id' => [1, 2, 3]
        ]);

        $this->delete('{{%user}}', [
            'id' => [1, 2, 3]
        ]);
    }
}
