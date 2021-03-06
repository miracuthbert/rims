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

/**
 * Auth Routes
 */
Route::group(['namespace' => 'Auth\Controllers'], function () {

    // Authentication Routes...
    Route::get('login', 'LoginController@showLoginForm')->name('login');
    Route::post('login', 'LoginController@login');
    Route::post('logout', 'LoginController@logout')->name('logout');

    // Registration Routes...
    Route::get('register', 'RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'RegisterController@register');

    // Password Reset Routes...
    Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'ResetPasswordController@reset');

    /**
     * Activation Group Routes
     */
    Route::group(['prefix' => '/activation', 'middleware' => ['guest'], 'as' => 'activation.'], function () {

        // resend index
        Route::get('/resend', 'ActivationResendController@index')->name('resend');

        // resend store
        Route::post('/resend', 'ActivationResendController@store')->name('resend.store');

        // activation
        Route::get('/{confirmation_token}', 'ActivationController@activate')->name('activate');
    });

    /**
     * Two Factor Login Group Routes
     */
    Route::group(['prefix' => '/login/twofactor', 'middleware' => ['guest'], 'as' => 'login.twofactor.'], function () {

        // index
        Route::get('/', 'TwoFactorLoginController@index')->name('index');

        // store
        Route::post('/', 'TwoFactorLoginController@verify')->name('verify');
    });
});

/**
 * Education Route
 */
Route::get('/education', 'Education\Controllers\EducationController@index');

/**
 * Levels Route
 */
Route::get('/levels', 'Level\Controllers\LevelController@index');

/**
 * Skills Route
 */
Route::get('/skills', 'Skill\Controllers\SkillController@index');

/**
 * Languages Route
 */
Route::get('/languages', 'Language\Controllers\LanguageController@index');

/**
 * Jobs Filters Route
 */
Route::get('/jobs/filters', 'Job\Controllers\JobListingController@filters')->name('jobs.filters');

/**
 * Currencies Index Route
 */
Route::get('/currencies', 'Currency\Controllers\CurrencyController@index')->name('currencies.index');

/**
 * Categories Routes
 */
Route::group(['namespace' => 'Category\Controllers'], function () {

    /**
     * Categories Route
     */
    Route::apiResource('/categories', 'CategoryController')->only('index', 'show');
});

/**
 * Home Routes
 */
Route::group(['namespace' => 'Home\Controllers'], function () {

    // index
    Route::get('/', 'HomeController@index')->name('home');
});

/**
 * Jobs Routes
 */
Route::group(['namespace' => 'Job\Controllers', 'as' => 'jobs.'], function () {

    /**
     * Jobs Group Routes
     */
    Route::group(['prefix' => '/jobs'], function () {

        /**
         * Jobs Group Routes
         */
        Route::group(['prefix' => '/{job}'], function () {

            // todo: add job group routes; ratings, reviews

            /**
             * Job Application Group Routes
             */
            Route::group(['prefix' => '/applications', 'middleware' => ['auth'], 'as' => 'applications.'], function () {

                /**
                 * Application Group Routes
                 */
                Route::group(['prefix' => '/{jobApplication}'], function () {

                    // cancel route
                    Route::post('/cancel', 'JobApplicationCancelController@store')->name('cancel');

                    // decline route
                    Route::post('/decline', 'JobApplicationDeclineController@store')->name('decline');

                    // CV store route
                    Route::get('/cv', 'JobCVController@show')->name('cv.show');

                    // CV store route
                    Route::post('/cv/store', 'JobCVController@store')->name('cv.store');

                    // create route
                    Route::get('/create', 'JobApplicationController@create')->name('create');

                    // store route
                    Route::post('/store', 'JobApplicationController@store')->name('store');
                });
            });

            /**
             * Job Applications Resource Routes
             */
            Route::resource('/applications', 'JobApplicationController', [
                'parameters' => [
                    'applications' => 'jobApplication'
                ]
            ])->except('create', 'store')->middleware(['auth']);
        });

        /**
         * Job Show Route
         */
        Route::get('/{job}', 'JobController@show')->name('show');
    });

    /**
     * Area Group Routes
     */
    Route::group(['prefix' => '/{area}'], function () {

        /**
         * Jobs Listings Route
         */
        Route::get('/jobs/listings', 'JobListingController@index')->name('listings');

        /**
         * Job Index Route
         */
        Route::get('/jobs', 'JobController@index')->name('index');
    });
});

