<?php
define("VER_JQ", "1.10.2");
define("VER_JQUI", "1.10.3");
define("DIR_ASSETS", "assets");
define("DIR_CSS", DIR_ASSETS."/css");
define("DIR_IMG", DIR_ASSETS."/img");
define("DIR_JS", DIR_ASSETS."/js");
define("DIR_CON", DIR_JS."/Console");
define("DIR_JQUI", DIR_JS."/jquery-ui");
define("DIR_CM", DIR_JS."/codemirror");
?><!doctype html>
<html><head>
<meta charset="utf-8">
<title>OneLiner</title>
<link rel="shortcut icon" href="<?=DIR_IMG?>/favicon.ico">
<!-- Console.js -->
<script src="<?=DIR_CON?>/Console.js" type="text/javascript"></script>
<!-- CodeMirror -->
<link rel="stylesheet" href="<?=DIR_CM?>/lib/codemirror.css" type="text/css">
<script src="<?=DIR_CM?>/lib/codemirror.js"></script>
<!-- Modes -->
<?php
$dirs = array_diff(scandir(DIR_CM."/mode"), array(".","..","index.html","meta.js","rpm"));
foreach($dirs as $dir)
	print "<script src=\"".DIR_CM."/mode/$dir/$dir.js\"></script>\r\n";
?>
<!-- jQuery -->
<script src="<?=DIR_JS?>/jquery-<?=VER_JQ?>.min.js" type="text/javascript"></script>
<!-- jQuery UI -->
<link href="<?=DIR_JQUI?>/css/redmond/jquery-ui-<?=VER_JQUI?>.min.css" rel="stylesheet" type="text/css">
<script src="<?=DIR_JQUI?>/jquery-ui-<?=VER_JQUI?>.min.js" type="text/javascript"></script>
<!-- Proprietary -->
<link rel="stylesheet" href="<?=DIR_CSS?>/styles.css" type="text/css">
<script src="<?=DIR_JS?>/scripts.js" type="text/javascript"></script>
<!-- Themes -->
<?php
$files = array_diff(scandir(DIR_CM."/theme"), array(".","..","index.html"));
foreach($files as $file)
	print "<link rel=\"stylesheet\" href=\"".DIR_CM."/theme/$file\" type=\"text/css\">\r\n";
?>
<script type="text/javascript">
// One of two global variables declared (OL and editor).
var editor;
$(function() {
	var input = document.getElementById("theme"),
		modal = { modal: true },
		pending,
		reverseStyles = { color: "#000000", fontStyle: "normal" },
		styles = { color: "#AAAAAA", fontStyle: "italic" };
	if (!String.prototype.reduce) {
		String.prototype.reduce = function() {
			var s = this,
				parts = s.split("\n").join(" ").replace(/[\t\r]/gi, "").trim().split(" "),
				newParts = [],
				newString = "";
			try {
				eval("for (part of parts) { if (part != '') { newParts.push(part); } }");
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
	if (!String.prototype.ucwords) {
		String.prototype.ucwords = function() {
			var str = this;
			return (str + '').replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function ($1) {
				return $1.toUpperCase();
			});
		}
	}
	OL.globals = { theme: OL.utils.getQueryVar("theme") || localStorage["theme"] || "" };
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
	editor.focus();
	editor.on("change", function() {
		clearTimeout(pending);
		setTimeout(OL.utils.setMode, 400);
	});
	
	// Events, etc.
	OL.events.reduce = function(e){
		var input = editor.getValue().reduce();
		if (input == "")
			$('<div title="Warning">Enter some text.</div>').dialog(modal);
		else
			editor.setValue(input);
		e.preventDefault();
	}
	OL.events.setTheme = function() {
		//OL.globals = { theme: OL.utils.getQueryVar("theme") || localStorage["theme"] || "" };
		var theme = input.options[input.selectedIndex].value;
		localStorage["theme"] = theme;
		console.debug("%cTheme:%c " + theme.replace("-", " ").ucwords(), "font-weight:bold", "font-weight:normal");
		editor.setOption("theme", theme);
	}
	OL.utils.setMixedMode = function(enabled) {
		if (typeof enabled === 'undefined') enabled = true;
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
		//console.debug(mode);
		return mode;
	}
	OL.utils.setMode = function () {
		// Determine the mode from the given text (detect the programming/scripting language).
		var mode = OL.utils.getMode(editor.getValue());
		// If the mode is mixed, set it so.
		if (mode == "mixed")
			OL.utils.setMixedMode();
		// Otherwise, set the mode detected.
		else
			editor.setOption("mode", mode);
	}
	
	// Deal with themes.
	if (OL.globals.theme) {
		for (var i = 0; i < input.length; i++) {
			var option = input.options[i];
			if (option.value == OL.globals.theme) {
				option.selected = true;
				editor.setOption("theme", OL.globals.theme);	
			}
		}
	}
	$("select").each(function(i, v) {
		var $this = $(this);
		$this.find("option").each(function(j, o) {
			var $option = $(this),
				value = $option.val();
			if (value.trim() == "" || /select/gi.test(value)) {
				$option.addClass("placeholder").removeClass("normal");
			} else {
				$option.addClass("normal").removeClass("placeholder");
			}
		});
		if ($this[0].selectedIndex == 0)
			$this.css(styles);
	}).change(function(e) {
		var $this = $(this),
			currentIndex = $this[0].selectedIndex;
		//console.debug(currentIndex);
		if (currentIndex == 0)
			$this.addClass("placeholder").removeClass("normal");
		else
			$this.addClass("normal").removeClass("placeholder");
	});
	$("#main").submit(OL.events.reduce);
	$("input[type=submit]").click(OL.events.reduce);
	$("#theme").change(OL.events.setTheme);
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
    <script type="text/javascript">;
	(function($) {
	})(jQuery);
	</script>
</body>
</html>