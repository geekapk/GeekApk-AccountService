<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Account;
use App\LoginRecord;
use App\Result;
use App\ErrorType;

class AccountController extends Controller
{
    public function login(Request $req) {
        $name = $req->input('name');
        $password = $req->input('password');

        $name = strtolower($name);

        $account = Account::where('name', $name)->first();
        if(!$account) {
            return Result::buildErr(ErrorType::ERR_LOGIN_FAILED);
        }
        $realPw = $account["password"];
        $ipAddr = $req->ip();
        $userAgent = $req->header("User-Agent");

        if(!password_verify($password, $realPw)) {
            return Result::buildErr(ErrorType::ERR_LOGIN_FAILED);
        }

        LoginRecord::create([
            "user_id" => $account["id"],
            "ip" => $ipAddr,
            "user_agent" => $userAgent
        ]);

        return Result::buildOk();
    }

    public function register(Request $req) {
        $name = $req->input('name');
        $email = $req->input('email');
        $password = $req->input('password');
        
        $name = strtolower($name);

        if(strlen($name) < 3) {
            return Result::buildErr(ErrorType::ERR_INVALID_USERNAME);
        }

        if(strlen($password) < 6) {
            return Result::buildErr(ErrorType::ERR_INVALID_PASSWORD);
        }

        if(strlen($email) < 1) {
            return Result::buildErr(ErrorType::ERR_INVALID_EMAIL);
        }

        $account = Account::where('name', $name)->first();
        if($account) {
            return Result::buildErr(ErrorType::ERR_USER_EXISTS);
        }

        $account = Account::where('email', $email)->first();
        if($account) {
            return Result::buildErr(ErrorType::ERR_EMAIL_EXISTS);
        }

        Account::create([
            "name" => $name,
            "email" => $email,
            "password" => password_hash($password, PASSWORD_BCRYPT)
        ]);

        return Result::buildOk();
    }
}
