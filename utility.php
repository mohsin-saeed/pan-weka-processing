<?php
/**
 * Created by PhpStorm.
 * User: mohsinsaeed
 * Date: 14/01/2018
 * Time: 6:01 PM

 *
 *
 */


function cleanWords($words){
    //r_print($words);
    //r_print(getAllStopWords());
    $stemmer = new Stemmer();

    $words = removeStopWords($words);
    $words = array_filter($words, "notURL");
    $words = array_filter($words, "notHash");
    $words = array_filter($words, "notUsername");

    foreach($words as $key => $word){
        $words[$key] = $stemmer->stem($word);
    }

    return $words;
}


function removeDuplicates($traitData){
    $traitData[3] = array_unique($traitData[3]);
    $traitData[4] = array_unique($traitData[4]);
    $traitData[5] = array_unique($traitData[5]);
    $traitData[6] = array_unique($traitData[6]);
    $traitData[7] = array_unique($traitData[7]);
    return $traitData;
}

function removeStopWords($words){
    $removedStopwords = array_diff($words, getAllStopWords());
    return $removedStopwords;
}

function getAllStopWords(){
    $data_directory = "data";
    $file = file_get_contents($data_directory.DIRECTORY_SEPARATOR.'/stopwords', true);
    $stopwords = explode(PHP_EOL,$file);
    return $stopwords;
}

function isURL($word){
    $regex = "((https?|ftp)\:\/\/)?"; // SCHEME
    $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
    $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
    $regex .= "(\:[0-9]{2,5})?"; // Port
    $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
    $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
    $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor

    if(preg_match("/^$regex$/i", $word)) // `i` flag for case-insensitive
    {
        return true;
    }
    return false;
}

function notURL($word){
    return !isURL($word);
}

function notHash($word){
    return !isHash($word);
}

function isHash($word){
    if(substr($word, 0, 1) == "#"){
        return true;
    }
    return false;
}


function notUsername($word){
    return !isUsername($word);
}

function isUsername($word){
    if(substr($word, 0, 1) == '@'){
        return true;
    }
    return false;
}


function populateFeatureWordsFiles($data){
    global $data_directory;
    foreach($data as $index => $traitWords){
        $traitWordsFile = $data_directory.DIRECTORY_SEPARATOR."feature_words".DIRECTORY_SEPARATOR.$index;
        echo "<<<< FILECONTENT >>><br/>";
        $traitWordsStr = implode("\n", $traitWords);
        echo "<pre>".$traitWordsStr."</pre>";
        file_put_contents($traitWordsFile,$traitWordsStr );
    }
}

function r_print($array){
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}