/**
 * User Group Routes
 */
Route::group(['prefix' => '/{username}'], function () {

    /**
     * Portfolio Namespace Routes
     */
    Route::group(['namespace' => 'Portfolio\Controllers'], function () {

        /**
         * Portfolio Resource Route
         */
        Route::apiResource('/portfolio', 'PortfolioController')->except('index', 'show');
    });
});

/**
 * Areas Routes
 */
Route::group(['namespace' => 'Area\Controllers'], function () {

    /**
     * Change Area
     */
    Route::get('/areas/{area}', 'AreaController@change')->name('areas.change');

    /**
     * Areas Index Route
     */
    Route::get('/areas', 'AreaController@index')->name('areas.index');
});

/**
 * Plans Routes
 */
Route::group(['namespace' => 'Subscription\Controllers'], function () {

    /**
     * Plans Group Routes
     */
    Route::group(['prefix' => '/plans', 'middleware' => ['subscription.inactive'], 'as' => 'plans.'], function () {

        // teams index
        Route::get('/teams', 'PlanTeamController@index')->name('teams.index');
    });

    /**
     * Plans Resource Routes
     */
    Route::resource('/plans', 'PlanController', [
        'only' => [
            'index',
            'show'
        ]
    ])->middleware(['subscription.inactive']);

    /**
     * Subscription Resource Routes
     */
    Route::resource('/subscription', 'SubscriptionController', [
        'only' => [
            'index',
            'store'
        ]
    ])->middleware(['auth.register', 'subscription.inactive']);
});

/**
 * Developer Routes.
 *
 * Handles developer routes.
 */
Route::group(['prefix' => '/developers', 'middleware' => ['auth'], 'namespace' => 'Developer\Controllers', 'as' => 'developer.'], function () {

    // index
    Route::get('/', 'DeveloperController@index')->name('index');
});

/**
 * Subscription: active Routes
 */
Route::group(['middleware' => ['subscription.active']], function () {
});

/**
 * Account Group Routes.
 *
 * Handles user's account routes.
 */
