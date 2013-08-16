<?php
define("DIR_ASSETS", "assets");
define("DIR_CSS", DIR_ASSETS."/css");
define("DIR_JS", DIR_ASSETS."/js");
define("DIR_CM", DIR_JS."/codemirror");
?><!doctype html>
<html><head>
<meta charset="utf-8">
<title>OneLiner</title>
<link rel="stylesheet" href="<?=DIR_CSS?>/styles.css" type="text/css">
<!-- CodeMirror -->
<link rel="stylesheet" href="<?=DIR_CM?>/lib/codemirror.css" type="text/css">
<script src="<?=DIR_CM?>/lib/codemirror.js"></script>
<!-- Modes -->
<?php
$dirs = scandir(DIR_CM."/mode");
foreach($dirs as $dir) {
	if (!in_array($dir, array(".","..","index.html","meta.js","rpm")))
		print "<script src=\"".DIR_CM."/mode/$dir/$dir.js\"></script>\r\n";
}
?>

<!-- jQuery -->
<script src="jquery-1.10.2.min.js" type="text/javascript"></script>
<script type="text/javascript">
var editor;

$(function() {
	if (!String.prototype.reduce) {
		String.prototype.reduce = function() {
			var s = this,
				parts = s.split("\n").join(" ").replace(/[\t\r]/gi, "").trim().split(" "),
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
			return newString;
		}
	}
	function change(e){
		editor.setValue(editor.getValue().reduce());
		e.preventDefault();
	}
	
	$("#main").submit(change);
	$("input[type=submit]").click(change);
});
</script>
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
    <script type="text/javascript">
	var pending;
	editor = CodeMirror.fromTextArea($("#input")[0], {
		mode: "javascript",
		lineNumbers: true,
		lineWrapping: true,
		tabMode: "indent" 
	});
	editor.on("change", function() {
		clearTimeout(pending);
		setTimeout(update, 400);
	});
	function getMode(code) {
		var mode;
		if (/(^\s*\(\s*function\b|alert|document|window|location|getElementsByName|getItems|open|close|write|writeln|execCommand|queryCommandEnabled|queryCommandIndeterm|queryCommandState|queryCommandSupported|queryCommandValue|clear|getSelection|captureEvents|releaseEvents|routeEvent|domain|cookie|body|head|images|embeds|plugins|links|forms|scripts|designMode|fgColor|linkColor|vlinkColor|alinkColor|bgColor|anchors|applets|getElementsByTagName|getElementsByTagNameNS|getElementsByClassName|getElementById|createElement|createElementNS|createDocumentFragment|createTextNode|createComment|createProcessingInstruction|importNode|adoptNode|createEvent|createRange|createNodeIterator|createTreeWalker|createCDATASection|createAttribute|createAttributeNS|hasFocus|releaseCapture|enableStyleSheetsForSet|elementFromPoint|caretPositionFromPoint|querySelector|querySelectorAll|getAnonymousNodes|getAnonymousElementByAttribute|getBindingParent|loadBindingDocument|createExpression|createNSResolver|evaluate|implementation|URL|documentURI|compatMode|characterSet|contentType|doctype|documentElement|inputEncoding|referrer|lastModified|readyState|title|dir|defaultView|activeElement|onreadystatechange|onmouseenter|onmouseleave|onwheel|oncopy|oncut|onpaste|onbeforescriptexecute|onafterscriptexecute|currentScript|hidden|visibilityState|styleSheets|selectedStyleSheetSet|lastStyleSheetSet|preferredStyleSheetSet|styleSheetSets|onabort|oncanplay|oncanplaythrough|onchange|onclick|oncontextmenu|ondblclick|ondrag|ondragend|ondragenter|ondragleave|ondragover|ondragstart|ondrop|ondurationchange|onemptied|onended|oninput|oninvalid|onkeydown|onkeypress|onkeyup|onloadeddata|onloadedmetadata|onloadstart|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onpause|onplay|onplaying|onprogress|onratechange|onreset|onseeked|onseeking|onselect|onshow|onstalled|onsubmit|onsuspend|ontimeupdate|onvolumechange|onwaiting|onononononblur|onerror|onfocus|onload|onscroll|hasChildNodes|insertBefore|appendChild|replaceChild|removeChild|normalize|cloneNode|isEqualNode|compareDocumentPosition|contains|lookupPrefix|lookupNamespaceURI|isDefaultNamespace|hasAttributes|nodeType|nodeName|baseURI|ownerDocument|parentNode|parentElement|childNodes|firstChild|lastChild|previousSibling|nextSibling|nodeValue|textContent|namespaceURI|prefix|localName|addEventListener|removeEventListener|dispatchEvent)/gi.test(code) || /^\s*[;\(]/.test(code))
			mode = "javascript";
		else if (/(select|count|insert|update|delete|from)/gi.test(code))
			mode = "sql";
		else if (/(print|echo|foreach)/gi.test(code))
			mode = "php";
		/*else if ()
			mode = "";
		else if ()
			mode = "";
		else if ()
			mode = "";*/
		else
			mode = "text/html";
		console.debug(mode);
		return mode;
	}
	function update() {
		editor.setOption("mode", getMode(editor.getValue()));
	}
	</script>
</body>
</html>