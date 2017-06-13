<?php

namespace UsuarioApiBundle\Services;


use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileManager
{

    /**
     * @var string
     */
    private $storagePath;

    public function __construct(string $storagePath)
    {
        $this->storagePath = $storagePath;
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function upload(UploadedFile $file)
    {
        $fileName = sha1(uniqid(mt_rand(), false)) . $file->getExtension();
        $file->move($this->storagePath, $fileName);
        return $fileName;
    }

    /**
     * @param string $file
     */
    public function unlinkFile(string $file)
    {
        $fileDir = $this->storagePath . DIRECTORY_SEPARATOR . $file;

        if (file_exists($fileDir)) {
            (new Filesystem())->remove($fileDir);
        }
    }

    /**
     * @param UploadedFile $newFile
     * @param $lastFile
     * @return string
     */
    public function updateFile(UploadedFile $newFile, $lastFile)
    {
        $this->unlinkFile($lastFile);
        return $this->upload($newFile);
    }
}