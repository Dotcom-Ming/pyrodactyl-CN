<?php

return [
    'auth' => [
        'fail' => '登录失败',
        'success' => '已登录',
        'password-reset' => '密码已重置',
        'reset-password' => '请求重置密码',
        'checkpoint' => '双因素身份验证已请求',
        'recovery-token' => '使用了双因素恢复令牌',
        'token' => '已通过双因素验证',
        'ip-blocked' => '来自未列入白名单的 IP 地址 :identifier 的请求已被拦截',
        'sftp' => [
            'fail' => 'SFTP 登录失败',
        ],
    ],
    'user' => [
        'account' => [
            'email-changed' => '邮箱从 :old 更改为 :new',
            'password-changed' => '密码已更改',
        ],
        'api-key' => [
            'create' => '创建了新的 API 密钥 :identifier',
            'delete' => '删除了 API 密钥 :identifier',
        ],
        'ssh-key' => [
            'create' => '向账户添加了 SSH 密钥 :fingerprint',
            'delete' => '从账户移除了 SSH 密钥 :fingerprint',
        ],
        'two-factor' => [
            'create' => '已启用双因素认证',
            'delete' => '已禁用双因素认证',
        ],
    ],
    'server' => [
        'reinstall' => '已重装服务器',
        'console' => [
            'command' => '在服务器上执行了 ":command"',
        ],
        'power' => [
            'start' => '已启动服务器',
            'stop' => '已停止服务器',
            'restart' => '已重启服务器',
            'kill' => '已强制终止服务器进程',
        ],
        'backup' => [
            'download' => '已下载备份 :name',
            'delete' => '已删除备份 :name',
            'restore' => '已恢复备份 :name（删除文件：:truncate）',
            'restore-complete' => '备份 :name 恢复完成',
            'restore-failed' => '备份 :name 恢复失败',
            'start' => '开始新的备份 :name',
            'complete' => '备份 :name 标记为完成',
            'fail' => '备份 :name 标记为失败',
            'lock' => '已锁定备份 :name',
            'unlock' => '已解锁备份 :name',
        ],
        'database' => [
            'create' => '创建了新数据库 :name',
            'rotate-password' => '数据库 :name 的密码已轮换',
            'delete' => '已删除数据库 :name',
        ],
        'file' => [
            'compress_one' => '压缩了 :directory:file',
            'compress_other' => '在 :directory 中压缩了 :count 个文件',
            'read' => '查看了 :file 的内容',
            'copy' => '创建了 :file 的副本',
            'create-directory' => '创建了目录 :directory:name',
            'decompress' => '在 :directory 中解压了 :files',
            'delete_one' => '删除了 :directory:files.0',
            'delete_other' => '在 :directory 中删除了 :count 个文件',
            'download' => '下载了 :file',
            'pull' => '从 :url 下载远程文件到 :directory',
            'rename_one' => '将 :directory:files.0.from 重命名为 :directory:files.0.to',
            'rename_other' => '在 :directory 中重命名了 :count 个文件',
            'write' => '向 :file 写入了新内容',
            'upload' => '开始文件上传',
            'uploaded' => '已上传 :directory:file',
        ],
        'sftp' => [
            'denied' => '因权限限制阻止了 SFTP 访问',
            'create_one' => '创建了 :files.0',
            'create_other' => '创建了 :count 个新文件',
            'write_one' => '修改了 :files.0 的内容',
            'write_other' => '修改了 :count 个文件的内容',
            'delete_one' => '删除了 :files.0',
            'delete_other' => '删除了 :count 个文件',
            'create-directory_one' => '创建了目录 :files.0',
            'create-directory_other' => '创建了 :count 个目录',
            'rename_one' => '将 :files.0.from 重命名为 :files.0.to',
            'rename_other' => '重命名或移动了 :count 个文件',
        ],
        'allocation' => [
            'create' => '向服务器添加了 :allocation',
            'notes' => '将 :allocation 的备注从 ":old" 更新为 ":new"',
            'primary' => '将 :allocation 设为主要服务器分配',
            'delete' => '删除了分配 :allocation',
        ],
        'schedule' => [
            'create' => '创建了计划任务 :name',
            'update' => '更新了计划任务 :name',
            'execute' => '手动执行了计划任务 :name',
            'delete' => '删除了计划任务 :name',
        ],
        'task' => [
            'create' => '为计划任务 :name 创建了新的":action"任务',
            'update' => '更新了计划任务 :name 的":action"任务',
            'delete' => '删除了计划任务 :name 的一个任务',
        ],
        'settings' => [
            'rename' => '服务器名称从 :old 更改为 :new',
            'description' => '服务器描述从 :old 更改为 :new',
        ],
        'startup' => [
            'edit' => '将变量 :variable 从 ":old" 更改为 ":new"',
            'image' => '服务器的 Docker 镜像从 :old 更新为 :new',
        ],
        'subuser' => [
            'create' => '添加了 :email 作为子用户',
            'update' => '更新了 :email 的子用户权限',
            'delete' => '移除了 :email 的子用户权限',
        ],
    ],
];
