<?php

namespace App\Service;

use \ZipArchive;

class FileHandler {

    private $zip;

    public function __construct() {
        $this->zip = new ZipArchive;
    }  

    public function unzipArchive($filePath, $extractToPath, $fileExtension) {
        $result = $this->zip->open($filePath);
        if($result == true) {

            if(strtoupper($fileExtension) == "MDB") {
                $mdbFile = $this->getTMFile();
            }else if(strtoupper($fileExtension) == "HTML") {
                $mdbFile = $this->getHTMLFile();
            }
            $this->zip->extractTo($extractToPath, $mdbFile);
            $relPath = $extractToPath.$mdbFile;
            $this->zip->close();
            return $relPath;
        }
        return null;
    }

    private function getHTMLFile() {
        return $this->zip->getNameIndex(0);
    }

    private function getTMFile() {
        for($i = 0; $i < $this->zip->numFiles; $i++) {
            if(strtoupper(substr($this->zip->getNameIndex($i),-4)) == ".MDB") {
                return $this->zip->getNameIndex($i);
            }
        }
    }
}

?>