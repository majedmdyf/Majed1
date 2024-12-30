<?php
define('BOT_TOKEN', '7718566267:AAFSejuL0M9cZXhYLaqu64o0WfdfZFXTraI');
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

function apiRequest($method, $parameters) {
    $url = API_URL . $method . '?' . http_build_query($parameters);
    $handle = curl_init($url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($handle);
    curl_close($handle);
    return json_decode($response, true);
}

function sendMessage($chat_id, $text, $reply_markup = null) {
    $params = [
        'chat_id' => $chat_id,
        'text' => $text,
        'reply_markup' => $reply_markup,
    ];
    apiRequest('sendMessage', $params);
}

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (isset($update['message'])) {
    $message = $update['message'];
    $chat_id = $message['chat']['id'];
    $from_id = $message['from']['id'];
    $text = $message['text'];
    $first_name = $message['from']['first_name'];

    if ($text == '/start' && $from_id == 584357776) {
        $response = "$first_name \n$chat_id \n
  جميع الحقوق محفوظة ماجد
@CoinsMDYF_bot
         اوامر للمدير 
         رسالة للكل
         /inall
         ارصدة المستخدمين
         /nn
         عدد المستخدمين
         /u ";
        sendMessage($chat_id, $response);
    }
if ($text == '/start' && $from_id != 584357776) {
$response = "$first_name \n$chat_id \n
مرحبا بك في بوت الاستثمار 
يقدم البوت خطط شهرية وسنوية وغيرها 
\n\nلمعرفة رصيدك الحالي، أرسل /m
\n لارسال رسالة للمدير او الاشتراك بخطة \n ارسل /n ثم الرسالة مثل \n /n hi \n
  لاضافة طلب لفتح حساب
  /new
  جميع الحقوق محفوظة ماجد
@CoinsMDYF_bot ";
sendMessage($chat_id, $response); 
}
elseif ($text == '/in') {
if ($from_id == 584357776) {
$response = "أرسل رسالتك بعد هذا الأمر مباشرةً في نفس السطر.";
sendMessage($chat_id, $response, [
                'reply_markup' => json_encode([
                    'force_reply' => true,
                ])
            ]);
        } 
    } elseif (preg_match('/^\/in all (.+)/', $text, $matches)) {
        if ($from_id == 584357776) {
            $message_text = $matches[1];
            $files = glob('user_*.txt');
            foreach ($files as $file) {
                $user_id = str_replace('user_', '', $file);
                $user_id = str_replace('.txt', '', $user_id);
                sendMessage($user_id, "رسالة جماعية من الإدارة:\n$message_text");
            }
            sendMessage($chat_id, 'تم إرسال رسالتك الجماعية بنجاح إلى جميع المستخدمين.');
        }
    } elseif ($from_id == 584357776) {
        if ($text == '/u') {
            $files = glob('user_*.txt');
            $response = "قائمة معرفات المستخدمين:\n";
            foreach ($files as $file) {
                $user_id = str_replace('user_', '', $file);
                $user_id = str_replace('.txt', '', $user_id);
                $response .= "المستخدم $user_id\n";
            }
            $user_count = count($files);
            $response .= "\nإجمالي عدد المستخدمين: $user_count";
            sendMessage($chat_id, $response);
        } elseif ($text == '/nn') {
            $files = glob('user_*.txt');
            $response = "قائمة أرصدة المستخدمين:\n";
            foreach ($files as $file) {
                $user_id = str_replace('user_', '', $file);
                $user_id = str_replace('.txt', '', $user_id);
                $saved_number = file_get_contents($file);
                $response .= "المستخدم $user_id: $saved_number\n";
            }
            sendMessage($chat_id, $response);
        } elseif (preg_match('/^\/n (\d+) (.+)/', $text, $matches)) {
            $target_id = $matches[1];
            $reply_text = $matches[2];
            sendMessage($target_id, "رد من الإدارة:\n$reply_text");
            sendMessage($chat_id, 'تم إرسال ردك بنجاح.');
        } elseif (preg_match('/^(\d+) add (\+|-) (\d+)$/', $text, $matches)) {
            $target_id = $matches[1];
            $operation = $matches[2];
            $number = $matches[3];

            if (is_numeric($target_id) && is_numeric($number)) {
                $filename = "user_{$target_id}.txt";

                // إنشاء ملف إذا لم يكن موجودًا
                if (!file_exists($filename)) {
                    file_put_contents($filename, '0');
                }

                $saved_number = file_get_contents($filename);

                if ($operation == '+') {
                    $new_number = $saved_number + $number;
                } else {
                    $new_number = $saved_number - $number;
                }

                file_put_contents($filename, $new_number);
                sendMessage($chat_id, "تم تحديث الرصيد للمستخدم $target_id بنجاح.");
            } else {
                sendMessage($chat_id, "تنسيق الرسالة غير صحيح. يرجى التأكد من صحة معرف المستخدم والرقم.");
            }
        } elseif ($text == '/m') {
            $filename = "user_{$from_id}.txt";
            $saved_number = file_get_contents($filename);
            if ($saved_number) {
                sendMessage($chat_id, "رصيدك الحالي هو: $saved_number");
            } else {
                sendMessage($chat_id, "رصيدك الحالي هو: 0");
            }
        }
    } else {
        if ($text == '/n') {
            $response = "أرسل رسالتك بعد هذا الأمر مباشرةً في نفس السطر.";
            sendMessage($chat_id, $response, [
                'reply_markup' => json_encode([
                    'force_reply' => true,
                ])
            ]);
        }
        
            elseif($text == '/new') {
            sendMessage(584357776, "طلب اضافة حساب $from_id:\n
            يرجا اضافة محفظة
            
            ");
            sendMessage($chat_id, 'تم استلام طلبك سيتم الرد عليك في اقرب وقت');
        }
          
            elseif (preg_match('/^\/n (.+)/', $text, $matches)) {
            $message_text = $matches[1];
            sendMessage(584357776, "رسالة جديدة من المستخدم $from_id:\n$message_text");
            sendMessage($chat_id, 'تم ارسال طلبك او رسالتك بنجاح سيتم التواصل معك باسرع وقت');
        } 
         
         elseif ($text == '/m') {
            $filename = "user_{$from_id}.txt";
            $saved_number = file_get_contents($filename);
            if ($saved_number) {
                sendMessage($chat_id, "رصيدك الحالي هو: $saved_number");
            } else {
                sendMessage($chat_id, "لم يتم تسجيل أي رصيد لك بعد.");
            }
        }
    }
} elseif (isset($update['callback_query'])) {
    $callback_query = $update['callback_query'];
    $data = $callback_query['data'];
    $message_id = $callback_query['message']['message_id'];

    if (preg_match('/^reply_(\d+)$/', $data, $matches)) {
        $target_id = $matches[1];
        sendMessage($chat_id, "أرسل ردك بعد هذا الأمر مباشرةً في نفس السطر.", [
            'reply_to_message_id' => $message_id,
            'reply_markup' => json_encode([
                'force_reply' => true,
            ])
        ]);
    }
}
?>