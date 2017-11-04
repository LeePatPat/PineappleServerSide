const http = require('http');
//const ee = require('events');
const qs = require('querystring');
const pa = require('pineappleAnalyser'); //settlement, _isFantasy, _handStringToValue
const url = require('url');
const port = 3000;

/**
 * Params: JSON Pineapple hands
 * Return: Array of Assoc Arrays of pineapple hands
 */
function _assocArrayGenerator(jsonhand1, jsonhand2, jsonhand3){
    var hands,
    assochand1 = {
        back : jsonhand1.back,
         mid : jsonhand1.mid,
       front : jsonhand1.front
    },
    assochand2 = {
        back : jsonhand2.back,
         mid : jsonhand2.mid,
       front : jsonhand2.front
    },
    assochand3 = null;
    
    if(jsonhand3 != null){
        assochand3 = {
            back : jsonhand3.back,
            mid : jsonhand3.mid,
            front : jsonhand3.front
        };
    }
    
    if(assochand3 !== null)
        hands = [assochand1 , assochand2 , assochand3];
    else
        hands = [assochand1 , assochand2];
        
    return hands;
}

/**
 * Params: hands - array of assoc arrays of pineapple hands (as their integer values)
 * Return: array of type boolean
 */
function _getFantasies(hands){
	for(var i=0; i<hands.length; i++){
		hands[i] = pa._isFantasy(hands[i].front);
	}
	return hands;
}

const requestHandler = (request, response) => { 
    var urlParts = url.parse(request.url, true),
        urlParams = urlParts.query, //JSON hand data
        body = "";
		process.setMaxListeners(0);
        response.setHeader("Access-Control-Allow-Origin", "*");
        response.setHeader("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
        
    // if the request is a POST request
    if(request.method == 'GET'){
        var body = "";
        var paramData = "";
        request.on('data' , function(data){
            body += data;
        });
        process.on("uncaughtException", function(e){
            //do nothing, stops annoying console issue
            console.log(e);
        });
        request.on("error", function(e){
            console.log("Error caught!");
        });
        
        request.on('end', function(){
			var hand1 = (urlParams.hand1 != null || urlParams.hand1 != undefined || urlParams.hand1 === "") 
						? JSON.parse(urlParams.hand1) : null,
				hand2 = (urlParams.hand2 != null || urlParams.hand2 != undefined || urlParams.hand2 === "")
						? JSON.parse(urlParams.hand2) : null,
				hand3 = (urlParams.hand3 != null || urlParams.hand3 != undefined || urlParams.hand3 === "")
						? JSON.parse(urlParams.hand3) : null;
			
			if(hand1 != null && hand2 != null){
				//process settlement of given hands
				var hands = _assocArrayGenerator(hand1, hand2, hand3),
					settlement = pa.settlement(hands),
					fantasies = _getFantasies(hands), //array of booleans [true,false,true];
					jsonSettlement = {
						 player1score : settlement[0],
						 player2score : settlement[1],
						 player3score : settlement[2],
					   player1fantasy :  fantasies[0],
					   player2fantasy :  fantasies[1],
					   player3fantasy :  fantasies[2],
					};
			
				response.writeHead(200, {'Content-type':'application/json'});
				console.log("Server accessed. Result: " + settlement);
				response.end(JSON.stringify(jsonSettlement));
			}else{
				response.end(null);
			}
        });
    }else{
        response.writeHead(200, {'Content-Type':'text/html'});
        response.end("Error: something went wrong with the GET request");
    }
};

const server = http.createServer(requestHandler);

server.listen(port, (err) => {  
  if (err) {
    return console.log('Error occured with the server: ', err);
  }

  console.log(`Server is listening on http://localhost:${port}`);
})