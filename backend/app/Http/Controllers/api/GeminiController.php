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

use App\Http\Requests\Gemini\GeminiRequest;


class GeminiController extends Controller
{
    private function parse_chinese_time($time_string, $client, $currentTime) {
        $nextWed = date('Y-m-d', strtotime('next wednesday'));
        $tomorrowDate = date('Y-m-d', strtotime('tomorrow'));
        $today2pm = new DateTime('today 14:30');
        $today2pm = $today2pm->format('Y-m-d H:i');
        
        // print($nextWed);
        $history = [
            Content::text('你知道現在是什麼時候嗎?', Role::User),
            Content::text(
                <<<TEXT
                現在的時間是$currentTime
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
            Content::text("如果有精確的時間，可以幫我也列出來嗎? 像是｢今天下午2點30分要跟同學聚餐」", Role::User),
            Content::text(
                <<<TEXT
                我會轉換成，要跟同學聚餐: $today2pm
                TEXT,
                Role::Model,
            ),
            Content::text("如果事情沒有時間，請幫我說沒有特定時間?像是「把數學作業寫完 整理包包 12/28paper study」", Role::User),
            Content::text(
                <<<TEXT
                好，我只會輸出，「把數學作業寫完: 沒有特定時間, 整理包包: 沒有特定時間, paper study 2024-12-28」。
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

    public function textInput(GeminiRequest $request)
    {
        $validated = $request->validated();
        $client = new Client($validated['api_key']);

        // $text = "隔天考期末考，記得要查資料，12/9參加競技";
        $currentTime = new DateTime();
        $currentTime = $currentTime->format('Y-m-d H:i:s');

        $chat = $client->generativeModel(ModelName::GEMINI_PRO)->startChat();

        $userinput = (string)$validated['userinput'];
                        
        $reviseInput = $this->parse_chinese_time($userinput, $client, $currentTime);

        // return $reviseInput;
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


如果沒有特別說時間，把時間都留白即可
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

        // save_to_file($userinput, $jsonDecoded);

        return response()->json($jsonDecoded);

    }
}

?>
