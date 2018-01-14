<?php
/**
 * Created by PhpStorm.
 * User: mohsinsaeed
 * Date: 14/01/2018
 * Time: 5:18 PM
 */


require_once('PorterStemmer.php');
require_once('utility.php');

echo "Extract Features and Save in arff files (WEKA format)";

/*   Global Variables */

$data_set_directory = "pan15_dataset";
$data_directory = "data";
$feature_words_directory = $data_directory.DIRECTORY_SEPARATOR."feature_words";
/*  End Global Variables */

$file_handle = fopen($data_set_directory.DIRECTORY_SEPARATOR."truth.txt", "r");
$traitData = [];
$minValue = 5;

$allCorpus = [];


$extroverted = 3;
$stable = 4;
$agreeable = 5;
$conscientious = 6;
$open = 7;

getExtrovertedWordsCount();

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



    $xml = simplexml_load_file($data_set_directory.DIRECTORY_SEPARATOR.$xmlFileName, 'SimpleXMLElement',LIBXML_NOCDATA) or die("Error: Cannot create object FOR ".$xmlFileName);
    foreach($xml->document as $tg){
        $words = explode(" ", trim($tg));
        $words = cleanWords($words);

        $linkCount = getLinkCount($words);
        $hashCount = getHashCount($words);
        $usernameCount = getUsernameCount($words);
        //$linkCount = getLinkCount($words);

        $extrovertedCount = getExtrovertedWordsCount($words);
        $stableCount = getStableWordsCount($words);
        $agreeableCount = getAgreeableWordsCount($words);
        $conscientiousCount = getConscientiousWordsCount($words);
        $openCount = getOpenWordsCount($words);
        $writableLine = $hashCount.', '.$usernameCount.', '.$linkCount.', '.$extrovertedCount.', '.$stableCount.', '.$agreeableCount.', '.$conscientiousCount.', '.$openCount.', '.$dominantPersonalityTrait;
        $allCorpus[] = $writableLine;

        echo $writableLine;
    }

}

//echo "PROCESS without removing duplicate TWEETS COUNT : ".sizeof($allCorpus)." <br/> ";

//$allCorpus = array_unique($allCorpus);

echo "PROCESS TWEETS COUNT : ".sizeof($allCorpus)." <br/> ";

$allCorpusStr = implode(PHP_EOL, $allCorpus);

fclose($file_handle);


$arffContent = '@RELATION personalityTrain
@ATTRIBUTE hashCount REAL
@ATTRIBUTE usernameCount REAL
@ATTRIBUTE linkCount REAL
@ATTRIBUTE extrovertedCount REAL
@ATTRIBUTE stableCount REAL
@ATTRIBUTE agreeableCount REAL
@ATTRIBUTE conscientiousCount REAL
@ATTRIBUTE openCount REAL
@ATTRIBUTE PERSONALITY {3,4,5,6,7}
@DATA
'.$allCorpusStr;

file_put_contents($data_directory.DIRECTORY_SEPARATOR."featurs.arff",$arffContent );
//r_print($traitData);


function getLinkCount($words = []){
    $count = 0;
    foreach($words as $word){
        if(isURL($word)){
            $count++;
        }
    }
    return $count;
}

function getHashCount($words = []){
    $count = 0;
    foreach($words as $word){
        if(isHash($word)){
            $count++;
        }
    }
    return $count;
}

function getPicCount($words = []){
    $count = 0;
    foreach($words as $word){
        $word = trim($word);
        if($word == '[pic]'){
            $count++;
        }
    }
    return $count;
}

function getUsernameCount($words = []){
    $count = 0;
    foreach($words as $word){
        $word = trim($word);
        if($word == '@username'){
            $count++;
        }
    }
    return $count;
}


function getExtrovertedWordsCount($words = []){
    global $feature_words_directory;
    global $extroverted;
    $fileName = $extroverted;
    $content = file_get_contents($feature_words_directory.DIRECTORY_SEPARATOR.$fileName);
    $fileWords = explode("\n", $content);
    $match = array_intersect($words , $fileWords);
    return sizeof($match);
}

function getStableWordsCount($words = []){
    global $feature_words_directory;
    global $stable;
    $fileName = $stable;
    $content = file_get_contents($feature_words_directory.DIRECTORY_SEPARATOR.$fileName);
    $fileWords = explode("\n", $content);
    $match = array_intersect($words , $fileWords);
    return sizeof($match);
}

function getAgreeableWordsCount($words = []){
    global $feature_words_directory;
    global $agreeable;
    $fileName = $agreeable;
    $content = file_get_contents($feature_words_directory.DIRECTORY_SEPARATOR.$fileName);
    $fileWords = explode("\n", $content);
    $match = array_intersect($words , $fileWords);
    return sizeof($match);
}

function getConscientiousWordsCount($words = []){
    global $feature_words_directory;
    global $conscientious;
    $fileName = $conscientious;
    $content = file_get_contents($feature_words_directory.DIRECTORY_SEPARATOR.$fileName);
    $fileWords = explode("\n", $content);
    $match = array_intersect($words , $fileWords);
    return sizeof($match);
}

function getOpenWordsCount($words = []){
    global $feature_words_directory;
    global $open;
    $fileName = $open;
    $content = file_get_contents($feature_words_directory.DIRECTORY_SEPARATOR.$fileName);
    $fileWords = explode("\n", $content);
    $match = array_intersect($words , $fileWords);
    return sizeof($match);
}




