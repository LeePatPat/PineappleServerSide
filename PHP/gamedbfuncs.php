<?php

include_once 'userdbfuncs.php';

//Check if the usernames exist
function checkUsersExist ($username1, $username2, $username3){
    if($username1!=="" && $username2!==""){
        if($username3!==""){
            return (usernameExists($username1) 
                    && usernameExists($username2)
                    && usernameExists($username3));
        }else{
            return (usernameExists($username1) && usernameExists($username2));
        }
    }else{
        return false;
    }
}

//return assoc array of player IDs involved in game(id)    
function getPlayersFromGameId($id){
    $conn = getDB();
    $sql = "SELECT player1id, player2id, player3id FROM games WHERE id=".$id."";
    if(!($result = $conn->query($sql))){
        echo $conn->error; //TODO remove this after debugging
        closeDB($conn);
        return null;
    }
    closeDB($conn);
    return $result;
}

//returns assoc array of all game data from database
function getGame($id){
    $conn = getDB();
    $sql = "SELECT * FROM games WHERE id=".$id."";
    if(!($result = $conn->query($sql))){
        echo $conn->error; //TODO remove this after debugging
        closeDB($conn);
        return null;
    }
    closeDB($conn);
    $result = $result->fetch_assoc();
    return $result;
}

function createTwoPlayerGame($username1, $username2, $pointLimit){
    if(!checkUsersExist($username1, $username2, ""))
            return false;
    
    $username1id = getId($username1);
    $username2id = getId($username2);
    
    $conn = getDB();
    $sql = "INSERT INTO games(player1id, player2id, player3id, pointLimit) "
            . "VALUES (".$username1id." , ".$username2id." , 0 , ".$pointLimit.")";
    if(!$conn->query($sql)){
        echo $conn->error; //TODO remove this after debugging
        closeDB($conn);
        return false;
    }
    echo "Game created for ".$username1." and ".$username2."</br>";
    
    $latestId = $conn->insert_id;
    closeDB($conn);
    return $latestId; //returns unique id of game just created
}
function createThreePlayerGame($username1, $username2, $username3, $pointLimit){
    if(!checkUsersExist($username1, $username2, $username3))
        return false;

    $username1id = getId($username1);
    $username2id = getId($username2);
    $username3id = getId($username3);
    
    $conn = getDB();
    $sql = "INSERT INTO games(player1id, player2id, player3id, pointLimit) "
            . "VALUES (".$username1id." , ".$username2id." , ".$username3id." , ".$pointLimit.")";
    if(!$conn->query($sql)){
        echo $conn->error; //TODO remove this after debugging
        closeDB($conn);
        return false;
    }
    echo "Game created for ".$username1." , ".$username2." and ".$username3."</br>";
    
    $latestId = $conn->insert_id;
    closeDB($conn);
    return $latestId;
}

/*
 * Returns ID of player from a certain position
 * E.g. player1id = 124   getPlayerIdFromPosition(x, 1) returns 124
 */    
function getPlayerIdFromPosition($gameId, $position){
    $conn = getDB();
    $sql = "SELECT player".$position."id FROM games WHERE id=".$gameId;
    $result = $conn->query($sql);
    if($result->num_rows !== 0){
        $result = array_values($result->fetch_assoc());
        closeDB($conn);
        return $result[0];
    }else{
        closeDB($conn);
        return 0;
    }
}

/*
 * Returns the position (1, 2 or 3) of $playerId in game($gameId)
 * Return false if the player does not exist in the specified game
 */
function getPositionFromPlayerId($gameId, $playerId){
    $conn = getDB();
    $sql = "SELECT player1id, player2id, player3id FROM games WHERE id=".$gameId;
    $r = $conn->query($sql);
    if($r->num_rows == 0)
        return false;
    
    $result = $r->fetch_assoc();
    closeDB($conn);
    
    if($result['player1id'] == $playerId){
        return 1;
    } elseif ($result['player2id'] == $playerId){
        return 2;
    } elseif ($result['player3id'] == $playerId){
        return 3;
    }
    return false;
}

/*
 * Deals given hand to given player in Game($gameId)
 */
function dealHandToPlayer($gameId, $hand, $playerId){
    $playerPosition = getPositionFromPlayerId($gameId, $playerId);
    if($playerPosition === false)
        return false;
    
    $conn = getDB();
    $sql = "UPDATE games SET player".$playerPosition."hand='".$hand."' WHERE id=".$gameId;
    if(!$conn->query($sql)){
        closeDB($conn);
        echo "\nFailed to run query in dealHandToPlayer!\n";
        return false;
    }
    closeDB($conn);
    return true;
}

/*
 * TODO: Returns the hand of the given playerId in the given gameId
 */
//function getPlayerHand($gameId, $playerId){
//    $conn = getDB();
//    $sql = "SELECT ";
//}

/*
 * Checks if player in game is in fantasy
 */
function fantasyCheck($gameId, $playerPosition){
	$gameDetails = getGame($gameId);
	$fantasy = $gameDetails['player'.$playerPosition.'fantasy'];
	if($fantasy == true || $fantasy == 1)
		return true;
	return false;
}

/*
 * Resets the deck back to its default value
 */
function resetDeck($gameId){
    $conn = getDB();
    $sql = "UPDATE games SET deck=DEFAULT WHERE id=".$gameId;
    if(!$conn->query($sql)){
        closeDB($conn);
        return false;
    }
    closeDB($conn);
    return false;
}

/*
 * Sets the deck without the cards that have just been dealt to the players
 */
function setDeck($gameId, $cardsArray){
    //change the cardsArray back into original string
    $deck = "";
    for($i=0; $i < count($cardsArray);){
        $currentCard = $cardsArray[0];
        $deck = $deck . $currentCard;
        unset($cardsArray[0]);
        $cardsArray = array_values($cardsArray);
    }
    
    //Connect to DB and replace out-of-date deck
    $conn = getDB();
    $sql = "UPDATE games SET deck='".$deck."' WHERE id=".$gameId;
    if(!$conn->query($sql)){
        closeDB($conn);
        echo "Failed to run query to setDeck()</br>";
        return false;
    }
    closeDB($conn);
    return true;
}

/*
 * Increments the round field in DB
 */
function incrementRound($gameId){
	$conn = getDB();
	$sql = "UPDATE games SET round = round + 1 WHERE id=".$gameId;
	if(!$conn->query($sql)){
		closeDB($conn);
		echo "\nFailed to increment round for gameId: " . $gameId;
		return false;
	}
	closeDB($conn);
	return true;
}