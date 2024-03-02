<?php
namespace App\MonBeauVelo\Lib;

class MessageFlash {

    // Les messages sont enregistrés en session associée à la clé suivante
    private static string $cleFlash = "_messagesFlash";

    // $type parmi "success", "info", "warning" ou "danger"
    public static function ajouter(string $type, string $message): void {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION[self::$cleFlash])) {
            $_SESSION[self::$cleFlash] = [];
        }

        if (!isset($_SESSION[self::$cleFlash][$type])) {
            $_SESSION[self::$cleFlash][$type] = [];
        }

        $_SESSION[self::$cleFlash][$type][] = $message;
    }

    public static function contientMessage(string $type): bool {
        if (!isset($_SESSION)) {
            session_start();
        }
        return isset($_SESSION[self::$cleFlash][$type]) && count($_SESSION[self::$cleFlash][$type]) > 0;
    }

    // Attention : la lecture doit détruire le message
    public static function lireMessages(string $type): array {
        if (!isset($_SESSION)) {
            session_start();
        }
        $messages = $_SESSION[self::$cleFlash][$type] ?? [];
        unset($_SESSION[self::$cleFlash][$type]);
        return $messages;
    }

    public static function lireTousMessages() : array {
        if (!isset($_SESSION)) {
            session_start();
        }
        $allMessages = $_SESSION[self::$cleFlash] ?? [];
        unset($_SESSION[self::$cleFlash]);
        return $allMessages;
    }
}
?>