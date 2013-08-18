<?php
define("DIR_ASSETS", "assets");
define("DIR_CSS", DIR_ASSETS."/css");
define("DIR_IMG", DIR_ASSETS."/img");
define("DIR_JS", DIR_ASSETS."/js");
define("DIR_CM", DIR_JS."/codemirror");
?><!doctype html>
<html><head>
<meta charset="utf-8">
<title>OneLiner</title>
<link rel="shortcut icon" href="<?=DIR_IMG?>/favicon.ico">
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
$files = scandir(DIR_CM."/theme");
foreach($files as $file) {
	if (!in_array($file, array(".","..","index.html")))
		print "<link rel=\"stylesheet\" href=\"".DIR_CM."/theme/$file\" type=\"text/css\">";
}
?>
<!-- jQuery -->
<script src="<?=DIR_JS?>/jquery-1.10.2.min.js" type="text/javascript"></script>
<!-- Proprietary -->
<script src="<?=DIR_JS?>/scripts.js" type="text/javascript"></script>
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
			try {
				eval("for (var i of []);");
				for (part of parts) {
					if (part != "") {
						newParts.push(part);
					}
				}
			} catch(ex) {
				$.each(parts, function(i, part) {
					if (part != "") {
						newParts.push(part);
					}
				});
			}
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
	$("#theme").change(OL.events.selectTheme);
});
</script>
</head>
<body>
	<div id="wrapper">
    	<header>
            <h1>OneLiner</h1>
            <span>reduce your text</span>
            <select id="theme">
                <option value="">Select a theme</option>
				<?php
                $files = scandir(DIR_CM."/theme");
                foreach($files as $file) {
                    if (!in_array($file, array(".","..","index.html"))) {
                        $name = basename($file, ".css");
                        $nameFormatted = ucwords(str_replace("-"," ",str_replace("xq", "XQ", $name)));
                        print "<option value=\"$name\">$nameFormatted</option>\r\n";
                    }
                }
                ?>
            </select>
        </header>
    	<form id="main" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
        	<div id="input_wrapper">
    			<textarea id="input" class="box" placeholder="Enter text here"></textarea>
            </div>
            <input class="button" type="submit" value="Reduce">
        </form>
    </div>
    <script type="text/javascript">
	var choice = document.location.search && decodeURIComponent(document.location.search.slice(1)),
		pending,
		input = document.getElementById("theme");
	editor = CodeMirror.fromTextArea($("#input")[0], {
		continuousScanning: 500,
		indentUnit: 4,
		lineNumbers: true,
		lineWrapping: true,/*
		parserfile: ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js", "tokenizephp.js", "parsephp.js", "parsephphtmlmixed.js"],
		stylesheet: ["<?=DIR_CM?>/ext/xmlcolors.css", "<?=DIR_CM?>/ext/jscolors.css", "<?=DIR_CM?>/ext/csscolors.css", "<?=DIR_CM?>/ext/phpcolors.css"],
		path: "<?=DIR_CM?>/ext/",*/
		tabMode: "indent"
	});
	editor.on("change", function() {
		clearTimeout(pending);
		setTimeout(OL.utils.setMode, 400);
	});
	if (choice || typeof localStorage["theme"] !== 'undefined') {
		if (typeof localStorage["theme"] !== 'undefined')
			choice = localStorage["theme"];
		for (var i = 0; i < input.length; i++) {
			var option = input.options[i];
			if (option.value == choice) {
				option.selected = true;
				editor.setOption("theme", choice);	
			}
		}
	}
	OL.events.selectTheme = function() {
		var theme = input.options[input.selectedIndex].value;
		localStorage["theme"] = theme;
		console.debug("%cTheme:%c " + theme, "font-weight:bold", "font-weight:normal");
		editor.setOption("theme", theme);
	}
	OL.utils.setMixedMode = function(enabled=true) {
		editor.setOption("path", ((enabled) ? "<?=DIR_CM?>/ext/" : ""));
		editor.setOption("parserfile", ((enabled) ? ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js", "tokenizephp.js", "parsephp.js", "parsephphtmlmixed.js"] : []));
		editor.setOption("stylesheet", ((enabled) ? ["<?=DIR_CM?>/ext/xmlcolors.css", "<?=DIR_CM?>/ext/jscolors.css", "<?=DIR_CM?>/ext/csscolors.css", "<?=DIR_CM?>/ext/phpcolors.css"] : []));
	}
	OL.utils.getMode = function(code) {
		var mode,
			jsKeywords = /(^\s*\(\s*function\b|alert|document|window|location|getElementsByName|getItems|open|close|write|writeln|execCommand|queryCommandEnabled|queryCommandIndeterm|queryCommandState|queryCommandSupported|queryCommandValue|clear|getSelection|captureEvents|releaseEvents|routeEvent|domain|cookie|body|head|images|embeds|plugins|links|forms|scripts|designMode|fgColor|linkColor|vlinkColor|alinkColor|bgColor|anchors|applets|getElementsByTagName|getElementsByTagNameNS|getElementsByClassName|getElementById|createElement|createElementNS|createDocumentFragment|createTextNode|createComment|createProcessingInstruction|importNode|adoptNode|createEvent|createRange|createNodeIterator|createTreeWalker|createCDATASection|createAttribute|createAttributeNS|hasFocus|releaseCapture|enableStyleSheetsForSet|elementFromPoint|caretPositionFromPoint|querySelector|querySelectorAll|getAnonymousNodes|getAnonymousElementByAttribute|getBindingParent|loadBindingDocument|createExpression|createNSResolver|evaluate|implementation|URL|documentURI|compatMode|characterSet|contentType|doctype|documentElement|inputEncoding|referrer|lastModified|readyState|title|dir|defaultView|activeElement|onreadystatechange|onmouseenter|onmouseleave|onwheel|oncopy|oncut|onpaste|onbeforescriptexecute|onafterscriptexecute|currentScript|hidden|visibilityState|styleSheets|selectedStyleSheetSet|lastStyleSheetSet|preferredStyleSheetSet|styleSheetSets|onabort|oncanplay|oncanplaythrough|onchange|onclick|oncontextmenu|ondblclick|ondrag|ondragend|ondragenter|ondragleave|ondragover|ondragstart|ondrop|ondurationchange|onemptied|onended|oninput|oninvalid|onkeydown|onkeypress|onkeyup|onloadeddata|onloadedmetadata|onloadstart|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onpause|onplay|onplaying|onprogress|onratechange|onreset|onseeked|onseeking|onselect|onshow|onstalled|onsubmit|onsuspend|ontimeupdate|onvolumechange|onwaiting|onononononblur|onerror|onfocus|onload|onscroll|hasChildNodes|insertBefore|appendChild|replaceChild|removeChild|normalize|cloneNode|isEqualNode|compareDocumentPosition|contains|lookupPrefix|lookupNamespaceURI|isDefaultNamespace|hasAttributes|nodeType|nodeName|baseURI|ownerDocument|parentNode|parentElement|childNodes|firstChild|lastChild|previousSibling|nextSibling|nodeValue|textContent|namespaceURI|prefix|localName|addEventListener|removeEventListener|dispatchEvent)/gi;
		if (/\<.*\>/gi.test(code) && jsKeywords.test(code) && (/\<\?php/gi.test(code) || /\<\?/gi.test(code)))
			mode = "mixed";
		else if (jsKeywords.test(code) || /^\s*[;\(]/.test(code))
			mode = "javascript";
		else if (/(select|count|insert|update|delete|from)/gi.test(code))
			mode = "sql";
		else if (/(\<\?php|print|echo|foreach)/gi.test(code))
			mode = "php";
		/*else if ()
			mode = "";
		else if ()
			mode = "";*/
		else if (typeof mode === 'undefined')
			mode = "text/html";
		console.debug(mode);
		return mode;
	}
	OL.utils.setMode = function () {
		var mode = OL.utils.getMode(editor.getValue());
		if (mode == "mixed")
			OL.utils.setMixedMode();
		else
			editor.setOption("mode", mode);
	}
	</script>
</body>
</html>