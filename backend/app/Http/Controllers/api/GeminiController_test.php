<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DateTime;

use App\Http\Requests\Gemini\GeminiRequest;

use Gemini\Enums\ModelVariation;
use Gemini\GeminiHelper;
use Gemini;


class GeminiController extends Controller
{
    public function textInput(GeminiRequest $request)
    {
        $validated = $request->validated();
        $client = Gemini::client($validated['api_key']);

        // $text = "隔天考期末考，記得要查資料，12/9參加競技";
        $currentTime = new DateTime();
        $currentTime = $currentTime->format('Y-m-d H:i:s');

        $userinput = (string)$validated['userinput'];
                        
        $result = $client->generativeModel(model: 'gemini-2.0-flash')->generateContent('hello');

        $result = $result->text(); // Hello! How can I assist you today?

        $jsonDecoded = json_decode($result, true);
        

        // return $result;
        if ($jsonDecoded === null) {
            return response()->json(['error' => 'Invalid JSON format from API response', 'raw' => $result], 400);
        }
        elseif ($result == "[]") {
            return response()->json(['error' => 'No todo be recognized', 'raw' => $result], 400);
        }

        // save_to_file($userinput, $jsonDecoded);

        return response()->json($jsonDecoded);

    }
}

?>
