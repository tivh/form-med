<?php

use App\Http\Controllers\Admin\ComplianceDocumentController as AdminComplianceDocumentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FormSubmissionController as AdminFormSubmissionController;
use App\Http\Controllers\Admin\GlpiFeedController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SupportAdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplianceDocumentController;
use App\Http\Controllers\PublicFormController;
use App\Http\Controllers\SupportRequestController;
use App\Http\Controllers\TaxRegimeFormController;
use App\Http\Controllers\TaxRegimeSubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicFormController::class, 'listForms'])->name('forms.list');
Route::get('/forms/{form}', [PublicFormController::class, 'show'])->name('forms.show');
Route::post('/forms/{form}/submit', [PublicFormController::class, 'submit'])
    ->middleware('throttle:10,1')
    ->name('forms.submit');
Route::get('/forms/{form}/sucesso', [PublicFormController::class, 'success'])->name('forms.success');

Route::get('/fornecedor-rh', [PublicFormController::class, 'show'])
    ->name('rh-form.show')
    ->defaults('form', 'fornecedor-rh');
Route::post('/fornecedor-rh/submit', [PublicFormController::class, 'submit'])
    ->middleware('throttle:10,1')
    ->name('rh-form.submit')
    ->defaults('form', 'fornecedor-rh');
Route::get('/fornecedor-rh/sucesso', [PublicFormController::class, 'success'])
    ->name('rh-form.success')
    ->defaults('form', 'fornecedor-rh');

Route::get('/regime-tributario', [TaxRegimeFormController::class, 'show'])->name('tax-regime.show');
Route::post('/regime-tributario/submit', [TaxRegimeFormController::class, 'submit'])
    ->middleware('throttle:10,1')
    ->name('tax-regime.submit');
Route::get('/regime-tributario/sucesso', [TaxRegimeFormController::class, 'success'])->name('tax-regime.success');

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

Route::get('/suporte', [SupportRequestController::class, 'create'])->name('support.create');
Route::post('/suporte', [SupportRequestController::class, 'store'])->name('support.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/minhas-solicitacoes', [SupportRequestController::class, 'index'])->name('support.index');
    Route::get('/minhas-solicitacoes/{supportRequest}', [SupportRequestController::class, 'show'])->name('support.show');
});

Route::get('/documentos-comp', [ComplianceDocumentController::class, 'index'])->name('compliance.index');
Route::get('/documentos-comp/{complianceDocument}/download', [ComplianceDocumentController::class, 'download'])->name('compliance.download');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // Hub: super admin vê os cards; usuário escopado é redirecionado direto pra sua área
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Área Compliance (form-med)
    Route::middleware('form.scope:form-med')->group(function () {
        Route::get('submissions', [AdminFormSubmissionController::class, 'index'])->name('submissions.index');
        Route::get('submissions/export', [AdminFormSubmissionController::class, 'export'])->name('submissions.export');
        Route::get('submissions/{submission}', [AdminFormSubmissionController::class, 'show'])->name('submissions.show');
        Route::get('submissions/{submission}/print', [AdminFormSubmissionController::class, 'print'])->name('submissions.print');
        Route::post('submissions/{submission}/toggle-verified', [AdminFormSubmissionController::class, 'toggleVerified'])->name('submissions.toggle-verified');
        Route::delete('submissions/{submission}', [AdminFormSubmissionController::class, 'destroy'])->name('submissions.destroy');
        Route::get('submissions/{submission}/download', [AdminFormSubmissionController::class, 'download'])->name('submissions.download');

        Route::get('compliance', [AdminComplianceDocumentController::class, 'index'])->name('compliance.index');
        Route::get('compliance/create', [AdminComplianceDocumentController::class, 'create'])->name('compliance.create');
        Route::post('compliance', [AdminComplianceDocumentController::class, 'store'])->name('compliance.store');
        Route::get('compliance/{complianceDocument}/edit', [AdminComplianceDocumentController::class, 'edit'])->name('compliance.edit');
        Route::put('compliance/{complianceDocument}', [AdminComplianceDocumentController::class, 'update'])->name('compliance.update');
        Route::delete('compliance/{complianceDocument}', [AdminComplianceDocumentController::class, 'destroy'])->name('compliance.destroy');
        Route::get('compliance/{complianceDocument}/download', [AdminComplianceDocumentController::class, 'download'])->name('compliance.download');
    });

    // Área Financeiro (regime-tributario)
    Route::middleware('form.scope:regime-tributario')->group(function () {
        Route::get('tax-regime', [TaxRegimeSubmissionController::class, 'index'])->name('tax-regime.index');
        Route::get('tax-regime/export', [TaxRegimeSubmissionController::class, 'export'])->name('tax-regime.export');
        Route::get('tax-regime/{taxRegimeSubmission}', [TaxRegimeSubmissionController::class, 'show'])->name('tax-regime.show');
        Route::post('tax-regime/{taxRegimeSubmission}/toggle-verified', [TaxRegimeSubmissionController::class, 'toggleVerified'])->name('tax-regime.toggle-verified');
        Route::delete('tax-regime/{taxRegimeSubmission}', [TaxRegimeSubmissionController::class, 'destroy'])->name('tax-regime.destroy');
    });
    // routes/web.php — dentro do grupo Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(...)

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('glpi-feed', [GlpiFeedController::class, 'index'])->name('glpi-feed.index');
    Route::get('glpi-feed/data', [GlpiFeedController::class, 'data'])->name('glpi-feed.data');

    Route::get('support', [SupportAdminController::class, 'feed'])->name('support.feed');
    Route::get('support/{supportRequest}', [SupportAdminController::class, 'show'])->name('support.show');
    Route::post('support/{supportRequest}/reply', [SupportAdminController::class, 'reply'])->name('support.reply');
    Route::patch('support/{supportRequest}/close', [SupportAdminController::class, 'close'])->name('support.close');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');   // NOVO
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');    // NOVO
});