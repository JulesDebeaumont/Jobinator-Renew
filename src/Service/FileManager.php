<?php

namespace App\Service;

use App\Entity\Candidat;
use App\Entity\Job;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileManager
{
    private string $targetDirectory;

    public function __construct(string $targetDirectory)
    {
        // Check config/services.yaml for the value
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file): string
    {
        // Génère un nom de fichier unique
        $newFile = md5(uniqid()) . '.' . $file->guessExtension();

        try {
            // Copie du fichier dans le dossier
            $file->move(
                // On va chercher la route du dossier dans le services.yaml
                $this->getTargetDirectory(),
                $newFile
            );
        } catch (FileException $error) {
            throw new FileException('An error has occured while uploading the file' . $file . '\n' . $error->getMessage());
        }

        return $newFile;
    }

    public function deleteAllJobRelatedFiles(Job $job): void
    {
        foreach ($job->getApplications() as $application) {
            foreach ($application->getFiles() as $file) {
                $fileName = $this->getTargetDirectory() . DIRECTORY_SEPARATOR . $file->getName();

                if (file_exists($fileName)) {
                    unlink($fileName);
                }
            }
        }

        // NEXT handle images
    }

    public function deleteAllCandidatFiles(Candidat $candidat): void
    {
        foreach ($candidat->getApplications() as $application) {
            foreach ($application->getFiles() as $file) {
                $fileName = $this->getTargetDirectory() . DIRECTORY_SEPARATOR . $file->getName();

                if (file_exists($fileName)) {
                    unlink($fileName);
                }
            }
        }
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
