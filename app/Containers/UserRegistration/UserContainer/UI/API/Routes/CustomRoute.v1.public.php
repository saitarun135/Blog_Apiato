<?php

/**
 * @apiGroup           UserContainer
 * @apiName            register
 *
 * @api                {POST} /v1/register Endpoint title here..
 * @apiDescription     Endpoint description here..
 *
 * @apiVersion         1.0.0
 * @apiPermission      none
 *
 * @apiParam           {String}  parameters here..
 *
 * @apiSuccessExample  {json}  Success-Response:
 * HTTP/1.1 200 OK
{
  // Insert the response of the request here...
}
 */

use App\Containers\UserRegistration\UserContainer\UI\API\Controllers\UserBlogController;
use Illuminate\Support\Facades\Route;

Route::post('register', [UserBlogController::class, 'register']);
Route::post('login',[UserBlogController::class,'login']);
Route::get('getBlogs',[UserBlogController::class,'displayUserBlogs']);

Route::post('adminSignUp',[UserBlogController::class,'adminRegistration']);
Route::post('adminSignIn',[UserBlogController::class,'adminLogin']);
Route::post('postBlog',[UserBlogController::class,'upload']);
Route::post('updateBlog/{id}',[UserBlogController::class,'updateBlog']);
Route::post('deleteBlog/{id}',[UserBlogController::class,'deleteBlog']);
Route::get('getBlogs',[UserBlogController::class,'displayUserBlogs']);
Route::get('myBlogs',[UserBlogController::class,'displayAdminBlogs']);
Route::get('getBlogByID/{id}',[UserBlogController::class,'displayBlogByID']);


  // ->name('api_usercontainer_register')
  // ->middleware(['auth:api']);

