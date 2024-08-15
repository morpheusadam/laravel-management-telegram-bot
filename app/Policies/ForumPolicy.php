<?php

namespace App\Policies;

use TeamTeaTime\Forum\Policies\ForumPolicy as Base;

class ForumPolicy extends Base
{
    public function createCategories($user): bool
    {
        return $user->user_type=='Admin';
    }

    public function moveCategories($user): bool
    {
       return $user->user_type=='Admin';
    }

    public function renameCategories($user): bool
    {
        return $user->user_type=='Admin';
    }

    public function viewTrashedThreads($user): bool
    {
        return $user->user_type=='Admin';
    }

    public function viewTrashedPosts($user): bool
    {
        return $user->user_type=='Admin';
    }
}