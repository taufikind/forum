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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('/forum','ForumController');
Route::get('forum/create/{id}', 'ForumController@create');
Route::get('/forum/read/{slug}', 'ForumController@show')->name('forumslug');

Route::post('/comment/addComment/{forum}', 'CommentController@addComment')->name('addComment');

Route::post('/comment/replyComment/{comment}', 'CommentController@replyComment')->name('replyComment');

Route::get('/populars', 'ForumController@populars')->name('populars');

Route::get('/user/{user}','ProfileController@index')->name('profile');

Route::resource('/tag','TagController');

//role
Route::get('/role', 'RoleController@index')->name('role')->middleware('auth');
Route::get('/list_role', 'RoleController@list')->name('list_role')->middleware('auth');
Route::post('/add_role', 'RoleController@store')->name('add_role')->middleware('auth');
Route::get('delete_role/{id}', 'RoleController@destroy');
Route::get('edit_role/{id}', 'RoleController@edit');
