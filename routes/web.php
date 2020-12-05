<?php
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

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/400', function () {
    return view('error.400');
})->name('400');

Route::get('/500', function () {
    return view('error.500');
})->name('500');

Auth::routes();

Route::group(['middleware' => ['auth']], function(){
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');
    
    /*ReturnProcess*/
    Route::get('Automation/O2ReturnProcess/File', ['uses'=>'Admin\Automation\O2ReturnProcessController@index','permission'=>497])->name('AutomationO2ReturnProcessFile');
    Route::get('Automation/O2ReturnProcess/Data', ['uses'=>'Admin\Automation\O2ReturnProcessController@data','permission'=>497])->name('AutomationO2ReturnProcessData');
    
    /*ReturnProcess*/
    Route::get('Automation/O2FreeSim/File', ['uses'=>'Admin\Automation\O2FreeSimController@index','permission'=>497])->name('AutomationO2FreeSimProcessFile');
    Route::get('Automation/O2FreeSim/Data', ['uses'=>'Admin\Automation\O2FreeSimController@data','permission'=>497])->name('AutomationO2FreeSimProcessData');
    
    /*Cron Management*/
    Route::get('cron', ['uses'=>'Admin\Cron\HomeController@index','permission'=>497])->name('CronManage');
    Route::get('cron/add', ['uses'=>'Admin\Cron\HomeController@add','permission'=>497])->name('CronAdd');
    Route::post('cron/store', ['uses'=>'Admin\Cron\HomeController@store','permission'=>497])->name('CronStore');
    Route::get('cron/edit/{id}', ['uses'=>'Admin\Cron\HomeController@edit','permission'=>497])->name('CronEdit');
    Route::post('cron/update/{id}', ['uses'=>'Admin\Cron\HomeController@update','permission'=>497])->name('CronUpdate');
    Route::get('cron/view/{id}', ['uses'=>'Admin\Cron\HomeController@view','permission'=>497])->name('CronView');
    Route::get('cron/remove/{id}', ['uses'=>'Admin\Cron\HomeController@remove','permission'=>497])->name('CronRemove');
    
    
    /*Octopus API Management*/
    Route::get('Octopus', ['uses'=>'Api\OctopusController@manage','permission'=>497])->name('Octopus');
    Route::get('Octopus/management', ['uses'=>'Api\OctopusController@index','permission'=>497])->name('OctopusManagement');
    
    /* API Management*/
    Route::get('field/validation/manage', ['uses'=>'Admin\Api\FieldsController@manage','permission'=>497])->name('APIFieldValidationManage');
    Route::get('field/validation/add', ['uses'=>'Admin\Api\FieldsController@add','permission'=>497])->name('APIFieldsValidationAdd');
    Route::post('field/validation/store', ['uses'=>'Admin\Api\FieldsController@store','permission'=>497])->name('APIFieldsValidationStore');
    Route::get('field/validation/edit/{id}', ['uses'=>'Admin\Api\FieldsController@edit','permission'=>497])->name('APIFieldsValidationEdit');
    Route::post('field/validation/update/{id}', ['uses'=>'Admin\Api\FieldsController@update','permission'=>497])->name('APIFieldsValidationUpdate');
    
    /*Leads Listings*/
    Route::get('Leads/manage', ['uses'=>'Admin\Api\LeadManageController@manage','permission'=>497])->name('LeadManage');
    
    
    
    /*O2UNICA Management*/
    Route::get('O2UNICA/FileImport/management', ['uses'=>'Admin\O2UNICA\FileImportController@manage','permission'=>497])->name('O2UNICAFileImport');
    Route::get('O2UNICA/FileImport/queue/add', ['uses'=>'Admin\O2UNICA\FileImportController@queue_add','permission'=>497])->name('O2UNICAFileQueueAdd');
    
    Route::get('O2UNICA/FileData/management', ['uses'=>'Admin\O2UNICA\FileDataController@manage','permission'=>497])->name('O2UNICAFileData');
    
    Route::get('supplier/leads', ['uses'=>'Admin\Supplier\LeadController@manage','permission'=>497])->name('SupplierLeadManage');
     
    /*User*/
    Route::get('user/manage', ['uses'=>'UserController@manage','permission'=>497])->name('UserListings');
    Route::get('user/add', ['uses'=>'UserController@add','permission'=>497])->name('UserAdd');
    Route::post('user/store', ['uses'=>'UserController@store','permission'=>497])->name('UserStore');
    Route::get('user/edit/{id}', ['uses'=>'UserController@edit','permission'=>497])->name('UserEdit');
    Route::post('user/update/{id}', ['uses'=>'UserController@update','permission'=>497])->name('UserUpdate');
    Route::get('user/view/{id}', ['uses'=>'UserController@view','permission'=>497])->name('UserView');
    Route::get('user/remove/{id}', ['uses'=>'UserController@remove','permission'=>497])->name('UserRemove');
    
    Route::get('User/Change/Password', ['uses'=>'UserController@change_password','permission'=>497])->name('UserChangePassword');
    Route::post('User/Change/Password', ['uses'=>'UserController@changePassword','permission'=>497])->name('PostChangePassword');
    
    /*Web Settings*/
    Route::get('database/connection',['uses'=>'Admin\Setting\HomeController@connections','permission'=>329])->name('DBCOnnections');
    
    /*Email Listings*/
    Route::get('email/manage',['uses'=>'EmailAddressController@manage','permission'=>329])->name('EmailManage');
    Route::get('email/add',['uses'=>'EmailAddressController@add','permission'=>329])->name('EmailAdd');
    Route::post('email/store',['uses'=>'EmailAddressController@store','permission'=>329])->name('EmailStore');
    Route::get('email/edit/{id}',['uses'=>'EmailAddressController@edit','permission'=>329])->name('EmailEdit');
    Route::post('email/update/{id}',['uses'=>'EmailAddressController@update','permission'=>329])->name('EmailUpdate');
    Route::get('email/view/{id}',['uses'=>'EmailAddressController@view','permission'=>329])->name('EmailView');
    Route::get('email/remove/{id}',['uses'=>'EmailAddressController@remove','permission'=>329])->name('EmailRemove');
    
});

Route::get('Energy/SaveMoney', ['uses'=>'Api\SEController@index','permission'=>497]);

Route::get('customer/create', ['uses'=>'Api\CustomerController@store','permission'=>497]);

Route::get('API/leads/create', ['uses'=>'Api\ListController@store','permission'=>497]);
Route::get('API/leads/detail', ['uses'=>'Api\ListController@detail','permission'=>497]);
Route::get('API/leads/manual/post', ['uses'=>'Api\ListController@manual_post','permission'=>497]);

Route::get('API/O2/Return/Process', ['uses'=>'TestController@index','permission'=>497]);


Route::get('TestCall', ['uses'=>'TestController@index']);





