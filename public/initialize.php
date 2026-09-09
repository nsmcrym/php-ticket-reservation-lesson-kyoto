<?php

define('BASEPASS', realpath(__DIR__ . '/../'));

// タイムゾーン
date_default_timezone_set('Asia/Tokyo');

// セッション開始
ob_start();
session_start();

//共通ヘッダーの出力
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');


use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// テンプレートエンジンを使う
require_once __DIR__ . '/../vendor/autoload.php';
$loader = new FilesystemLoader(__DIR__ . '/../views');
$twig = new Environment($loader, [
  // 開発時だけ有効化
  // 'strict_variables' => true,
]);

function getConfig(): array {
    static $config = null;

    if ($config === null) {
        $config = require __DIR__ . '/../config.php';
    }
    
    return $config;
}

function getEventInstance(): EventPolicyInterface{
    static $eventObj = null;
    if ($eventObj === null) {
        $config = getConfig();

        $eventConfig = $config['event'];
        // var_dump($eventConfig);

        require_once BASEPASS . "/app/Domain/Validate/" . $eventConfig["email_validator_class"] . ".php";
        $emailValidator = new $eventConfig['email_validator_class']();

        require_once BASEPASS . "/app/Domain/Event/" . $eventConfig["class_name"] . ".php";
        $eventObj = new $eventConfig['class_name'](emailValidator: $emailValidator);
    }
    return $eventObj;
}

//DB接続取得
function getDbh(): PDO
{
    static $dbh = null;

    if ($dbh === null) {
        // DBハンドルの取得
        $config = getConfig();
        $db_config = $config['db'];
        $dsn = "mysql:dbname={$db_config['database']};host={$db_config['host']};port={$db_config['port']};charset={$db_config['charset']}";

        $opt = [
            // セキュリティ上必須
            PDO::ATTR_EMULATE_PREPARES => false,  // エミュレート無効
            Pdo\Mysql::ATTR_MULTI_STATEMENTS => false,  // 複文無効
            // お好みで
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // データ取得モード
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // エラーが発生した場合、PDOException をスロー
        ];
        try {
            $dbh = new \PDO($dsn, $db_config['user'], $db_config['pass'], $opt);
        } catch (\PDOException $e) {
            // XXX 暫定: 本来はlogに出力する & エラーページを出力する
            echo $e->getMessage();
            exit;
        }
    }


    // var_dump($dbh);
    // exit;

    return $dbh;
}
