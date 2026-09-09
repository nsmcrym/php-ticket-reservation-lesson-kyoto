<?php

declare(strict_types=1);

//共通処理の読み取り
require_once __DIR__ . '/../initialize.php';

// 認可チェック
require_once BASEPASS . '/public/admin/authorization.php';


echo "csv_download.php";

//必要な情報を作る
$dbh = getDbh();
var_dump($dbh);

require_once BASEPASS . '/app/Models/TicketPurchase.php';

$list = new TicketPurchase($dbh) -> getList();
// var_dump($list);

/*csvダウンロードをさせる*/
$fn = 'TicketPurchase.' . date('YmdHis') . '.csv';
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename={$fn}");

//出力用ストリーム
$fp = fopen('php://output', 'w');
if (false === $fp) {
    echo "fopenでエラー";
    exit;
}

$keys = array_keys($list[0]);
fputcsv($fp, $keys, escape:"");

foreach ($list as $datum) {
    $datum_s = mb_convert_encoding($datum, 'SJIS-win', 'UTF-8');
    $r = fputcsv($fp, $datum, escape:'');
    if (false === $fp) {
        echo "fputcsvでエラー";
        exit;
    }
}

fclose($fp);
