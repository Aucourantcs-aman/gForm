<?php
return [
    '@class' => 'Grav\\Common\\File\\CompiledYamlFile',
    'filename' => 'E:/xamp-htdocs/gravcms/gForm/user/config/plugins/email.yaml',
    'modified' => 1760349300,
    'size' => 494,
    'data' => [
        'enabled' => true,
        'from' => 'aman.aucourantcs@gmail.com',
        'to' => 'aman.aucourantcs@gmail.com',
        'mailer' => [
            'engine' => 'smtp',
            'smtp' => [
                'server' => 'smtp.gmail.com',
                'port' => 587,
                'encryption' => 'tls',
                'user' => 'aman.aucourantcs@gmail.com',
                'password' => 'mnsq gwmd oqsl mavn'
            ],
            'sendmail' => [
                'bin' => '/usr/sbin/sendmail -bs'
            ]
        ],
        'content_type' => 'text/html',
        'debug' => false,
        'cc' => NULL,
        'bcc' => NULL,
        'reply_to' => NULL,
        'body' => NULL
    ]
];
