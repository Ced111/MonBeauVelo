<?php
namespace App\MonBeauVelo\Lib;

use App\MonBeauVelo\Modele\HTTP\Session;

class MessageFlash {
    private static string $cleFlash = "_messagesFlash";

    public static function ajouter(string $type, string $message): void {
        $session = Session::getInstance();
        if (!isset($_SESSION[self::$cleFlash])) {
            $_SESSION[self::$cleFlash] = [];
        }

        if (!isset($_SESSION[self::$cleFlash][$type])) {
            $_SESSION[self::$cleFlash][$type] = [];
        }

        $_SESSION[self::$cleFlash][$type][] = $message;
    }

    public static function contientMessage(string $type): bool {
        $session = Session::getInstance();
        return isset($_SESSION[self::$cleFlash][$type]) && count($_SESSION[self::$cleFlash][$type]) > 0;
    }

    public static function lireMessages(string $type): array {
        $session = Session::getInstance();
        $messages = $_SESSION[self::$cleFlash][$type] ?? [];
        unset($_SESSION[self::$cleFlash][$type]);
        return $messages;
    }

    public static function lireTousMessages() : array {
        $session = Session::getInstance();
        $allMessages = $_SESSION[self::$cleFlash] ?? [];
        unset($_SESSION[self::$cleFlash]);
        return $allMessages;
    }
}
