<?php

return [
    'daemon_connection_failed' => '尝试与守护进程通信时出现异常，返回 HTTP/:code 响应码。此异常已被记录。',
    'node' => [
        'servers_attached' => '节点必须没有关联的服务器才能被删除。',
        'daemon_off_config_updated' => '守护进程配置<strong>已更新</strong>，但在尝试自动更新守护进程配置文件时遇到错误。您需要手动更新配置文件（config.yml）以使更改生效。',
    ],
    'allocations' => [
        'server_using' => '当前有服务器正在使用此分配。只有没有服务器使用时才能删除分配。',
        'too_many_ports' => '不支持在单个范围内一次性添加超过 1000 个端口。',
        'invalid_mapping' => '为 :port 提供的映射无效，无法处理。',
        'cidr_out_of_range' => 'CIDR 表示法仅允许 /25 到 /32 之间的掩码。',
        'port_out_of_range' => '分配中的端口必须大于 1024 且小于等于 65535。',
        'disabled' => '无法为此服务器分配端口：分配功能已禁用。',
        'limit_reached' => '无法为此服务器分配更多端口：已达到限制。',
        'no_limit_set' => '无法删除此服务器的分配：未设置分配限制。',
        'cannot_delete_primary' => '无法删除此服务器的主分配。',
    ],
    'nest' => [
        'delete_has_servers' => '不能从面板删除带有活跃服务器的分类。',
        'egg' => [
            'delete_has_servers' => '不能从面板删除带有活跃服务器的选项。',
            'invalid_copy_id' => '被选为复制脚本来源的选项不存在，或它本身正在复制脚本。',
            'must_be_child' => '此选项的"复制设置自"指令必须是所选分类的子选项。',
            'has_children' => '此选项是一个或多个其他选项的父级。请先删除这些选项，然后再删除此选项。',
        ],
        'variables' => [
            'env_not_unique' => '环境变量 :name 必须在此选项中唯一。',
            'reserved_name' => '环境变量 :name 受保护，不能分配给变量。',
            'bad_validation_rule' => '验证规则 ":rule" 对此应用程序无效。',
        ],
        'importer' => [
            'json_error' => '尝试解析 JSON 文件时出错：:error。',
            'file_error' => '提供的 JSON 文件无效。',
            'invalid_json_provided' => '提供的 JSON 文件格式无法识别。',
        ],
    ],
    'subusers' => [
        'editing_self' => '不允许编辑自己的子用户账户。',
        'user_is_owner' => '不能将服务器所有者添加为此服务器的子用户。',
        'subuser_exists' => '具有该邮箱地址的用户已作为此服务器的子用户。',
    ],
    'databases' => [
        'delete_has_databases' => '不能删除关联有活跃数据库的数据库主机服务器。',
    ],
    'tasks' => [
        'chain_interval_too_long' => '链式任务的最大间隔时间为 15 分钟。',
        'schedule_task_limit' => '每个计划最多只能关联 :limit 个任务。创建此任务会超过该计划的限制。',
        'backup_disabled' => '此服务器已禁用备份，无法创建备份任务。',
        'backup_limit_zero' => '当服务器备份限制为 0 时，无法创建备份任务。',
        'permission_denied' => '您没有执行此操作的权限。',
        'task_queued' => '无法删除当前正在排队或处理中的任务。',
        'subuser_permission_denied' => '无法分配您的账户当前不拥有的子用户权限。',
    ],
    'locations' => [
        'has_nodes' => '不能删除关联有活跃节点的位置。',
    ],
    'users' => [
        'node_revocation_failed' => '撤销 <a href=":link">节点 #:node</a> 上的密钥失败。:error',
    ],
    'deployment' => [
        'no_viable_nodes' => '找不到满足自动部署要求的节点。',
        'no_viable_allocations' => '找不到满足自动部署要求的分配。',
    ],
    'api' => [
        'resource_not_found' => '请求的资源在此服务器上不存在。',
        'api_key_limit' => '您已达到账户 API 密钥数量上限。',
        'client_key_required' => '您正在使用应用 API 密钥访问需要客户端 API 密钥的接口。',
        'application_key_required' => '此账户没有访问 API 的权限。',
        'ip_not_allowed' => '此 IP 地址（:ip）没有使用这些凭据访问 API 的权限。',
    ],
    'auth' => [
        'totp_already_enabled' => '此账户已启用双因素认证。',
        'password_invalid' => '提供的密码无效。',
        'totp_required' => '需要双因素认证代码。',
        'totp_invalid' => '提供的双因素认证代码无效。',
    ],
    'backup' => [
        'unknown_disk' => '请求的备份引用了未知的磁盘驱动类型，无法下载。',
        'restore_state' => '此服务器当前状态不允许恢复备份。',
        'restore_unavailable' => '此备份当前无法恢复：备份尚未完成或已失败。',
        'incomplete_download' => '无法下载未完成的备份。',
        'access_denied' => '您没有访问该备份的权限。',
        'already_completed' => '此备份已处于完成状态。',
        'cannot_update_completed' => '无法更新已标记为完成的备份状态。',
        'locked_delete' => '无法删除已锁定的备份。',
        'size_required' => '必须提供非空的 "size" 查询参数。',
        's3_required' => '当前配置的备份适配器不是 S3 兼容适配器。',
    ],
    'startup' => [
        'variable_missing' => '您尝试编辑的环境变量不存在。',
        'variable_read_only' => '您尝试编辑的环境变量是只读的。',
        'docker_image_not_allowed' => '此服务器不允许使用请求的 Docker 镜像。',
        'no_default_docker_image' => '此服务器的 Egg 没有可用的默认 Docker 镜像。',
    ],
    'server' => [
        'must_be_online_for_command' => '服务器必须在线才能发送命令。',
        'failed_install_recover' => '此服务器处于安装失败状态，无法恢复。请删除并重新创建服务器。',
        'transfer_toggle_suspension' => '无法切换正在转移中的服务器暂停状态。',
    ],
    'websocket' => [
        'connect_denied' => '您没有连接此服务器 WebSocket 的权限。',
        'transfer_logs_denied' => '您没有查看服务器转移日志的权限。',
    ],
    'sftp' => [
        'missing_identifier' => '请求中未包含有效的服务器标识符。',
        'too_many_attempts' => '此账户登录尝试次数过多，请在 :seconds 秒后重试。',
        'bad_credentials' => '认证凭据不正确，请重试。',
        'access_denied' => '您没有访问此服务器 SFTP 的权限。',
    ],
    'captcha' => [
        'required' => '请完成验证码验证。',
        'failed' => '验证码验证失败，请重试。',
    ],
];
