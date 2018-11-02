<?php

namespace App\Service;

class ClientHandler {

    private $client_executable;

    public function __construct($client_executable) {
        $this->client_executable = $client_executable;
    }

    // TODO: implement records
    public function run($tm_database, $updateRecords = false, $updateAthletes = false) {
        $command = "{$this->client_executable} -database=$tm_database -dbformat=TM ";

        if($updateRecords && $updateAthletes){
            $command .= "-tasks=RECORDS;ATHLETES ";
        }else if($updateRecords) {
            $command .= "-tasks=RECORDS ";
        }else if($updateAthletes) {
            $command .= '-tasks=ATHLETES ';
        }

        $process = proc_open($command, array(
           0 => array('pipe','r'),
           1 => array('pipe','w'),
           2 => array('pipe','w'),
        ), $pipes);

        if(is_resource($process)) {

            $stdout = stream_get_contents($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);

            fclose($pipes[1]);
            fclose($pipes[2]);
        }

        return array(
            1 => $stdout,
            2 => $stderr,
        );
    }
}

?>