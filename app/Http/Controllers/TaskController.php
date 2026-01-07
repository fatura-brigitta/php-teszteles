<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       return TaskResource::collection(Task::all());

        // $tasks = Task::all();
        // return new TaskResource($tasks);

          /**
         * TaskResource::collection($lista) - Ez egy speciális "gyári" (static) metódus
         * - Veszi a listát (a Task::all() által visszaadott Collection-t).
         * - Végigiterál a lista minden egyes elemén.
         * - A lista minden egyes Task modelljére automatikusan meghívja a new TaskResource($task)-ot.
         * - Az így kapott egyedi resource-okat összegyűjti egy speciális "Resource Collection" objektumba, ami aztán JSON tömbként ([ ... ]) jelenik meg.
         *
         * Analógia: Itt odaadod a doboz almát egy segédnek (::collection), aki kiveszi az almákat egyenként, mindegyiket meghámozza (ráhúzza a new TaskResource()-t), és az így kapott hámozott almákat egy új tálcára teszi.
         */

        // Hibás
        // $task = Task::all();
        // return new TaskResource($task);
        /*
            Mit csinál a Task::all()? Visszaad egy Collection-t (egy listát), ami tele van Task
            modellekkel.
            Mit vár a new TaskResource($data)? A konstruktora egy egyetlen $data elemet vár (egy Task modellt). A toArray() metódusodban valószínűleg ilyenek vannak: $this->id, $this->name.
            Mi a hiba? Te a listát magát (a Collection objektumot) adod át a konstruktornak. Amikor a resource megpróbálja elérni, hogy $this->id, akkor a listán keresi az id tulajdonságot, nem pedig a benne lévő elemeken. Ez hibát okoz, mert a listának magának nincs id-ja vagy name-je.

            Analógia: Ez olyan, mintha egy egész doboz almát próbálnál meg meghámozni egyetlen almahámozóval, ahelyett, hogy az almákat egyenként vennéd ki a dobozból.
        */
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable'
        ]);

        $task = Task::create($validateData);
        return new TaskResource($task);

    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validateData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable'
        ]);

        $task->update($validateData);
        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {

        $task->delete();
        return response()->noContent();
    }
}
