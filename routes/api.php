<?php
//Acceso Publico
Route::post('login', 'AuthController@login');

//Cargar listado por medio de un envio de url
Route::get('cargar', 'PackageController@cargar');
Route::get('cargarurl', 'PackageController@cargarurl');

//Listado de rutas
Route::get('listas', 'PackageController@index');
Route::get('totales', 'PackageController@total_listados');
Route::get('/totales/status/{id}', 'PackageController@total_estatus');

//Paquete Individual
Route::get('/package/{id}/{lista}', 'PackageController@individual');

//ver un listado 
Route::get('/lista/{id}', 'PackageController@show');
Route::get('/lista/entregados/{id}', 'PackageController@entregados');
Route::get('/lista/porentregar/{id}', 'PackageController@porentregar');
Route::get('/lista/devueltos/{id}', 'PackageController@devueltos');

//ver listado asignado a un conductor 
Route::get('/lista/chofer/{id}', 'PackageController@listado_asignado');

//Cargar y calsificar al camiòn un listado 
Route::get('/cargar/lista/{id}', 'PackageController@cargar_al_camion');

//Para el modelo de usuario
Route::resource('/user', 'UserController')->except(['create', 'edit']);
Route::post('register', 'AuthController@register');
Route::get('/getrole/{id}', 'AuthController@get_role');
Route::get('logout', 'AuthController@logout');

//Mildware de login con jwt y auth
Route::group(['middleware' => ['jwt.auth']], function() {

    //Para el incio de la api
    Route::get('/', 'PackageController@index');
    Route::get('home', 'PackageController@index');
    
});