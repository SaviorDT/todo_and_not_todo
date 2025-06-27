<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DateTime;
use DateTimeZone;

use App\Http\Requests\Gemini\GeminiRequest;

use Gemini;
use Gemini\GeminiHelper;
use Gemini\Data\Content;
use Gemini\Enums\ModelVariation;
use Gemini\Enums\Role;


class GeminiController extends Controller
{
    /**
     * 從 LLM 回應中抽取 JSON 陣列並解碼
     *
     * @param string $response LLM 回應文字
     * @return array|null 成功就回傳解好的 PHP 陣列，失敗就回傳 null
     */
    private function extractJsonArray(string $response): ?array
    {
        if (preg_match('/\[[\s\S]*\]/', $response, $matches)) {
            $jsonString = $matches[0];

            // 嘗試解碼
            $data = json_decode($jsonString, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }
        }
        return null;
    }
    /**
     * 解析中文時間字串，並使用 Gemini API 生成事件與時間的配對
     *
     * @param string $time_string 使用者輸入的時間字串
     * @param Gemini\GeminiClient $client Gemini 客戶端
     * @param DateTime $currentTime 當前時間
     * @return string 解析後的事件與時間配對
     */
    private function parse_chinese_time($time_string, $client, $currentTime) {
        $timezone = new DateTimeZone('Asia/Taipei');
        $nextWed = new DateTime('next wednesday', $timezone);
        $nextWed = $nextWed->format('Y-m-d');
        $tomorrowDate = new DateTime('tomorrow', $timezone);
        $tomorrowDate = $tomorrowDate->format('Y-m-d');
        $today2pm = new DateTime('today 14:30', $timezone);
        $today2pm = $today2pm->format('Y-m-d H:i');
        $currentTime = $currentTime->format('Y-m-d H:i:s');
        
        // print($nextWed);
        $history = [
            Content::parse('你知道現在是什麼時候嗎?', Role::USER,),
            Content::parse(
                <<<TEXT
                現在的時間是$currentTime
                TEXT,
                Role::MODEL,
            ),
            Content::parse('下禮拜三是幾月幾號?', Role::USER),
            Content::parse(
                <<<TEXT
                下禮拜三是，$nextWed
                TEXT,
                Role::MODEL,
            ),
            Content::parse('可以幫我分析事件與時間嗎? 我可能會給多個事件或單一一個事件而已', Role::USER),
            Content::parse(
                <<<TEXT
                沒問題，我會把你的輸出變成事件與timestamp的配對，例如:下禮拜三考C++期末考，我就會轉換成，考C++期末考: $nextWed\，
                如果有多個事件也可以，例如:明天交線性代數作業，打大資盃12/18，我就會轉換成，交線性代數作業: $tomorrowDate, 打大資盃: 2024-12-18。
                TEXT,
                Role::MODEL,
            ),
            Content::parse("我有些事件會有補充的說明，可以幫我保留所有文字在一個事件就好了嗎? 像是「明天要出門，會很冷記得要穿外套」，那就幫我變成，要出門記得要穿外套: $tomorrowDate", Role::USER),
            Content::parse(
                <<<TEXT
                好的，我會轉換成: 要出門會很冷記得要穿外套: $tomorrowDate
                TEXT,
                Role::MODEL,
            ),
            Content::parse("如果有精確的時間，可以幫我也列出來嗎? 像是｢今天下午2點30分要跟同學聚餐」", Role::USER),
            Content::parse(
                <<<TEXT
                我會轉換成，要跟同學聚餐: $today2pm
                TEXT,
                Role::MODEL,
            ),
            Content::parse("如果事情沒有時間，請幫我說沒有特定時間?像是「把數學作業寫完 整理包包 12/28paper study」", Role::USER),
            Content::parse(
                <<<TEXT
                好，我只會輸出，「把數學作業寫完: 沒有特定時間, 整理包包: 沒有特定時間, paper study 2024-12-28」。
                TEXT,
                Role::MODEL,
            ),
            Content::parse("如果你沒有看到特定的時間或事件，因為可能是使用者的習慣，那就把那句話原封不動的說出來，並且說明沒有特定時間。像是有人輸入，「今朝有酒今朝醉」，我們不知道是什麼意思，那你應該怎麼輸出？", Role::USER),
            Content::parse(
                <<<TEXT
                今朝有酒今朝醉: 沒有特定時間
                TEXT,
                Role::MODEL,
            ),
        ];
        $chat = $client->generativeModel(model: 'gemini-2.0-flash')
                       ->startChat(history: $history);

        // print($time_string);
        $response = $chat->sendMessage($time_string);
        // print($response->text());
        return $response->text();
    }

