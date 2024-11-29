<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Todo\CreateTodoRequest;
use App\Http\Requests\Todo\UpdateTodoRequest;

use App\Models\Todo;

class TodoController extends Controller
{
    public function create(CreateTodoRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->user()['id'];

        $record = Todo::create($validated)->fresh();

        return $record;
    }

    public function read(Request $request)
    {
        $record = Todo::where('user_id', auth()->user()['id']);

        return response($record->get()->toJson(JSON_PRETTY_PRINT), 200);
    }

    public function update(UpdateTodoRequest $request)
    {
        $validated = $request->validated();

        $record = Todo::find($validated['id']);

        if ($record['user_id'] != auth()->user()['id']) {
            return response()->json(['message' => "You can not change other user's todo"], 403);
        }

        $record->update($validated);
 
        return $record->fresh();
    }
}
