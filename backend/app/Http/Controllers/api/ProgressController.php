<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Progress\CreateProgressRequest;
use App\Http\Requests\Progress\UpdateProgressRequest;

use App\Models\Progress;

class ProgressController extends Controller
{
    public function create(CreateProgressRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->user()['id'];

        $record = Progress::create($validated)->fresh();

        return $record;
    }

    public function read(Request $request)
    {
        $record = Progress::where('user_id', auth()->user()['id']);

        return response($record->get()->toJson(JSON_PRETTY_PRINT), 200);
    }

    public function update(UpdateProgressRequest $request)
    {
        $validated = $request->validated();

        $record = Progress::find($validated['id']);

        if ($record['user_id'] != auth()->user()['id']) {
            return response()->json(['message' => "You can not change other user's todo"], 403);
        }

        $record->update($validated);
 
        return $record->fresh();
    }
}
