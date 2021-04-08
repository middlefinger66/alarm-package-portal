<?php
/**
 *  Created by Wahyu Arif P (warifp)
 *  Modified by Putra AR (N1ght420)
 *  08 April 2021
 * 
 * https://www.linkedin.com/in/warifp/
 */

require __DIR__ . '/vendor/autoload.php';
include 'config.php';
if (!file_exists('address')){
    mkdir('address');
}

use Curl\Curl;

// init curl
$curl = new Curl();

function telegramConnect($curl, $telegramToken, $telegramChatId) {
    $curl->get('https://api.telegram.org/bot' . $telegramToken . '/sendMessage?chat_id=' . $telegramChatId . '&text=Package Portal alarm is connected.');
    return $curl->response->ok;
}

if(telegramConnect($curl, $telegramToken, $telegramChatId)) {
    echo 'Package Portal alarm is connected.';
} else {
    echo 'Failed to connect your Telegram bot.';
    exit;
}

start:
$list = file_get_contents($inputList);
$datas = explode("\n", str_replace("\r", "", $list));

for ($i = 0; $i < count($datas); $i++) {
    $address = $datas[$i];

    if (!file_exists("address/$address")){
        file_put_contents("address/$address", '0');
    }
    $jumlah = file_get_contents("address/$address");
    if ($jumlah == ''){$jumlah = '0';}

    $curl->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36');
    $curl->setHeader('Origin', 'https://viewblock.io');
    $curl->get('https://api.viewblock.io/zilliqa/addresses/' . $address . '?network=mainnet&page=1');
    $response = $curl->response->txs->docs;
    $jumarray = count($response)-1;

    if ($curl->error) {
        echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
    } else {
        if (isset($curl->response->tokens->zil18f5rlhqz9vndw4w8p60d0n7vg3n9sqvta7n6t2)) {
            if ($jumarray > $jumlah){
                file_put_contents("address/$address", $jumarray);
                $curl->get('https://api.telegram.org/bot' . $telegramToken . '/sendMessage?chat_id=' . $telegramChatId . '&text=Hei, address : ' . $address . ' landing!');
            }
        }
    }
}

sleep(18000);
goto start;

?>
