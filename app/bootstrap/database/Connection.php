<?php

abstract class Connection
{
    private static $conn;

    public static function getConn()
    {
        if (self::$conn == null) {
            self::$conn = new PDO("{$_ENV['DB_ADAPTER']}: host={$_ENV['DB_HOST']}; port={$_ENV['DB_PORT']}; dbname={$_ENV['DB_DATABASE']};", $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
        }

        return self::$conn;
    }
}