<?php

return [
    'location' => [
        'no_location_found' => '找不到与提供的短代码匹配的记录。',
        'ask_short' => '位置短代码',
        'ask_long' => '位置描述',
        'created' => '已成功创建新位置（:name），ID 为 :id。',
        'deleted' => '已成功删除请求的位置。',
    ],
    'user' => [
        'search_users' => '输入用户名、用户 ID 或邮箱地址',
        'select_search_user' => '要删除的用户 ID（输入"0"重新搜索）',
        'deleted' => '用户已成功从面板删除。',
        'confirm_delete' => '确定要从面板删除此用户吗？',
        'no_users_found' => '未找到与搜索条件匹配的用户。',
        'multiple_found' => '找到多个匹配的账户，因使用了 --no-interaction 标志而无法删除用户。',
        'ask_admin' => '此用户是管理员吗？',
        'ask_email' => '邮箱地址',
        'ask_username' => '用户名',
        'ask_name_first' => '名',
        'ask_name_last' => '姓',
        'ask_password' => '密码',
        'ask_password_tip' => '如果您想创建一个随机密码并通过邮件发送给用户，请重新运行此命令（CTRL+C）并传递 --no-password 标志。',
        'ask_password_help' => '密码长度至少为 8 个字符，且至少包含一个大写字母和一个数字。',
        '2fa_help_text' => [
            '此命令将禁用用户账户的双因素认证（如果已启用）。仅应在用户无法登录其账户时用作账户恢复命令。',
            '如果这不是您想要的操作，请按 CTRL+C 退出此过程。',
        ],
        '2fa_disabled' => ':email 的双因素认证已被禁用。',
    ],
    'schedule' => [
        'output_line' => '正在为 `:schedule`（:hash）的第一个任务分发作业。',
    ],
    'maintenance' => [
        'deleting_service_backup' => '正在删除服务备份文件 :file。',
    ],
    'server' => [
        'rebuild_failed' => '":name"（#:id）在节点":node"上的重建请求失败，错误：:message',
        'reinstall' => [
            'failed' => '":name"（#:id）在节点":node"上的重装请求失败，错误：:message',
            'confirm' => '您即将对一组服务器执行重装操作。是否继续？',
        ],
        'power' => [
            'confirm' => '您即将对 :count 台服务器执行 :action 操作。是否继续？',
            'action_failed' => '":name"（#:id）在节点":node"上的电源操作请求失败，错误：:message',
        ],
    ],
    'environment' => [
        'mail' => [
            'ask_smtp_host' => 'SMTP 主机（例如 smtp.gmail.com）',
            'ask_smtp_port' => 'SMTP 端口',
            'ask_smtp_username' => 'SMTP 用户名',
            'ask_smtp_password' => 'SMTP 密码',
            'ask_mailgun_domain' => 'Mailgun 域名',
            'ask_mailgun_endpoint' => 'Mailgun 端点',
            'ask_mailgun_secret' => 'Mailgun 密钥',
            'ask_mandrill_secret' => 'Mandrill 密钥',
            'ask_postmark_username' => 'Postmark API 密钥',
            'ask_driver' => '应使用哪个驱动程序发送邮件？',
            'ask_mail_from' => '邮件发件人地址',
            'ask_mail_name' => '邮件发件人名称',
            'ask_encryption' => '要使用的加密方法',
        ],
    ],
];
