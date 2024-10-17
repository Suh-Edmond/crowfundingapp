<?php

namespace App\interfaces;

interface UserInterface
{
    public function createAccount($request);

    public function login($request);

    public function logout($request);

}
