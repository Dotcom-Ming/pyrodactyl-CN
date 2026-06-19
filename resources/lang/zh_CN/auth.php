<?php

return [
    'sign_in' => '登录',
    'go_to_login' => '前往登录',
    'failed' => '未找到与所提供的凭证匹配的账户。',

    'forgot_password' => [
        'label' => '忘记密码？',
        'label_help' => '输入您的账户邮箱地址，接收重置密码的说明。',
        'button' => '找回账户',
    ],

    'reset_password' => [
        'button' => '重置并登录',
    ],

    'two_factor' => [
        'label' => '双因素令牌',
        'title' => '双因素认证',
        'label_help' => '此账户需要进行双重身份验证才能继续。请输入您的设备生成的代码以完成登录。',
        'checkpoint_failed' => '双因素身份验证令牌无效。',
        'recovery_code_help' => '请输入设置双因素认证时生成的任一恢复代码以继续。',
        'token_help' => '请输入设备上显示的双因素认证令牌。',
        'lost_device' => '我丢失了设备',
        'have_device' => '我有设备',
    ],

    'throttle' => '登录尝试过于频繁，请在 :seconds 秒后重试。',
    'password_requirements' => '密码长度至少为 8 个字符，且应与此站点唯一。',
    '2fa_must_be_enabled' => '管理员要求您必须启用双因素身份验证才能使用面板。',
    'validation_username_or_email_required' => '必须填写用户名或邮箱。',
    'validation_account_password_required' => '请输入您的账户密码。',
    'validation_captcha_required' => '请先完成验证码验证。',
    'validation_email_required' => '必须填写邮箱地址。',
    'validation_email_valid' => '请输入有效的邮箱地址。',
    'validation_new_password_required' => '必须填写新密码。',
    'validation_new_password_min' => '新密码长度至少应为 8 个字符。',
    'validation_password_confirmation_required' => '新密码确认不匹配。',
    'validation_password_confirmation_mismatch' => '新密码确认不匹配。',
];
