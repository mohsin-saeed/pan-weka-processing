<?php
/**
 * Created by PhpStorm.
 * User: mohsinsaeed
 * Date: 13/01/2018
 * Time: 10:31 PM
 *
 *
 *
 *
 * https://github.com/igorbrigadir/stopwords/blob/master/en/atire_puurula.txt
 *
 *
 *
 */

require_once('PorterStemmer.php');
require_once('utility.php');

echo "GOING TO PRE-PROCESS AND GENERATE SOME DATA FILES";

/*   Global Variables */




$data_set_directory = "pan15_dataset";
$data_directory = "data";
/*  End Global Variables */

$file_handle = fopen($data_set_directory.DIRECTORY_SEPARATOR."truth.txt", "r");
$traitData = [];
$usersData = [];
$minValue = 5;


while (!feof($file_handle)) {
    $line = fgets($file_handle);

    $tokens = explode(":::",$line);
    $userID = $tokens[0];
    $xmlFileName = $userID.".xml";
    unset($tokens[0]);
    unset($tokens[1]);
    unset($tokens[2]);

    //echo $line."<br/>";



    if(sizeof($tokens) < 1){
        continue;
    }
    $max = max($tokens);
    //echo $max."  --- ";
    if($max < $minValue){
        $minValue = $max;
    }

    $dominantPersonalityTrait = array_search($minValue, $tokens);

    if(empty($dominantPersonalityTrait)){
        continue;
    }
    //echo($dominantPersonalityTrait." --- DDD");
    //r_print($tokens);
    //echo ("smallest so far >>> ".$minValue);



    $usersData[$userID] = [];
    $usersData[$userID]['all_words'] = "";


    $xml = simplexml_load_file($data_set_directory.DIRECTORY_SEPARATOR.$xmlFileName, 'SimpleXMLElement',LIBXML_NOCDATA) or die("Error: Cannot create object FOR ".$xmlFileName);
    foreach($xml->document as $tg){
        $usersData[$userID]['all_words'] .= " ".trim($tg);
    }



   // echo "Totatl WORDS ".sizeof($usersData[$userID]['all_words']);
    $usersData[$userID]['all_words'] = explode(" ", strtolower($usersData[$userID]['all_words']));

    $traitData[$dominantPersonalityTrait] = $usersData[$userID]['all_words'];
    $traitData[$dominantPersonalityTrait] = cleanWords($traitData[$dominantPersonalityTrait]);

    //r_print($traitData);
    //die("STOP on First Iteration");
}

//r_print($traitData);

$traitData = removeDuplicates($traitData);

populateFeatureWordsFiles($traitData);
echo "Sizes are going here: ";
echo (sizeof($traitData[4]))." - ";
echo (sizeof($traitData[5]))." - ";
echo (sizeof($traitData[6]))." - ";
echo (sizeof($traitData[7]))." - ";
//r_print($traitData);

fclose($file_handle);


/*

$traitData = removeDuplicates($traitData);

populateFeatureWordsFiles($traitData);
echo "Sizes are going here: ";
echo (sizeof($traitData[4]))." - ";
echo (sizeof($traitData[5]))." - ";
echo (sizeof($traitData[6]))." - ";
echo (sizeof($traitData[7]))." - ";
//r_print($traitData);

*/

