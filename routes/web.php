<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('dashboard', [App\Http\Controllers\VBeta\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Project
Route::view('/project-list', 'v_beta.project-list')->name('project.list');
// Route::view('/project-create', 'v_beta.project-design')->name('project.create');
// Route::view('/project-edit/{projectId}', 'v_beta.project-design-edit')->name('project.edit');

// End Project

// Admin - IT

Route::view('/admin-it/type_of_project', 'v_beta.admin.type_of_project.index')->middleware(['auth'])->name('admin.it.type.of.project');

// Route pour afficher le formulaire de création
Route::view('/admin-it/project-types/create', 'v_beta.admin.type_of_project.form')->name('admin.it.project.types.create');

// Route pour afficher le formulaire d'édition
Route::view('/admin-it/project-types/{projectTypeId}/edit', 'v_beta.admin.type_of_project.form')->name('admin.it.project.types.edit');

// End Admin - IT




// Proposition de projet Start

// Route pour afficher le formulaire de création
Route::view('/creator-proposal/proposal-project/create', 'v_beta.proposal-project.form')->name('creator.proposal.project.create');

// Route pour afficher le formulaire d'édition
Route::view('/creator-proposal/proposal-project/{projectId}/edit', 'v_beta.proposal-project.form')->name('creator.proposal.project.edit');

// Route pour afficher les details
Route::get('/projects/{projectId}', [App\Http\Controllers\VBeta\ProjectShowController::class, 'index'])->name('project.show');

// Proposition de projet End

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
