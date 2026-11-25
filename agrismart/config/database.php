<?php
/**
 * config/database.php
 * Connexion PDO sécurisée - patron Singleton : la connexion n'est créée qu'une seule fois
 * et partagée par toute l'application.
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function getConnexion(): PDO
    {
        if (self::$pdo === null) {
            $config = require __DIR__ . '/config.php';
            $db = $config['db'];
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $db['host'], $db['port'], $db['name'], $db['charset']);
            try {
                self::$pdo = new PDO($dsn, $db['user'], $db['pass'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // une erreur SQL lève une exception
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,                    // vraies requêtes préparées (anti injection SQL)
                ]);
            } catch (PDOException $e) {
                error_log('AgriSmart DB: ' . $e->getMessage());
                http_response_code(500);
                die(!empty($config['debug'])
                    ? 'Erreur base de données : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
                    : 'Service temporairement indisponible (connexion à la base de données impossible).');
            }
        }
        return self::$pdo;
    }

    /** Alias (orthographe anglaise utilisée dans l'énoncé du workshop). */
    public static function getConnection(): PDO
    {
        return self::getConnexion();
    }
}
