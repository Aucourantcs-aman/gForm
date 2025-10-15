<?php
return [
    '@class' => 'Grav\\Common\\File\\CompiledYamlFile',
    'filename' => 'E:/xamp-htdocs/gravcms/gForm/user/plugins/email/email.yaml',
    'modified' => 1760335693,
    'size' => 221,
    'data' => [
        'enabled' => true,
        'from' => NULL,
        'to' => NULL,
        'mailer' => [
            'engine' => 'sendmail',
            'smtp' => [
                'server' => 'localhost',
                'port' => 25,
                'encryption' => 'none',
                'user' => NULL,
                'password' => NULL
            ],
            'sendmail' => [
                'bin' => '/usr/sbin/sendmail -bs'
            ]
        ],
        'content_type' => 'text/html',
        'debug' => false
    ]
];
