var pa = require("pineappleAnalyser");
var ofcp = require("ofcp");

function _assocArrayGenerator(){
    var hands,
    assochand1 = {
        back : "7c 7s 7h Th Td",
         mid : "Ks Qh Js Ts 9c",
       front : "8d 8s 8h"
    },
    assochand2 = {
        back : "Ac Kc Jc 8c 6c",
         mid : "Ad Qd 7d 6d 4d",
       front : "6s 4s 2s"
    },
    assochand3 = {
        back : "3h 3s 3c 3d 6h",
         mid : "As Ah 2h 2c 4c",
       front : "Qs Ts 5s"
    };
    
    hands = [ assochand1 , assochand2 , assochand3 ];
    return hands;
}

var hands = _assocArrayGenerator();

console.log(hands[0]);

var stringToVal = ofcp.hand(hands[0].front);
console.log(stringToVal);

//for(var key in hands){
//    console.log("key: " + key);
//    hands[key].front = ofcp.hand(hands[key].front);
//      hands[key].mid = ofcp.hand(hands[key].mid);
//     hands[key].back = ofcp.hand(hands[key].back);
//}
//console.log(hands);

//var hstv = pa._handStringToValues(hands);

var stl = pa.settlement(hands);
console.log(stl);