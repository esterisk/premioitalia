<?php

use App\Filament\Resources\Albos\Pages\CreateAlbo;
use App\Filament\Resources\Albos\Pages\EditAlbo;
use App\Filament\Resources\Albos\Pages\ListAlbos;
use App\Models\Albo;
use App\Models\Categoria;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

beforeEach(function () {
    if (! Schema::hasTable('annate')) {
        Schema::create('annate', function ($table) {
            $table->id();
            $table->integer('anno');
            $table->timestamps();
        });
    }
    if (! Schema::hasTable('categorie')) {
        Schema::create('categorie', function ($table) {
            $table->id();
            $table->string('nome');
            $table->timestamps();
        });
    }
    if (! Schema::hasTable('albo')) {
        Schema::create('albo', function ($table) {
            $table->id();
            $table->integer('categoria_id');
            $table->integer('anno');
            $table->integer('finalista')->default(1);
            $table->string('descrizione');
            $table->string('note')->nullable();
            $table->text('campi')->nullable();
            $table->integer('posizione')->nullable();
            $table->timestamps();
        });
    }

    $this->actingAs(User::factory()->create(['admin' => 1]));
    $this->categoria = Categoria::forceCreate(['nome' => 'Romanzo']);
});

test('lists albo records', function () {
    $voce = Albo::create([
        'categoria_id' => $this->categoria->id,
        'anno' => 2025,
        'finalista' => 2,
        'descrizione' => 'Autore - Titolo',
        'campi' => json_encode(['autore' => 'Autore']),
        'posizione' => 1,
    ]);

    Livewire::test(ListAlbos::class)->assertCanSeeTableRecords([$voce]);
});

test('creates a record storing campi as json', function () {
    Livewire::test(CreateAlbo::class)
        ->fillForm([
            'categoria_id' => $this->categoria->id,
            'anno' => 2024,
            'finalista' => 2,
            'posizione' => 1,
            'descrizione' => 'Autore - Titolo',
            'campi' => ['autore' => 'Autore', 'titolo' => 'Titolo'],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(json_decode(Albo::first()->campi, true))->toBe(['autore' => 'Autore', 'titolo' => 'Titolo']);
});

test('edits a record round-tripping campi', function () {
    $voce = Albo::create([
        'categoria_id' => $this->categoria->id,
        'anno' => 2025,
        'finalista' => 1,
        'descrizione' => 'Vecchia',
        'campi' => json_encode(['autore' => 'Autore']),
    ]);

    Livewire::test(EditAlbo::class, ['record' => $voce->getKey()])
        ->assertFormSet(['campi' => ['autore' => 'Autore']])
        ->fillForm(['descrizione' => 'Nuova'])
        ->call('save')
        ->assertHasNoFormErrors();

    $voce->refresh();
    expect($voce->descrizione)->toBe('Nuova')
        ->and(json_decode($voce->campi, true))->toBe(['autore' => 'Autore']);
});
