
Step 3: Implement API Endpoints

        3. Backup Database API

	#[Route('/api/backup', methods: ['GET'])]

	public function backupDatabase(): Response
	{
    
		$backupFile = 'backup.sql';
    
		exec("mysqldump -u root -p backend_db > $backupFile");

    		return $this->json(['message' => 'Backup completed', 'file' => $backupFile]);
	}