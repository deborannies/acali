<?php

namespace Lib;

class FlashMessage
{
    private const FLASH_KEY = 'flash_messages';

    public static function success(string $message): void
    {
        self::addMessage($message, 'success');
    }

    public static function danger(string $message): void
    {
        self::addMessage($message, 'danger');
    }

    public static function warning(string $message): void
    {
        self::addMessage($message, 'warning');
    }

    private static function addMessage(string $message, string $type): void
    {
        if (!isset($_SESSION[self::FLASH_KEY])) {
            $_SESSION[self::FLASH_KEY] = [];
        }

        $_SESSION[self::FLASH_KEY][] = ['message' => $message, 'type' => $type];
    }

    /**
     * @return array<string, string>[]
     */
    public static function getMessages(): array
    {
        if (!isset($_SESSION[self::FLASH_KEY])) {
            return [];
        }

        $messages = $_SESSION[self::FLASH_KEY];
        unset($_SESSION[self::FLASH_KEY]);
        return $messages;
    }

    public static function hasMessages(): bool
    {
        return isset($_SESSION[self::FLASH_KEY]) && !empty($_SESSION[self::FLASH_KEY]);
    }
}
