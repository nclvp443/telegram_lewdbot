<?php
ini_set('error_reporting', E_ALL);

$config = json_decode(
    file_get_contents('config.json')
);

//Telegram Hook Api key
$token = $config->telegram->bot_token;
$site='https://api.telegram.org/bot'.$token;

//Booru Api Key
$apikey = $config->booru->api_key;
$login = $config->booru->booru_username;

$update = json_decode(file_get_contents('php://input'), true);

$id = $update["message"]["chat"]["id"];
$msg = $update["message"]["text"];

$keyword = str_replace('/getlewds ','',$msg);
$query = str_replace(' ','_',$keyword);

$booruAPI = json_decode(
    file_get_contents('https://danbooru.donmai.us/posts.json?limit=1&tags='.$query.'%20rating:e&random=true&login='.$login.'&api_key='.$apikey),
    true);

$urlkey= $booruAPI[0]['file_url'];
$sauce = $booruAPI[0]['source'];

switch ($msg){
    case '/getlewds '.$keyword:
        $image = 'https://danbooru.donmai.us'.$urlkey;
        sendImage($id, $image, $image);
        if(empty($booruAPI)){
            sendMessage($id,'No Lewds Found for '.$query);
        }
        break;

    case '/start':
        sendMessage($id,'Yahallo!');
        break;
}

function sendMessage($id,$msg){
    $url = $GLOBALS[site].'/sendMessage?chat_id='.$id.'&text='.urlencode($msg);
    file_get_contents($url);
}

function sendImage($id,$photo,$caption){
    $url = $GLOBALS[site].'/sendPhoto?chat_id='.$id.'&photo='.$photo.'&caption='.$caption;
    file_get_contents($url);
}

?>