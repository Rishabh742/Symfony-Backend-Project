
This project is a Symfony-based backend for User Data Management and Twitter OAuth Authentication. It includes the following features:

(1) User Data Management (CRUD operations)
(2) CSV Upload & Storage in Database
(3) Automatic Email Notifications
(4) Database Backup & Restore
(5) Twitter OAuth Authentication

-> Project Structure

backend_project/
│── src/
│   ├── Controller/
│   │   ├── UserController.php  # Handles user-related operations
│   │   ├── TwitterAuthController.php  # Handles Twitter OAuth
│   ├── Entity/
│   │   ├── User.php  # User entity definition
│   ├── Repository/
│   │   ├── UserRepository.php  # Handles database queries
│── config/
│   ├── routes.yaml  # API route configuration
│── .env  # Environment variables (database & API keys)
│── README.md  # Project documentation
│── composer.json  # Dependencies & packages
│── public/  # Public access files
│── migrations/  # Database migration scripts
│── var/  # Cache & logs
│── vendor/  # Symfony dependencies

-> Step 1: Setting Up Symfony
1. Install Symfony
  Make sure PHP 8+, Composer, and MySQL are installed, then run:

    composer create-project symfony/skeleton backend_project
    cd backend_project
    composer require symfony/webapp-pack
  This initializes a Symfony project with web dependencies.

2. Configure MySQL Database
  Modify the .env file:

    DATABASE_URL="mysql://root:password@127.0.0.1:3306/backend_db"
    Then, create the database:

      php bin/console doctrine:database:create

-> Step 2: Create the User Entity
  An entity represents a table in the database.

1. Generate User Entity
Run:

   php bin/console make:entity User

Modify src/Entity/User.php:

  namespace App\Entity;

  use Doctrine\ORM\Mapping as ORM;

  #[ORM\Entity]
  #[ORM\Table(name: "users")]
  class User
  {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    
    private string $name;

    #[ORM\Column(length: 255, unique: true)]
    
    private string $email;

    #[ORM\Column(length: 255, unique: true)]
    
    private string $username;

    #[ORM\Column(type: "text")]
    
    private string $address;

    #[ORM\Column(length: 50)]
    
    private string $role;

    // Getters & Setters...
}

Run:

  php bin/console make:migration

  php bin/console doctrine:migrations:migrate

 This creates the users table in MySQL.

-> Step 3: Implement API Endpoints
1. CSV Upload API
   Uploads a CSV file, stores users in the database, and sends an email notification.

Modify src/Controller/UserController.php:

    #[Route('/api/upload', methods: ['POST'])]
    public function uploadCsv(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
      /** @var UploadedFile $file */
      $file = $request->files->get('file');

      if (!$file || $file->getClientOriginalExtension() !== 'csv') {
          return $this->json(['message' => 'Invalid file format'], Response::HTTP_BAD_REQUEST);
    }

    $handle = fopen($file->getPathname(), 'r');
    fgetcsv($handle); // Skip header

    while ($row = fgetcsv($handle)) {
        $user = new User();
        $user->setName($row[0]);
        $user->setEmail($row[1]);
        $user->setUsername($row[2]);
        $user->setAddress($row[3]);
        $user->setRole($row[4]);

        $em->persist($user);

        // Send async email
        $email = (new Email())
            ->from('admin@example.com')
            ->to($row[1])
            ->subject('Welcome!')
            ->text('Your details have been stored successfully.');
        $mailer->send($email);
    }
    fclose($handle);

    $em->flush();

    return $this->json(['message' => 'Users uploaded successfully']);
  }
 CSV Parsing
 User Storage
 Async Email Sending

2. Get All Users API

  #[Route('/api/users', methods: ['GET'])]

  public function getUsers(EntityManagerInterface $em): Response
  {
    $users = $em->getRepository(User::class)->findAll();
    return $this->json($users);
  }

  Retrieves all users from the database.

3. Database Backup API
php
Copy
Edit
#[Route('/api/backup', methods: ['GET'])]
public function backupDatabase(): Response
{
    $backupFile = 'backup.sql';
    exec("mysqldump -u root -p backend_db > $backupFile");

    return $this->json(['message' => 'Backup completed', 'file' => $backupFile]);
}
✔ Creates a database backup.

4. Restore Database API

  #[Route('/api/restore', methods: ['POST'])]
  public function restoreDatabase(): Response
  {
    $backupFile = 'backup.sql';
    exec("mysql -u root -p backend_db < $backupFile");

    return $this->json(['message' => 'Database restored successfully']);
  }

Restores the database from a backup.

-> Step 4: Twitter OAuth Authentication
1. Install Twitter OAuth Library

    composer require league/oauth1-client

2. Configure Twitter API Keys
Edit .env:

  TWITTER_API_KEY="your_api_key"

  TWITTER_API_SECRET="your_api_secret"
3. Implement Twitter OAuth Controller

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

Handles Twitter Login & Callback.

-> Step 5: Documentation & Submission

  (1) README.md (Setup instructions, API docs)

  (2) Postman Collection (API testing)

  (3) Video Submission

      (1) Introduction Video (Explain project)

      (2) Screen Recording (Show API in action)
