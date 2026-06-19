<?php

return [
    'exceptions' => [
        'no_new_default_allocation' => '您正在尝试删除此服务器的默认分配，但没有可用的回退分配。',
        'marked_as_failed' => '此服务器已标记为安装失败。当前状态无法在此状态下切换。',
        'bad_variable' => ':name 变量存在验证错误。',
        'daemon_exception' => '尝试与守护进程通信时发生异常，导致 HTTP/:code 响应代码。此异常已被记录。（请求 ID：:request_id）',
        'default_allocation_not_found' => '未在服务器的分配中找到请求的默认分配。',
    ],
    'alerts' => [
        'startup_changed' => '此服务器的启动配置已更新。如果此服务器的分类或 Egg 已更改，现在将进行重新安装。',
        'server_deleted' => '服务器已成功从系统中删除。',
        'server_created' => '服务器已成功在面板上创建。请给守护进程几分钟时间来完全安装此服务器。',
        'build_updated' => '此服务器的构建详细信息已更新。某些更改可能需要重启才能生效。',
        'suspension_toggled' => '服务器暂停状态已更改为 :status。',
        'rebuild_on_boot' => '此服务器已标记为需要 Docker 容器重建。这将在下次服务器启动时发生。',
        'install_toggled' => '此服务器的安装状态已切换。',
        'server_reinstalled' => '此服务器已排队等待重新安装。',
        'details_updated' => '服务器详细信息已成功更新。',
        'docker_image_updated' => '已成功更改此服务器的默认 Docker 镜像。需要重启才能应用此更改。',
        'node_required' => '在向此面板添加服务器之前，您必须至少配置一个节点。',
        'transfer_nodes_required' => '在传输服务器之前，您必须至少配置两个节点。',
        'transfer_started' => '服务器传输已开始。',
        'transfer_not_viable' => '所选节点没有足够的磁盘空间或内存来容纳此服务器。',
    ],
];