Route::group(['prefix' => '/account', 'middleware' => ['auth'], 'namespace' => 'Account\Controllers', 'as' => 'account.'], function () {

    /**
     * Dashboard
     */
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

    /**
     * Jobs Routes
     */
    Route::group(['prefix' => '/jobs', 'namespace' => 'Job', 'as' => 'jobs.'], function () {

        /**
         * Job Applications Routes
         */
        Route::group(['prefix' => '/applications', 'as' => 'applications.'], function () {

            Route::get('/incomplete', 'JobIncompleteApplicationController@index')->name('incomplete.index');

            Route::get('/drafts', 'JobDraftApplicationController@index')->name('drafts.index');

            Route::get('/pending', 'JobPendingApplicationController@index')->name('pending.index');

            Route::get('/accepted', 'JobAcceptedApplicationController@index')->name('accepted.index');

            Route::get('/rejected', 'JobRejectedApplicationController@index')->name('rejected.index');

        });
    });

    /**
     * Education Routes
     */
    Route::get('/education/index', 'UserEducationIndexController@index');

    Route::get('/education/levels', 'UserEducationIndexController@levels');

    /**
     * Education Resource Routes
     */
    Route::resource('/education', 'UserEducationController', [
        'parameters' => [
            'education' => 'userEducation'
        ]
    ]);

    /**
     * Skills Routes
     */
    Route::group(['prefix' => '/skills'], function () {

        /**
         * List user skills
         */
        Route::get('/index', 'UserSkillIndexController@index');

        /**
         * List skills
         */
        Route::get('/list', 'UserSkillIndexController@skills');

        /**
         * List levels
         */
        Route::get('/levels', 'UserSkillIndexController@levels');

        /**
         * Toggle user skill status
         */
        Route::put('/{id}/status', 'UserSkillStatusController@toggleStatus');
    });

    /**
     * Skills Resource Routes
     */
    Route::resource('/skills', 'UserSkillController')->only('index', 'store', 'update', 'destroy');

    /**
     * Languages Routes
     */
    Route::group(['prefix' => '/languages', 'as' => 'languages.'], function () {

        /**
         * List user languages
         */
        Route::get('/index', 'UserLanguageIndexController@index');

        /**
         * List languages
         */
        Route::get('/list', 'UserLanguageIndexController@languages');

        /**
         * List levels
         */
        Route::get('/levels', 'UserLanguageIndexController@levels');

        /**
         * Toggle user language status
         */
        Route::put('/{id}/status', 'UserLanguageStatusController@toggleStatus');
    });

    /**
     * Language Resource Routes
     */
    Route::resource('/languages', 'UserLanguageController')->only('index', 'store', 'update', 'destroy');

    /**
     * Portfolio Namespace Routes
     */
    Route::group(['namespace' => 'Portfolio'], function () {

        /**
         * Portfolios Routes
         */
        Route::group(['prefix' => '/portfolios', 'as' => 'portfolios.'], function () {

            /**
             * Skills Group Routes
             */
            Route::group(['prefix' => '/skills'], function () {

                /**
                 * List skills
                 */
                Route::get('/list', 'PortfolioSkillIndexController@skills');

                /**
                 * List levels
                 */
                Route::get('/levels', 'PortfolioSkillIndexController@levels');
            });

            /**
             * List user's portfolios
             */
            Route::get('/index', 'PortfolioIndexController@index');

            /**
             * Portfolio Group Routes
             */
            Route::group(['prefix' => '/{portfolio}'], function () {

                /**
                 * Skills Resource Routes
                 */
                Route::resource('/skills', 'PortfolioSkillController', [
                    'parameters' => [
                        'skills' => 'portfolioSkill'
                    ]
                ])->only('index', 'store', 'update', 'destroy');

                /**
                 * Uploads Resource Routes
                 */
                Route::resource('/uploads', 'PortfolioUploadController')->only('index', 'store', 'destroy');

                /**
                 * Image Resource Routes
                 */
                Route::resource('/image', 'PortfolioImageController')->only('store', 'destroy');

                /**
                 * Toggle user's portfolio status
                 */
                Route::put('/status', 'PortfolioStatusController@toggleStatus')->name('status.update');

                /**
                 * Store user's portfolio
                 */
                Route::post('/', 'PortfolioController@store')->name('store');
            });
        });

        /**
         * Portfolios Resource Routes
         */
        Route::resource('/portfolios', 'PortfolioController')->except('store', 'show', 'edit');
    });

    /**
     * Companies Resource Routes
     */
    Route::resource('/companies', 'Company\CompanyController', [
        'only' => [
            'index',
            'create',
            'store'
        ]
    ]);

    /**
     * Account
     */
    // account index
    Route::get('/', 'AccountController@index')->name('index');

    /**
     * Profile
     */
    // profile index
    Route::get('/profile', 'ProfileController@index')->name('profile.index');

    // profile update
    Route::post('/profile', 'ProfileController@store')->name('profile.store');

    /**
     * Avatar Namespace Routes
     */
    Route::group(['namespace' => 'Avatar', 'prefix' => '/avatar', 'as' => 'avatar.'], function () {

        /**
         * Avatar Upload
         */
        Route::post('/upload', 'AvatarUploadController@store')->name('upload.store');

        /**
         * Avatar Index
         */
        Route::get('/', 'AvatarController@index')->name('index');

        /**
         * Avatar Update
         */
        Route::post('/', 'AvatarController@store')->name('store');
    });

    /**
     * Password
     */
    // password index
    Route::get('/password', 'PasswordController@index')->name('password.index');

    // password store
    Route::post('/password', 'PasswordController@store')->name('password.store');

    /**
     * Deactivate
     */
    // deactivate account index
    Route::get('/deactivate', 'DeactivateController@index')->name('deactivate.index');

    // deactivate store
    Route::post('/deactivate', 'DeactivateController@store')->name('deactivate.store');

    /**
     * Two factor
     */
    Route::group(['prefix' => '/twofactor', 'as' => 'twofactor.'], function () {
        // two factor index
        Route::get('/', 'TwoFactorController@index')->name('index');

        // two factor store
        Route::post('/', 'TwoFactorController@store')->name('store');

        // two factor verify
        Route::post('/verify', 'TwoFactorController@verify')->name('verify');

        // two factor verify
        Route::delete('/', 'TwoFactorController@destroy')->name('destroy');
    });

    /**
     * Tokens
     */
    Route::group(['prefix' => '/tokens', 'as' => 'tokens.'], function () {
        // personal access token index
        Route::get('/', 'PersonalAccessTokenController@index')->name('index');
    });

    /**
     * Subscription
     */
    Route::group(['prefix' => '/subscription', 'namespace' => 'Subscription',
        'middleware' => ['subscription.owner'], 'as' => 'subscription.'], function () {
        /**
         * Cancel
         *
         * Accessed if there is an active subscription.
         */
        Route::group(['prefix' => '/cancel', 'middleware' => ['subscription.notcancelled'], 'as' => 'cancel.'], function () {
            // cancel subscription index
            Route::get('/', 'SubscriptionCancelController@index')->name('index');

            // cancel subscription
            Route::post('/', 'SubscriptionCancelController@store')->name('store');
        });

        /**
         * Resume
         *
         * Accessed if subscription is cancelled but not expired.
         */
        Route::group(['prefix' => '/resume', 'middleware' => ['subscription.cancelled'], 'as' => 'resume.'], function () {
            // resume subscription index
            Route::get('/', 'SubscriptionResumeController@index')->name('index');

            // resume subscription
            Route::post('/', 'SubscriptionResumeController@store')->name('store');
        });

        /**
         * Swap Subscription
         *
         * Accessed if there is an active subscription.
         */
        Route::group(['prefix' => '/swap', 'middleware' => ['subscription.notcancelled'], 'as' => 'swap.'], function () {
            // swap subscription index
            Route::get('/', 'SubscriptionSwapController@index')->name('index');

            // swap subscription store
            Route::post('/', 'SubscriptionSwapController@store')->name('store');
        });

        /**
         * Card
         */
        Route::group(['prefix' => '/card', 'middleware' => ['subscription.customer'], 'as' => 'card.'], function () {
            // card index
            Route::get('/', 'SubscriptionCardController@index')->name('index');

            // card store
            Route::post('/', 'SubscriptionCardController@store')->name('store');
        });

        /**
         * Team
         */
        Route::group(['prefix' => '/team', 'middleware' => ['subscription.team'], 'as' => 'team.'], function () {
            // team index
            Route::get('/', 'SubscriptionTeamController@index')->name('index');

            // team update
            Route::put('/', 'SubscriptionTeamController@update')->name('update');

            // store team member
            Route::post('/member', 'SubscriptionTeamMemberController@store')->name('member.store');

            // destroy team member
            Route::delete('/member/{user}', 'SubscriptionTeamMemberController@destroy')->name('member.destroy');
        });
    });
});

