<?php

namespace App\Policies;

use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Policies\CategoryPolicy as Base;

class CategoryPolicy extends Base
{
    public function createThreads($user, Category $category): bool
    {
       return $user->user_type=='Admin' || $user->user_type=='Manager' || ($user->parent_user_id==1 && $user->enable_forum_thread=='1'); // admin,team and admin sub users only
    }

    public function deleteThreads($user, Category $category): bool // admin can delete all, user can delete only his
    {
        return true;
    }

    public function enableThreads($user, Category $category): bool
    {
        return $user->user_type=='Admin';
    }

    public function moveThreadsFrom($user, Category $category): bool
    {
        return $user->user_type=='Admin';
    }

    public function moveThreadsTo($user, Category $category): bool
    {
        return $user->user_type=='Admin';
    }

    public function lockThreads($user, Category $category): bool
    {
        return $user->user_type=='Admin';
    }

    public function pinThreads($user, Category $category): bool
    {
        return $user->user_type=='Admin';
    }

    public function view($user, Category $category): bool
    {
        return $user->user_type=='Admin' || ( $user->user_type=='Manager' && $user->parent_user_id==1);
    }

    public function delete($user, Category $category): bool
    {
        return $user->user_type=='Admin';
    }
}