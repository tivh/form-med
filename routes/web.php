<?php

use App\Http\Controllers\Admin\ComplianceDocumentController as AdminComplianceDocumentController;
use App\Http\Controllers\Admin\FormSubmissionController as AdminFormSubmissionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ComplianceDocumentController;
use App\Http\Controllers\PublicFormController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicFormController::class, 'listForms'])->name('forms.list');
Route::get('/forms/{form}', [PublicFormController::class, 'show'])->name('forms.show');
Route::post('/forms/{form}/submit', [PublicFormController::class, 'submit'])
    ->middleware('throttle:10,1')
    ->name('forms.submit');
Route::get('/forms/{form}/sucesso', [PublicFormController::class, 'success'])->name('forms.success');

// Legacy aliases kept for existing bookmarks until the new form URLs are fully adopted
Route::get('/form-med', [PublicFormController::class, 'show'])
    ->name('form.show')
    ->defaults('form', 'form-med');
Route::post('/form-med/submit', [PublicFormController::class, 'submit'])
    ->middleware('throttle:10,1')
    ->name('form.submit')
    ->defaults('form', 'form-med');
Route::get('/form-med/sucesso', [PublicFormController::class, 'success'])
    ->name('form.success')
    ->defaults('form', 'form-med');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/documentos-comp', [ComplianceDocumentController::class, 'index'])->name('compliance.index');
Route::get('/documentos-comp/{complianceDocument}/download', [ComplianceDocumentController::class, 'download'])->name('compliance.download');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('submissions', [AdminFormSubmissionController::class, 'index'])->name('submissions.index');
    Route::get('submissions/export', [AdminFormSubmissionController::class, 'export'])->name('submissions.export');
    Route::get('submissions/{submission}', [AdminFormSubmissionController::class, 'show'])->name('submissions.show');
    Route::delete('submissions/{submission}', [AdminFormSubmissionController::class, 'destroy'])->name('submissions.destroy');
    Route::get('submissions/{submission}/download', [AdminFormSubmissionController::class, 'download'])->name('submissions.download');

    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');

    Route::get('compliance', [AdminComplianceDocumentController::class, 'index'])->name('compliance.index');
    Route::get('compliance/create', [AdminComplianceDocumentController::class, 'create'])->name('compliance.create');
    Route::post('compliance', [AdminComplianceDocumentController::class, 'store'])->name('compliance.store');
    Route::get('compliance/{complianceDocument}/edit', [AdminComplianceDocumentController::class, 'edit'])->name('compliance.edit');
    Route::put('compliance/{complianceDocument}', [AdminComplianceDocumentController::class, 'update'])->name('compliance.update');
    Route::delete('compliance/{complianceDocument}', [AdminComplianceDocumentController::class, 'destroy'])->name('compliance.destroy');
    Route::get('compliance/{complianceDocument}/download', [AdminComplianceDocumentController::class, 'download'])->name('compliance.download');
});
