<?php
    include_once 'PHP/gamefuncs.php';
?>
<html>
    <head>
        <title>Settler</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
		<div id="hand1" style="float:left; margin:3px;">
			HAND 1<br/>
			<input type="text" id="hand1top" placeholder="top"/><br/>
			<input type="text" id="hand1mid" placeholder="mid"/><br/>
			<input type="text" id="hand1bot" placeholder="bot"/><br/>
		</div>
		<div id="hand2" style="float:left; margin:3px;">
			HAND 2<br/>
			<input type="text" id="hand2top" placeholder="top"/><br/>
			<input type="text" id="hand2mid" placeholder="mid"/><br/>
			<input type="text" id="hand2bot" placeholder="bot"/><br/>
		</div>
		<div id="hand3" style="float:left; margin:3px;">
			HAND 3<br/>
			<input type="text" id="hand3top" placeholder="top"/><br/>
			<input type="text" id="hand3mid" placeholder="mid"/><br/>
			<input type="text" id="hand3bot" placeholder="bot"/><br/>
		</div>
		<br/><br/><br/><br/><br/><br/>
        <button id="btn">Submit</button>
        <button id="btn2">Example</button>
        <button id="btn3">Example2</button>
        <button id="btn4">Example3</button>
        <button id="btn5">Example4</button>
		<button id="btn6">Example5</button>
		<br/>
		<button id="btnClear">Clear</button>
		<p id="expected"></p>
        <br/>
        <p id="result"></p>
    </body>
    
    <script src="JS/handSender.js"></script>
</html>