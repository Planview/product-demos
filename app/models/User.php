<?php

use Zizaco\Confide\ConfideUser;
use Zizaco\Confide\ConfideUserInterface;
use Zizaco\Entrust\HasRole;

class User extends Eloquent implements ConfideUserInterface
{
    use ConfideUser, HasRole;

    public function demos()
    {
        // demo_user
        return $this->belongsToMany('Demo', 'user_demo_access');
    }

    public function isrs()
    {
        // isr_user
        return $this->hasMany('Isr');
    }

    /**
     * Create a simple array of the user's roles by id
     */
    public function rolesById()
    {
        $roles = array();
        foreach ($this->roles as $role) {
            $roles[] = $role->id;
        }
        return $roles;
    }

    /**
     * Create an array of clients (non-admins)
     * @return User object array
     */
    public static function usersClientsOnly()
    {
        return self::orderBy('company', 'asc')->get()->filter(function ($user) {
            return !$user->can('manage_clients');
        });
    }

    /**
     * Create an array of users with specific abilities
     * @return User object array
     */
    public static function usersWithAbility($role, $permission = array())
    {
        $options = array(
            'validate_all' => true,
            'return_type'  => 'boolean'
        );
        return self::orderBy('email', 'asc')->get()->filter(function ($user) use ($role, $options, $permission) {
            return $user->ability($role, $permission, $options);
        });
    }


    /**
     * Create an array of users with a specific permission
     * @return User object array
     */
    public static function usersWithPermission($permission)
    {
        return self::orderBy('email', 'asc')->get()->filter(function ($user) use ($permission) {
            return $user->can($permission);
        });
    }

    /**
     * Create an array of user IDs with a specific permission
     * @return User id array
     */
    public static function usersWithPermissionIdArray($permission)
    {
        $userIds = array();

        $filteredUsers = self::orderBy('email', 'asc')->get()->filter(function ($user) use ($permission) {
            return $user->can($permission);
        });

        foreach ($filteredUsers as $filteredUser) {
            $userIds[] = $filteredUser->id;
        }

        return $userIds;
    }

    /**
     * Create an array of users without a specific permission
     * @return User object array
     */
    public static function usersWithoutPermission($permission)
    {
        return self::orderBy('email', 'asc')->get()->filter(function ($user) use ($permission) {
            return !$user->can($permission);
        });
    }

    /**
    * Create a random password
    * @return string password
    */
    public function autoGeneratePassword()
    {
        $length = 8;
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $password = substr(str_shuffle($chars),0,$length);

        $this->password = $password;
        $this->password_confirmation = $password;

        return $password;
    }

    /**
     * Make an options list for a select drop-down form item
     * @return array All ISRs keyed as id => name
     */
    public static function isrList()
    {
        $list = array();
        $isrs = DB::table('isrs')->orderBy('isr_last_name', 'asc')->orderBy('isr_first_name', 'asc')->get();

        foreach ($isrs as $isr) {
            // User::find(Auth::id()->ability('ISR Admin', 'manage_clients')
            if ((User::find($isr->user_id)->ability('ISR Admin', 'manage_clients')) && ($isr->isr_first_name != '') && ($isr->isr_last_name != '')) {
                $list[$isr->user_id] = $isr->isr_first_name.' '.$isr->isr_last_name;
            }
        }
        return $list;
    }

    /**
     * Gather ISR-specific information from the db
     * @return object ISR information
     */
    public function isrInfo()
    {
        if (!($isr = DB::table('isrs')->where('user_id', '=', $this->id)->first())) {
            $isr = new Isr();
        }
        return $isr;
    }

    /**
     * Make a list of the user's demo access
     * @return array of demo IDs
     */
    public function demoAccessList()
    {
        $list = array();

        foreach ($this->demos as $demo) {
            $list[] = $demo->id;
        }
        return $list;
    }


}
