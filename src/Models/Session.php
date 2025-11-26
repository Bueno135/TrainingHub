<?php
// ============================================
// src/Models/Session.php
// ============================================
class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove($key) {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function destroy() {
        self::start();
        session_unset();
        session_destroy();
    }

    public static function setUser($user) {
        self::set('user_id', $user['id']);
        self::set('user_email', $user['email']);
        self::set('user_tipo', $user['tipo']);
        self::set('logged_in', true);
    }

    public static function getUser() {
        if (!self::isLoggedIn()) {
            return null;
        }
        return [
            'id' => self::get('user_id'),
            'email' => self::get('user_email'),
            'tipo' => self::get('user_tipo')
        ];
    }

    public static function isLoggedIn() {
        return self::get('logged_in', false) === true;
    }

    public static function logout() {
        self::destroy();
    }
}

