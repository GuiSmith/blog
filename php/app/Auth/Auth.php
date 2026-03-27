<?php

namespace App\Auth;

use App\Models\User;

class Auth
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function user()
    {
        if (isset($_SESSION['user'])){
            return User::find($_SESSION['user']);
        }
    }

    public function check()
    {
        return isset($_SESSION['user']);
    }

    public function attempt(string $email, string $password)
    {
        $users = User::where('email', $email)->get();
        
        // Checking if there's more than one user with the same e-mail
        if($users->count() > 1){
            $this->container->flash->addMessage('error', 'Multiple accoutns registered with the same e-mail. Please, contact Support team');
            return false;
        }

        $user = $users->first();

        // Checking password
        if($user->count() == 0 || !password_verify($password,$user->password)){
            $this->container->flash->addMessage('error', 'Invalid credentials, try again!');
            return false;
        }

        $_SESSION['user'] = $user->id;

        return true;
    }

    public function logout(){
        unset($_SESSION['user']);
        if(isset($_SESSION['user'])){
            $this->container->flash->addMessage('error', "Couldn't log out. Contact our support team");
            return false;
        } else{
            $this->container->flash->addMessage('success', 'Logged out successfully');
            return true;
        }
    }
}
