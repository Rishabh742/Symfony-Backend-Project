
Step 3: Implement API Endpoints

        4. Restore Database API

	#[Route('/api/restore', methods: ['POST'])]

	public function restoreDatabase(): Response
	{
    
		$backupFile = 'backup.sql';
    
		exec("mysql -u root -p backend_db < $backupFile");

    		return $this->json(['message' => 'Database restored successfully']);
	}