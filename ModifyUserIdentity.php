
Step 2: Create the User Entity

      Modify src/Entity/User.php:

	namespace App\Entity;

	use Doctrine\ORM\Mapping as ORM;

	
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

    	  // Getters & Setters ...
	}