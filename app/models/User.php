<?php
    class User{
        private $db;

        public function __construct(){
            $this->db = new Database;
        }

        // Register User
        public function register($data){
            $this->db->query('INSERT INTO users (name, email, password)    VALUES (:name, :email,  :password)');

            // Bind the values
            $this->db->bind(':name',$data['name']);
            $this->db->bind(':email',$data['email']);
            $this->db->bind(':password',$data['password']);

            // Execute
            if($this->db->execute()){
                return true;
            }
            return false;
        }

        // login user
        public function login($email, $password){
            $this->db->query('SELECT * FROM users WHERE email= :email');

            $this->db->bind(':email', $email);

            $row = $this->db->single();

            $hashed_password = $row->password;
            if(password_verify($password, $hashed_password)){
                return $row;
            }
            return false;
        }
        
        // find user by email
        public function findUserByEmail($email){
            $this->db->query('SELECT * FROM users WHERE email= :email');

            // Bind the values
            $this->db->bind(':email',$email);

            $row = $this->db->single();

            // check Row
            if($this->db->rowCount() > 0){
                return true;
            }
        }

        // find user by email
        public function getUserByid($id){
            $this->db->query('SELECT * FROM users WHERE id= :id');

            // Bind the values
            $this->db->bind(':id',$id);

            return $this->db->single();

        }
    }