<?php

class Facebook {

    private $FB_APP_ID;
    private $FB_APP_SECRET;
    private $whatsApp;
    private $facebook;

    public function __construct() {
        $this->FB_APP_ID = FB_APP_ID;
        $this->FB_APP_SECRET = FB_APP_SECRET;
        $this->whatsApp = new GupshupAPI();

        $this->facebook = new \Facebook\Facebook([
            'app_id' => FB_APP_ID,
            'app_secret' => FB_APP_SECRET,
            'default_graph_version' => 'v22.0',
            'persistent_data_handler' => 'session' 
        ]);
    }

    public function getLoginUrl($redirectUrl) {
        
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        $helper = $this->facebook->getRedirectLoginHelper();

        unset($_SESSION['FBRLH_state']);
        
        $permissions = [
            'business_management', 
            'email', 
            'public_profile',
            'pages_show_list',
            'pages_read_engagement',
            'business_management'
        ];
        
        $loginUrl = $helper->getLoginUrl($redirectUrl, $permissions);
        return $loginUrl;
    }

    public function handleCallback() {
        
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        $helper = $this->facebook->getRedirectLoginHelper();

        if (isset($_GET['state'])) {
            $_SESSION['FBRLH_state'] = $_GET['state'];
        }
       
        try {
            $accessToken = $helper->getAccessToken();
            
            if (!$accessToken) {
                return [
                    'success' => false,
                    'message' => 'Failed to get access token'
                ];
            }
            
            $_SESSION['fb_access_token'] = (string) $accessToken;
            
            $debugTokenResponse = $this->facebook->get(
                '/debug_token?input_token=' . $accessToken,
                $this->FB_APP_ID . '|' . $this->FB_APP_SECRET
            );
            $tokenMetadata = $debugTokenResponse->getGraphNode();

            error_log('Token metadata: ' . print_r($tokenMetadata->asArray(), true));
            
            $response = $this->facebook->get('/me?fields=id,name,email', $accessToken);
            $user = $response->getGraphUser();
            
            try {
                $businessResponse = $this->facebook->get('/me/businesses', $accessToken);
                $businesses = $businessResponse->getGraphEdge()->asArray();
            } catch (\Exception $e) {
                error_log('First business fetch failed: ' . $e->getMessage());
                $businesses = [];
            }
            
            if (empty($businesses)) {
                return [
                    'success' => false,
                    'message' => 'No Facebook business accounts found. Please make sure: 1. You have a Facebook Business account associated with your profile 2. You\'ve granted all the necessary permissions 3. You are an admin of the Business account',
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'] ?? null
                    ],
                    // 'token_info' => $tokenMetadata->asArray()
                ];
            }
            
            return [
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'] ?? null
                ],
                'businesses' => $businesses,
                // 'access_token' => (string) $accessToken,
                // 'token_info' => $tokenMetadata->asArray()
            ];
            
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            error_log('Graph API Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Graph returned an error: ' . $e->getMessage()
            ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            error_log('Facebook SDK Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Facebook SDK returned an error: ' . $e->getMessage()
            ];
        } catch(\Exception $e) {
            error_log('General Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }

    public function completeWhatsAppIntegration($userId, $businessId) {

        if (!isset($_SESSION['fb_access_token'])) {
            return [
                'success' => false,
                'message' => 'Facebook authentication required'
            ];
        }
        
        $accessToken = $_SESSION['fb_access_token'];
        
        // Verify business account
        $verificationResult = $this->verifyFacebookBusinessAccount($accessToken, $businessId);
        
        if (!$verificationResult['success']) {
            return $verificationResult;
        }
        
        $onboardingResult = $this->whatsApp->onboardUserToWhatsApp(
            $userId,
            $verificationResult['business']
        );
        
        if ($onboardingResult['status'] === 'success') {
            return [
                'success' => true,
                'message' => 'WhatsApp integration successful',
                'appId' => $onboardingResult['appId'],
                'details' => $onboardingResult['details']
            ];
        } else {
            return [
                'success' => false,
                'message' => $onboardingResult['message'],
                'error' => $onboardingResult['error'] ?? null
            ];
        }
    }

    public function verifyFacebookBusinessAccount($accessToken, $businessData) {
        
        $tokenInfo = $this->verifyAccessToken($accessToken);
        
        if (!$tokenInfo['verified']) {
            return [
                'verified' => false,
                'reason' => 'Invalid Facebook access token'
            ];
        }
        
        if (empty($businessData['data'])) {
            return [
                'verified' => false,
                'reason' => 'No Facebook business accounts found'
            ];
        }
        
        $selectedBusiness = $businessData['data'][0];
        $businessId = $selectedBusiness['id'];
        
        $businessDetails = $this->getBusinessDetails($businessId, $accessToken);
        
        if (empty($businessDetails)) {
            return [
                'verified' => false,
                'reason' => 'Failed to fetch business account details'
            ];
        }
        
        if (!$businessDetails['verification_status'] || $businessDetails['verification_status'] !== 'verified') {
            return [
                'verified' => false,
                'reason' => 'Facebook business account is not verified'
            ];
        }
        
        return [
            'verified' => true,
            'businessDetails' => [
                'businessId' => $businessId,
                'businessName' => $businessDetails['name'],
                'contactName' => $businessDetails['primary_page']['name'] ?? $businessDetails['name'],
                'contactEmail' => $businessDetails['primary_page']['emails'][0] ?? '',
                'contactPhone' => $businessDetails['primary_page']['phone'] ?? '',
                'countryCode' => $businessDetails['primary_page']['location']['country'] ?? 'US'
            ]
        ];
    }

    private function verifyAccessToken($accessToken) {
        $url = "https://graph.facebook.com/debug_token?input_token={$accessToken}&access_token={$this->FB_APP_ID}|{$this->FB_APP_SECRET}";
        
        $response = file_get_contents($url);
        $tokenInfo = json_decode($response, true);
        
        if (isset($tokenInfo['data']) && isset($tokenInfo['data']['is_valid']) && $tokenInfo['data']['is_valid']) {
            return [
                'verified' => true,
                'userId' => $tokenInfo['data']['user_id']
            ];
        }
        
        return [
            'verified' => false
        ];
    }

    private function getBusinessDetails($businessId, $accessToken) {
        try {
            // Get detailed business info
            $response = $this->facebook->get('/' . $businessId . '?fields=name,verification_status,primary_page.fields(name,emails,phone,location)', $accessToken);
            $business = $response->getGraphNode()->asArray();
            
            return [
                'success' => true,
                'business' => $business
            ];
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            return [
                'success' => false,
                'message' => 'Graph returned an error: ' . $e->getMessage()
            ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return [
                'success' => false,
                'message' => 'Facebook SDK returned an error: ' . $e->getMessage()
            ];
        }
    }
}