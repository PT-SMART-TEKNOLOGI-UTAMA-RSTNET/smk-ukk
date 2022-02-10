<?php
function responseFormat($code, $message = null, $params = null) {
    if (!$message) $message = 'ok';
    if ($code <= 0) $code = 400;
    return response()->json([
        'status_code' => $code,
        'message' => $message,
        'params' => $params
    ],$code);
}
function listMapelGroup(){
    $response = collect([]);
    $response->push(['value' => 'Nasional', 'label' => 'Muatan Nasional', 'meta' => [ 'kurikulum' => 'K13', 'nomor' => 'A' ]]);
    $response->push(['value' => 'Kewilayahan', 'label' => 'Muatan Kewilayahan', 'meta' => [ 'kurikulum' => 'K13', 'nomor' => 'B' ]]);
    $response->push(['value' => 'Dasar Bidang Keahlian', 'label' => 'Dasar Bidang Keahlian', 'meta' => [ 'kurikulum' => 'K13', 'nomor' => 'C1' ]]);
    $response->push(['value' => 'Dasar Program Keahlian', 'label' => 'Dasar Program Keahlian', 'meta' => [ 'kurikulum' => 'K13', 'nomor' => 'C2' ]]);
    $response->push(['value' => 'Kompetensi Keahlian', 'label' => 'Kompetensi Keahlian', 'meta' => [ 'kurikulum' => 'K13', 'nomor' => 'C3' ]]);
    $response->push(['value' => 'Umum', 'label' => 'Muatan Umum', 'meta' => [ 'kurikulum' => 'PK', 'nomor' => 'A' ]]);
    $response->push(['value' => 'Kejuruan', 'label' => 'Muatan Kejuruan', 'meta' => [ 'kurikulum' => 'PK', 'nomor' => 'B' ]]);
    return $response;
}
function toNum($str) {
    $limit = 5; //apply max no. of characters
    $colLetters = strtoupper($str); //change to uppercase for easy char to integer conversion
    $strlen = strlen($colLetters); //get length of col string
    if($strlen > $limit)	return "Column too long!"; //may catch out multibyte chars in first pass
    preg_match("/^[A-Z]+$/",$colLetters,$matches); //check valid chars
    if(!$matches)return "Invalid characters!"; //should catch any remaining multibyte chars or empty string, numbers, symbols
    $it = 0; $vals = 0; //just start off the vars
    for($i=$strlen-1;$i>-1;$i--){ //countdown - add values from righthand side
        $vals += (ord($colLetters[$i]) - 64 ) * pow(26,$it); //cumulate letter value
        $it++; //simple counter
    }
    return $vals; //this is the answer
}

function toStr($n,$case = 'upper') {
    $alphabet   = array(
        'A',	'B',	'C',	'D',	'E',	'F',	'G',
        'H',	'I',	'J',	'K',	'L',	'M',	'N',
        'O',	'P',	'Q',	'R',	'S',	'T',	'U',
        'V',	'W',	'X',	'Y',	'Z'
    );
    $n 			= $n;
    if($n <= 26){
        $alpha 	=  $alphabet[$n-1];
    } elseif($n > 26) {
        $dividend   = ($n);
        $alpha      = '';
        $modulo;
        while($dividend > 0){
            $modulo     = ($dividend - 1) % 26;
            $alpha      = $alphabet[$modulo].$alpha;
            $dividend   = floor((($dividend - $modulo) / 26));
        }
    }
    if($case=='lower'){
        $alpha = strtolower($alpha);
    }
    return $alpha;
}