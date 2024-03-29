<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ConfirmationLetterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DaybookController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VatBillController;
use App\Http\Controllers\VatController;
use Illuminate\Support\Facades\Route;
use App\Mail\PasswordResetMail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Testing route
Route::get('test', function () {

    return view('test');
});
//Starting of the routes
Route::get('/', [LoginController::class, 'index'])->name('startingpoint');
Route::post('/login', [LoginController::class, 'Login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout']);
//Forget password routing code starts from here
Route::get('/forgetpassword', [ForgotPasswordController::class, 'forgetPassword']);
Route::post('/forgetpassword', [ForgotPasswordController::class, 'forgetPassword'])->name('ForgetPasswordPost');
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'ResetPassword'])->name('ResetPasswordGet');
Route::post('reset-password', [ForgotPasswordController::class, 'ResetPasswordStore'])->name('ResetPasswordPost');

Route::middleware(['auth'])->group(function () {
    //Parties route
    Route::get('parties', [AccountController::class, 'partiesHandler'])->name('parties');
    Route::get('addnewparty', [AccountController::class, 'addNewParty'])->name('addnewparty');
    Route::get('cash', [AccountController::class, 'partiesCash'])->name('cash');
    Route::get('parties/purchaserecord', [AccountController::class, 'partiesPurchase'])->name('ledgerpurchase');
    Route::get('parties/viewledger/{companyname}', [AccountController::class, 'viewLedger'])->name('viewLedger');
    Route::get('parties/editledger/{id}/{companyname}', [AccountController::class, 'editPartyLedgerDetails']);
    Route::get('parties/editparty/{companyname}', [AccountController::class, 'editPartyDetails'])->name('editcompany');
    Route::get('parties/edittransaction', [AccountController::class, 'editPartyTransaction'])->name('editpartytransaction');
    Route::get('party/deletetransaction/{id}', [AccountController::class, 'deletePartyTransaction'])->name('deletepartytransaction');
    

    //Customer route
    Route::get('customers', [AccountController::class, 'customerhandler'])->name('customers');
    Route::get('addnewcustomer', [AccountController::class, 'addNewCustomer'])->name('addnewcustomer');
    Route::get('customers/viewledger/{customername}', [AccountController::class, 'viewCustomerLedger'])->name('viewCustomerLedger');
    Route::get('customers/cash', [AccountController::class, 'customerCash'])->name('customercash');
    Route::get('customers/salesrecord', [AccountController::class, 'customerSales'])->name('ledgersales');
    Route::get('customers/editparty/{customername}', [AccountController::class, 'editCustomerDetails'])->name('editcustomer');
    Route::get('customer/edittransaction', [AccountController::class, 'editCustomerTransaction'])->name('editcustomertransaction');


    //Stocks route
    Route::get('stocks-list', [StockController::class, 'stockHandler'])->name('stocks');
    Route::get('addnewstock', [StockController::class, 'addNewStock'])->name('addnewstock');
    Route::get('stock/viewStockledger/{stockname}', [StockController::class, 'viewStockLedger'])->name('viewStockLedger');
    Route::get('stock/sales', [StockController::class, 'stockSales'])->name('stocksales');
    Route::get('stock/purchase', [StockController::class, 'stockPurchase'])->name('stockpurchase');
    Route::get('stocks/editstock/{stockname}', [StockController::class, 'editStockDetails'])->name('editstock');
    Route::get('stocks/editstockledger/{id}/{stockname}', [StockController::class, 'editStockLedgerDetails'])->name('editstockledgerdetails');
    Route::middleware(['notmaketing'])->group(function () {
        //Dashboard
        Route::get('dashboard', [DashboardController::class, 'dashboard']);
        Route::post('dashboard/update-chequerecord/{from}/{id}', [DashboardController::class, 'updateChequeRecord']);
        Route::post('dashboard/update-billrecord/{id}', [DashboardController::class, 'updateBillRecord']);
        //admin, accountant ledger activities route
        Route::get('delete-party/{id}', [AccountController::class, 'deleteParty'])->name('deleteparty');
        //Bill
        Route::get('bill/{billtype}', [BillController::class, 'BillPage'])->where('billtype', 'purchase|sales')->name('billpage');
        Route::get('/billingrecord/{billtype}', [BillController::class, 'billingRecord'])->name('billingrecord');
        Route::get('/returnback', [BillController::class, 'returnBack'])->name('returnback');
        //Confirmation Letters route
        Route::get('/confirmationletters', [ConfirmationLetterController::class, 'indexPage']);
        Route::get('/confirmationletters/{name}', [ConfirmationLetterController::class, 'viewList']);
        Route::get('/confirmationletters/viewpartiesconfirmation/{name}', [ConfirmationLetterController::class, 'viewPartiesConfirmation'])->name('viewpartiesconfirmation');
        Route::get('/confirmationletters/viewcustomersconfirmation/{name}', [ConfirmationLetterController::class, 'viewCustomersConfirmation'])->name('viewcustomersconfirmation');
        Route::post('/customerdownload/{name}', [ConfirmationLetterController::class, 'pdfCustomerDownload'])->name('customersdownload.pdf');
        Route::post('/partydownload/{name}', [ConfirmationLetterController::class, 'pdfPartyDownload'])->name('partiesdownload.pdf');
        //VAT Route
        Route::get('/vat', [VatController::class, 'indexPage']);
        //Purchase And Sales Bill
        Route::get('/purchasebillmonths', [VatBillController::class, 'purchaseMonths']);
        Route::get('/salesbillmonths', [VatBillController::class, 'salesMonths']);
        Route::get('/purchasebilldetail/{month}', [VatBillController::class, 'purchaseDetails'])->name('purchaseDetail');
        Route::get('/salesbilldetail/{month}', [VatBillController::class, 'salesDetails'])->name('salesDetail');
        //Inovices Route
        Route::get('/quotation', [InvoiceController::class, 'quotation']);
        Route::get('/savequotation', [InvoiceController::class, 'saveQuotation'])->name('savequotation');
        Route::get('/updatequotation', [InvoiceController::class, 'updateQuotation'])->name('updatequotation');
        Route::get('/quotationrecord', [InvoiceController::class, 'quotationRecord'])->name('quotationrecord');
        Route::get('/quotationrecorddetail/{billno}/{customername}', [InvoiceController::class, 'quotationRecordDetail'])->name('quotationrecorddetail');
        Route::get('/downloadquotation/{billno}/{customername}', [InvoiceController::class, 'pdfQuotation'])->name('downloadquotation');
        //Daybook Route
        Route::get('/daybook', [DaybookController::class, 'daybookPage']);
        Route::get('/viewdaybook', [DaybookController::class, 'viewDayBook']);
        //Setting Route
        Route::get('/setting/{id}', [SettingController::class, 'settingPage'])->name('setting');
        Route::post('/setting/update/{id}', [SettingController::class, 'updateUser'])->name('updatesetting');
        Route::post('/setting/passwordupdate/{id}', [SettingController::class, 'updatePassword'])->name('changepassword');
        Route::post('/setting/addcompany', [SettingController::class, 'addCompany'])->name('addcompany');
        Route::post('/setting/updatecompany', [SettingController::class, 'updateCompany'])->name('updatecompany');
        //order route
        Route::get('/order', [OrderController::class, 'orderList']);
        Route::get('/orderdetails/{companyname}', [OrderController::class, 'orderDetail']);
        Route::post('/saveorderdetails/{companyname}', [OrderController::class, 'saveOrderDetail'])->name('saveorder');
        Route::get('/sendorder/{companyname}', [OrderController::class, 'orderMail'])->name('sendorder');
        Route::get('/deleteorder/{id}', [OrderController::class, 'destroy']);
    });
    //user routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/adduser', [UserController::class, 'userPage'])->name('adduser');
        Route::get('/user-list', [UserController::class, 'userList'])->name('userlist');
        Route::post('/user-create', [UserController::class, 'createUser'])->name('createuser');
        Route::get('/viewuserlist/{id}', [UserController::class, 'viewUserList'])->name('viewuserlist');
        Route::post('user/delete/{id}', [UserController::class, 'deleteUser'])->name('deleteUser');
    });
});

