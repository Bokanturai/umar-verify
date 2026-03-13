<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SupportController;

// Utility Controllers
use App\Http\Controllers\Action\AirtimeController;
use App\Http\Controllers\Action\DataController;
use App\Http\Controllers\Action\SmeDataController;
use App\Http\Controllers\Action\EducationalController;
use App\Http\Controllers\Action\ElectricityController;
use App\Http\Controllers\Action\CableController;

// Verification Controllers
use App\Http\Controllers\NINverificationController;
use App\Http\Controllers\NINDemoVerificationController;
use App\Http\Controllers\NINPhoneVerificationController;
use App\Http\Controllers\BvnverificationController;

// Agency & Specialized Service Controllers
use App\Http\Controllers\Agency\BvnServicesController;
use App\Http\Controllers\Agency\BvnModificationController;
use App\Http\Controllers\Agency\ManualSearchController;
use App\Http\Controllers\Agency\TinRegistrationController;
use App\Http\Controllers\Agency\NinValidationController;
use App\Http\Controllers\Agency\NinModificationController;
use App\Http\Controllers\Agency\IpeController;
use App\Http\Controllers\Agency\BvncrmController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

Route::post('/palmpay/webhook', [PaymentWebhookController::class, 'handleWebhook'])
    ->middleware('throttle:60,1');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
        Route::post('/pin', [ProfileController::class, 'updatePin'])->name('profile.pin');
        Route::post('/update-required', [ProfileController::class, 'updateRequired'])->name('profile.updateRequired');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Wallet & Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
    Route::get('/thankyou', function () { return view('thankyou'); })->name('thankyou');
    
    Route::prefix('wallet')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('wallet');
        Route::post('/create-virtual-account', [WalletController::class, 'createWallet'])->name('virtual.account.create');
        Route::post('/claim-bonus', [WalletController::class, 'claimBonus'])->name('wallet.claimBonus');
    });

    // Support
    Route::get('/support', [SupportController::class, 'index'])->name('support');

    /*
    |--------------------------------------------------------------------------
    | Utility & Bill Payment Services
    |--------------------------------------------------------------------------
    */
    
    // Airtime
    Route::prefix('airtime')->group(function () {
        Route::get('/', [AirtimeController::class, 'airtime'])->name('airtime');
        Route::post('/buy', [AirtimeController::class, 'buyAirtime'])->name('buyairtime');
    });

    // Standard Data
    Route::prefix('data')->group(function () {
        Route::get('/', [DataController::class, 'data'])->name('buy-data');
        Route::post('/buy', [DataController::class, 'buydata'])->name('buydata');
        Route::get('/fetch-bundles', [DataController::class, 'fetchBundles'])->name('fetch.bundles');
        Route::get('/fetch-price', [DataController::class, 'fetchBundlePrice'])->name('fetch.bundle.price');
        Route::post('/verify-pin', [DataController::class, 'verifyPin'])->name('verify.pin');
    });

    // SME Data
    Route::prefix('sme-data')->group(function () {
        Route::get('/', [SmeDataController::class, 'index'])->name('buy-sme-data');
        Route::post('/buy', [SmeDataController::class, 'buySMEdata'])->name('buy-sme-data.submit');
        Route::get('/fetch-type', [SmeDataController::class, 'fetchDataType'])->name('sme.fetch.type');
        Route::get('/fetch-plan', [SmeDataController::class, 'fetchDataPlan'])->name('sme.fetch.plan');
        Route::get('/fetch-price', [SmeDataController::class, 'fetchSmeBundlePrice'])->name('sme.fetch.price');
    });

    // Education
    Route::prefix('education')->group(function () {
        Route::get('/', [EducationalController::class, 'pin'])->name("education");
        Route::post('/buy-pin', [EducationalController::class, 'buypin'])->name('buypin');
        Route::get('/receipt/{transaction}', [EducationalController::class, 'receipt'])->name('education.receipt');
        Route::get('/get-variation', [EducationalController::class, 'getVariation'])->name('get-variation');
        
        // JAMB
        Route::get('/jamb', [EducationalController::class, 'jamb'])->name('jamb');
        Route::post('/verify-jamb', [EducationalController::class, 'verifyJamb'])->name('verify.jamb');
        Route::post('/buy-jamb', [EducationalController::class, 'buyJamb'])->name('buyjamb');
    });

    // Electricity
    Route::prefix('electricity')->group(function () {
        Route::get('/', [ElectricityController::class, 'index'])->name('electricity');
        Route::post('/verify', [ElectricityController::class, 'verifyMeter'])->name('verify.electricity');
        Route::post('/buy', [ElectricityController::class, 'purchase'])->name('buy.electricity');
    });

    // Cable TV
    Route::prefix('cable')->group(function () {
        Route::get('/', [CableController::class, 'index'])->name('cable');
        Route::get('/variations', [CableController::class, 'getVariations'])->name('cable.variations');
        Route::post('/verify', [CableController::class, 'verifyIuc'])->name('verify.cable');
        Route::post('/buy', [CableController::class, 'purchase'])->name('buy.cable');
    });

    /*
    |--------------------------------------------------------------------------
    | Verification Services
    |--------------------------------------------------------------------------
    */
    
    // NIN Verification
    Route::prefix('nin-verification')->group(function () {
        Route::get('/', [NINverificationController::class, 'index'])->name('nin.verification.index');
        Route::post('/', [NINverificationController::class, 'store'])->name('nin.verification.store');
        Route::post('/{id}/status', [NINverificationController::class, 'updateStatus'])->name('nin.verification.status');
        Route::get('/standardSlip/{id}', [NINverificationController::class, 'standardSlip'])->name('standardSlip');
        Route::get('/premiumSlip/{id}', [NINverificationController::class, 'premiumSlip'])->name('premiumSlip');
        Route::get('/vninSlip/{id}', [NINverificationController::class, 'vninSlip'])->name('vninSlip');
    });

    // NIN Demographic Verification
    Route::prefix('nin-demo-verification')->group(function () {
        Route::get('/', [NINDemoVerificationController::class, 'index'])->name('nin.demo.index');
        Route::post('/', [NINDemoVerificationController::class, 'store'])->name('nin.demo.store');
        Route::get('/freeSlip/{id}', [NINDemoVerificationController::class, 'freeSlip'])->name('nin.demo.freeSlip');
        Route::get('/regularSlip/{id}', [NINDemoVerificationController::class, 'regularSlip'])->name('nin.demo.regularSlip');
        Route::get('/standardSlip/{id}', [NINDemoVerificationController::class, 'standardSlip'])->name('nin.demo.standardSlip');
        Route::get('/premiumSlip/{id}', [NINDemoVerificationController::class, 'premiumSlip'])->name('nin.demo.premiumSlip');
    });

    // NIN Phone Verification
    Route::prefix('nin-phone-verification')->group(function () {
        Route::get('/', [NINPhoneVerificationController::class, 'index'])->name('nin.phone.index');
        Route::post('/', [NINPhoneVerificationController::class, 'store'])->name('nin.phone.store');
        Route::get('/freeSlip/{id}', [NINPhoneVerificationController::class, 'freeSlip'])->name('nin.phone.freeSlip');
        Route::get('/regularSlip/{id}', [NINPhoneVerificationController::class, 'regularSlip'])->name('nin.phone.regularSlip');
        Route::get('/standardSlip/{id}', [NINPhoneVerificationController::class, 'standardSlip'])->name('nin.phone.standardSlip');
        Route::get('/premiumSlip/{id}', [NINPhoneVerificationController::class, 'premiumSlip'])->name('nin.phone.premiumSlip');
    });

    // BVN Verification
    Route::prefix('bvn-verification')->group(function () {
        Route::get('/', [BvnverificationController::class, 'index'])->name('bvn.verification.index');
        Route::post('/', [BvnverificationController::class, 'store'])->name('bvn.verification.store');
        Route::get('/standardBVN/{id}', [BvnverificationController::class, 'standardBVN'])->name("standardBVN");
        Route::get('/premiumBVN/{id}', [BvnverificationController::class, 'premiumBVN'])->name("premiumBVN");
        Route::get('/plasticBVN/{id}', [BvnverificationController::class, 'plasticBVN'])->name("plasticBVN");
    });

    // TIN Registration
    Route::prefix('tin-reg')->group(function () {
        Route::get('/', [TinRegistrationController::class, 'index'])->name('tin.index');
        Route::post('/validate', [TinRegistrationController::class, 'validateTin'])->name('tin.validate');
        Route::post('/download', [TinRegistrationController::class, 'downloadSlip'])->name('tin.download');
    });

    // NIN Modification
    Route::prefix('nin-modification')->group(function () {
        Route::get('/', [NinModificationController::class, 'index'])->name('nin-modification');
        Route::post('/', [NinModificationController::class, 'store'])->name('nin-modification.store');
        Route::get('/check/{id}', [NinModificationController::class, 'checkStatus'])->name('nin-modification.check');
    });

    // NIN Validation
    Route::prefix('nin-validation')->group(function () {
        Route::get('/', [NinValidationController::class, 'index'])->name('nin-validation');
        Route::post('/', [NinValidationController::class, 'store'])->name('nin-validation.store');
        Route::get('/check/{id}', [NinValidationController::class, 'checkStatus'])->name('nin-validation.check');
    });

    // IPE Services
    Route::prefix('ipe')->group(function () {
        Route::get('/', [IpeController::class, 'index'])->name('ipe.index');
        Route::post('/', [IpeController::class, 'store'])->name('ipe.store');
        Route::get('/check/{id}', [IpeController::class, 'check'])->name('ipe.check');
        Route::get('/{id}/details', [IpeController::class, 'details'])->name('ipe.details');
        Route::post('/batch-check', [IpeController::class, 'batchCheck'])->name('ipe.batch-check');
    });

    // BVN Services & CRM
    Route::get('/bvn-crm', [BvncrmController::class, 'index'])->name('bvn-crm');
    Route::post('/bvn-crm', [BvncrmController::class, 'store'])->name('crm.store');
    Route::get('/bvn-crm/check/{id}', [BvncrmController::class, 'checkStatus'])->name('crm.check');

    Route::get('/send-vnin', [BvnServicesController::class, 'index'])->name('send-vnin');
    Route::post('/send-vnin', [BvnServicesController::class, 'store'])->name('send-vnin.store');

    Route::get('/modification-fields/{serviceId}', [BvnModificationController::class, 'getServiceFields'])->name('modification.fields');
    Route::get('/modification', [BvnModificationController::class, 'index'])->name('modification');
    Route::post('/modification', [BvnModificationController::class, 'store'])->name('modification.store');
    Route::get('/modification/check/{id}', [BvnModificationController::class, 'checkStatus'])->name('modification.check');

    // Phone Search
    Route::prefix('phone-search')->group(function () {
        Route::get('/', [ManualSearchController::class, 'index'])->name('phone.search.index');
        Route::post('/', [ManualSearchController::class, 'store'])->name('phone.search.store');
        Route::get('/{id}/details', [ManualSearchController::class, 'showDetails'])->name('phone.search.details');
    });
});

require __DIR__.'/auth.php';
