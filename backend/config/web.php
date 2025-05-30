<?php
$config = [
    'homeUrl' => Yii::getAlias('@backendUrl'),
    'controllerNamespace' => 'backend\controllers',
    'defaultRoute' => 'home/index',
    'components' => [
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'request' => [
            'cookieValidationKey' => env('BACKEND_COOKIE_VALIDATION_KEY'),
            'baseUrl' => env('BACKEND_BASE_URL'),
        ],
        'user' => [
            'class' => yii\web\User::class,
            'identityClass' => common\models\User::class,
            'loginUrl' => ['sign-in/login'],
            'enableAutoLogin' => true,
            'as afterLogin' => common\behaviors\LoginTimestampBehavior::class,
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@common/mail',
            'useFileTransport' => true, // Cambia a false para enviar correos reales
            'transport' => [
                'scheme' => 'smtp',
                'host' => 'smtp.gmail.com',
                'username' => 'mauricie.seba@gmail.com',
                'password' => 'ocpu eudp qgmb eqgu',  // recuerda usar password de aplicación si usas gmail
                'port' => 587,
                'encryption' => 'tls',
            ],
        ],
    ],
    'modules' => [
        'content' => [
            'class' => backend\modules\content\Module::class,
        ],
        'widget' => [
            'class' => backend\modules\widget\Module::class,
        ],
        'file' => [
            'class' => backend\modules\file\Module::class,
        ],
        'system' => [
            'class' => backend\modules\system\Module::class,
        ],
        'translation' => [
            'class' => backend\modules\translation\Module::class,
        ],
        'rbac' => [
            'class' => backend\modules\rbac\Module::class,
            'defaultRoute' => 'rbac-auth-item/index',
        ],
        'import' => [
            'class' => 'backend\modules\import\Module',
        ],
    ],
    'as globalAccess' => [
        'class' => common\behaviors\GlobalAccessBehavior::class,
        'rules' => [
            [
                'controllers' => ['sign-in'],
                'allow' => true,
                'roles' => ['?'],
                'actions' => ['login'],
            ],
            [
                'controllers' => ['sign-in'],
                'allow' => true,
                'roles' => ['@'],
                'actions' => ['logout'],
            ],
            [
                'controllers' => ['site'],
                'allow' => true,
                'roles' => ['?', '@'],
                'actions' => ['error'],
            ],
            [
                'controllers' => ['debug/default'],
                'allow' => true,
                'roles' => ['?'],
            ],
            [
                'controllers' => ['user'],
                'allow' => true,
                'roles' => ['administrator', 'manager', 'user'],
            ],
            [
                'controllers' => ['user'],
                'allow' => false,
            ],
            [
                'controllers' => ['import/mining-group'],
                'allow' => true,
                'roles' => ['webmaster', 'manager'],
                'actions' => ['create', 'assign'],
            ],
            [
                'controllers' => ['import/mining-group'],
                'allow' => false,
                'roles' => ['user'],
                'actions' => ['create', 'assign'],
            ],
            [
                'controllers' => ['import/import-data'],
                'allow' => true,
                'roles' => ['@'],
                'actions' => ['index', 'process'],
            ],
            [
                'controllers' => ['import/import-companies'],
                'allow' => true,
                'roles' => ['@'],
                'actions' => ['index', 'process', 'template'],
            ],

            [
                'allow' => true,
                'roles' => ['administrator'],
            ],
            [
                'controllers' => ['home'],
                'allow' => true,
                'roles' => ['super-adminstrator', 'administrator', 'manager', 'reviewer', 'inspector', 'user'],
                'actions' => ['index'],
            ],
        ],
    ],
];

if (YII_ENV_DEV) {
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::class,
        'generators' => [
            'crud' => [
                'class' => yii\gii\generators\crud\Generator::class,
                'templates' => [
                    'yii2-starter-kit' => Yii::getAlias('@backend/views/_gii/templates'),
                ],
                'template' => 'yii2-starter-kit',
                'messageCategory' => 'backend',
            ],
        ],
    ];
}

return $config;
