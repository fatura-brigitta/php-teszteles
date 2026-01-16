<?php

use App\Http\Controllers\TaskController;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


//SHOW  – GET /api/tasks/{id}


it('létezik a show metódus a TaskControllerben', function () {
    expect(method_exists(TaskController::class, 'show'))->toBeTrue();
});

it('a show végpont létező task esetén 200-as státusszal tér vissza', function () {
    $task = Task::create([
        'title' => 'Teszt task',
        'description' => 'Teszt leírás',
    ]);

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(200);
});

it('a show végpont a megfelelő task adatait adja vissza', function () {
    $task = Task::create([
        'title' => 'Egyedi task',
        'description' => 'Egyedi leírás',
    ]);

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertJsonPath('data.id', $task->id)
             ->assertJsonPath('data.title', 'Egyedi task');
});

it('a show végpont nem létező task esetén 404-es hibát ad', function () {
    $response = $this->getJson('/api/tasks/9999');

    $response->assertStatus(404);
});


//UPDATE – PATCH /api/tasks/{id}

it('létezik az update metódus a TaskControllerben', function () {
    expect(method_exists(TaskController::class, 'update'))->toBeTrue();
});

it('az update végpont érvényes adatokkal 200-as státusszal frissít', function () {
    $task = Task::create([
        'title' => 'Régi cím',
        'description' => 'Régi leírás',
    ]);

    $response = $this->patchJson("/api/tasks/{$task->id}", [
        'title' => 'Új cím',
    ]);

    $response->assertStatus(200)
             ->assertJsonPath('data.title', 'Új cím');

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Új cím',
    ]);
});

it('az update végpont nem létező task esetén 404-es hibát ad', function () {
    $response = $this->patchJson('/api/tasks/9999', [
        'title' => 'Nem létező',
    ]);

    $response->assertStatus(404);
});

it('az update végpont érvénytelen adatok esetén 422-es hibát ad', function () {
    $task = Task::create([
        'title' => 'Eredeti cím',
        'description' => 'Leírás',
    ]);

    $response = $this->patchJson("/api/tasks/{$task->id}", [
        'title' => '',
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['title']);
});


//DESTROY – DELETE /api/tasks/{id}


it('létezik a destroy metódus a TaskControllerben', function () {
    expect(method_exists(TaskController::class, 'destroy'))->toBeTrue();
});

it('a destroy végpont 200-as státusszal törli a taskot', function () {
    $task = Task::create([
        'title' => 'Törlendő task',
        'description' => 'Leírás',
    ]);

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(200);

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});

it('a destroy végpont nem létező task esetén 404-es hibát ad', function () {
    $response = $this->deleteJson('/api/tasks/9999');

    $response->assertStatus(404);
});

it('a destroy végpont JSON válasza tartalmaz visszajelző üzenetet', function () {
    $task = Task::create([
        'title' => 'Üzenet teszt',
        'description' => 'Leírás',
    ]);

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertJsonStructure([
        'message'
    ]);
});



//index metódus tesztjei

it('létezik az index metódus a TaskControllerben', function () {
    expect(method_exists(TaskController::class, 'index'))->toBeTrue();
});

it('az index végpont meghívható és 200-as státuszt ad', function () {
    $response = $this->getJson('/api/tasks');

    $response->assertStatus(200);
});

it('az index végpont visszaadja az összes taskot', function () {
    Task::create([
        'title' => 'Teszt feladat 1',
        'description' => 'Teszt leírás 1',
        'created_at' => '2026-01-09T07:44:58.000000Z',
        'updated_at' => '2026-01-09T07:44:58.000000Z'
    ]);

    Task::create([
        'title' => 'Teszt feladat 2',
        'description' => 'Teszt leírás 2',
        'created_at' => '2026-01-09T07:44:58.000000Z',
        'updated_at' => '2026-01-09T07:44:58.000000Z'
    ]);

    $response = $this->getJson('/api/tasks');

    $response->assertStatus(200)
             ->assertJsonCount(2, 'data');
});

it('az index végpont helyes JSON struktúrát ad vissza', function () {
    Task::create([
        'title' => 'Teszt feladat',
        'description' => 'Teszt leírás',
        'created_at' => '2026-01-09T07:44:58.000000Z',
        'updated_at' => '2026-01-09T07:44:58.000000Z'
    ]);

    $response = $this->getJson('/api/tasks');

    $response->assertJsonStructure([
        'data' => [
            [
                'id',
                'title',
                'description',
                'created_at',
                'updated_at',
            ]
        ]
    ]);
});

/*
STORE – POST /api/tasks
*/

it('létezik a store metódus a TaskControllerben', function () {
    expect(method_exists(TaskController::class, 'store'))->toBeTrue();
});

it('a store végpont érvényes adatokkal 201-es státusszal hoz létre taskot', function () {
    $payload = [
        'title' => 'Új feladat',
        'description' => 'Feladat leírás'
    ];

    $response = $this->postJson('/api/tasks', $payload);

    $response->assertStatus(201)
             ->assertJsonPath('data.title', 'Új feladat')
             ->assertJsonPath('data.description', 'Feladat leírás');

    $this->assertDatabaseHas('tasks', [
        'title' => 'Új feladat',
        'description' => 'Feladat leírás',
    ]);
});

it('a store végpont title nélkül 422-es validációs hibát ad', function () {
    $response = $this->postJson('/api/tasks', [
        'description' => 'Nincs cím'
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['title']);
});

it('a store végpont túl hosszú title esetén hibát ad', function () {
    $response = $this->postJson('/api/tasks', [
        'title' => str_repeat('a', 256),
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['title']);
});

it('a store végpont JSON válasza tartalmazza az összes elvárt mezőt', function () {
    $response = $this->postJson('/api/tasks', [
        'title' => 'JSON teszt',
        'description' => 'Leírás'
    ]);

    $response->assertJsonStructure([
        'data' => [
            'id',
            'title',
            'description',
            'created_at',
            'updated_at',
        ]
    ]);
});


