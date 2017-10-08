<?php

namespace App;

class Result {
    public static function buildErr(string $t) {
        return [
            'ok' => false,
            'err' => $t
        ];
    }

    public static function buildOk($data = null) {
        return [
            'ok' => true,
            'data' => $data
        ];
    }
}

abstract class ErrorType {
    const ERR_INVALID_USERNAME = "ERR_INVALID_USERNAME";
    const ERR_INVALID_PASSWORD = "ERR_INVALID_PASSWORD";
    const ERR_INVALID_EMAIL = "ERR_INVALID_EMAIL";
    const ERR_USER_EXISTS = "ERR_USER_EXISTS";
    const ERR_EMAIL_EXISTS = "ERR_EMAIL_EXISTS";
    const ERR_LOGIN_FAILED = "ERR_LOGIN_FAILED";
}
