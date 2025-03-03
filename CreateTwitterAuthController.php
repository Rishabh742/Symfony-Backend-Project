
Step 4: Twitter OAuth Authentication

       3. Create Twitter Auth Controller

		namespace App\Controller;

		use Abraham\TwitterOAuth\TwitterOAuth;

		use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

		use Symfony\Component\HttpFoundation\Request;

		use Symfony\Component\HttpFoundation\Response;

		use Symfony\Component\Routing\Annotation\Route;

		class TwitterAuthController extends AbstractController
		{
    
			#[Route('/auth/twitter', methods: ['GET'])]
    
			public function twitterLogin(): Response
    			{
        
				$connection = new TwitterOAuth($_ENV['TWITTER_API_KEY'], $_ENV['TWITTER_API_SECRET']);
        
				$requestToken = $connection->oauth('oauth/request_token', ['oauth_callback' => 'http://localhost:8000/auth/twitter/callback']);

        			$url = $connection->url('oauth/authenticate', ['oauth_token' => $requestToken['oauth_token']]);

        			return $this->redirect($url);
    			}

    			#[Route('/auth/twitter/callback', methods: ['GET'])]
    
			public function twitterCallback(Request $request): Response
    			{
        
				$oauthToken = $request->query->get('oauth_token');
        
				$oauthVerifier = $request->query->get('oauth_verifier');
        
				$connection = new TwitterOAuth($_ENV['TWITTER_API_KEY'], $_ENV['TWITTER_API_SECRET']);
        
				$accessToken = $connection->oauth('oauth/access_token', ['oauth_verifier' => $oauthVerifier]);

        			return $this->json(['message' => 'Twitter Authentication Successful', 'user' => $accessToken]);
    			}
		}