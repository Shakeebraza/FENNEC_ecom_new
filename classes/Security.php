<?php

class Security {
    private $cipher = "AES-128-ECB";
    private $key;

    public function __construct($key) {
        $this->key = substr(hash('sha256', $key), 0, 16);
    }

    public function encrypt($data) {
        return base64_encode(openssl_encrypt($data, $this->cipher, $this->key, OPENSSL_RAW_DATA));
    }

    public function decrypt($data) {
        return openssl_decrypt(base64_decode($data), $this->cipher, $this->key, OPENSSL_RAW_DATA);
    }
}
