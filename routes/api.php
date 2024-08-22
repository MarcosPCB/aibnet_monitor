<?php

use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MainBrandController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\DeltaController;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\SocialFetcherController;
use App\Http\Controllers\ChatLLMController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

// Operator
Route::post('/operator/register', [OperatorController::class, 'register'])->name('operator.register');

Route::middleware(['auth:sanctum', 'isOperator'])->group(function () {
    // Account
    Route::post('/account/create', [AccountController::class, 'create'])->name('account.create');
    Route::post('/account/complete', [AccountController::class, 'createComplete'])->name('account.createComplete');

    // Brand
    Route::prefix('brand')->group(function () {
        Route::post('/create', [BrandController::class, 'create'])->name('brand.create');
        Route::patch('/update/{id}', [BrandController::class, 'update'])->name('brand.update');
    });

    // Platforms
    Route::post('/platform/create', [PlatformController::class, 'create'])->name('platform.create');
    Route::patch('/platform/update/{id}', [PlatformController::class, 'update'])->name('platform.update');
    Route::post('/platform/check', [PlatformController::class, 'check'])->name('platform.check');
    Route::patch('/platform/deactivate/{id}', [PlatformController::class, 'deactivate'])->name('platform.deactivate');
    Route::delete('/platform/delete/{id}', [PlatformController::class, 'destroy'])->name('platform.delete');

    // Posts
    Route::prefix('post')->group(function () {
        Route::post('/create', [PostController::class, 'create'])->name('post.create');
        Route::patch('/update/{id}', [PostController::class, 'update'])->name('post.update');
        Route::delete('/delete/{id}', [PostController::class, 'delete'])->name('postsdelete');
    });

    // Comment
    Route::prefix('comment')->group(function () {
        Route::post('/create', [CommentController::class, 'create'])->name('comment.create');
        Route::patch('/update/{id}', [CommentController::class, 'update'])->name('comment.update');
        Route::delete('/delete/{id}', [CommentController::class, 'delete'])->name('comment.delete');
    });

    // Operator
    Route::patch('/operator/update/{id}', [OperatorController::class, 'update'])->name('operator.update');
    Route::patch('/operator/permit/{id}', [OperatorController::class, 'permit'])->name('operator.permit');
    Route::delete('/operator/delete/{id}', [OperatorController::class, 'delete'])->name('operator.delete');

    // Delta
    Route::post('/delta', [DeltaController::class, 'create']);
    Route::patch('/delta/{id}', [DeltaController::class, 'update']);
    Route::get('/delta/find', [DeltaController::class, 'findByDate']);
    Route::delete('/delta/delete/{id?}', [DeltaController::class, 'delete']);
    Route::post('/delta/builder', [DeltaController::class, 'deltaBuilder']);
    Route::get('/delta/find', [DeltaController::class, 'findDelta']);

    // Get routes
    Route::get('operator/{id}', [OperatorController::class, 'get'])->name('operator.get');
    Route::get('brand/{id}', [BrandController::class, 'get'])->name('brand.get');
    Route::get('platform/{id}', [PlatformController::class, 'get'])->name('platform.get');
    Route::get('post/{id}', [PostController::class, 'get'])->name('post.get');
    Route::get('comment/{id}', [CommentController::class, 'get'])->name('comment.get');

    //API Tokens
    Route::post('api-tokens', [ApiTokenController::class, 'create']);
    Route::patch('api-tokens/{id}', [ApiTokenController::class, 'update']);
    Route::get('api-tokens/{id}', [ApiTokenController::class, 'get']);
    Route::patch('api-tokens/{id}/restart-limit', [ApiTokenController::class, 'restartLimit']);
    Route::patch('api-tokens/{id}/restart-limit-force', [ApiTokenController::class, 'restartLimitForce']);
    Route::patch('api-tokens/{id}/update-limit-used', [ApiTokenController::class, 'updateLimitUsed']);
    Route::patch('api-tokens/{id}/deactivate', [ApiTokenController::class, 'deactivate']);
    Route::delete('api-tokens/{id}', [ApiTokenController::class, 'delete']);

    //Social Fetcher
    Route::prefix('social')->group(function () {
        Route::post('fetch-profile', [SocialFetcherController::class, 'fetchProfile']);
        Route::post('fetch-posts', [SocialFetcherController::class, 'fetchPosts']);
        Route::post('fetch-comments', [SocialFetcherController::class, 'fetchComments']);
        Route::post('get-limit', [SocialFetcherController::class, 'getLimit']);
    });
});

Route::middleware(['auth:sanctum', 'isOperatorOrAccountUser'])->group(function () {
     // Account
     Route::patch('/account/update/{account_id}', [AccountController::class, 'update'])->name('account.update');
 
     // User
     Route::post('/user/create/{account_id}', [UserController::class, 'create'])->name('user.create');
     Route::patch('/user/update/{id}/{account_id}', [UserController::class, 'update'])->name('user.update');
     Route::delete('/user/delete/{id}/{account_id}', [UserController::class, 'delete'])->name('user.delete');
 
     // Main brand
     Route::prefix('main-brand')->group(function () {
         Route::post('/create/{account_id}', [MainBrandController::class, 'create'])->name('main-brand.create');
         Route::patch('/update/{id}/{account_id}', [MainBrandController::class, 'update'])->name('main-brand.update');
         Route::delete('/delete/{id}/{account_id}', [MainBrandController::class, 'delete'])->name('main-brand.delete');
     });

     Route::get('account/{account_id}', [AccountController::class, 'get'])->name('account.get');
     Route::get('user/{id}/{account_id}', [UserController::class, 'get'])->name('user.get');
     Route::get('mainbrand/{id}/{account_id}', [MainBrandController::class, 'get'])->name('main-brand.get');
     Route::get('mainbrand/weekly/{id}/{account_id}', [MainBrandController::class, 'buildDelta'])->name('main-brand.weekly');

     // Chat
     Route::prefix('chat')->group(function () {
        Route::post('/create/{account_id}', [ChatLLMController::class,'create'])->name('chat.create');
        Route::post('/create-run/{account_id}', [ChatLLMController::class,'createAndRun'])->name('chat.create-run');
        Route::post('/add/text/{id}/{account_id}',[ChatLLMController::class,'addTextToThread'])->name('chat.add.text');
        Route::patch('/add/{id}/{account_id}', [ChatLLMController::class,'addText'])->name('chat.add');
        Route::patch('/add/thread/{id}/{account_id}', [ChatLLMController::class,'attachThread'])->name('chat.add.thread');
        Route::get('/get/{id}/{account_id}', [ChatLLMController::class,'getChat'])->name('chat.get');
        Route::get('/list/{main_brand_id}/{account_id}', [ChatLLMController::class,'listAll'])->name('chat.list');
        Route::delete('/delete/{id}/{account_id}', [ChatLLMController::class, 'delete'])->name('chat.delete');
     });
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum'); // Middleware para autenticação com Sanctum ou outro guard

Route::get('/mock-complete-profile', function () {
    $filePath = base_path('tests/Mocks/complete-profile.json');

    // Verifica se o arquivo existe
    if (File::exists($filePath)) {
        // Lê o conteúdo do arquivo
        $jsonContent = File::get($filePath);

        // Retorna o conteúdo como JSON
        return response()->json(json_decode($jsonContent), 200);
    }

    // Caso o arquivo não exista, retorna uma resposta de erro
    return response()->json(['error' => 'Mock file not found'], 404);
});