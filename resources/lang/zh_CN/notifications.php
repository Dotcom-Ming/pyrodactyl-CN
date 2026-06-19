<?php

return [
    'account_created' => [
        'greeting' => ':name，您好！',
        'intro' => '您收到这封邮件，是因为系统已在 :app 上为您创建了一个账户。',
        'setup_action' => '设置您的账户',
    ],
    'password_reset' => [
        'subject' => '重置密码',
        'intro' => '您收到这封邮件，是因为我们收到了针对您账户的密码重置请求。',
        'action' => '重置密码',
        'outro' => '如果这不是您本人发起的密码重置请求，则无需执行任何操作。',
    ],
    'added_to_server' => [
        'greeting' => ':name，您好！',
        'intro' => '您已被添加为以下服务器的子用户，并获得了相应的服务器管理权限。',
        'action' => '访问服务器',
    ],
    'removed_from_server' => [
        'greeting' => ':name，您好。',
        'intro' => '您已被移出以下服务器的子用户列表。',
        'action' => '访问面板',
    ],
    'server_installed' => [
        'greeting' => ':name，您好。',
        'intro' => '您的服务器已完成安装，现在可以开始使用了。',
        'action' => '登录并开始使用',
    ],
    'mail_tested' => [
        'subject' => 'Pyrodactyl 测试邮件',
        'greeting' => ':name，您好！',
        'intro' => '这是一封来自 Pyrodactyl 邮件系统的测试邮件，一切已经就绪。',
    ],
    'labels' => [
        'username' => '用户名：:username',
        'email' => '邮箱：:email',
        'server_name' => '服务器名称：:name',
    ],
    'template' => [
        'greeting_default' => '您好！',
        'greeting_error' => '出错了！',
        'regards' => '此致，',
        'trouble_clicking' => '如果您无法点击“:action”按钮，请将下方链接复制并粘贴到浏览器中打开：',
        'all_rights_reserved' => '保留所有权利。',
    ],
];
