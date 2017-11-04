<?php
include_once 'gamedbfuncs.php';

//deals hand to all players in game ID
function dealHand($gameId){
    //all data relating to game($gameId) as assoc array
    $gameData = getGame($gameId);
	
    $deck = $gameData['deck']; //string of total deck
    $player3id = $gameData['player3id'];
    $noPlayers = ($player3id != 0) ? 3 : 2;
    
    //split deck string into the cards
    $cards = str_split($deck);
    $cardsArray = [];
    //make a deck, where $cards[i] is a card at index i
    for($i=0; $i < count($cards); $i+=2 ){
        array_push($cardsArray, $cards[$i].$cards[$i+1]);
    }
    
    //DEAL CARDS TO PLAYERS
    for($n=1; $n < $noPlayers+1; $n++){ //deal to each player in game
        $cardsToBeDealtString = "";
		
		//check if end of game
		$round = $gameData['round'];
		$fantasy = fantasyCheck($gameId, $n);
		if($fantasy)    	$noCardsToBeDealt = 14; //14 for player in fantasy-land
		elseif($round == 1) $noCardsToBeDealt =  5; //5 cards dealt initially.
		elseif($round <= 5) $noCardsToBeDealt =  3; //3 cards for rest of game
		else{ //hand completed, settle to obtain points
			settleHand($gameId);
			return true;
		}
		
		//internal loop to deal cards to designated player
        for($i=0; $i < $noCardsToBeDealt; $i++){
            //choose a random card
            $rng = mt_rand(0, count($cardsArray)-1);
            $cardToBeDealt = $cardsArray[$rng];
            unset($cardsArray[$rng]); //remove card from deck
            $cardsArray = array_values($cardsArray); //reindex array
            
            $cardsToBeDealtString = $cardsToBeDealtString . 
									$cardToBeDealt . " ";
        }
		$cardsToBeDealtString = trim($cardsToBeDealtString);
        $currentPlayerId = getPlayerIdFromPosition($gameId, $n);
        //send hand to db, then remove cards dealt from deck
        dealHandToPlayer($gameId, $cardsToBeDealtString, $currentPlayerId);
        setDeck($gameId, $cardsArray); //add the new deck back to the DB
    }
	
	//INCREMENT ROUND
	incrementRound($gameId);
}

/**
 * 
 * @param type $gameId
 * @param type $playerId
 * @param type $hand - Assoc Array "front"=>"As Ks Qs" , etc etc 
 *                     of cards the user wishes to set to their board.
 */
function setHand($gameId, $playerId, $hand){
    
    //retrieve the currently set cards---------
    $position = getPositionFromPlayerId($gameId, $playerId);
    if($position === FALSE) return false;
    
    $conn = getDB();
    $sql = "SELECT player".$position."board FROM games WHERE id=".$gameId;
    $r = $conn->query($sql);
    closeDB($conn);
    if($r->num_rows == 0) return false;
    
    $playerBoard = array_values($r->fetch_assoc())[0];
    $playerBoard = json_decode($playerBoard, true); //$playerBoard['front'] etc
    
    //Check added cards are valid--------------
    $playerBoard['front'] .= " " . $hand['front'] . " ";
      $playerBoard['mid'] .= " " . $hand['mid']   . " ";
     $playerBoard['back'] .= " " . $hand['back']  . " ";
	$playerBoard['front']  = trim($playerBoard['front']);
	$playerBoard['mid']    = trim($playerBoard['mid']);
	$playerBoard['back']   = trim($playerBoard['back']);
    //invalid hand sizes
    if(   strlen($playerBoard['front']) > 8  ||		//      "As Ks Qs".len = 8
          strlen($playerBoard['mid'])   > 14 ||		//"As Ks Qs Js Ts".len = 14
          strlen($playerBoard['back'])  > 14   )    //"As Ks Qs Js Ts".len = 14
            return false;
	
    //add set cards to database--------------
    $playerBoard = json_encode($playerBoard);
    $conn = getDB();
    $sql = "UPDATE games SET player".$position."hand=NULL, player".$position."board=".$playerBoard." WHERE id=".$gameId;
    if(!$conn->query($sql)){
		closeDB($conn);
		return false;
	}
	closeDB($conn);
	
	//if user's board is full, check if game is complete. If so finish game and settle scores
	if(strlen($playerBoard['front'])==8 && strlen($playerBoard['mid'])==8 && $playerBoard['back']==8){
		if(_checkHandComplete($gameId)){
			//settleHand and update scores
			settleHand($gameId);
			//increment turn field, set round field to 1 in DB
			_startNextTurn($gameId);
		}
	}
    return true;
}

