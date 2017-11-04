var hand1top = document.getElementById("hand1top");
var hand1mid = document.getElementById("hand1mid");
var hand1bot = document.getElementById("hand1bot");
var hand2top = document.getElementById("hand2top");
var hand2mid = document.getElementById("hand2mid");
var hand2bot = document.getElementById("hand2bot");
var hand3top = document.getElementById("hand3top");
var hand3mid = document.getElementById("hand3mid");
var hand3bot = document.getElementById("hand3bot");
var   result = document.getElementById("result");
var expected = document.getElementById("expected");

function getHandsFromDom(){
    var hand1 = {back:hand1bot.value , mid:hand1mid.value , front:hand1top.value};
    var hand2 = {back:hand2bot.value , mid:hand2mid.value , front:hand2top.value};
	var hand3 = {};
    hand1 = JSON.stringify(hand1);
    hand2 = JSON.stringify(hand2);
	
	if(hand3top.value==null || hand3top.value=="",hand3mid.value==null ||
							   hand3mid.value=="",hand3bot.value==null ||
							   hand3bot.value==""){
								   
		return [hand1, hand2];
	}
	
	hand3 = {back:hand3bot.value , mid:hand3mid.value , front:hand3top.value};
	hand3 = JSON.stringify(hand3);
	return [hand1, hand2, hand3];
}

function sendHands(){
    console.log("sending hands");
    var xhr = new XMLHttpRequest();
    var params = getHandsFromDom();
    var hand1 = params[0];
    var hand2 = params[1];
	var hand3 = params[2];
    var url = "http://localhost:3000/?hand1="+hand1+"&"+"hand2="+hand2;
	if(hand3 != undefined || hand3 != null){
		var url = "http://localhost:3000/?hand1="+hand1+"&hand2="+hand2+"&hand3="+hand3;
	}
	console.log("Params being sent: \n" + hand1 +"\n"+ hand2 + "\n" + hand3);
	console.log("URL: " + url);
    xhr.open("GET", url, true);
    xhr.setRequestHeader("Content-type", "application/json");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log("We have recieved a response from the Node server: " + 
                        xhr.responseText);
            var json = JSON.parse(xhr.responseText);
            result.innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}

document.addEventListener('DOMContentLoaded', init, false);

function init(){
    document.getElementById("btn2").addEventListener("click", function(){
        console.log("You have clicked button2");
        
        hand1top.value = "Kh Ts 8s";
        hand1mid.value = "Kc Qc 8c 6c 7c";
        hand1bot.value = "Ad Jd 9d 5d 4d";
        
        hand2top.value = "2s 2d 6h";
        hand2mid.value = "Kd Th Td 9h 8h";
        hand2bot.value = "2c 9c 2h 3h 3s";
		
		hand3top.value = "";
        hand3mid.value = "";
        hand3bot.value = "";
		
		expected.innerHTML = "13";
    });
    document.getElementById("btn3").addEventListener("click", function(){
        console.log("You have clicked button3");
        
        hand1top.value = "Qc Kd Th";
        hand1mid.value = "2s Ad 8d 5h Jc";
        hand1bot.value = "3c 3d Js 3h Jd";
        
        hand2top.value = "6d Kh 4h";
        hand2mid.value = "7h 2h 9h 8s 8c";
        hand2bot.value = "Tc 4c 6c Kc 9c";
		
		hand3top.value = "";
        hand3mid.value = "";
        hand3bot.value = "";
		
		expected.innerHTML = "3";
    });
    document.getElementById("btn4").addEventListener("click", function(){
        console.log("You have clicked button4");
        
        hand1top.value = "Kd Ks As";
        hand1mid.value = "3d 5s 6d 4h 7h";
        hand1bot.value = "Js 7s Ks 2s 5s";
        
        hand2top.value = "9s 9c 7s";
        hand2mid.value = "Jd Td 5d 7d 2s";
        hand2bot.value = "5h Kh Th 8c 2h";
		
		hand3top.value = "";
        hand3mid.value = "";
        hand3bot.value = "";
		
		expected.innerHTML = "22";
    });
    document.getElementById("btn5").addEventListener("click", function(){
        console.log("You have clicked button5");
        
        hand1top.value = "Tc Qh Ts";
        hand1mid.value = "Kd Jd 3d Jc 2s";
        hand1bot.value = "5h 4h 4c 9s 8h";
        
        hand2top.value = "Kh Js Th";
        hand2mid.value = "Ac 9c 3s 2d 8d";
        hand2bot.value = "Qc Qs Qd 6d 6s";
		
		hand3top.value = "";
        hand3mid.value = "";
        hand3bot.value = "";
		
		expected.innerHTML = "-12";
    });
	document.getElementById("btn6").addEventListener("click", function(){
		console.log("You have clicked button6");
        hand1top.value = "8d 8s 8h";
        hand1mid.value = "Ks Qh Js Ts 9c";
        hand1bot.value = "7c 7s 7h Th Td";
        hand2top.value = "6s 4s 2s";
        hand2mid.value = "Ad Qd 7d 6d 4d";
        hand2bot.value = "Ac Kc Jc 8c 6c";
		hand3top.value = "Qs Ts 5s";
        hand3mid.value = "As Ah 2h 2c 4c";
        hand3bot.value = "3h 3s 3c 3d 6h";
		
		expected.innerHTML = "[34, -14, -18]";
	});
    document.getElementById("btn").addEventListener("click", function(){
        console.log("You have clicked button");
        sendHands();
    });
	document.getElementById("btnClear").addEventListener("click", function(){
		console.log("All input boxes cleared");
		hand1top.value = "";
		hand1mid.value = "";
		hand1bot.value = "";
		hand2top.value = "";
		hand2mid.value = "";
		hand2bot.value = "";
		hand3top.value = "";
		hand3mid.value = "";
		hand3bot.value = "";
	});
}










