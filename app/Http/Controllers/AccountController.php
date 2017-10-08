<?php

namespace App\Http\Controllers;

use Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Account;
use App\PendingAccount;
use App\LoginRecord;
use App\Result;
use App\ErrorType;
use App\EmailQueue;
use App\Email;
use Validator;
use \Firebase\JWT\JWT;

class AccountController extends Controller
{
    private function getAccountFromRequest(Request $req) {
        $jwt = $req->cookie('GEEKAPK_JWT');
        if(!$jwt) {
            return null;
        }

        $key = config('app.JWT_KEY');
        $info = JWT::decode($jwt, $key, [ 'HS256' ]);
        if(!$info) {
            return null;
        }

        $info = (array) $info;

        $account = Account::find($info["user_id"]);
        return $account;
    }

    public function login(Request $req) {
        Validator::make(
            $req->all(),
            [
                'name' => 'required|string|max:255',
                'password' => 'required|string|max:255'
            ]
        )->validate();

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

        $resp = new Response(Result::buildOk());
        $info = [
            'user_id' => $account['id'],
            'username' => $account['name'],
            'email' => $account['email'],
            'create_time' => time()
        ];
        $key = config('app.JWT_KEY');
        assert($key != null && strlen($key) > 0);
        $jwt = JWT::encode($info, $key, 'HS256');
        $resp->withCookie('GEEKAPK_JWT', $jwt, 60 /* minutes */);

        return $resp;
    }

    public function verify_email(Request $req) {
        Validator::make(
            $req->all(),
            [
                'token' => 'required|string|max:255'
            ]
        );
        $token = $req->input('token');

        $pa = PendingAccount::where('email_token', $token)->first();
        if(!$pa) {
            return Result::buildErr(ErrorType::ERR_INVALID_TOKEN);
        }

        $pa->delete();

        $account = Account::where('name', $pa['name'])->first();
        if($account) {
            return Result::buildErr(ErrorType::ERR_USER_EXISTS);
        }

        $account = Account::where('email', $pa['email'])->first();
        if($account) {
            return Result::buildErr(ErrorType::ERR_EMAIL_EXISTS);
        }

        Account::create([
            "name" => $pa['name'],
            "email" => $pa['email'],
            "password" => $pa['password']
        ]);

        return Result::buildOk();
    }

    public function register(Request $req) {
        Validator::make(
            $req->all(),
            [
                'name' => 'required|string|max:255|min:3',
                'email' => 'required|string|max:255|email',
                'password' => 'required|string|max:255|min:6'
            ]
        )->validate();

        $name = $req->input('name');
        $email = $req->input('email');
        $password = $req->input('password');
        
        $name = strtolower($name);

        $account = Account::where('name', $name)->first();
        if($account) {
            return Result::buildErr(ErrorType::ERR_USER_EXISTS);
        }

        $account = Account::where('email', $email)->first();
        if($account) {
            return Result::buildErr(ErrorType::ERR_EMAIL_EXISTS);
        }

        $emailToken = bin2hex(random_bytes(16));

        PendingAccount::create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'email_token' => $emailToken
        ]);

        EmailQueue::push(new Email(
            $email,
            'GeekApk 注册确认',
            'Token: ' . $emailToken // TODO: User-friendly email content
        ));

        return Result::buildOk();
    }

    public function info(Request $req) {
        $account = $this->getAccountFromRequest($req);
        if(!$account) {
            return Result::buildErr(ErrorType::ERR_INVALID_TOKEN);
        }

        return Result::buildOk([
            'name' => $account['name'],
            'email' => $account['email']
        ]);
    }
}
