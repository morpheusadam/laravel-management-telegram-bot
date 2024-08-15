<?php

namespace App\Policies;

use Illuminate\Support\Facades\Gate;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Policies\ThreadPolicy as Base;

class ThreadPolicy extends Base
{
    public function deletePosts($user, Thread $thread): bool
    {
        return $user->user_type=='Admin';
    }

    public function rename($user, Thread $thread): bool
    {
        return $user->id === $thread->author_id || $user->user_type=='Admin';
    }

    public function delete($user, Thread $thread): bool
    {
        return $user->id === $thread->author_id || $user->user_type=='Admin';
    }

    public function restore($user, Thread $thread): bool
    {
        return $user->id === $thread->author_id || $user->user_type=='Admin';
    }

    public function reply($user, Thread $thread): bool
    {
        return !$thread->locked && ($user->user_type=='Admin' || $user->user_type=='Manager'  || ($user->parent_user_id==1 && $user->enable_forum_thread=='1') ) ; // admin and admin sub users only
    }
}