<?php

namespace Model;

use App;
use Exception;
use Model\User_model;
use System\Core\CI_Model;

class Login_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();

    }

    public static function logout()
    {
        App::get_ci()->session->unset_userdata('id');
    }

    /**
     * @return User_model
     * @throws Exception
     */
    public static function login($login, $password): User_model
    {
        // TODO: task 1, аутентификация
        $user = User_model::find_user_by_email($login);

        if($user->is_loaded() && self::password_verify($user, $password)) {
                self::start_session($user->get_id());
        }
        return $user;
    }

    public static function start_session(int $user_id)
    {
        // если перенедан пользователь
        if (empty($user_id))
        {
            throw new Exception('No id provided!');
        }

        App::get_ci()->session->set_userdata('id', $user_id);
    }
    
    /**
     * @param \Model\User_model $user
     * @param string $password
     * @return bool
     */
    public static function password_verify(User_model $user, string $password) : bool
    {
        return $user->get_password() === $password;
    }
}
