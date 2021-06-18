<?php
    class Users extends Controller{
        public function __construct(){
            $this->userModel = $this->model('User');
        }   

        public function register(){
            // check for posts
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                // process the form

                // Sanitise post Data
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                // init the data
                $data = [
                    'name' => trim($_POST['name']),
                    'email' => trim($_POST['email']),
                    'password' => trim($_POST['password']),
                    'confirm_password' => trim($_POST['confirm_password']),
                    'name_error' => '',
                    'email_error' => '',
                    'password_error' => '',
                    'confirm_password_error' => '',
                ];

                // validate the email
                if(empty($data['email'])){
                    $data['email_err'] = 'Please enter email';
                }else{
                    // check if email already exists
                    if($this->userModel->findUserByEmail($data['email'])){
                        $data['email_err'] = 'Email is already taken';
                    }
                }

                // validate the name
                if(empty($data['name'])){
                    $data['name_err'] = 'Please enter name';
                }

                // validate the password
                if(empty($data['password']) ){
                    $data['password_err'] = 'Please enter password';
                }elseif(strlen($data['password']) < 6){
                    $data['password_err'] = 'Password must be at least 6 characters';
                }

                // validate the confirm password
                if(empty($data['confirm_password']) ){
                    $data['confirm_password_err'] = 'Please confirm password';
                }elseif($data['password'] != $data['password']){
                    $data['confirm_password_err'] = 'Password does not match';
                }

                // make sure erros are empty
                if(empty($data['name_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])){
                    // validated

                    // Hash the password
                    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                    // Register user
                    if($this->userModel->register($data)){
                        flash('register_sucess', 'You are registered and can log in');
                        redirect('users/login');
                    }else{
                        die("Something went wrong");
                    }

                }else{
                    // load view with errors
                    $this->view('/users/register',$data);
                }



            }else{
                // load the form
                // init the data
                $data = [
                    'name' => '',
                    'email' => '',
                    'password' => '',
                    'confirm_password' => '',
                    'name_error' => '',
                    'email_error' => '',
                    'password_error' => '',
                    'confirm_password_error' => '',
                ];

                $this->view('users/register', $data);
            }
        }

        public function login(){
            // check for posts
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                // process the form

                // Sanitise post Data
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                // init the data
                $data = [
                    'email' => trim($_POST['email']),
                    'password' => trim($_POST['password']),
                    'email_error' => '',
                    'password_error' => '',
                ];

                // validate the email
                if(empty($data['email'])){
                    $data['email_err'] = 'Please enter email';
                }

                if(empty($data['password']) ){
                    $data['password_err'] = 'Please enter password';
                }

                // check for user/email
                if($this->userModel->findUserByEmail($data['email'])){
                    // user found
                }else{
                    // user not found
                    $data['email_err'] = 'No user found';
                }
                // make sure erros are empty
                if(empty($data['password_err']) && empty($data['confirm_password_err'])){
                    // validated
                    // check and set logged in user
                    $loggedInUser = $this->userModel->login($data['email'],$data['password']);

                    if($loggedInUser){
                        // create session variables

                        $this->createUserSession($loggedInUser);

                    }else{
                        $data['password_err'] = 'Password Incorrect';
                        $this->view('/users/login', $data);
                    }
                }else{
                    // load view with errors
                    $this->view('/users/login',$data);
                }


            }else{
                // load the form
                // init the data
                $data = [
                    'email' => '',
                    'password' => '',
                    'email_error' => '',
                    'password_error' => '',
                ];

                $this->view('users/login', $data);
            }
        }
        
        public function createUserSession($user){
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->name;
            $_SESSION['user_email'] = $user->email;

            redirect('posts');
        }

        public function logout(){
            unset($_SESSION['user_id']);
            unset($_SESSION['user_email']);
            unset($_SESSION['user_name']);
            session_destroy();
            redirect('users/login');
        }

    }