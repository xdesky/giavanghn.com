<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ArticleManagementController;
use App\Http\Controllers\Admin\NewsManagementController;
use App\Http\Controllers\Admin\SubscriberManagementController;
use App\Http\Controllers\GoldPriceController;
use App\Http\Controllers\SitemapPageController;
use App\Http\Controllers\SitemapXmlController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [SitemapXmlController::class, 'index'])->name('sitemap.xml');

Route::get('/', [GoldPriceController::class, 'index'])->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    
    // Social Login Routes
    Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirect'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'callback'])->name('social.callback');
});

Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// User Dashboard (requires authentication)
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::get('/', function () {
        return view('user.dashboard');
    })->name('user.dashboard');
    
    Route::get('/profile', function () {
        return view('user.profile');
    })->name('user.profile');

    Route::put('/profile', [App\Http\Controllers\Auth\ProfileController::class, 'update'])->name('user.profile.update');
    Route::put('/profile/password', [App\Http\Controllers\Auth\ProfileController::class, 'updatePassword'])->name('user.profile.password');
    
    Route::get('/subscription', function () {
        return view('user.subscription');
    })->name('user.subscription');
});

// Admin Routes (requires admin role)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // User Management
    Route::resource('users', UserManagementController::class);
    Route::post('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
    
    // Article Management
    Route::resource('articles', ArticleManagementController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
    Route::post('/articles/{article}/toggle-publish', [ArticleManagementController::class, 'togglePublish'])->name('articles.toggle-publish');
    Route::post('/articles/regenerate', [ArticleManagementController::class, 'regenerate'])->name('articles.regenerate');

    // News Management
    Route::get('/news', [NewsManagementController::class, 'index'])->name('news.index');
    Route::delete('/news/{news}', [NewsManagementController::class, 'destroy'])->name('news.destroy');
    Route::post('/news/bulk-destroy', [NewsManagementController::class, 'bulkDestroy'])->name('news.bulk-destroy');

    // Subscriber Management
    Route::get('/subscribers', [SubscriberManagementController::class, 'index'])->name('subscribers.index');
    Route::post('/subscribers/bulk-destroy', [SubscriberManagementController::class, 'bulkDestroy'])->name('subscribers.bulkDestroy');
    Route::get('/subscribers/push', [SubscriberManagementController::class, 'pushForm'])->name('subscribers.push');
    Route::post('/subscribers/push', [SubscriberManagementController::class, 'pushSend'])->name('subscribers.pushSend');
    Route::get('/subscribers/push/{log}', [SubscriberManagementController::class, 'pushShow'])->name('subscribers.pushShow');
    Route::post('/subscribers/{subscriber}/toggle-status', [SubscriberManagementController::class, 'toggleStatus'])->name('subscribers.toggle');
    Route::delete('/subscribers/{subscriber}', [SubscriberManagementController::class, 'destroy'])->name('subscribers.destroy');
});

Route::get('/tin-tuc-gia-vang/trong-nuoc/tag/{tagSlug}', [GoldPriceController::class, 'analysisByTag'])->name('analysis.tag');
Route::get('/tin-tuc-gia-vang/trong-nuoc/{slug}', [GoldPriceController::class, 'showAnalysis'])->name('analysis.show');

Route::prefix('dashboard-api')->group(function (): void {
	Route::get('/snapshot', [GoldPriceController::class, 'snapshot'])->name('dashboard.snapshot');
	Route::post('/subscribe', [GoldPriceController::class, 'subscribe'])->name('dashboard.subscribe');
});

// Unsubscribe
Route::get('/unsubscribe/{token}', [GoldPriceController::class, 'unsubscribe'])->name('unsubscribe');

$sitemap = config('gold_sitemap', []);

$flattenPaths = function (array $nodes, string $prefix = '') use (&$flattenPaths): array {
	$paths = [];

	foreach ($nodes as $slug => $meta) {
		$fullPath = $prefix === '' ? $slug : $prefix . '/' . $slug;
		$paths[] = $fullPath;

		if (isset($meta['children']) && is_array($meta['children'])) {
			$paths = array_merge($paths, $flattenPaths($meta['children'], $fullPath));
		}
	}

	return $paths;
};

foreach ($flattenPaths($sitemap) as $path) {
	Route::get('/' . $path, [SitemapPageController::class, 'show'])
		->defaults('path', $path)
		->name('page.' . str_replace('/', '.', $path));
}
