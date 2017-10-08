<?php

namespace App;

use Predis;

class EmailQueue {
    private static $conn = null;

    public static function init() {
        if(self::$conn) return;
        self::$conn = new Predis\Client();
    }

    public static function push(Email $email) {
        self::init();
        self::$conn->lpush(config('app.REDIS_PREFIX') . 'email_queue', json_encode($email));
    }
}

class Email {
    public function __construct(string $receiver, string $title, string $content) {
        $this->receiver = $receiver;
        $this->title = $title;
        $this->content = $content;
    }
}
