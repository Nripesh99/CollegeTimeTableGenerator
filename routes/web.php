<?php

use Illuminate\Support\Facades\Route;

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // Redirect root to dashboard
    Route::get('/', function () {
        return redirect('/dashboard');
    });

    // Dashboard
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

    // Rooms module
    Route::resource('rooms', 'RoomsController');

    // Courses module
    Route::resource('courses', 'CoursesController');

    // Timeslots module
    Route::resource('timeslots', 'TimeslotsController');

    // Professors module
    Route::resource('professors', 'ProfessorsController');

    // College classes module
    Route::resource('classes', 'CollegeClassesController');

    // Timetable generation
    Route::post('timetables', 'TimetablesController@store')->name('timetables.store');
    Route::get('timetables', 'TimetablesController@index')->name('timetables.index');
    Route::get('timetables/view/{id}', 'TimetablesController@view')->name('timetables.view');

    // User account activation
    Route::get('/users/activate', 'UsersController@showActivationPage')->name('users.activate.show');
    Route::post('/users/activate', 'UsersController@activateUser')->name('users.activate');

    // Home route
    Route::get('/home', 'HomeController@index')->name('home');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', 'UsersController@showLoginPage')->name('login');
    Route::post('/login', 'UsersController@loginUser')->name('login.post');

    // Password reset
    Route::get('/request_reset', 'UsersController@showPasswordRequestPage')->name('password.request');
    Route::post('/request_reset', 'UsersController@requestPassword')->name('password.email');
    Route::get('/reset_password', 'UsersController@showResetPassword')->name('password.reset');
    Route::post('/reset_password', 'UsersController@resetPassword')->name('password.update');
});

// User Account Management
Route::middleware(['auth'])->group(function () {
    Route::get('/my_account', 'UsersController@showAccountPage')->name('account.show');
    Route::post('/my_account', 'UsersController@updateAccount')->name('account.update');
});

// Logout
Route::get('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');
