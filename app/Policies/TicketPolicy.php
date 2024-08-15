<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use RexlManu\LaravelTickets\Models\Ticket;
use RexlManu\LaravelTickets\Models\TicketUpload;

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see all ticket list (for admin)
     *
     * @param  User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function all(User $user) // admin all tickets
    {
        if(!in_array($user->parent_user_id,[0,1])) return false;
        return in_array($user->user_type,['Admin','Manager']);
    }

    /**
     * Determine whether the user can see own ticket list.
     *
     * @param  User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function list(User $user)
    {
        return true; // everybody can see own ticket list
    }

    /**
     * Determine whether the user can view ticket single.
     *
     * @param  User  $user
     * @param  Ticket  $ticket
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function show(User $user, Ticket $ticket)
    {
        return true; // handled manually in Support controller
    }

    /**
     * Determine whether the user can open ticket.
     *
     * @param  User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if(!in_array($user->parent_user_id,[0,1])) return false;
        return in_array($user->user_type,['Admin','Manager']) || $user->enable_ticketing=='1';
    }

    /**
     * Determine whether the user can close the ticket.
     *
     * @param  User  $user
     * @param  Ticket  $ticket
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function close(User $user, Ticket $ticket)
    {
        return true; // handled manually in Support controller
    }

    /**
     * Determine whether the user can save the ticket-message model.
     *
     * @param User $user
     * @param Ticket $ticket
     */
    public function message(User $user, Ticket $ticket)
    {
        return true; // handled manually in Support controller
    }

    /**
     * Determine whether the user can download the ticket-upload model.
     *
     * @param User $user
     * @param Ticket $ticket
     * @param TicketUpload $ticketUpload
     */
    public function download(User $user, Ticket $ticket, TicketUpload $ticketUpload)
    {
        if(!in_array($user->parent_user_id,[0,1])) return false;
        return in_array($user->user_type,['Admin','Manager']) || $ticket->user_id==$user->id;
    }
}
