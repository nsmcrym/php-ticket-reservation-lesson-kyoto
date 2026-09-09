<?php

// 認可チェック
require_once __DIR__ . '/authorization.php';
if (false === isset($_SESSION['admin_logged_in'])) {
    // ログイン画面へ
    header('Location: /admin/index.php');
    exit;
}
