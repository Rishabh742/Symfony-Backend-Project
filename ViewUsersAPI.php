
Step 3: Implement API Endpoints

        2. View Users API

	#[Route('/api/users', methods: ['GET'])]

	public function getUsers(EntityManagerInterface $em): Response
	{
    
		$users = $em->getRepository(User::class)->findAll();
    
		return $this->json($users);
	}