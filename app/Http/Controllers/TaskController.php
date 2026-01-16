<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    //összes rekord lekérdezése
    public function index()
    {
       return TaskResource::collection(Task::all());
    }

    //új rekord létrehozása
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable'
        ]);

        $task = Task::create($validateData);
        return new TaskResource($task);

    }

    //egy adott rekord megjelenítése
    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    //rekord frissítése
    public function update(Request $request, Task $task)
    {
        $validateData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable'
        ]);

        $task->update($validateData);
        return new TaskResource($task);
    }


    //rekord törlése
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json([
            'message' => 'Task sikeresen törölve'
        ], 200);
    }
}
