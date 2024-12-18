<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GeminiAPI\Client;
use GeminiAPI\Resources\ModelName;
use GeminiAPI\Resources\Parts\TextPart;
use GeminiAPI\Resources\Content;
use GeminiAPI\Enums\Role;
use DateTime;

use App\Http\Requests\Gemini\CreateGeminiRequest;
use App\Http\Requests\Gemini\UpdateGeminiRequest;


class GeminiController extends Controller
{
    public function textInput(CreateGeminiRequest $request)
    {
        $validated = $request->validated();
        $client = new Client($validated['api_key']);

        function parse_chinese_time($time_string, $client, $today) {
            $nextWed = date('Y-m-d', strtotime('next wednesday'));
            $tomorrowDate = date('Y-m-d', strtotime('tomorrow'));
            
            // print($nextWed);
            $history = [
                Content::text('你知道今天是幾月幾日嗎?', Role::User),
                Content::text(
                    <<<TEXT
                    今天是$today
                    TEXT,
                    Role::Model,
                ),
                Content::text('下禮拜三是幾月幾號?', Role::User),
                Content::text(
                    <<<TEXT
                    下禮拜三是，$nextWed
                    TEXT,
                    Role::Model,
                ),
                Content::text('可以幫我分析事件與時間嗎? 我可能會給多個事件或單一一個事件而已', Role::User),
                Content::text(
                    <<<TEXT
                    沒問題，我會把你的輸出變成事件與timestamp的配對，例如:下禮拜三考C++期末考，我就會轉換成，考C++期末考: $nextWed\，
                    如果有多個事件也可以，例如:明天交線性代數作業，打大資盃12/18，我就會轉換成，交線性代數作業: $tomorrowDate, 打大資盃: 2024-12-18。
                    TEXT,
                    Role::Model,
                ),
                Content::text("我有些事件會有補充的說明，可以幫我保留所有文字在一個事件就好了嗎? 像是「明天要出門，會很冷記得要穿外套」，那就幫我變成，要出門記得要穿外套: $tomorrowDate", Role::User),
                Content::text(
                    <<<TEXT
                    好的，我會轉換成: 要出門會很冷記得要穿外套: $tomorrowDate
                    TEXT,
                    Role::Model,
                ),
            ];
            $chat = $client->generativeModel(ModelName::GEMINI_PRO)
            ->startChat()
            ->withHistory($history);

            // print($time_string);
            $response = $chat->sendMessage(new TextPart($time_string));
            return $response->text();
        }

        // $text = "隔天考期末考，記得要查資料，12/9參加競技";
        $today = new DateTime();
        $today = $today->format('Y-m-d');

        $chat = $client->generativeModel(ModelName::GEMINI_PRO)->startChat();

        $userinput = (string)$validated['userinput'];
                        
        $reviseInput = parse_chinese_time($userinput, $client, $today);

        // print($reviseInput);
        
        $response = $chat->sendMessage(
            new TextPart("
            你現在的角色是嚴謹的格式化人員，會把指派的輸入轉換成正確且統一的格式，並且會分析一句話的架構，可以看出一句話分別有哪些事件。
            統一的json格式：
[
  {\"title\": \"主要事情內容\", \"start_date\": null, \"due_date\": \"結束日期\", \"description\": \"提醒事項或注意事項\"}
]
範例1：
輸入：'我想要在11/5這天去啊罵家吃飯，小心啊罵給我太多飯吃'
輸出1：
[{\"title\": \"去啊罵家吃飯\", \"start_date\": null, \"due_date\": \"2024-11-05\", \"description\": \"小心啊罵給太多飯\"}]
範例2：
輸入：'11/8到11/9打大資盃'
輸出2：
[{\"title\": \"打大資盃\", \"start_date\": \"2024-11-08\", \"due_date\": \"2024-11-09\", \"description\": \"小心啊罵給太多飯\"}]


請再幫我盡量去區隔主要的事情，與提醒事項，依照你的觀點放在正確的欄位中。
我希望輸出只保留json

現在輸入是：
{$reviseInput}"),
        );


        // 直接解析生成的文字為 JSON
        $result = $response->text();

        

        $jsonDecoded = json_decode($result, true);

        // return $result;

        if ($jsonDecoded === null) {
            return response()->json(['error' => 'Invalid JSON format from API response', 'raw' => $result], 400);
        }

        function save_to_file($userinput, $response_json, $file_path = '/var/www/html/app/Http/Controllers/api/geminiResponse2.json') {
            // 準備要儲存的資料
            $log_entry = [
                'timestamp' => date('Y-m-d H:i:s'),
                'user_input' => $userinput,
                'response' => is_array($response_json) ? $response_json : json_decode($response_json, true),
            ];
        
            // 確保 $log_entry['response'] 是陣列，否則會轉換失敗
            if (json_last_error() !== JSON_ERROR_NONE) {
                // 如果 $response_json 不是有效的 JSON，記錄錯誤訊息
                $log_entry['response'] = [
                    'error' => 'Invalid response JSON format',
                    'original_response' => $response_json,
                ];
            }
        
            // 檢查檔案是否存在
            if (!file_exists($file_path)) {
                // 如果檔案不存在，初始化為空的 JSON 陣列
                file_put_contents($file_path, json_encode([]));
            }
        
            // 讀取現有內容
            $existing_logs = json_decode(file_get_contents($file_path), true);
        
            // 如果解碼失敗，初始化為空陣列
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($existing_logs)) {
                $existing_logs = [];
            }
        
            // 添加新的日誌
            $existing_logs[] = $log_entry;
        
            // 寫回檔案，並加上 JSON_PRETTY_PRINT 格式化
            file_put_contents($file_path, json_encode($existing_logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
        }

        save_to_file($userinput, $jsonDecoded);

        return response()->json($jsonDecoded);


        
        
    }
}

?>
