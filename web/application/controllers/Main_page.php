<?php

use Model\Boosterpack_model;
use Model\Comment_model;
use Model\Post_model;
use Model\User_model;
use Model\Login_model;

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 10.11.2018
 * Time: 21:36
 */
class Main_page extends MY_Controller
{
    
    public function __construct()
    {

        parent::__construct();

        if (is_prod())
        {
            die('In production it will be hard to debug! Run as development environment!');
        }
    }
    
    public function index()
    {
        $user = User_model::get_user();
        
        App::get_ci()->load->view('main_page', ['user' => User_model::preparation($user, 'default')]);
    }
    
    public function get_all_posts()
    {
        $posts = Post_model::preparation_many(Post_model::get_all(), 'default');
        return $this->response_success(['posts' => $posts]);
    }
    
    public function get_boosterpacks()
    {
        $posts = Boosterpack_model::preparation_many(Boosterpack_model::get_all(), 'default');
        return $this->response_success(['boosterpacks' => $posts]);
    }
    
    
    public function login()
    {
        // TODO: task 1, аутентификация
        $login = App::get_ci()->input->post('login');
        $password = App::get_ci()->input->post('password');
        
        if (!empty($login) && !empty($password)) {
            
            $user = Login_model::login($login, $password);
            
            if (User_model::is_logged()) {
                $this->response_success(['user' => $user]);
            } else {
                $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_WRONG_PARAMS);
            }
        } else {
            $this->response_error(System\Libraries\Core::RESPONSE_STATUS_ERROR);
        }
    }
    
    public function logout()
    {
        // TODO: task 1, аутентификация
        Login_model::logout();
        if (!User_model::is_logged()) {
            $this->response_success();
        }
    }
    
    public function comment()
    {
        // TODO: task 2, комментирование
        if (!User_model::is_logged()) {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }
        
        $postId = (int)App::get_ci()->input->post('postId');
        $replyId = (int)App::get_ci()->input->post('replyId') ?? null;
        $commentText = (string)App::get_ci()->input->post('commentText');
        $userId = User_model::get_session_id();
        
        if (!empty($postId) && !empty($commentText)) {
            $data = [
                'user_id' => $userId,
                'assign_id' => $postId,
                'reply_id' => $replyId,
                'text' => htmlspecialchars(trim($commentText))
            ];
            $comment = Comment_model::preparation(Comment_model::create($data), 'default');
            
            return $this->response_success(['comment' => $comment]);
        } else return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NO_DATA);
    }
    
    /**
     * @param int $comment_id
     * @return object|string|void
     * @throws Exception
     */
    public function like_comment(int $comment_id)
    {
        // TODO: task 3, лайк комментария
        if (!User_model::is_logged()) {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }
        
        $user = User_model::get_user();
        $comment = Comment_model::get_by_id($comment_id);
    
        if ($user->get_likes_balance() > 1) {
            if ($comment->increment_likes() && $user->decrement_likes()) {
                return $this->response_success();
            } else return $this->response_error(System\Libraries\Core::RESPONSE_STATUS_ERROR);
        } else return $this->response_error('Balance likes is empty');
    }
    
    public function like_post(int $post_id)
    {
        // TODO: task 3, лайк поста
        if (!User_model::is_logged()) {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }
        
        $user = User_model::get_user();
        $post = Post_model::get_by_id($post_id);
        
        if ($user->get_likes_balance() > 1) {
            if ($post->increment_likes($user) && $user->decrement_likes()) {
                return $this->response_success(['likes' => $post->get_likes()]);
            } else return $this->response_error(System\Libraries\Core::RESPONSE_STATUS_ERROR);
        } else return $this->response_error('Balance likes is empty');
    }
    
    public function add_money()
    {
        // TODO: task 4, пополнение баланса
        
        $sum = (float)App::get_ci()->input->post('sum');
        
    }
    
    public function get_post(int $post_id)
    {
        // TODO получения поста по id
        $post = Post_model::preparation(Post_model::get_by_id($post_id), 'full_info');
        return $this->response_success(['post' => $post]);
    }
    
    public function buy_boosterpack()
    {
        // Check user is authorize
        if (!User_model::is_logged()) {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }
        
        // TODO: task 5, покупка и открытие бустерпака
    }
    
    
    /**
     * @return object|string|void
     */
    public function get_boosterpack_info(int $bootserpack_info)
    {
        // Check user is authorize
        if (!User_model::is_logged()) {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }
        
        
        //TODO получить содержимое бустерпака
    }
}