/**
 * Admin Group Routes
 */
Route::group(['prefix' => '/admin', 'namespace' => 'Admin\Controllers', 'as' => 'admin.'], function () {

    /**
     * Impersonate destroy
     */
    Route::delete('/users/impersonate', 'User\UserImpersonateController@destroy')->name('users.impersonate.destroy');

    /**
     * Admin Role Middleware Routes
     */
    Route::group(['middleware' => ['auth', 'role:admin']], function () {

        // dashboard
        Route::get('/dashboard', 'AdminDashboardController@index')->name('dashboard');

        /**
         * Category Namespace Routes
         */
        Route::group(['namespace' => 'Category'], function () {

            /**
             * Categories Group Routes
             */
            Route::group(['prefix' => '/categories', 'as' => 'categories.'], function () {

                /**
                 * Toggle Category Status Route
                 */
                Route::put('/{category}/status', 'CategoryStatusController@update')->name('status');
            });

            /**
             * Categories Resource Routes
             */
            Route::resource('/categories', 'CategoryController');
        });

        /**
         * Area Namespace Routes
         */
        Route::group(['namespace' => 'Area'], function () {

            /**
             * Areas Group Routes
             */
            Route::group(['prefix' => '/areas', 'as' => 'areas.'], function () {

                /**
                 * Toggle Area Status Route
                 */
                Route::put('/{area}/status', 'AreaStatusController@update')->name('status');
            });

            /**
             * Areas Resource Routes
             */
            Route::resource('/areas', 'AreaController');
        });

        /**
         * Currency Namespace Routes
         */
        Route::group(['namespace' => 'Currency'], function () {

            /**
             * Areas Group Routes
             */
            Route::group(['prefix' => '/currencies', 'as' => 'currencies.'], function () {

                /**
                 * Toggle Currency Status Route
                 */
                Route::put('/{currency}/status', 'CurrencyStatusController@update')->name('status');
            });

            /**
             * Areas Resource Routes
             */
            Route::resource('/currencies', 'CurrencyController');
        });

        /**
         * User Namespace Routes
         */
        Route::group(['namespace' => 'User'], function () {

            /**
             * Users Group Routes
             */
            Route::group(['prefix' => '/users', 'as' => 'users.'], function () {

                /**
                 * User Impersonate Routes
                 */
                Route::group(['prefix' => '/impersonate', 'as' => 'impersonate.'], function () {
                    // index
                    Route::get('/', 'UserImpersonateController@index')->name('index');

                    // store
                    Route::post('/', 'UserImpersonateController@store')->name('store');
                });


                /**
                 * User Group Routes
                 */
                Route::group(['prefix' => '/{user}'], function () {
                    Route::resource('/roles', 'UserRoleController', [
                        'except' => [
                            'show',
                            'edit',
                        ]
                    ]);
                });
            });

            /**
             * Permissions Group Routes
             */
            Route::group(['prefix' => '/permissions', 'as' => 'permissions.'], function () {

                /**
                 * Role Group Routes
                 */
                Route::group(['prefix' => '/{permission}'], function () {

                    // toggle status
                    Route::put('/status', 'PermissionStatusController@toggleStatus')->name('toggleStatus');
                });
            });

            /**
             * Permissions Resource Routes
             */
            Route::resource('/permissions', 'PermissionController');

            /**
             * Roles Group Routes
             */
            Route::group(['prefix' => '/roles', 'as' => 'roles.'], function () {

                /**
                 * Role Group Routes
                 */
                Route::group(['prefix' => '/{role}'], function () {

                    // toggle status
                    Route::put('/status', 'RoleStatusController@toggleStatus')->name('toggleStatus');

                    // revoke users access
                    Route::put('/revoke', 'RoleUsersDisableController@revokeUsersAccess')->name('revokeUsersAccess');

                    /**
                     * Permissions Resource Routes
                     */
                    Route::resource('/permissions', 'RolePermissionController', [
                        'only' => [
                            'index',
                            'store',
                            'destroy',
                        ]
                    ]);
                });
            });

            /**
             * Roles Resource Routes
             */
            Route::resource('/roles', 'RoleController');

            /**
             * Users Resource Routes
             */
            Route::resource('/users', 'UserController');
        });
    });
});

/**
 * Webhooks Routes
 */
Route::group(['namespace' => 'Webhook\Controllers'], function () {

    // Stripe Webhook
    Route::post('/webhooks/stripe', 'StripeWebhookController@handleWebhook');
});