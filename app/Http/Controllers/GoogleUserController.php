<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Mail\Hello;
use Mail;

class GoogleUserController extends Controller
{
    //
    public function login(Request $request) {
        $id_token = $request->input('token');

        $client_id = config('services.google.client_id');
        $client_secret = config('services.google.client_secret');
        $api_key = config('services.google.api_key');
        
        $redirectUri = route('GoogleUser');
        
        $guzzleClient = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));

        
        $gclient = new \Google_Client();
        $gclient->setApplicationName("Laravel Google Sign-In");
        $gclient->setAccessType('online');
        $gclient->setClientId("14473722966-gdnbfrr17vg2lobfgedqmab6cfs6c5mn.apps.googleusercontent.com");
        $gclient->setClientSecret("LFBaUznPqD__zP95KKFLqP0P");
        $gclient->setRedirectUri($redirectUri);
        $gclient->setDeveloperKey("AIzaSyBH21fesMqYWoeh1bBZmn7iIk46TXC5uZ8");
        $gclient->setScopes(array(
            'https://www.googleapis.com/auth/plus.me',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
            'https://www.googleapis.com/auth/gmail.readonly',
            'https://www.googleapis.com/auth/gmail.compose',
            'https://www.googleapis.com/auth/gmail.modify',
            'https://www.google.com/m8/feeds',
            'https://www.googleapis.com/auth/contacts.readonly'
        ));
        $httpClient = $gclient->setHttpClient($guzzleClient);
        

        //Generate Service Instance
        $google_oauthV2 = new \Google_Service_Oauth2($gclient);
        
        if ($request->get('code')){
            //Request the AccessToken by passing authorization code
            $gclient->authenticate($request->get('code'));
            
            $request->session()->put('token', $gclient->getAccessToken());
            
        }
        
        $gmail_service = new \Google_Service_Gmail($gclient);
        
        if ($request->session()->get('token'))
        {
            $gclient->setAccessToken($request->session()->get('token'));
        }
        if ($gclient->getAccessToken())
        {
            //For logged in user, get details from google using access token
            $guser = $google_oauthV2->userinfo->get();  
            
                
            $request->session()->put('name', $guser['name']);
            if ($user =User::where('email',$guser['email'])->first())
            {
                //logged your user via auth login
                Auth::login($user);
            }else{
                //register your user with response data
            }

            // Fetch EMail Data by OAuth Access_Token 
            // Use Google_Client - GoogleClient API
            // Use GMail API 
            // $service = new /Google_Service_Gmail($gclient); - $gclient: Google_Client Instance



            // $pageToken = NULL;
            // $messages = array();
            // $opt_param = array();
            // do {
            //     try {
            //         if ($pageToken) {
            //             $opt_param['pageToken'] = $pageToken;
            //             $opt_param['maxResults'] = 5; // Return Only 5 Messages
            //             $opt_param['labelIds'] = 'INBOX';
            //         }
            //         $messagesResponse = $gmail_service->users_messages->listUsersMessages($guser['email'], $opt_param);
            //         if ($messagesResponse->getMessages()) {

            //             $pageToken = $messagesResponse->getNextPageToken();
            //             for ($i=0; $i < count($messagesResponse->getMessages()); $i++) { 

            //                 $messageId = $messagesResponse[$i]->getId(); // Grab first Message

            //                 $optParamsGet = [];
            //                 $optParamsGet['format'] = 'full'; // Display message in payload
            //                 $message = $gmail_service->users_messages->get('me',$messageId,$optParamsGet);
            //                 $messagePayload = $message->getPayload();
            //                 $headers = $message->getPayload()->getHeaders();
            //                 $parts = $message->getPayload()->getParts();
                            

            //                 $body = $parts[0]['body'];
                            
            //                 $rawData = $body->data;
            //                 $sanitizedData = strtr($rawData,'-_', '+/');
            //                 $decodedMessage = base64_decode($sanitizedData);
                            
            //                 array_push($messages,$decodedMessage);
            //             }
                        
            //         }
            //     } catch (Exception $e) {
            //         print 'An error occurred: ' . $e->getMessage();
            //     }
            // } while ($pageToken);

            // var_dump($messages);exit;

            // var_dump($gclient->getAccessToken());exit;
            $id_token = $gclient->getAccessToken();
            $google_service = new \Google_Service_people($gclient);
            

            $optParams = array(
                'requestMask.includeField' => "person.names,person.emailAddresses"
            );

            $response = $google_service->people_connections->listPeopleConnections('people/me', $optParams );

            $keys = array();
            foreach ($response->connections  as  $value) {
                
                $emailAddress = $value['emailAddresses'][0]['value'];
                $name = $value['names'][0]['displayName'];
                $container = array(
                    'email' => $emailAddress,
                    'name' => $name
                );
            
                // \Mail::send(new Hello,['text' => 'You have just sent the Emall'],function($message) use ($container) {
                    
                //     $message->to($container['email'],$container['name'])->subject('User has been approved');
                //     $message->from("mike.lee881123@gmail.com",'Michael Lee');
                    
                // });
                \Mail::to($container['email'],$container['name'])->send(new Hello($name));
                array_push($keys,$emailAddress,$name);
            }

            return redirect()->route('user.glist');          
        } else
        {
            //For Guest user, get google login url
            $authUrl = $gclient->createAuthUrl();
            return redirect()->to($authUrl);
        }
    }


}
