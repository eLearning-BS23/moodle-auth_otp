<?php

namespace otp\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\context;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\core_userlist_provider;
use core_privacy\local\request\userlist;

class provider implements \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    core_userlist_provider
{

    /**
     * @inheritDoc
     */
    public static function get_metadata(collection $collection): collection
    {
        // TODO: Implement get_metadata() method.
    }

    public static function get_contexts_for_userid(int $userid): contextlist
    {
        // TODO: Implement get_contexts_for_userid() method.
    }

    public static function export_user_data(approved_contextlist $contextlist)
    {
        // TODO: Implement export_user_data() method.
    }

    public static function delete_data_for_all_users_in_context(\context $context)
    {
        // TODO: Implement delete_data_for_all_users_in_context() method.
    }

    public static function delete_data_for_user(approved_contextlist $contextlist)
    {
        // TODO: Implement delete_data_for_user() method.
    }

    public static function get_users_in_context(userlist $userlist)
    {
        // TODO: Implement get_users_in_context() method.
    }

    public static function delete_data_for_users(approved_userlist $userlist)
    {
        // TODO: Implement delete_data_for_users() method.
    }
}