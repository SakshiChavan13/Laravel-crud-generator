<?php

declare(strict_types=1);

namespace App\Constants\Permissions;

class PostPermissions
{
    const CREATE_POST = [
        'name' => 'create_posts',
        'group' => 'posts',
        'sub_group' => 'posts',
        'guard_name' => 'web',
    ];

    const UPDATE_POST = [
        'name' => 'update_posts',
        'group' => 'posts',
        'sub_group' => 'posts',
        'guard_name' => 'web',
    ];

    const DELETE_POST = [
        'name' => 'delete_posts',
        'group' => 'posts',
        'sub_group' => 'posts',
        'guard_name' => 'web',
    ];

    const VIEW_POST = [
        'name' => 'view_posts',
        'group' => 'posts',
        'sub_group' => 'posts',
        'guard_name' => 'web',
    ];
}
