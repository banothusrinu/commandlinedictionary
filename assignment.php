<?php
define("APIKEY", "b972c7ca44dda72a5b482052b1f5e13470e01477f3fb97c85d5313b3c112627073481104fec2fb1a0cc9d84c2212474c0cbe7d8e59d7b95c7cb32a1133f778abd1857bf934ba06647fda4f59e878d164");
define("APIHOST", "https://fourtytwowords.herokuapp.com");
/*
*********** API's Available ***************
{apihost}/words/randomWord?api_key={api_key}
{apihost}/word/{word}/definitions?api_key={api_key}
{apihost}/word/{word}/examples?api_key={api_key}
{apihost}/word/{word}/relatedWords?api_key={api_key}
*/
//global $wordDescription;
$wordDescription = array();
$type = $word = $url = "";
if (isset($argv[1]) && isset($argv[2])) {
    $type = $argv[1];
    $word = $argv[2];
//    echo $argv[1] . " " . $argv[2] . "\n";
} else if (isset($argv[1]) && empty($argv[2])) {
    if ($argv[1] == "play") {
        $type = "play";
    }
    $word = $argv[1];
}
switch ($type) {
    case "defn":
        $wordDefination = getDefination($word);
        echo $wordDefination;
    break;
    case "syn":
        $wordSynonym = getSynonyms($word);
        echo $wordSynonym;
    break;
    case "ant":
        $wordAntonym = getAntonyms($word);
        echo $wordAntonym;
    break;
    case "ex":
        $wordExample = getExample($word);
        echo $wordExample;
    break;
    case "":
        if ((!$type) && ($word)) {
            $wordDictionary = getWordFullDictionary($word);
            echo $wordDictionary;
        } else if ((!$type) && (!$word)) {
            $word = getRandomWord();
            $wordDictionary = getWordFullDictionary($word);
            echo $wordDictionary;
        }
        break;
    case "play":
        //Get the random word
        $getRandomWord = getRandomWord();
        letsPlay($getRandomWord);
        break;
    default:
        echo "Please enter a valid argument";
    }
    function letsPlay($getRandomWord) {
        //        echo $getRandomWord . "\n";
        $wordDictionary = getWordInfo($getRandomWord);
        echo $wordDictionary . "\n";
        echo "Guess the word \n";
        $text = fopen("php://stdin", "r");
        $guessWord = fgets($text);
        checkGuessWord($guessWord);
    }
    function checkGuessWord($guessWord) {
        global $wordDescription;
        $result = "";
        $validAnswers = array();
        $correctWord = $wordDescription["word"];
        $word = trim($guessWord);
        if (!empty($wordDescription["synonyms"])) {
            $validAnswers = $wordDescription["synonyms"];
            unset($validAnswers[$wordDescription["displaysynindex"]]);
        } else if (!empty($wordDescription["antonym"])) {
            $validAnswers = $wordDescription["antonym"];
            unset($validAnswers[$wordDescription["displayantindex"]]);
        }
        if ($word === $correctWord) {
            echo "You have Gussed a correct word";
        } else if (in_array($word, $validAnswers)) {
            echo "You have Gussed a correct word";
        } else {
            $result = "";
            $result.= "Wrong answer \n";
            $result.= "Press 1 : To try again \n";
            $result.= "Press 2 : To display hint and try again \n";
            $result.= "Press 3 : Exit \n";
            echo $result;
            $input = fopen("php://stdin", "r");
            $inputNumber = fgets($input);
            switch ($inputNumber) {
                case 1:
                    echo "Guess the word \n";
                    $text = fopen("php://stdin", "r");
                    $guessWord = fgets($text);
                    checkGuessWord($guessWord);
                break;
                case 2:
                    $result = "";
                    $result.= "Press 1 : Display the word randomly jumbled \n";
                    $result.= "Press 2 : Display another defination of the word \n";
                    $result.= "Press 3 : Display another synonym of the word \n";
                    $result.= "Press 4 : Display another antonym of the word \n";
                    echo $result;
                    $num = fopen("php://stdin", "r");
                    $getnum = fgets($num);
                    switch ($getnum) {
                        case 1:
                            echo shuffleWord($correctWord) . "\n";
                            echo "Guess the word \n";
                            $text = fopen("php://stdin", "r");
                            $guessWord = fgets($text);
                            checkGuessWord($guessWord);
                        break;
                        case 2:
                            $validDefinations = $wordDescription["defination"];
                            unset($validDefinations[$wordDescription["displaydefindex"]]);
                            if (!empty($validDefinations)) {
                                $getNumber = rand(0, count($validDefinations));
                                echo "Defination : " . $validDefinations[$getNumber]["text"] . "\n";
                                $wordDescription["displaydefindex"] = $getNumber;
                            }
                            echo "Guess the word \n";
                            $text = fopen("php://stdin", "r");
                            $guessWord = fgets($text);
                            checkGuessWord($guessWord);
                        break;
                        case 3:
                        case 4:
                            $validAnswers = array();
                            $text = "";
                            if ($getnum == 3) {
                                if (!empty($wordDescription["synonyms"])) {
                                    $validAnswers = $wordDescription["synonyms"];
                                    unset($validAnswers[$wordDescription["displaysynindex"]]);
                                    $text.= "Synonym : ";
                                } else {
                                    $text.= "No synonym found";
                                }
                            } else if ($getnum == 4) {
                                if (!empty($wordDescription["antonym"])) {
                                    $validAnswers = $wordDescription["antonym"];
                                    unset($validAnswers[$wordDescription["displayantindex"]]);
                                    $text.= "Antonym : ";
                                } else {
                                    $text.= "No antonym found";
                                }
                            }
                            if (!empty($validAnswers)) {
                                $getNumber = rand(0, count($validAnswers));
                                $text.= $validAnswers[$getNumber] . "\n";
                                $wordDescription["displaysynindex"] = $getNumber;
                                echo $text . "\n";
                            }
                            echo "Guess the word \n";
                            $text = fopen("php://stdin", "r");
                            $guessWord = fgets($text);
                            checkGuessWord($guessWord);
                            break;
                        default:
                            echo "Choose valid hint number";
                        }
                    break;
                    case 3:
                        $word = $wordDescription["word"];
                        echo $word . "\n";
                        echo getWordFullDictionary($word);
                        echo "Thank you.good bye";
                    break;
                    default:
                        echo "Press valid number";
                }
        }
    }
    function shuffleWord($word) {
        $wordArray = str_split($word);
        shuffle($wordArray);
        return implode('', $wordArray);
    }
    function getRandomWord() {
        global $wordDescription;
        $url = APIHOST . "/words/randomWord?api_key=" . APIKEY;
        $getResults = getCurlResult($url);
        $getWord = json_decode($getResults, true);
        if (isset($getWord["error"])) {
            return $getWord["error"];
        } else {
            $wordDescription["word"] = $getWord['word'];
            return $getWord['word'];
        }
    }
    function getDefination($word) {
        global $wordDescription;
        $def = "";
        $url = APIHOST . "/word/" . $word . "/definitions?api_key=" . APIKEY;
        $getResults = getCurlResult($url);
        $definations = json_decode($getResults, true);
        $i = 0;
        if (isset($definations["error"])) {
            return $definations["error"];
        } else if(!empty($definations)){
            $wordDescription["defination"] = $definations;
            foreach ($definations as $defination) {
                if ($i == 0) {
                    $def = "Defination of the word " . $word . " - " . $defination['text'];
                } else {
                    $def = $def . " , " . $defination['text'];
                }
                $i++;
            }
            return $def;
        }
    }
    function getSynonyms($word) {
        global $wordDescription;
        $syn = "";
        $url = APIHOST . "/word/" . $word . "/relatedWords?api_key=" . APIKEY;
        $getResults = getCurlResult($url);
        $synonyms = json_decode($getResults, true);
        if (isset($synonyms["error"])) {
            return $synonyms["error"];
        } else if (!empty($synonyms)) {
            for ($i = 0;$i < count($synonyms);$i++) {
                $synonymsArray = $synonyms[$i];
                $relationType = $synonymsArray["relationshipType"];
                if ($relationType === "synonym") {
                    $wordsArray = $synonymsArray["words"];
                    $wordDescription["synonyms"] = $wordsArray;
                    for ($i = 0;$i < count($wordsArray);$i++) {
                        if ($i == 0) {
                            $syn = "Synonyms of the word " . $word . " - " . $wordsArray[$i];
                        } else {
                            $syn = $syn . " , " . $wordsArray[$i];
                        }
                    }
                }
            }
        } else {
            $syn = "No synonyms found for the given word : " . $word;
        }
        return $syn;
    }
    function getAntonyms($word) {
        global $wordDescription;
        $ant = "";
        $url = APIHOST . "/word/" . $word . "/relatedWords?api_key=" . APIKEY;
        $getResults = getCurlResult($url);
        $antonyms = json_decode($getResults, true);
        if (isset($antonyms["error"])) {
            return $antonyms["error"];
        }else if (!empty($antonyms)) {
            for ($i = 0;$i < count($antonyms);$i++) {
                $antonymsArray = $antonyms[$i];
                $relationType = $antonymsArray["relationshipType"];
                if ($relationType === "antonym") {
                    $wordsArray = $antonymsArray["words"];
                    $wordDescription["antonym"] = $wordsArray;
                    for ($i = 0;$i < count($wordsArray);$i++) {
                        if ($i == 0) {
                            $ant = "Antonyms of the word " . $word . " - " . $wordsArray[$i];
                        } else {
                            $ant = $ant . " , " . $wordsArray[$i];
                        }
                    }
                }
            }
        } else {
            $ant = "No antonyms found for the given word : " . $word;
        }
        return $ant;
    }
    function getExample($word) {
        $exp = "";
        $url = APIHOST . "/word/" . $word . "/examples?api_key=" . APIKEY;
        $getResults = getCurlResult($url);
        $examples = json_decode($getResults, true);
        $i = 0;
        if (isset($examples["error"])) {
            return $examples["error"];
        } else {
            foreach ($examples as $example) {
                foreach ($example as $ex) {
                    if ($i == 0) {
                        $exp = "Examples of the word " . $word . " - " . $ex['text'];
                    } else {
                        $exp = $exp . " , " . $ex['text'];
                    }
                    $i++;
                }
            }
            return $exp;
        }
    }
    function getWordFullDictionary($word) {
        $wordDictionary = "";
        $wordDefination = getDefination($word);
        $wordSynonyms = getSynonyms($word);
        $wordAntonyms = getAntonyms($word);
        $wordExamples = getExample($word);
        if (!empty($wordDefination)) {
            $wordDictionary.= "Definations : " . $wordDefination . "\n";
        }
        if (!empty($wordSynonyms)) {
            $wordDictionary.= " Synonyms : " . $wordSynonyms . "\n";
        }
        if (!empty($wordAntonyms)) {
            $wordDictionary.= " Antonyms : " . $wordAntonyms . "\n";
        }
        if (!empty($wordExamples)) {
            $wordDictionary.= " Examples : " . $wordExamples . "\n";
        }
        return $wordDictionary;
    }
    function getWordInfo($word) {
        global $wordDescription;
        $wordDictionary = "";
        getDefination($word);
        getSynonyms($word);
        getAntonyms($word);
        if (!empty($wordDescription["defination"])) {
            $getNumber = rand(0, count($wordDescription["defination"]));
            $wordDictionary.= "Definations : " . $wordDescription["defination"][$getNumber]["text"] . "\n";
            $wordDescription["displaydefindex"] = $getNumber;
        }
        if (!empty($wordDescription["synonyms"])) {
            $getNumber = rand(0, count($wordDescription["synonyms"]));
            $wordDictionary.= "Synonym : " . $wordDescription["synonyms"][$getNumber] . "\n";
            $wordDescription["displaysynindex"] = $getNumber;
        } else if (!empty($wordDescription["antonym"])) {
            $getNumber = rand(0, count($wordDescription["antonym"]));
            $wordDictionary.= "Antonym : " . $wordDescription["antonym"][$getNumber] . "\n";
            $wordDescription["displayantindex"] = $getNumber;
        }
        return $wordDictionary;
    }
    function getCurlResult($url) {
        //  Initiate curl
        $ch = curl_init();
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set the url
        curl_setopt($ch, CURLOPT_URL, $url);
        // Execute
        $result = curl_exec($ch);
        // Closing
        curl_close($ch);
        return $result;
    }
?>
