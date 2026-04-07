<?php

use Illuminate\Support\Facades\Route;

// Base Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\TransferController;

// Utility Controllers (Action)
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
use App\Http\Controllers\Agency\LicenseController;

// Admin Management Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AdminWalletController;
use App\Http\Controllers\Admin\DataVariationController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\AdminSmeDataController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;

// Admin Agency Management Controllers
use App\Http\Controllers\Admin\Agency\BVNmodController;
use App\Http\Controllers\Admin\Agency\BVNserviceController;
use App\Http\Controllers\Admin\Agency\BvnSearchController;
use App\Http\Controllers\Admin\Agency\CRMController;
use App\Http\Controllers\Admin\Agency\NINmodController;
use App\Http\Controllers\Admin\Agency\NinIpeController;
use App\Http\Controllers\Admin\Agency\NinPersonalisationController;
use App\Http\Controllers\Admin\Agency\ValidationController;
use App\Http\Controllers\Admin\Agency\VninToNibssController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/fintava/webhook', [PaymentWebhookController::class, 'handleWebhook'])
    ->middleware('throttle:60,1');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // Core Dashboard & Profile
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
        Route::post('/pin', [ProfileController::class, 'updatePin'])->name('profile.pin');
        Route::post('/update-required', [ProfileController::class, 'updateRequired'])->name('profile.updateRequired');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Wallet, Transactions & Referrals
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
    Route::get('/thankyou', function () {
        return view('thankyou'); })->name('thankyou');

    Route::prefix('wallet')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('wallet');
        Route::post('/create-virtual-account', [WalletController::class, 'createWallet'])->name('virtual.account.create');
        Route::post('/claim-bonus', [WalletController::class, 'claimBonus'])->name('wallet.claimBonus');

        // Transfer Routes
        Route::get('/transfer', [TransferController::class, 'index'])->name('wallet.transfer');
        Route::post('/transfer/verify-user', [TransferController::class, 'verifyUser'])->name('wallet.transfer.verify');
        Route::post('/transfer/verify-pin', [TransferController::class, 'verifyPin'])->name('wallet.transfer.pin');
        Route::post('/transfer/process', [TransferController::class, 'processTransfer'])->name('wallet.transfer.process');
    });

    Route::get('/support', [SupportController::class, 'index'])->name('support');

    Route::prefix('referral')->group(function () {
        Route::get('/', [ReferralController::class, 'index'])->name('refferal');
        Route::post('/claim', [ReferralController::class, 'claimBonus'])->name('refferal.claim');
    });

    /*
    |--------------------------------------------------------------------------
    | Service Utilities (Airtime, Data, Bills)
    |--------------------------------------------------------------------------
    */

    Route::prefix('airtime')->group(function () {
        Route::get('/', [AirtimeController::class, 'airtime'])->name('airtime');
        Route::post('/buy', [AirtimeController::class, 'buyAirtime'])->name('buyairtime');
    });

    Route::prefix('data')->group(function () {
        Route::get('/', [DataController::class, 'data'])->name('buy-data');
        Route::post('/buy', [DataController::class, 'buydata'])->name('buydata');
        Route::get('/fetch-bundles', [DataController::class, 'fetchBundles'])->name('fetch.bundles');
        Route::get('/fetch-price', [DataController::class, 'fetchBundlePrice'])->name('fetch.bundle.price');
        Route::post('/verify-pin', [DataController::class, 'verifyPin'])->name('verify.pin');
    });

    Route::prefix('sme-data')->group(function () {
        Route::get('/', [SmeDataController::class, 'index'])->name('buy-sme-data');
        Route::post('/buy', [SmeDataController::class, 'buySMEdata'])->name('buy-sme-data.submit');
        Route::get('/fetch-type', [SmeDataController::class, 'fetchDataType'])->name('sme.fetch.type');
        Route::get('/fetch-plan', [SmeDataController::class, 'fetchDataPlan'])->name('sme.fetch.plan');
        Route::get('/fetch-price', [SmeDataController::class, 'fetchSmeBundlePrice'])->name('sme.fetch.price');
    });

    Route::prefix('education')->group(function () {
        Route::get('/', [EducationalController::class, 'pin'])->name("education");
        Route::post('/buy-pin', [EducationalController::class, 'buypin'])->name('buypin');
        Route::get('/receipt/{transaction}', [EducationalController::class, 'receipt'])->name('education.receipt');
        Route::get('/get-variation', [EducationalController::class, 'getVariation'])->name('get-variation');
        Route::get('/jamb', [EducationalController::class, 'jamb'])->name('jamb');
        Route::post('/verify-jamb', [EducationalController::class, 'verifyJamb'])->name('verify.jamb');
        Route::post('/buy-jamb', [EducationalController::class, 'buyJamb'])->name('buyjamb');
    });

    Route::prefix('electricity')->group(function () {
        Route::get('/', [ElectricityController::class, 'index'])->name('electricity');
        Route::post('/verify', [ElectricityController::class, 'verifyMeter'])->name('verify.electricity');
        Route::post('/buy', [ElectricityController::class, 'purchase'])->name('buy.electricity');
    });

    Route::prefix('cable')->group(function () {
        Route::get('/', [CableController::class, 'index'])->name('cable');
        Route::get('/variations', [CableController::class, 'getVariations'])->name('cable.variations');
        Route::post('/verify', [CableController::class, 'verifyIuc'])->name('verify.cable');
        Route::post('/buy', [CableController::class, 'purchase'])->name('buy.cable');
    });

    /*
    |--------------------------------------------------------------------------
    | User Verification Services
    |--------------------------------------------------------------------------
    */

    Route::prefix('nin-verification')->group(function () {
        Route::get('/', [NINverificationController::class, 'index'])->name('nin.verification.index');
        Route::post('/', [NINverificationController::class, 'store'])->name('nin.verification.store');
        Route::get('/standardSlip/{id}', [NINverificationController::class, 'standardSlip'])->name('standardSlip');
        Route::get('/premiumSlip/{id}', [NINverificationController::class, 'premiumSlip'])->name('premiumSlip');
        Route::get('/vninSlip/{id}', [NINverificationController::class, 'vninSlip'])->name('vninSlip');
    });

    Route::prefix('nin-demo-verification')->group(function () {
        Route::get('/', [NINDemoVerificationController::class, 'index'])->name('nin.demo.index');
        Route::post('/', [NINDemoVerificationController::class, 'store'])->name('nin.demo.store');
        Route::get('/freeSlip/{id}', [NINDemoVerificationController::class, 'freeSlip'])->name('nin.demo.freeSlip');
        Route::get('/regularSlip/{id}', [NINDemoVerificationController::class, 'regularSlip'])->name('nin.demo.regularSlip');
    });

    Route::prefix('nin-phone-verification')->group(function () {
        Route::get('/', [NINPhoneVerificationController::class, 'index'])->name('nin.phone.index');
        Route::post('/', [NINPhoneVerificationController::class, 'store'])->name('nin.phone.store');
    });

    Route::prefix('bvn-verification')->group(function () {
        Route::get('/', [BvnverificationController::class, 'index'])->name('bvn.verification.index');
        Route::post('/', [BvnverificationController::class, 'store'])->name('bvn.verification.store');
        Route::get('/standardBVN/{id}', [BvnverificationController::class, 'standardBVN'])->name("standardBVN");
        Route::get('/premiumBVN/{id}', [BvnverificationController::class, 'premiumBVN'])->name("premiumBVN");
    });

    Route::prefix('tin-reg')->group(function () {
        Route::get('/', [TinRegistrationController::class, 'index'])->name('tin.index');
        Route::post('/validate', [TinRegistrationController::class, 'validateTin'])->name('tin.validate');
        Route::post('/download', [TinRegistrationController::class, 'downloadSlip'])->name('tin.download');
    });

    Route::prefix('nin-modification')->group(function () {
        Route::get('/', [NinModificationController::class, 'index'])->name('nin-modification');
        Route::post('/', [NinModificationController::class, 'store'])->name('nin-modification.store');
        Route::get('/check/{id}', [NinModificationController::class, 'checkStatus'])->name('nin-modification.check');
        Route::post('/batch-check', [NinModificationController::class, 'batchCheck'])->name('nin-modification.batch-check');
    });

    Route::prefix('nin-validation')->group(function () {
        Route::get('/', [NinValidationController::class, 'index'])->name('nin-validation');
        Route::post('/', [NinValidationController::class, 'store'])->name('nin-validation.store');
        Route::get('/check/{id}', [NinValidationController::class, 'checkStatus'])->name('nin-validation.check');
        Route::post('/batch-check', [NinValidationController::class, 'batchCheck'])->name('nin-validation.batch-check');
    });

    Route::prefix('ipe')->group(function () {
        Route::get('/', [IpeController::class, 'index'])->name('ipe.index');
        Route::post('/', [IpeController::class, 'store'])->name('ipe.store');
        Route::get('/check/{id}', [IpeController::class, 'check'])->name('ipe.check');
        Route::post('/batch-check', [IpeController::class, 'batchCheck'])->name('ipe.batch-check');
    });

    Route::get('/bvn-crm', [BvncrmController::class, 'index'])->name('bvn-crm');
    Route::post('/bvn-crm', [BvncrmController::class, 'store'])->name('crm.store');
    Route::get('/bvn-crm/check/{id}', [BvncrmController::class, 'checkStatus'])->name('crm.check');
    Route::post('/bvn-crm/batch-check', [BvncrmController::class, 'batchCheck'])->name('crm.batch-check');

    Route::get('/send-vnin', [BvnServicesController::class, 'index'])->name('send-vnin');
    Route::post('/send-vnin', [BvnServicesController::class, 'store'])->name('send-vnin.store');

    Route::get('/modification-fields/{serviceId}', [BvnModificationController::class, 'getServiceFields'])->name('modification.fields');
    Route::get('/modification', [BvnModificationController::class, 'index'])->name('modification');
    Route::post('/modification', [BvnModificationController::class, 'store'])->name('modification.store');
    Route::get('/modification/check/{id}', [BvnModificationController::class, 'checkStatus'])->name('modification.check');
    Route::post('/modification/batch-check', [BvnModificationController::class, 'batchCheck'])->name('modification.batch-check');

    Route::prefix('phone-search')->group(function () {
        Route::get('/', [ManualSearchController::class, 'index'])->name('phone.search.index');
        Route::post('/', [ManualSearchController::class, 'store'])->name('phone.search.store');
        Route::get('/{id}/details', [ManualSearchController::class, 'showDetails'])->name('phone.search.details');
    });

    Route::prefix('license')->group(function () {
        Route::get('/', [LicenseController::class, 'index'])->name('license.index');
        Route::post('/', [LicenseController::class, 'store'])->name('license.store');
        Route::post('/batch-check', [LicenseController::class, 'batchCheck'])->name('license.batch-check');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin & Management Routes (Super Admin Only)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // User Management
        Route::resource('users', UserManagementController::class);
        Route::get('transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
        Route::patch('users/{user}/status', [UserManagementController::class, 'updateStatus'])->name('users.update-status');
        Route::patch('users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.update-role');
        Route::patch('users/{user}/limit', [UserManagementController::class, 'updateLimit'])->name('users.update-limit');
        Route::patch('users/{user}/verify-email', [UserManagementController::class, 'verifyEmail'])->name('users.verify-email');
        Route::post('users/import', [UserManagementController::class, 'import'])->name('users.import');
        Route::get('users/download-sample', [UserManagementController::class, 'downloadSample'])->name('users.download-sample');

        Route::post('users/block-ip', [UserManagementController::class, 'blockIp'])->name('users.block-ip');
        Route::delete('users/unblock-ip/{blockedIp}', [UserManagementController::class, 'unblockIp'])->name('users.unblock-ip');

        // Wallet & Funding
        Route::get('wallet', [AdminWalletController::class, 'index'])->name('wallet.index');
        Route::get('wallet/fund', [AdminWalletController::class, 'fundView'])->name('wallet.fund.view');
        Route::post('wallet/fund', [AdminWalletController::class, 'fund'])->name('wallet.fund');
        Route::get('wallet/bulk-fund', [AdminWalletController::class, 'bulkFundView'])->name('wallet.bulk-fund.view');
        Route::post('wallet/bulk-fund', [AdminWalletController::class, 'bulkFund'])->name('wallet.bulk-fund');

        // Services & Data Management
        Route::resource('services', ServiceController::class);
        Route::post('services/{service}/fields', [ServiceController::class, 'storeField'])->name('services.fields.store');
        Route::put('service-fields/{field}', [ServiceController::class, 'updateField'])->name('services.fields.update');
        Route::delete('service-fields/{field}', [ServiceController::class, 'destroyField'])->name('services.fields.destroy');

        Route::post('services/{service}/prices', [ServiceController::class, 'storePrice'])->name('services.prices.store');
        Route::put('service-prices/{price}', [ServiceController::class, 'updatePrice'])->name('services.prices.update');
        Route::delete('service-prices/{price}', [ServiceController::class, 'destroyPrice'])->name('services.prices.destroy');

        Route::resource('data-variations', DataVariationController::class);
        Route::post('data-variations/sync', [DataVariationController::class, 'sync'])->name('data-variations.sync');
        Route::get('sme-data', [AdminSmeDataController::class, 'index'])->name('sme-data.index');
        Route::post('sme-data', [AdminSmeDataController::class, 'store'])->name('sme-data.store');
        Route::put('sme-data/{smeData}', [AdminSmeDataController::class, 'update'])->name('sme-data.update');
        Route::delete('sme-data/{smeData}', [AdminSmeDataController::class, 'destroy'])->name('sme-data.destroy');
        Route::post('sme-data/sync', [AdminSmeDataController::class, 'sync'])->name('sme-data.sync');

        // Agency Services Management
        Route::prefix('agency')->group(function () {

            // BVN Modification
            Route::prefix('bvnmod')->name('bvnmod.')->group(function () {
                Route::get('/', [BVNmodController::class, 'index'])->name('index');
                Route::get('/{id}', [BVNmodController::class, 'show'])->name('show');
                Route::post('/{id}/update', [BVNmodController::class, 'update'])->name('update');
                Route::get('/check/{id}', [BVNmodController::class, 'checkStatus'])->name('check');
            });

            // BVN Service
            Route::prefix('bvnservice')->name('bvnservice.')->group(function () {
                Route::get('/', [BVNserviceController::class, 'index'])->name('index');
                Route::get('/{id}', [BVNserviceController::class, 'show'])->name('show');
                Route::post('/{id}/update', [BVNserviceController::class, 'update'])->name('update');
            });

            // BVN Search
            Route::prefix('bvn-search')->name('bvn-search.')->group(function () {
                Route::get('/', [BvnSearchController::class, 'index'])->name('index');
                Route::get('/{id}', [BvnSearchController::class, 'show'])->name('show');
                Route::post('/{id}/update', [BvnSearchController::class, 'update'])->name('update');
            });

            // CRM
            Route::prefix('crm')->name('crm.')->group(function () {
                Route::get('/', [CRMController::class, 'index'])->name('index');
                Route::get('/{id}', [CRMController::class, 'show'])->name('show');
                Route::post('/{id}/update', [CRMController::class, 'update'])->name('update');
                Route::get('/export/csv', [CRMController::class, 'exportCsv'])->name('export-csv');
                Route::get('/export/excel', [CRMController::class, 'exportExcel'])->name('export-excel');
            });

            // NIN Modification
            Route::prefix('ninmod')->name('ninmod.')->group(function () {
                Route::get('/', [NINmodController::class, 'index'])->name('index');
                Route::get('/{id}', [NINmodController::class, 'show'])->name('show');
                Route::post('/{id}/update', [NINmodController::class, 'update'])->name('update');
                Route::get('/check/{id}', [NINmodController::class, 'checkStatus'])->name('check');
            });

            // NIN IPE
            Route::prefix('ninipe')->name('ninipe.')->group(function () {
                Route::get('/', [NinIpeController::class, 'index'])->name('index');
                Route::get('/{id}', [NinIpeController::class, 'show'])->name('show');
                Route::post('/{id}/update', [NinIpeController::class, 'update'])->name('update');
                Route::get('/check/{id}', [NinIpeController::class, 'checkStatus'])->name('check');
            });

            // NIN Personalisation
            Route::prefix('nin-personalisation')->name('nin-personalisation.')->group(function () {
                Route::get('/', [NinPersonalisationController::class, 'index'])->name('index');
                Route::get('/{id}', [NinPersonalisationController::class, 'show'])->name('show');
                Route::post('/{id}/update', [NinPersonalisationController::class, 'update'])->name('update');
                Route::get('/export/csv', [NinPersonalisationController::class, 'exportCsv'])->name('export-csv');
                Route::get('/export/excel', [NinPersonalisationController::class, 'exportExcel'])->name('export-excel');
            });

            // Validation
            Route::prefix('validation')->name('validation.')->group(function () {
                Route::get('/', [ValidationController::class, 'index'])->name('index');
                Route::get('/{id}', [ValidationController::class, 'show'])->name('show');
                Route::post('/{id}/update', [ValidationController::class, 'update'])->name('update');
                Route::get('/check/{id}', [ValidationController::class, 'checkStatus'])->name('check');
            });

            // VNIN to NIBSS
            Route::prefix('vnin-nibss')->name('vnin-nibss.')->group(function () {
                Route::get('/', [VninToNibssController::class, 'index'])->name('index');
                Route::get('/{id}', [VninToNibssController::class, 'show'])->name('show');
                Route::post('/{id}/update', [VninToNibssController::class, 'update'])->name('update');
            });
        });
    });
});

require __DIR__ . '/auth.php';
