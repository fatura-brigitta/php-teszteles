<?php

use App\Http\Controllers\TaskController;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

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
