<?php

use App\Http\Controllers\LogsController;
use App\Domain\Channel;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $channel = Channel::query()->orderByDesc('num_members')->first();

    abort_if($channel === null, 404, 'No Slack channels have been imported yet.');

    return redirect()->route('logs.channel', ['chan' => $channel->name]);
});

Route::get('/{chan}/search/{query?}', [LogsController::class, 'search'])->name('logs.search');
Route::get('/{chan}/infinite/{direction}/{id}', [LogsController::class, 'infinite'])
    ->where('direction', 'up|down')
    ->name('logs.infinite');
Route::get('/{chan}/{date}/{time}', [LogsController::class, 'datetime'])
    ->where(['date' => '\\d{4}-\\d{2}-\\d{2}', 'time' => '\\d{2}:\\d{2}:\\d{2}'])
    ->name('logs.datetime');
Route::get('/{chan}/{date?}', [LogsController::class, 'channel'])
    ->where('date', '\\d{4}-\\d{2}-\\d{2}')
    ->name('logs.channel');
