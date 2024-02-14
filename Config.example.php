<?php

class Config {

    private static $key = '';
    private static $phpmailer_googlekey = '';
    private static $mail_contact = '';
    private static $mail_user = '';
    private static $mail_user_password = '';

    public static function getKey() {

        return self::$key;
    }

    public static function setKey($key) {

        self::$key = $key;
        return self::$key;
    }

    public static function getPhpMailerGoogleKey() {

        return self::$phpmailer_googlekey;
    }

    public static function setPhpMailerGoogleKey($phpmailer_googlekey) {

        self::$phpmailer_googlekey = $phpmailer_googlekey;
        return self::$phpmailer_googlekey;
    }

    public static function getMailContact() {

        return self::$mail_contact;
    }

    public static function setMailContact($mail_contact) {

        self::$mail_contact = $mail_contact;
        return self::$mail_contact;
    }

    public static function getMailUser() {

        return self::$mail_user;
    }

    public static function setMailUser($mail_user) {

        self::$mail_user = $mail_user;
        return self::$mail_user;
    }

    public static function getMailUserPassword() {

        return self::$mail_user_password;
    }

    public static function setMailUserPassword($mail_user_password) {

        self::$mail_user_password = $mail_user_password;
        return self::$mail_user_password;
    }
}