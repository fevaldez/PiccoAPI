<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::get('/', function () {
    return view('index');
    // return view('welcome');
    // View::make('index');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {

	// Route::get('accountStatus/{bu}/{startdate}/{enddate?}', 'BalanceController@accountStatusByRange');
	Route::get('accountStatus/{company}/{bu}/{startdate}/{enddate?}', 'BalanceController@accountStatusByRange');
	Route::get('accountStatus', 'BalanceController@accountStatus');
	Route::get('accounts/{acc}/bu/{bu}/initialBalance/{startdate}', 'BalanceController@accountInitialBalance');
	Route::get('accounts/{acc}/bu/{bu}/debitsCredits/{startdate}/{enddate?}', 'BalanceController@accountDebitsCredits');
	Route::resource('accounts', 'BalanceController', ['only' => [ 'index', 'show'] ]);

	/*
	Projects
	*/
	Route::get('projects/active', 'ProjectController@active');
	Route::get('projects/active/details', 'ProjectController@activeDetails');
	Route::get('projects/{id}/accounts', 'ProjectController@accounts');
	Route::resource('projects', 'ProjectController', ['only' => [ 'index', 'show'] ]);

	Route::get('projects/{id}/income/{active?}', 'ProjectIncomeController@details');
	Route::resource('projects.income', 'ProjectIncomeController', ['only' => [ 'index', 'show'] ]);
	Route::resource('projects.outcome', 'ProjectOutcomeController', ['only' => [ 'index', 'show'] ]);

	Route::resource('users', 'UserController', ['only' => ['index', 'show'] ]);
	Route::resource('accountTypes', 'AccountTypeController', ['only' => ['index'] ]);

});

Route::group(['prefix' => 'api'], function()
{
	Route::post('authenticate', 'AuthenticateController@authenticate');
	Route::get('authenticate/user', 'AuthenticateController@getAuthenticatedUser');
	Route::group(['middleware' => 'jwt.auth'], function()
	{
		Route::resource('authenticate', 'AuthenticateController', ['only' => ['index']]);
	});
});