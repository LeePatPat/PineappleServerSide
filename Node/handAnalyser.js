var ofcp = require('ofcp');
var pe = require('poker-evaluator');

//offical pineapple/ofcp royalties
var rules = {front:true, mid:[2,4,8,12,20,30,50], back:[2,4,6,10,15,25]};

//var hand1frontCards = '8d 8s 8h';
//var hand2frontCards = '6s 4s 2s';  //'6s 4s 2s';
//var hand3frontCards = 'Qs Ts 5s';
//
//var hand1 = {
//    back: ofcp.hand('7c 7s 7h Th Td'),  //
//     mid: ofcp.hand('Ks Qh Js Ts 9c'),  //
//   front: ofcp.hand(hand1frontCards)    //
//};
//var hand2 = {
//    back: ofcp.hand('Ac Kc Jc 8c 6c'),  //
//     mid: ofcp.hand('Ad Qd 7d 6d 4d'),  //  
//   front: ofcp.hand(hand2frontCards)    //
//};
//var hand3 = {
//    back: ofcp.hand('3h 3s 3c 3d 6h'),  //
//     mid: ofcp.hand('As Ah 2h 2c 4c'),  //
//   front: ofcp.hand(hand3frontCards)    //
//};

/*
 * 
 * @param Array hands
 * @returns {unresolved}
 */
function _handStringToValues(hands){
    for(var key in hands){
        hands[key].front = ofcp.hand(hands.front);
          hands[key].mid = ofcp.hand(hands.mid);
         hands[key].back = ofcp.hand(hands.back);
    }
    return hands;
}

/*
 * Returns array of points for each player in the hand
 * 
 * params: hands - Array of hands in the format of:
 * var hand1 = {
 *      back: ofcp.hand('7s 7s 7s 3s 3s'),
 *       mid: ofcp.hand('Ts Ts Ts Ts 3s'),
 *     front: ofcp.hand('As As As')
 * }, etc etc
 */
function settlement(hands){
    hands = _handStringToValues(hands); //change to numerical values
    if(hands.length === 2){
        let score = ofcp.settle(hands[0], hands[1], rules);
        return [score, -1*score];
    }else{
        let score12 = ofcp.settle(hands[0], hands[1], rules),
            score13 = ofcp.settle(hands[0], hands[2], rules),
            score23 = ofcp.settle(hands[1], hands[2], rules),
            player1 = score12 + score13,
            player2 = (-1 * score12) + score23,
            player3 = (-1 * score13) + (-1 * score23);
        return [player1, player2, player3];
    }
}

/**
 * FANTASY LAND CONDITIONS:
 * Front row must be minimum pair of Jacks.
 * Test this by using ofcp.evalFrontHand(hand);
 *                                       //Jh, Jd, 2s
 * In poker-evaluator: ofcp.evalFrontHand([39, 38, 4])  
 *                                              returns {handtype:2,handName:'one pair',handRank}
 * 
 * Thus, the condition for the player to be in fantasy land is:
 *              - handType == 2 (pair) || handType==4 (three of a kind)
 *              - handRank >= 131 (pair of Jacks or better of handType==2)
 * Note: value does increase with the kicker. I.e. JJ2.value < JJA.value
 *       However, due to the rounding nature of the algorithm, some hands may
 *       have the same value. I.e. JJ2.value === JJ3.value
 *       
 * @param hand : Array of card STRINGS:  E.g. ["8h", "8c", "8d"]
 * @returns if hand qualifies for following hand to be fantasy
 */
function _isFantasy(hand){
    console.log("HAND in _isFantasy: " + hand);
    
    var handVals = [];
    for(var i=0; i < hand.length; i++){
        handVals.push(pe.CARDS[hand[i].toLowerCase()]);
    }
    
    var evaluatedHand = ofcp.evalFrontHand(handVals);
    
    //return true if hand is trips or a pair of JJ or better
    return (evaluatedHand.handType === 4 || 
               (evaluatedHand.handType === 2 && evaluatedHand.handRank >= 131));
}