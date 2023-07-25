<?php
//Задаём класс
class TG {
    $telegram = new Api('375466075:AAEARK0r2nXjB67JiB35JCXXhKEyT42Px8s'); //Устанавливаем токен, полученный у BotFather
    $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
    
    $text = $result["message"]["text"]; //Текст сообщения
    $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
    $keyboard = [["Последние статьи"],["Картинка"],["Гифка"]]; //Клавиатура

    public $token = ''; //Создаём публичную переменную для токена, который нужно отправлять каждый раз при использовании апи тг
 
    public function __construct($token) {
        $this->token = $token; //Забиваем в переменную токен при конструкте класса
    }
     
    public function send($id, $message) {   //Задаём публичную функцию send для отправки сообщений
        //Заполняем массив $data инфой, которую мы через api отправим до телеграмма
		$data = array(
			'chat_id'      => $id,
			'text' 	   => $message,
		);
        //Получаем ответ через функцию отправки до апи, которую создадим ниже
        $out = $this->request('sendMessage', $data);
        //И пусть функция вернёт ответ. Правда в данном примере мы это никак не будем использовать, пусть будет задаток на будущее
        return $out;
    }   
     
    public  function request($method, $data = array()) {
        $curl = curl_init(); //мутим курл-мурл в переменную. Для отправки предпочтительнее использовать курл, но можно и через file_get_contents если сервер не поддерживает
         
		curl_setopt($curl, CURLOPT_URL, 'https://api.telegram.org/bot' . $this->token .  '/' . $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST'); //Отправляем через POST
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); //Сами данные отправляемые
         
        $out = json_decode(curl_exec($curl), true); //Получаем результат выполнения, который сразу расшифровываем из JSON'a в массив для удобства
         
        curl_close($curl); //Закрываем курл
         
        return $out; //Отправляем ответ в виде массива
    }

    if($text){
         if ($text == "/start") {
            $reply = "Добро пожаловать в бота!";
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
        }elseif ($text == "/help") {
            $reply = "Информация с помощью.";
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
        }elseif ($text == "Картинка") {
            $url = "https://68.media.tumblr.com/6d830b4f2c455f9cb6cd4ebe5011d2b8/tumblr_oj49kevkUz1v4bb1no1_500.jpg";
            $telegram->sendPhoto([ 'chat_id' => $chat_id, 'photo' => $url, 'caption' => "Описание." ]);
        }elseif ($text == "Гифка") {
            $url = "https://68.media.tumblr.com/bd08f2aa85a6eb8b7a9f4b07c0807d71/tumblr_ofrc94sG1e1sjmm5ao1_400.gif";
            $telegram->sendDocument([ 'chat_id' => $chat_id, 'document' => $url, 'caption' => "Описание." ]);
        }elseif ($text == "Последние статьи") {
            $html=simplexml_load_file('http://netology.ru/blog/rss.xml');
            foreach ($html->channel->item as $item) {
         $reply .= "\xE2\x9E\xA1 ".$item->title." (<a href='".$item->link."'>читать</a>)\n";
            }
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode' => 'HTML', 'disable_web_page_preview' => true, 'text' => $reply ]);
        }else{
            $reply = "По запросу \"<b>".$text."</b>\" ничего не найдено.";
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => $reply ]);
        }
    }else{
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "Отправьте текстовое сообщение." ]);
    }
}
