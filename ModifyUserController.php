
Step 3: Implement API Endpoints

       Modify src/Controller/UserController.php:

	namespace App\Controller;

	use App\Entity\User;

	use Doctrine\ORM\EntityManagerInterface;

	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

	use Symfony\Component\HttpFoundation\Request;

	use Symfony\Component\HttpFoundation\Response;

	use Symfony\Component\Routing\Annotation\Route;

	use Symfony\Component\Mime\Email;

	use Symfony\Component\Mailer\MailerInterface;

	use Symfony\Component\HttpFoundation\File\UploadedFile;

	class UserController extends AbstractController
	{
    
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
}