/*
 * Start the next turn of pineapple
 */
function _startNextTurn($gameId){
	$conn = getDB();
	$sql  = "SELECT player3id, turn FROM games WHERE id=".$gameId;
	   $r = $conn->query($sql);
	closeDB($conn);
	if($r->num_rows == 0) return false;
	
	   $result = $r->fetch_assoc();
	     $turn = $result['turn'];
	$player3id = $result['player3id'];
	
	if($player3id > 0)
		if(++$turn > 3) $turn = 1;
	else
		if(++$turn > 2) $turn = 1;
	
	$conn = getDB();
	$sql  = "UPDATE games SET turn=" .$turn. " , round=1 WHERE id=" . $gameId;
	if(!$conn->query($sql))
		return false;
	return true;
}

/*
 * Checks if the given game has been completed
 */
function _checkHandComplete($gameId){
	$conn = getDB();
	$sql = "SELECT * FROM games WHERE id=".$gameid;
	$r = $conn->query($sql);
	closeDB($conn);
	if($r->num_rows == 0) return false;
	
	//retrieve boards for size analysis
	$result = $r->fetch_assoc(); //$result['player1board'] etc etc
	$player1board = json_decode($result['player1board']);
	$player2board = json_decode($result['player2board']);
	$player3board = ($result['player3id'] === 0) 
					 ? json_decode($result['player3board']) : null ;
	
	if($player3board === null){
		if( strlen($player1board['front'])===8 && strlen($player1board['mid'])===14 && strlen($player1board['back'])===14 &&
			strlen($player2board['front'])===8 && strlen($player2board['mid'])===14 && strlen($player2board['back'])===14)
			return true;
		else
			return false;
	}
	
	if( strlen($player1board['front'])===8 && strlen($player1board['mid'])===14 && strlen($player1board['back'])===14 &&
		strlen($player2board['front'])===8 && strlen($player2board['mid'])===14 && strlen($player2board['back'])===14 &&
		strlen($player3board['front'])===8 && strlen($player3board['mid'])===14 && strlen($player3board['back'])===14)
		return true;
	return false;
}

/*
 * Gives points to each player based on set cards
 */
function settleHand($gameId){
	//initiate connection with the database
    $conn = getDB();
     $sql = "SELECT player3id, player1board, player2board, player3board 
			FROM games WHERE id=".$gameId;
	   $r = $conn->query($sql);
	closeDB($conn);
	if($r->num_rows == 0) return false;
	
	//construct the url and parameters for Node server
	$player3exist = ($result['player3id']==0) ? false : true;
	$result = $r->fetch_assoc();
	if(!$player3exist) $result['player3board'] = "null";
	$url = "http://localhost:3000/?hand1=".$result['player1board']."&hand2=".$result['player2board']."&hand3=".$result['player3board'];
	
	//initiate connection and data transfer with Node server
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	$result = curl_exec($ch);
	curl_close($ch);
	
	//obtain scores and set the new scores in the database
	$result = json_decode($result, true);
	if(!$player3exist){
		$conn = getDB();
		 $sql = "UPDATE games SET player1points = player1points + ".$result['player1score'].
								 "player2points = player2points + ".$result['player2score'].
							     "WHERE id=".$gameId;
		if(!$conn->query($sql)){
			closeDB($conn);
			return false;
		}
		return true;
	}else{
		$conn = getDB();
		 $sql = "UPDATE games SET player1points = player1points + ".$result['player1score'].
								 "player2points = player2points + ".$result['player2score'].
								 "player3points = player3points + ".$result['player3score'].
							     "WHERE id=".$gameId;
		if(!$conn->query($sql)){
			closeDB($conn);
			return false;
		}
		return true;
	}
}
