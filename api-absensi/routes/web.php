<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
    // return redirect(env('WEB_URL'));
});


// cors
$router->group(['middleware' => 'cors'], function () use ($router) {
    $router->options('/{any:.*}', function () {
        return response()->json(['methods' => 'OPTIONS'], 200);
    });

    // system app
    $router->group(['middleware' => 'identify'], function () use ($router) {

        // router for Auth namespace
        $router->group(['namespace' => 'Auth', 'prefix' => 'auth'], function () use ($router) {
            // login
            $router->post('in', 'AuthController@authIn');
            // logout
            $router->post('out', 'AuthController@authOut');
            // check auth
            $router->post('check', 'AuthController@checkAuth');
        });

        // router for Settings namespace
        $router->group(['namespace' => 'Settings', 'prefix' => 'mst'], function () use ($router) {

            // router Group for Setting User
            $router->group(['prefix' => 'test'], function () use ($router) {
                // show all user
                $router->post('trial', 'TestController@trial');
                $router->post('verif', 'TestController@verif');
            });

            $router->group(['middleware' => 'authenticate'], function () use ($router) {
                // router Group for Setting User
                $router->group(['prefix' => 'user'], function () use ($router) {
                    // show all user
                    $router->get('show', 'UserController@show');
                    // show all user
                    $router->get('getBy/{id}', 'UserController@getBy');
                    // show all user
                    $router->get('getByUserId/{id}', 'UserController@getByUserId');
                    // add new user per 1 record
                    $router->post('add', 'UserController@add');
                    // edit user
                    $router->post('edit', 'UserController@edit');
                    // validate email
                    $router->get('valid', 'UserController@valid');
                    // reset password
                    $router->post('resetPassword', 'UserController@resetPassword');
                    // send verification
                    $router->post('sendVerification', 'UserController@sendVerification');
                    // export template
                    $router->post('exportData', 'UserController@exportData');
                });
            });
        });
        // route master namespace
        $router->group(['namespace' => 'Master', 'prefix' => 'mst'], function () use ($router) {

            // router Group for Master Absence
            $router->group(['prefix' => 'masterAbsence'], function () use ($router) {
                // get all data master absence
                $router->get('show', 'MasterAbsenceController@show');
                // generate code
                $router->get('getCode', 'MasterAbsenceController@getCode');
                // get by id data master absence
                $router->get('getById/{id}', 'MasterAbsenceController@getById');
                // add new master absence per 1 record
                $router->post('add', 'MasterAbsenceController@add');
                // edit new master absence per 1 record
                $router->post('edit', 'MasterAbsenceController@edit');
                // delete row
                $router->post('delete', 'MasterAbsenceController@delete');
                // get master cuti
                $router->get('getCuti', 'MasterAbsenceController@getCuti');
                // get master sakit
                $router->get('getSakit', 'MasterAbsenceController@getSakit');
                // get master izin
                $router->get('getIzin', 'MasterAbsenceController@getIzin');
            });

            // router Group for Master role access
            $router->group(['prefix' => 'masterRoles'], function () use ($router) {
                // get all data master role access
                $router->get('show', 'MasterRolesController@show');
                // generate code
                $router->get('getCode', 'MasterRolesController@getCode');
                // get by id data master role access
                $router->get('getById/{id}', 'MasterRolesController@getById');
                // add new master role access per 1 record
                $router->post('add', 'MasterRolesController@add');
                // edit new master role access per 1 record
                $router->post('edit', 'MasterRolesController@edit');
                // delete row
                $router->post('delete', 'MasterRolesController@delete');
            });

            // router Group for Master Customer
            $router->group(['prefix' => 'masterCustomer'], function () use ($router) {
                // get all data master customer
                $router->get('show', 'MasterCustomerController@show');
                // generate code
                $router->get('getCode', 'MasterCustomerController@getCode');
                // get by id data master customer
                $router->get('getByCustomerCode/{id}', 'MasterCustomerController@getByCustomerCode');
                // add new master customer per 1 record
                $router->post('add', 'MasterCustomerController@add');
                // edit new master customer per 1 record
                $router->post('edit', 'MasterCustomerController@edit');
                // delete row
                $router->post('delete', 'MasterCustomerController@delete');
            });

            // router Group for Master Cutoff
            $router->group(['prefix' => 'masterCutoff'], function () use ($router) {
                // get all data master cutoff
                $router->get('show', 'MasterCutoffController@show');
                // generate code
                $router->get('getCode', 'MasterCutoffController@getCode');
                // get by id data master cutoff
                $router->get('getById/{id}', 'MasterCutoffController@getById');
                // get by id data master cutoff
                $router->get('getByCustomerCode/{id}', 'MasterCutoffController@getByCustomerCode');
                // add new master cutoff per 1 record
                $router->post('add', 'MasterCutoffController@add');
                // edit new master cutoff per 1 record
                $router->post('edit', 'MasterCutoffController@edit');
                // delete row
                $router->post('delete', 'MasterCutoffController@delete');
            });

            // router Group for Master Schedule
            $router->group(['prefix' => 'masterSchedule'], function () use ($router) {
                // get all data master schedule
                $router->get('show', 'MasterScheduleController@show');
                // get all data master schedule group by schedule code
                $router->get('getAll', 'MasterScheduleController@getAll');
                // get by schedule code
                $router->get('getByScheduleCode/{id}', 'MasterScheduleController@getByScheduleCode');
                // get all data master schedule by cust code
                $router->get('getByCustomerCode/{id}', 'MasterScheduleController@getByCustomerCode');
                // generate code
                $router->get('getCode', 'MasterScheduleController@getCode');
                // get by id data master schedule
                $router->get('getById/{id}', 'MasterScheduleController@getById');
                // add new master schedule per 1 record
                $router->post('add', 'MasterScheduleController@add');
                // edit new master schedule per 1 record
                $router->post('edit', 'MasterScheduleController@edit');
                // delete row
                $router->post('delete', 'MasterScheduleController@delete');
            });

            // router Group for Master Departemen
            $router->group(['prefix' => 'masterDepartemen'], function () use ($router) {
                // get all data master departemen
                $router->get('show', 'MasterDepartemenController@show');
                // get all data master departemen by customer code
                $router->get('getByCustomerCode/{id}', 'MasterDepartemenController@getByCustomerCode');
                // generate code
                $router->get('getCode', 'MasterDepartemenController@getCode');
                // get by id data master departemen
                // $router->get('getById/{id}', 'MasterDepartemenController@getById');
                // add new master departemen per 1 record
                $router->post('add', 'MasterDepartemenController@add');
                // edit new master departemen per 1 record
                $router->post('edit', 'MasterDepartemenController@edit');
                // delete row
                $router->post('delete', 'MasterDepartemenController@delete');
            });
        });

        // route transaction namespace
        $router->group(['namespace' => 'Transactions', 'prefix' => 'trx'], function () use ($router) {

            // router Group for Absence
            $router->group(['prefix' => 'absence'], function () use ($router) {
                // add new absence per 1 record
                $router->post('add', 'AbsenceController@add');
                // edit new absence per 1 record
                $router->post('edit', 'AbsenceController@edit');
                // get data absence
                $router->get('show', 'AbsenceController@show');
                // upload from base64
                $router->post('upload', 'AbsenceController@upload');
                // upload from file type
                $router->post('uploadTypeFile', 'AbsenceController@uploadTypeFile');
                // edit data
                $router->post('edit', 'AbsenceController@edit');
                // edit data
                $router->get('getByIdUsers/{id}', 'AbsenceController@getByIdUsers');
            });

            // router Group for Attendee
            $router->group(['prefix' => 'attendee'], function () use ($router) {
                // add new attendee per 1 record
                $router->post('add', 'AttendeeController@add');
                // get data last in
                $router->post('getScheduleOut', 'AttendeeController@getScheduleOut');
                // edit new attendee per 1 record
                $router->post('edit', 'AttendeeController@edit');
                // get all data attendee
                $router->get('show', 'AttendeeController@show');
                // get data attendee by id
                $router->get('getById/{id}', 'AttendeeController@getById');
                // get one row by users id data attendee
                $router->get('getByUsersId/{id}', 'AttendeeController@getByUsersId');
                // get by id
                $router->get('getAllByUserId/{id}', 'AttendeeController@getAllByUserId');
                // get by date
                $router->post('getRekapByDate', 'AttendeeController@getRekapByDate');
                // get by periode
                $router->post('getRekapByPeriode', 'AttendeeController@getRekapByPeriode');
                // save image attendee
                $router->post('upload', 'AttendeeController@upload');
                // export excel
                $router->post('exportData', 'AttendeeController@exportData');
                // get summary record
                $router->post('summary', 'AttendeeController@summary');
                // get summary record for excel export
                $router->post('exportDataSummary', 'AttendeeController@exportDataSummary');
            });

            // router Group for Schedule
            $router->group(['prefix' => 'schedule'], function () use ($router) {
                // add new schedule per 1 record
                $router->post('add', 'ScheduleController@add');
                // edit new schedule per 1 record
                $router->post('edit', 'ScheduleController@edit');
                // get all data schedule
                $router->get('show', 'ScheduleController@show');
                // get data schedule per user
                $router->post('getScheduleByUserId', 'ScheduleController@getScheduleByUserId');
                // get all data schedule
                $router->get('getScheduleByCustomer/{id}', 'ScheduleController@getScheduleByCustomer');
                // get data schedule by id
                $router->get('getById/{id}', 'ScheduleController@getById');
                // upload via excel
                $router->post('uploadSchedule', 'ScheduleController@uploadSchedule');
                // export excel
                $router->post('exportData', 'ScheduleController@exportData');
                // check shift
                $router->get('checkShift/{id}', 'ScheduleController@checkShift');
            });

            // router Group for Holiday
            $router->group(['prefix' => 'holiday'], function () use ($router) {
                // add new holiday per 1 record
                $router->post('add', 'HolidayController@add');
                // edit new holiday per 1 record
                $router->post('edit', 'HolidayController@edit');
                // get all data holiday
                $router->get('show', 'HolidayController@show');
                // get all data holiday by date range
                $router->post('getByDateRange', 'HolidayController@getByDateRange');
                // upload holiday
                $router->post('uploadHoliday', 'HolidayController@uploadHoliday');
                // upload holiday
                $router->post('exportData', 'HolidayController@exportData');
                // generate code holiday
                $router->get('getCode', 'HolidayController@getCode');
                // get data holiday by id
                $router->get('getById/{id}', 'HolidayController@getById');
                // delete row
                $router->post('delete', 'HolidayController@delete');
            });

            // router Group for Placement Departemen
            $router->group(['prefix' => 'departemen'], function () use ($router) {
                // add new departemen per 1 record
                $router->post('add', 'DepartemenController@add');
                // edit new departemen per 1 record
                $router->post('edit', 'DepartemenController@edit');
                // get data departemen
                $router->get('show', 'DepartemenController@show');
                // upload route
                $router->post('upload', 'DepartemenController@upload');
            });

            // router Group for Site Schedule
            $router->group(['prefix' => 'siteSchedule'], function () use ($router) {
                // get data departemen
                $router->get('show', 'SiteScheduleController@show');
                // get all data master schedule by cust code group by schedule code
                $router->get('groupScheduleByCustomerCode/{id}', 'SiteScheduleController@groupScheduleByCustomerCode');
                // add new departemen per 1 record
                $router->post('add', 'SiteScheduleController@add');
                // edit new departemen per 1 record
                $router->post('edit', 'SiteScheduleController@edit');
                // edit new departemen per 1 record
                $router->get('getScheduleByCustomer/{id}', 'SiteScheduleController@getScheduleByCustomer');
                // export schedule customer
                $router->post('exportData', 'SiteScheduleController@exportData');
            });
        });
    });
});
