<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include "__ini.php";
define('BMU_MIGRATION_TEST_NR', '');
$autoMigrator = new Wheels4AllAutoMigrator();
$createdResources = $autoMigrator->run();

//writeToFile($createdResources, BMU_MIGRATION_TEST_NR . '-resources.txt');

$personMigrator = new Wheels4AllPersonMigrator();
$persons = $personMigrator->run();
//$persons = new AppendableArrayFileFormat('TEST-1000000080persons');
//
$legalMigrator = new Wheels4AllLegalMigrator($persons);
$legals = $legalMigrator->run();
//$legals = new AppendableArrayFileFormat('TEST-1000000081legals');
$contractMigrator = new Wheels4AllContractMigrator($persons, $legals);
$contracts = $contractMigrator->run();

writeToFile($contracts, 'contracts.txt');


function writeToFile($createdResources, $fileName) {
   $fh = fopen($fileName, 'w');
   fwrite($fh, var_export($createdResources,true));
   fclose($fh);
}

?>
