<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb combination of simplicity, elegance, and innovation give you tools you need to build any application with which you are tasked.

## Learning Laravel

Laravel has the most extensive and thorough documentation and video tutorial library of any modern web application framework. The [Laravel documentation](https://laravel.com/docs) is thorough, complete, and makes it a breeze to get started learning the framework.

If you're not in the mood to read, [Laracasts](https://laracasts.com) contains over 900 video tutorials on a range of topics including Laravel, modern PHP, unit testing, JavaScript, and more. Boost the skill level of yourself and your entire team by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for helping fund on-going Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](http://patreon.com/taylorotwell):

- **[Vehikl](http://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[British Software Development](https://www.britishsoftware.co)**
- **[Styde](https://styde.net)**
- [Fragrantica](https://www.fragrantica.com)
- [SOFTonSOFA](https://softonsofa.com/)

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

**Google Authentication using OAuth2 for server-side-app**

Pre-Requirements to authenticate using OAuth
    - Client_Id,Client_Secret,API_KEY
To Get the above, in Google API Console,
    Create New Google Project in drop-down menu 
    And In Credentials, 
        create new OAuth Client Id
            At this time, Authorized Redirect Url is same as the redirect Url configured in the code when create the Google_Client Instance
        create new API Key
        Clip the Client_id,Client_Secret,API Key by clicking OAuth ClientId and API Key links.
    And Library window
        APIs to use get enabled.
1. Dependency - google\api-client injection
    By this command: composer require google\api-client: "^2.0"

2. Add Route like this:
    Route::get('GoogleUser',array('as' => 'GoogleUser', 'uses' => 'GoogleUserController@login'));
    Route::post('/UserList',array('as' => 'UserList', 'uses' => 'GoogleUserController@userlist'));
3. Create Controller -> GoogleUserController
    1) create function called login
        Generate new Google_Client Instance for authorization
        At this point, set Redirect Url same as Authroized Redirect Url in the Google API Console - OAuth Client ID
        Scope: limited Which APIs this application have to use.
        After Authorization with authenticate(), get resource by passing access token.
    2) create listGoogleUser
        Fetch User List and Display Users Info in the table
4. Create View Template -> users/list.blade.php

    ** This app has two btn - Log in With Google and Sign in with Google
        the first is genereated by following the tutorial and the second is cutomized by me with tutorial reference.

**This app get the Users's Google Contract and Send Email to them**
    - Fetch the User's Google Contract
        Use Google_Service_People Service/people.connections property/listPeopleConnections method.
            At this point, optParams must contain requestMask.includeField or personFields,but I recommend plz userequestMask.includeField and I used it in this app.
            And this method returns object, so you must use foreach ($response->connections  as  $value) to fetch person's info - name and email,...
    - Send Email to User Contact members
        I use Mail Facade Laravel offered.
        This app use gmail host. Before send mail, you must configure .env file and some Google Account settings.

        1. Google Account Configuration
            In myaccount.google.com page, plz allow lesssecureapp.
            And GMail Settings, plz Enable POS and IMAP setting.  
        2. Configure .env File
            MAIL_DRIVER=smtp
            MAIL_HOST=smtp.gmail.com
            MAIL_PORT=587
            MAIL_USERNAME=your gmail address
            MAIL_PASSWORD=your gmail pass(not app pass)
            MAIL_ENCRYPTION=tls
        3. Create Mail
            By this command, php artisan make:mail Home --markdown="MAIL TEMPLATE URL LIKE(emails.hello)"
            This command will generate mailable class and view template
            In mail Home class/build(), customize return value like this: return $this->from('SENDER GMAIL ADDRESS')->markdown('emails.hello');
        4. Send Mail
            Using Mail Facade, I sent gmail.
            
