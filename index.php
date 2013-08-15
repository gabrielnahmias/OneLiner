<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>OneLiner</title>
<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(function() {
	function reduce(e) {
		var s = $("#input").val(),
			parts = s.replace(/[\t\n\r]/gi, "").trim().split(" "),
			newParts = [],
			newString = "";
		if (s == "")
			alert("Enter some text.");
		$.each(parts, function(i, v) {
			if (v != "") {
				newParts.push(v);
			}
		});
		newString = newParts.join(" ");
		$("#input").val(newString);
		e.preventDefault();
	}
	$("#main").submit(reduce);
	$("input[type=submit]").click(reduce);
});
</script>
<style type="text/css">
*:not(h1):not(textarea) {
	font: 12px Verdana, Geneva, sans-serif;
}
body {
	color: #666666;
}
h1 {
	margin: 5px 0;
}
h1, textarea {
	font-family: Consolas, "Courier New", Courier, monospace;
}
h1 span {
	color: #999999;
	letter-spacing: 4px;
}
input[type=submit] {
	display: block;
	width: 100%;
}
textarea {
	border: 2px dashed #666666;
	border-width: 2px 0;
	font-size: 14px;
	height: 200px;
	margin-bottom: 5px;
	max-width: 100%;
	min-width: 100%;
	padding: 5px 0;
	width: 100%;
}
#input_wrapper {
/*	border: 2px dashed #666666;
	border-width: 2px 0;
	margin-bottom: 5px;
	padding: 5px 0;*/
}
#wrapper {
	margin: auto;
	width: 500px;
}
.box {
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}
</style>
</head>

<body>
	<div id="wrapper">
    	<h1>OneLiner <span>reduce your text</span></h1>
    	<form id="main" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
        	<div id="input_wrapper">
    			<textarea id="input" class="box" placeholder="Enter text here"></textarea>
            </div>
            <input type="submit" value="Reduce">
        </form>
    </div>
</body>

</html>