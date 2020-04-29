<?php

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]
    ], function() {
    /*
     * Include our API routes file
     */
    include_once('api_routes.php');

    /*
     * -------------------------
     * Installer
     * -------------------------
     */
    Route::get('install', [
        'as'   => 'showInstaller',
        'uses' => 'InstallerController@showInstaller',
    ]);

    Route::post('install', [
        'as'   => 'postInstaller',
        'uses' => 'InstallerController@postInstaller',
    ]);

    /*
     * Stripe connect return
     */
    Route::any('payment/return/stripe', [
        'as'   => 'showStripeReturn',
        'uses' => 'ManageAccountController@showStripeReturn',
    ]);

    /*
     * Logout
     */
    Route::any('/logout', [
        'uses' => 'UserLogoutController@doLogout',
        'as'   => 'logout',
    ]);

    Route::group(['middleware' => ['installed']], function () {

        /*
         * Login
         */
        Route::get('/login', [
            'as'   => 'login',
            'uses' => 'UserLoginController@showLogin',
        ]);
        Route::post('/login', 'UserLoginController@postLogin');

        /*
         * Forgot password
         */
        Route::get('login/forgot-password', [
            'as'   => 'forgotPassword',
            'uses' => 'RemindersController@getRemind',
        ]);

        Route::post('login/forgot-password', [
            'as'   => 'postForgotPassword',
            'uses' => 'RemindersController@postRemind',
        ]);

        /*
         * Reset Password
         */
        Route::get('login/reset-password/{token}', [
            'uses' => 'RemindersController@getReset',
        ])->name('password.reset');

        Route::post('login/reset-password', [
            'as'   => 'postResetPassword',
            'uses' => 'RemindersController@postReset',
        ]);

        /*
         * Registration / Account creation
         */
        Route::get('/signup', [
            'uses' => 'UserSignupController@showSignup',
            'as'   => 'showSignup',
        ]);
        Route::post('/signup', 'UserSignupController@postSignup');

        /*
         * Confirm Email
         */
        Route::get('signup/confirm_email/{confirmation_code}', [
            'as'   => 'confirmEmail',
            'uses' => 'UserSignupController@confirmEmail',
        ]);
    });

    /*
     * Public organiser page routes
     */
    Route::group(['prefix' => 'o'], function () {

        Route::get('/{organiser_id}/{organier_slug?}', [
            'as'   => 'showOrganiserHome',
            'uses' => 'OrganiserViewController@showOrganiserHome',
        ]);

    });

    /*
     * Public event page routes
     */
    Route::group(['prefix' => 'e'], function () {

        /*
         * Embedded events
         */
        Route::get('/{event_id}/embed', [
            'as'   => 'showEmbeddedEventPage',
            'uses' => 'EventViewEmbeddedController@showEmbeddedEvent',
        ]);

        Route::get('/{event_id}/calendar.ics', [
            'as'   => 'downloadCalendarIcs',
            'uses' => 'EventViewController@showCalendarIcs',
        ]);

        Route::get('/{event_id}/{event_slug?}', [
            'as'   => 'showEventPage',
            'uses' => 'EventViewController@showEventHome',
        ]);

        Route::post('/{event_id}/contact_organiser', [
            'as'   => 'postContactOrganiser',
            'uses' => 'EventViewController@postContactOrganiser',
        ]);

        Route::post('/{event_id}/show_hidden', [
            'as'   => 'postShowHiddenTickets',
            'uses' => 'EventViewController@postShowHiddenTickets',
        ]);

        /*
         * Used for previewing designs in the backend. Doesn't log page views etc.
         */
        Route::get('/{event_id}/preview', [
            'as'   => 'showEventPagePreview',
            'uses' => 'EventViewController@showEventHomePreview',
        ]);

        Route::post('{event_id}/checkout/', [
            'as'   => 'postValidateTickets',
            'uses' => 'EventCheckoutController@postValidateTickets',
        ]);

        Route::post('{event_id}/checkout/validate', [
            'as'   => 'postValidateOrder',
            'uses' => 'EventCheckoutController@postValidateOrder',
        ]);

        Route::get('{event_id}/checkout/payment', [
            'as'   => 'showEventPayment',
            'uses' => 'EventCheckoutController@showEventPayment',
        ]);

        Route::get('{event_id}/checkout/create', [
            'as'   => 'showEventCheckout',
            'uses' => 'EventCheckoutController@showEventCheckout',
        ]);

        Route::get('{event_id}/checkout/success', [
            'as'   => 'showEventCheckoutPaymentReturn',
            'uses' => 'EventCheckoutController@showEventCheckoutPaymentReturn',
        ]);

        Route::post('{event_id}/checkout/create', [
            'as'   => 'postCreateOrder',
            'uses' => 'EventCheckoutController@postCreateOrder',
        ]);
    });

    /*
     * Public view order routes
     */
    Route::get('order/{order_reference}', [
        'as'   => 'showOrderDetails',
        'uses' => 'EventCheckoutController@showOrderDetails',
    ]);

    Route::get('order/{order_reference}/tickets', [
        'as'   => 'showOrderTickets',
        'uses' => 'EventCheckoutController@showOrderTickets',
    ]);

    /*
     * Backend routes
     */
    Route::group(['middleware' => ['auth', 'first.run']], function () {

        /*
         * Edit User
         */
        Route::group(['prefix' => 'user'], function () {

            Route::get('/', [
                'as'   => 'showEditUser',
                'uses' => 'UserController@showEditUser',
            ]);
            Route::post('/', [
                'as'   => 'postEditUser',
                'uses' => 'UserController@postEditUser',
            ]);

        });

        /*
         * Manage account
         */
        Route::group(['prefix' => 'account'], function () {

            Route::get('/', [
                'as'   => 'showEditAccount',
                'uses' => 'ManageAccountController@showEditAccount',
            ]);

            Route::post('/', [
                'as'   => 'postEditAccount',
                'uses' => 'ManageAccountController@postEditAccount',
            ]);
            Route::post('/edit_payment', [
                'as'   => 'postEditAccountPayment',
                'uses' => 'ManageAccountController@postEditAccountPayment',
            ]);

            Route::post('invite_user', [
                'as'   => 'postInviteUser',
                'uses' => 'ManageAccountController@postInviteUser',
            ]);

        });

        Route::get('select_organiser', [
            'as'   => 'showSelectOrganiser',
            'uses' => 'OrganiserController@showSelectOrganiser',
        ]);

        /*
         * Organiser routes
         */
        Route::group(['prefix' => 'organiser'], function () {

            Route::get('{organiser_id}/dashboard', [
                'as'   => 'showOrganiserDashboard',
                'uses' => 'OrganiserDashboardController@showDashboard',
            ]);
            Route::get('{organiser_id}/events', [
                'as'   => 'showOrganiserEvents',
                'uses' => 'OrganiserEventsController@showEvents',
            ]);

            Route::get('{organiser_id}/customize', [
                'as'   => 'showOrganiserCustomize',
                'uses' => 'OrganiserCustomizeController@showCustomize',
            ]);
            Route::post('{organiser_id}/customize', [
                'as'   => 'postEditOrganiser',
                'uses' => 'OrganiserCustomizeController@postEditOrganiser',
            ]);

            Route::get('create', [
                'as'   => 'showCreateOrganiser',
                'uses' => 'OrganiserController@showCreateOrganiser',
            ]);
            Route::post('create', [
                'as'   => 'postCreateOrganiser',
                'uses' => 'OrganiserController@postCreateOrganiser',
            ]);

            Route::post('{organiser_id}/page_design', [
                'as'   => 'postEditOrganiserPageDesign',
                'uses' => 'OrganiserCustomizeController@postEditOrganiserPageDesign'
            ]);
        });

        /*
         * Events dashboard
         */
        Route::group(['prefix' => 'events'], function () {

            /*
             * ----------
             * Create Event
             * ----------
             */
            Route::get('/create', [
                'as'   => 'showCreateEvent',
                'uses' => 'EventController@showCreateEvent',
            ]);

            Route::post('/create', [
                'as'   => 'postCreateEvent',
                'uses' => 'EventController@postCreateEvent',
            ]);

	     Route::get('{event_id}/archive', [
                'as'   => 'archiveEvent',
                'uses' => 'EventController@archiveEvent',
            ]);

            Route::get('{event_id}/restore', [
                 'as'   => 'restoreEvent',
                 'uses' => 'EventController@restoreEvent',
            ]);

	     Route::get('{event_id}/duplicate', [
		   'as'   => 'duplicateEvent',
                 'uses' => 'EventController@CreateCloneEvent',
	     ]);



        });

        /*
         * Upload event images
         */
        Route::post('/upload_image', [
            'as'   => 'postUploadEventImage',
            'uses' => 'EventController@postUploadEventImage',
        ]);

        /*
         * Event management routes
         */
        Route::group(['prefix' => 'event'], function () {

            /*
             * Dashboard
             */
            Route::get('{event_id}/dashboard/', [
                    'as'   => 'showEventDashboard',
                    'uses' => 'EventDashboardController@showDashboard',
                ]
            );

            Route::get('{event_id}/', [
                    'uses' => 'EventDashboardController@redirectToDashboard',
                ]
            );

            /*
             * @todo Move to a controller
             */
             Route::get('{event_id}/go_live', [
                'as'   => 'MakeEventLive',
                'uses' => 'EventController@makeEventLive',
            ]);

            /*
             * -------
             * Tickets
             * -------
             */
            Route::get('{event_id}/tickets/', [
                'as'   => 'showEventTickets',
                'uses' => 'EventTicketsController@showTickets',
            ]);
            Route::get('{event_id}/tickets/edit/{ticket_id}', [
                'as'   => 'showEditTicket',
                'uses' => 'EventTicketsController@showEditTicket',
            ]);
            Route::post('{event_id}/tickets/edit/{ticket_id}', [
                'as'   => 'postEditTicket',
                'uses' => 'EventTicketsController@postEditTicket',
            ]);
            Route::get('{event_id}/tickets/create', [
                'as'   => 'showCreateTicket',
                'uses' => 'EventTicketsController@showCreateTicket',
            ]);
            Route::post('{event_id}/tickets/create', [
                'as'   => 'postCreateTicket',
                'uses' => 'EventTicketsController@postCreateTicket',
            ]);
            Route::post('{event_id}/tickets/delete', [
                'as'   => 'postDeleteTicket',
                'uses' => 'EventTicketsController@postDeleteTicket',
            ]);
            Route::post('{event_id}/tickets/pause', [
                'as'   => 'postPauseTicket',
                'uses' => 'EventTicketsController@postPauseTicket',
            ]);
            Route::post('{event_id}/tickets/order', [
                'as'   => 'postUpdateTicketsOrder',
                'uses' => 'EventTicketsController@postUpdateTicketsOrder',
            ]);

            /*
             * -------
             * Attendees
             * -------
             */
            Route::get('{event_id}/attendees/', [
                'as'   => 'showEventAttendees',
                'uses' => 'EventAttendeesController@showAttendees',
            ]);

            Route::get('{event_id}/attendees/message', [
                'as'   => 'showMessageAttendees',
                'uses' => 'EventAttendeesController@showMessageAttendees',
            ]);

            Route::post('{event_id}/attendees/message', [
                'as'   => 'postMessageAttendees',
                'uses' => 'EventAttendeesController@postMessageAttendees',
            ]);

            Route::get('{event_id}/attendees/single_message', [
                'as'   => 'showMessageAttendee',
                'uses' => 'EventAttendeesController@showMessageAttendee',
            ]);

            Route::post('{event_id}/attendees/single_message', [
                'as'   => 'postMessageAttendee',
                'uses' => 'EventAttendeesController@postMessageAttendee',
            ]);

            Route::get('{event_id}/attendees/resend_ticket', [
                'as'   => 'showResendTicketToAttendee',
                'uses' => 'EventAttendeesController@showResendTicketToAttendee',
            ]);

            Route::post('{event_id}/attendees/resend_ticket', [
                'as'   => 'postResendTicketToAttendee',
                'uses' => 'EventAttendeesController@postResendTicketToAttendee',
            ]);

            Route::get('{event_id}/attendees/invite', [
                'as'   => 'showInviteAttendee',
                'uses' => 'EventAttendeesController@showInviteAttendee',
            ]);

            Route::post('{event_id}/attendees/invite', [
                'as'   => 'postInviteAttendee',
                'uses' => 'EventAttendeesController@postInviteAttendee',
            ]);

            Route::get('{event_id}/attendees/import', [
                'as'   => 'showImportAttendee',
                'uses' => 'EventAttendeesController@showImportAttendee',
            ]);

            Route::post('{event_id}/attendees/import', [
                'as'   => 'postImportAttendee',
                'uses' => 'EventAttendeesController@postImportAttendee',
            ]);

            Route::get('{event_id}/attendees/print', [
                'as'   => 'showPrintAttendees',
                'uses' => 'EventAttendeesController@showPrintAttendees',
            ]);

            Route::get('{event_id}/attendees/{attendee_id}/export_ticket', [
                'as'   => 'showExportTicket',
                'uses' => 'EventAttendeesController@showExportTicket',
            ]);

            Route::get('{event_id}/attendees/{attendee_id}/ticket', [
                'as'   => 'showAttendeeTicket',
                'uses' => 'EventAttendeesController@showAttendeeTicket',
            ]);

            Route::get('{event_id}/attendees/export/{export_as?}', [
                'as'   => 'showExportAttendees',
                'uses' => 'EventAttendeesController@showExportAttendees',
            ]);

            Route::get('{event_id}/attendees/{attendee_id}/edit', [
                'as'   => 'showEditAttendee',
                'uses' => 'EventAttendeesController@showEditAttendee',
            ]);
            Route::post('{event_id}/attendees/{attendee_id}/edit', [
                'as'   => 'postEditAttendee',
                'uses' => 'EventAttendeesController@postEditAttendee',
            ]);

            Route::get('{event_id}/attendees/{attendee_id}/cancel', [
                'as'   => 'showCancelAttendee',
                'uses' => 'EventAttendeesController@showCancelAttendee',
            ]);
            Route::post('{event_id}/attendees/{attendee_id}/cancel', [
                'as'   => 'postCancelAttendee',
                'uses' => 'EventAttendeesController@postCancelAttendee',
            ]);

            /*
             * -------
             * Orders
             * -------
             */
            Route::get('{event_id}/orders/', [
                'as'   => 'showEventOrders',
                'uses' => 'EventOrdersController@showOrders',
            ]);

            Route::get('order/{order_id}', [
                'as'   => 'showManageOrder',
                'uses' => 'EventOrdersController@manageOrder',
            ]);

            Route::post('order/{order_id}/resend', [
                'as' => 'resendOrder',
                'uses' => 'EventOrdersController@resendOrder',
            ]);

            Route::get('order/{order_id}/show/edit', [
                'as' => 'showEditOrder',
                'uses' => 'EventOrdersController@showEditOrder',
            ]);

            Route::post('order/{order_id}/edit', [
                'as' => 'postOrderEdit',
                'uses' => 'EventOrdersController@postEditOrder',
            ]);

            Route::get('order/{order_id}/cancel', [
                'as'   => 'showCancelOrder',
                'uses' => 'EventOrdersController@showCancelOrder',
            ]);

            Route::post('order/{order_id}/cancel', [
                'as'   => 'postCancelOrder',
                'uses' => 'EventOrdersController@postCancelOrder',
            ]);

            Route::post('order/{order_id}/mark_payment_received', [
                'as'   => 'postMarkPaymentReceived',
                'uses' => 'EventOrdersController@postMarkPaymentReceived',
            ]);

            Route::get('{event_id}/orders/export/{export_as?}', [
                'as'   => 'showExportOrders',
                'uses' => 'EventOrdersController@showExportOrders',
            ]);
            Route::get('{event_id}/orders/message', [
                'as'   => 'showMessageOrder',
                'uses' => 'EventOrdersController@showMessageOrder',
            ]);

            Route::post('{event_id}/orders/message', [
                'as'   => 'postMessageOrder',
                'uses' => 'EventOrdersController@postMessageOrder',
            ]);

            /*
             * -------
             * Edit Event
             * -------
             */
            Route::post('{event_id}/customize', [
                'as'   => 'postEditEvent',
                'uses' => 'EventController@postEditEvent',
            ]);

            /*
             * -------
             * Customize Design etc.
             * -------
             */
            Route::get('{event_id}/customize', [
                'as'   => 'showEventCustomize',
                'uses' => 'EventCustomizeController@showCustomize',
            ]);
            Route::get('{event_id}/customize/{tab?}', [
                'as'   => 'showEventCustomizeTab',
                'uses' => 'EventCustomizeController@showCustomize',
            ]);
            Route::post('{event_id}/customize/order_page', [
                'as'   => 'postEditEventOrderPage',
                'uses' => 'EventCustomizeController@postEditEventOrderPage',
            ]);
            Route::post('{event_id}/customize/design', [
                'as'   => 'postEditEventDesign',
                'uses' => 'EventCustomizeController@postEditEventDesign',
            ]);
            Route::post('{event_id}/customize/ticket_design', [
                'as'   => 'postEditEventTicketDesign',
                'uses' => 'EventCustomizeController@postEditEventTicketDesign',
            ]);
            Route::post('{event_id}/customize/social', [
                'as'   => 'postEditEventSocial',
                'uses' => 'EventCustomizeController@postEditEventSocial',
            ]);
            Route::post('{event_id}/customize/fees', [
                'as'   => 'postEditEventFees',
                'uses' => 'EventCustomizeController@postEditEventFees',
            ]);

            /*
             * -------
             * Event Widget page
             * -------
             */
            Route::get('{event_id}/widgets', [
                'as'   => 'showEventWidgets',
                'uses' => 'EventWidgetsController@showEventWidgets',
            ]);

            /*
             * -------
             * Event Access Codes page
             * -------
             */
            Route::get('{event_id}/access_codes', [
                'as'   => 'showEventAccessCodes',
                'uses' => 'EventAccessCodesController@show',
            ]);

            Route::get('{event_id}/access_codes/create', [
                'as' => 'showCreateEventAccessCode',
                'uses' => 'EventAccessCodesController@showCreate',
            ]);

            Route::post('{event_id}/access_codes/create', [
                'as' => 'postCreateEventAccessCode',
                'uses' => 'EventAccessCodesController@postCreate',
            ]);

            Route::post('{event_id}/access_codes/{access_code_id}/delete', [
                'as' => 'postDeleteEventAccessCode',
                'uses' => 'EventAccessCodesController@postDelete',
            ]);

            /*
             * -------
             * Event Survey page
             * -------
             */
            Route::get('{event_id}/surveys', [
                'as'   => 'showEventSurveys',
                'uses' => 'EventSurveyController@showEventSurveys',
            ]);
            Route::get('{event_id}/question/create', [
                'as'   => 'showCreateEventQuestion',
                'uses' => 'EventSurveyController@showCreateEventQuestion'
            ]);

            Route::post('{event_id}/question/create', [
                'as'   => 'postCreateEventQuestion',
                'uses' => 'EventSurveyController@postCreateEventQuestion'
            ]);


            Route::get('{event_id}/question/{question_id}', [
                'as'   => 'showEditEventQuestion',
                'uses' => 'EventSurveyController@showEditEventQuestion'
            ]);

            Route::post('{event_id}/question/{question_id}', [
                'as'   => 'postEditEventQuestion',
                'uses' => 'EventSurveyController@postEditEventQuestion'
            ]);

            Route::post('{event_id}/question/delete/{question_id}', [
                'as'   => 'postDeleteEventQuestion',
                'uses' => 'EventSurveyController@postDeleteEventQuestion'
            ]);

            Route::get('{event_id}/question/{question_id}/answers', [
                'as'   => 'showEventQuestionAnswers',
                'uses' => 'EventSurveyController@showEventQuestionAnswers',
            ]);

            Route::post('{event_id}/questions/update_order', [
                'as'   => 'postUpdateQuestionsOrder',
                'uses' => 'EventSurveyController@postUpdateQuestionsOrder'
            ]);

            Route::get('{event_id}/answers/export/{export_as?}', [
                'as'   => 'showExportAnswers',
                'uses' => 'EventSurveyController@showExportAnswers',
            ]);

            Route::post('{event_id}/question/{question_id}/enable', [
                'as'   => 'postEnableQuestion',
                'uses' => 'EventSurveyController@postEnableQuestion',
            ]);


            /*
             * -------
             * Check In App
             * -------
             */
            Route::get('{event_id}/check_in', [
                'as'   => 'showCheckIn',
                'uses' => 'EventCheckInController@showCheckIn',
            ]);
            Route::post('{event_id}/check_in/search', [
                'as'   => 'postCheckInSearch',
                'uses' => 'EventCheckInController@postCheckInSearch',
            ]);
            Route::post('{event_id}/check_in/', [
                'as'   => 'postCheckInAttendee',
                'uses' => 'EventCheckInController@postCheckInAttendee',
            ]);

            Route::post('{event_id}/qrcode_check_in', [
                'as'   => 'postQRCodeCheckInAttendee',
                'uses' => 'EventCheckInController@postCheckInAttendeeQr',
            ]);

            Route::post('{event_id}/confirm_order_tickets/{order_id}', [
                'as'   => 'confirmCheckInOrderTickets',
                'uses' => 'EventCheckInController@confirmOrderTicketsQr',
            ]);


            /*
             * -------
             * Promote
             * -------
             */
            Route::get('{event_id}/promote', [
                'as'   => 'showEventPromote',
                'uses' => 'EventPromoteController@showPromote',
            ]);
        });
    });

    Route::get('/', [
        'as'   => 'index',
        'uses' => 'IndexController@showIndex',
    ]);
});