    /**
     * 主程式
     * 處理使用者輸入的文字，並將其轉換為統一的 JSON 格式
     * @param GeminiRequest $request 使用者輸入的請求
     * @return \Illuminate\Http\JsonResponse 返回 JSON 格式的事件與時間配對
     * @throws \Exception 當 API 回應無效或格式錯誤時
     */
    public function textInput(GeminiRequest $request)
    {
        //* Fetch internal data
        $validated = $request->validated();
        $client = Gemini::client($validated['api_key']);
        $userinput = (string)$validated['userinput'];

        //* API data
        $currentRawTime = new DateTime();  

        //* revised data
        $reviseInput = $this->parse_chinese_time($userinput, $client, $currentRawTime);
        $currentRawTime = $currentRawTime->format('Y-m-d H:i:s');

        //* Prompt
            // 1. role prompt
            // 2. few shot
            // 3. user input

        $history = [
            Content::parse(
                <<<TEXT
                你現在的角色是嚴謹的格式化人員，會把指派的輸入轉換成正確且統一的格式，並且會分析一句話的架構，可以看出一句話分別有哪些事件。
                統一的json格式：
                [
                {"title": "主要事情內容", "start_date": null, "due_date": "結束日期", "description": "提醒事項或注意事項"}
                ]
                TEXT,
                Role::USER,
            ),
            Content::parse(
                <<<TEXT
                好的，我會依你給的規範輸出事件的統一json格式，並區隔主要事情與提醒事項。
                TEXT,
                Role::MODEL,
            ),

            Content::parse(
                "我想要在11/5這天去啊罵家吃飯，小心啊罵給我太多飯吃",
                Role::USER,
            ),
            Content::parse(
                <<<TEXT
                [{"title": "去啊罵家吃飯", "start_date": null, "due_date": "2024-11-05", "description": "小心啊罵給太多飯"}]
                TEXT,
                Role::MODEL,
            ),

            Content::parse(
                "11/8到11/9打大資盃",
                Role::USER,
            ),
            Content::parse(
                <<<TEXT
                [{"title": "打大資盃", "start_date": "2024-11-08", "due_date": "2024-11-09", "description": "小心啊罵給太多飯"}]
                TEXT,
                Role::MODEL,
            ),

            Content::parse(
                "蒙地卡羅: 沒有特定時間",
                Role::USER,
            ),
            Content::parse(
                <<<TEXT
                [{"title": "蒙地卡羅", "start_date": null, "due_date": null, "description": "沒有特定時間"}]
                TEXT,
                Role::MODEL,
            ),
        ];

        //* Send data
        $chat = $client->generativeModel(model: 'gemini-2.0-flash')
                       ->startChat(history: $history);

        $response = $chat->sendMessage($reviseInput);

        //* Process response
        $result = $response->text();
        
        $jsonDecoded = $this->extractJsonArray($result);

        if ($jsonDecoded === null) {
            return response()->json(['error' => 'Invalid JSON format from API response', 'raw' => $result], 400);
        }
        elseif ($result == "[]") {
            return response()->json(['error' => 'No todo be recognized', 'raw' => $result], 400);
        }
        
        return response()->json($jsonDecoded);

    }
}

?>
