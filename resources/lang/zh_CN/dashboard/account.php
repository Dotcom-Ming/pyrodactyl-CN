<?php

return [
    'email' => [
        'title' => '更新您的邮箱',
        'updated' => '您的邮箱地址已更新。',
    ],
    'password' => [
        'title' => '更改您的密码',
        'requirements' => '您的新密码长度至少为 8 个字符。',
        'updated' => '您的密码已更新。',
    ],
    'two_factor' => [
        'button' => '配置双因素认证',
        'disabled' => '您账户的双因素认证已禁用。今后登录时将不再需要提供令牌。',
        'enabled' => '您账户的双因素认证已启用！从现在开始，登录时需要提供设备生成的代码。',
        'invalid' => '提供的令牌无效。',
        'setup' => [
            'title' => '设置双因素认证',
            'help' => '无法扫描二维码？请在您的应用输入以下代码：',
            'field' => '输入令牌',
        ],
        'disable' => [
            'title' => '禁用双因素认证',
            'field' => '输入令牌',
        ],
    ],
];
