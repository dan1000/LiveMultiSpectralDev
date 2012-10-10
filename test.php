<?php

    function getAllSubdirectories($base) {
    $dir_array = array();
     
    if ($dh = opendir($base)) {
	
    while (($file=readdir($dh)) !== false) {
    if ($file == '.' || $file == '..') continue;
     
    if (is_dir($base.'/'.$file)) {
	if(isset($_GET['dirname']) && $file == $_GET['dirname']) {
		echo '<option value=$file selected="selected">'.$file.'</option>';				
	}
	else {
		if(isset($_GET['fdirname']) && $file == $_GET['fdirname']) {
			echo "<option value=$file selected='selected'>".$file."</option>";	
		}
		else {
			echo "<option value=$file>".$file."</option>";			
		}
	}

    }
    }
	echo "</select>";
    closedir($dh);
    }
    }
	
    function getAllSubfiles($base) {
	$dir_array = array();
    if ($dh = opendir('multispectral/'.$base)) {
	$startString="";
	$endString="";
	$numChannels="";
	$description="";
		 
		if (is_file('multispectral/'.$base.'/Metadata.txt')) 
		{
		
					$handle = @fopen('multispectral/'.$base.'/Metadata.txt', "r");
					if ($handle) 
					{
						while (($buffer = fgets($handle, 4096)) !== false) {
						$description = $description.$buffer;
						if ( strstr( $buffer, "base_name" ) )
							{
								$startString=str_replace("base_name,", "", trim($buffer));
							}	
						if ( strstr( $buffer, "end_string" ) )
							{
								$endString=str_replace("end_string,", "", trim($buffer));
							}	
						if ( strstr( $buffer, "Num_Channels" ) )
							{
								$numChannels=str_replace("Num_Channels,", "", trim($buffer));
							}								
						}
						
						if (!feof($handle)) {
							echo "Error: unexpected fgets() fail\n";
						}
						fclose($handle);
					}


		}
		$fname = "";
		while (($file=readdir($dh)) !== false) {
		if ($file == '.' || $file == '..') continue;
		 
		if (is_file('multispectral/'.$base.'/'.$file)) {

			if ($file != 'Metadata.txt') {
				if ($numChannels == '1') {
					if ($fname != substr(str_replace($endString, "", str_replace($startString, "", $file)), 0, strlen(str_replace($endString, "", str_replace($startString, "", $file))))) {
						$fname = substr(str_replace($endString, "", str_replace($startString, "", $file)), 0, strlen(str_replace($endString, "", str_replace($startString, "", $file))));
						if(isset($_GET['filename']) && ($startString.$fname == $_GET['filename'])) {
							echo "<option value='".$startString.$fname."' selected='selected'>".$startString.$fname."</option>";				
						}
						else {
							echo "<option value='".$startString.$fname."'>".$startString.$fname."</option>";						
						}
						
					}				
				}
				else {
					if ($fname != substr(str_replace($endString, "", str_replace($startString, "", $file)), 0, (strlen(str_replace($endString, "", str_replace($startString, "", $file))) - 2))) {
						$fname = substr(str_replace($endString, "", str_replace($startString, "", $file)), 0, (strlen(str_replace($endString, "", str_replace($startString, "", $file))) - 2));
						if(isset($_GET['filename']) && ($startString.$fname == $_GET['filename'])) {
							echo "<option value='".$startString.$fname."' selected='selected'>".$startString.$fname."</option>";				
						}
						else {
							echo "<option value='".$startString.$fname."'>".$startString.$fname."</option>";						
						}
						
					}
				}
			}
		}
		}	
		echo "</select>";
		echo "<input type='hidden' name='numChannels' value='".$numChannels."'>";
		echo "<input type='hidden' name='endString' value='".$endString."'>";
		echo "<input type='hidden' name='fdirname' value='".$base."'>";
		echo "</form>";	
		echo "<textarea rows='10' cols='50'>".$description."</textarea>";
		closedir($dh);
    }
    }	
	
	
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Web Based Multichannel Image Viewer</title>
	
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link type="text/css" href="lib/jqueryui/flora.all.css" rel="stylesheet" />
		<script type="text/javascript" src="lib/jquery/jquery-1.2.6.min.js"></script>
		<script type="text/javascript" src="lib/jqueryui/jquery-ui-1.6.custom.min.js"></script>
		<script type="text/javascript" src="lib/pixastic/pixastic-0.1.3.js"></script>
		
		<style type="text/css">
		/* Stacks and Panels
		/* ========================================================================== */

		.stack, .panel { display: inline-block; vertical-align: top; width: 100%; }
		.stack { font-size: 0; text-align: justify; }
		.stack:after { content: ""; display: inline-block; width: 100%; }
		.panel { font-size: 16px; font-size: 1rem; text-align: left; }

		/* Stacks and Panels (for oldIEs)
		/* ========================================================================== */

		.stack, .panel { *display: inline; *zoom: 1; } .iestack { *letter-spacing: -10px; } .iestack .panel { *letter-spacing: 0; }
		.stack { *-ms-stack: expression(this.firstChild.className!="iestack"&&this.appendChild((function(c,e,i){e.className="iestack";while(c.length)e.appendChild(c[0]);return e})(this.childNodes,document.createElement("div"),0))); }

		/* Default View
		/* -------------------------------------------------------------------------- */

		.p2, .p3, .p4 { width: 33.3333%; }

		/* Handheld Portrait View
		/* -------------------------------------------------------------------------- */

		@media screen and (max-width: 479px) {
			.stack, .panel { width: 100%; }
		}

		/* Handheld Landscape View
		/* -------------------------------------------------------------------------- */

		@media screen and (min-width: 480px) and (max-width: 719px) {
			.stack, .panel { width: 100%; }
			/* .p2, .p3 { width: 50%; } */
		}

		/* Tablet Landscape / Desktop View
		/* -------------------------------------------------------------------------- */

		@media screen and (min-width: 960px) {
			.stack, .panel { width: 100%; }
			.s1 { max-width:  1200px; }
			.s2 { width:  100%; }
			.p1 { width:  100%; }
			.p2 { width:  33.3333%; }
			.p3 { width:  33.3333%; }
			.p4 { width:  33.3333%; }
		}

		/* Design
		/* ========================================================================== */

		html { font: 100%/1 sans-serif; }
		body { margin: 1em; text-align: center; }

		.panel span { display: block; margin: 0.25em; padding: 1em; text-align: center; }
		.panel span { background: #CCC; border-radius: 0.5em; }
		</style>
		<?php if (isset($_GET["numChannels"])) { 
			if ((int)$_GET["numChannels"] >= 1) {
		?>
		<script type="text/javascript">
		/* Channel-1 */
		function demo1() {
			Pixastic.revert(document.getElementById("channel-1-img"));

			Pixastic.process(document.getElementById("channel-1-img"), "unsharpmask", {
				threshold: $("#value-threshold-1").val()
			});

			Pixastic.process(document.getElementById("channel-1-img"), "coloradjust", {
				red : $("#value-red-1").val(),
				green : $("#value-green-1").val(),
				blue : $("#value-blue-1").val()
			});
			$("#channel-1-img").animate({ opacity: $("#value-opacity-1").val() }, 0);
			
			$("#channel-1-img").css( 'z-index', $("#value-zindex-1").val() );
			
			Pixastic.revert(document.getElementById("channel-11-img"));

			Pixastic.process(document.getElementById("channel-11-img"), "unsharpmask", {
				threshold: $("#value-threshold-1").val()
			});

			Pixastic.process(document.getElementById("channel-11-img"), "coloradjust", {
				red : $("#value-red-1").val(),
				green : $("#value-green-1").val(),
				blue : $("#value-blue-1").val()
			});
			$("#channel-11-img").animate({ opacity: $("#value-opacity-1").val() }, 0);

			$("#channel-11-img").css( 'z-index', $("#value-zindex-1").val() );

			}
		

		$(document).ready(function(){
			$("#slider-red-1").slider({
					slide: function() {
						$("#value-red-1").val((($("#slider-red-1").slider("value") / 100 * 2) - 1).toFixed(2));
						demo1();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-green-1").slider({
					slide: function() {
						$("#value-green-1").val((($("#slider-green-1").slider("value") / 100 * 2) - 1).toFixed(2));
						demo1();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-blue-1").slider({
					slide: function() {
						$("#value-blue-1").val((($("#slider-blue-1").slider("value") / 100 * 2) - 1).toFixed(2));
						demo1();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-threshold-1").slider({
					slide: function() {
						$("#value-threshold-1").val(($("#slider-threshold-1").slider("value") * 2.55).toFixed(0));
						demo1();
					}, value : 0
			}).slider("moveTo", 0);	
			$("#slider-opacity-1").slider({
					slide: function() {
						$("#value-opacity-1").val(($("#slider-opacity-1").slider("value") * 0.01).toFixed(2));
						demo1();
					}, value : 50
			}).slider("moveTo", 50);	
			$("#slider-zindex-1").slider({
					slide: function() {
						$("#value-zindex-1").val(($("#slider-zindex-1").slider("value") * 0.08).toFixed(0));
						demo1();
					}, value : 0
			}).slider("moveTo", 0);	

		});
		</script>
		<?php } 
			}
		?>

		<?php if (isset($_GET["numChannels"])) { 
			if ((int)$_GET["numChannels"] >= 2) {
		?>
		<script type="text/javascript">

		/* Channel-2 */
		function demo2() {
			Pixastic.revert(document.getElementById("channel-2-img"));

			Pixastic.process(document.getElementById("channel-2-img"), "unsharpmask", {
				threshold: $("#value-threshold-2").val()
			});

			Pixastic.process(document.getElementById("channel-2-img"), "coloradjust", {
				red : $("#value-red-2").val(),
				green : $("#value-green-2").val(),
				blue : $("#value-blue-2").val()
			});
			$("#channel-2-img").animate({ opacity: $("#value-opacity-2").val() }, 0);
			
			$("#channel-2-img").css( 'z-index', $("#value-zindex-2").val() );
			
			Pixastic.revert(document.getElementById("channel-22-img"));

			Pixastic.process(document.getElementById("channel-22-img"), "unsharpmask", {
				threshold: $("#value-threshold-2").val()
			});

			Pixastic.process(document.getElementById("channel-22-img"), "coloradjust", {
				red : $("#value-red-2").val(),
				green : $("#value-green-2").val(),
				blue : $("#value-blue-2").val()
			});
			$("#channel-22-img").animate({ opacity: $("#value-opacity-2").val() }, 0);

			$("#channel-22-img").css( 'z-index', $("#value-zindex-2").val() );

			}
		

		$(document).ready(function(){
			$("#slider-red-2").slider({
					slide: function() {
						$("#value-red-2").val((($("#slider-red-2").slider("value") / 100 * 2) - 1).toFixed(2));
						demo2();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-green-2").slider({
					slide: function() {
						$("#value-green-2").val((($("#slider-green-2").slider("value") / 100 * 2) - 1).toFixed(2));
						demo2();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-blue-2").slider({
					slide: function() {
						$("#value-blue-2").val((($("#slider-blue-2").slider("value") / 100 * 2) - 1).toFixed(2));
						demo2();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-threshold-2").slider({
					slide: function() {
						$("#value-threshold-2").val(($("#slider-threshold-2").slider("value") * 2.55).toFixed(0));
						demo2();
					}, value : 0
			}).slider("moveTo", 0);	
			$("#slider-opacity-2").slider({
					slide: function() {
						$("#value-opacity-2").val(($("#slider-opacity-2").slider("value") * 0.01).toFixed(2));
						demo2();
					}, value : 50
			}).slider("moveTo", 50);	
			$("#slider-zindex-2").slider({
					slide: function() {
						$("#value-zindex-2").val(($("#slider-zindex-2").slider("value") * 0.08).toFixed(0));
						demo2();
					}, value : 12
			}).slider("moveTo", 12);	

		});
		</script>
		<?php } 
			}
		?>

		<?php if (isset($_GET["numChannels"])) { 
			if ((int)$_GET["numChannels"] >= 3) {
		?>
		<script type="text/javascript">

		/* Channel-3 */
		function demo3() {
			Pixastic.revert(document.getElementById("channel-3-img"));

			Pixastic.process(document.getElementById("channel-3-img"), "unsharpmask", {
				threshold: $("#value-threshold-3").val()
			});

			Pixastic.process(document.getElementById("channel-3-img"), "coloradjust", {
				red : $("#value-red-3").val(),
				green : $("#value-green-3").val(),
				blue : $("#value-blue-3").val()
			});
			
			$("#channel-3-img").animate({ opacity: $("#value-opacity-3").val() }, 0);

			$("#channel-3-img").css( 'z-index', $("#value-zindex-3").val() );
			
			Pixastic.revert(document.getElementById("channel-33-img"));

			Pixastic.process(document.getElementById("channel-33-img"), "unsharpmask", {
				threshold: $("#value-threshold-3").val()
			});

			Pixastic.process(document.getElementById("channel-33-img"), "coloradjust", {
				red : $("#value-red-3").val(),
				green : $("#value-green-3").val(),
				blue : $("#value-blue-3").val()
			});
			$("#channel-33-img").animate({ opacity: $("#value-opacity-3").val() }, 0);

			$("#channel-33-img").css( 'z-index', $("#value-zindex-3").val() );

			}
		

		$(document).ready(function(){
			$("#slider-red-3").slider({
					slide: function() {
						$("#value-red-3").val((($("#slider-red-3").slider("value") / 100 * 2) - 1).toFixed(2));
						demo3();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-green-3").slider({
					slide: function() {
						$("#value-green-3").val((($("#slider-green-3").slider("value") / 100 * 2) - 1).toFixed(2));
						demo3();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-blue-3").slider({
					slide: function() {
						$("#value-blue-3").val((($("#slider-blue-3").slider("value") / 100 * 2) - 1).toFixed(2));
						demo3();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-threshold-3").slider({
					slide: function() {
						$("#value-threshold-3").val(($("#slider-threshold-3").slider("value") * 2.55).toFixed(0));
						demo3();
					}, value : 0
			}).slider("moveTo", 0);	
			$("#slider-opacity-3").slider({
					slide: function() {
						$("#value-opacity-3").val(($("#slider-opacity-3").slider("value") * 0.01).toFixed(2));
						demo3();
					}, value : 50
			}).slider("moveTo", 50);	
			$("#slider-zindex-3").slider({
					slide: function() {
						$("#value-zindex-3").val(($("#slider-zindex-3").slider("value") * 0.08).toFixed(0));
						demo3();
					}, value : 24
			}).slider("moveTo", 24);	

		});
		</script>
		<?php } 
			}
		?>

		<?php if (isset($_GET["numChannels"])) { 
			if ((int)$_GET["numChannels"] >= 4) {
		?>
		<script type="text/javascript">
		/* Channel-4 */
		function demo4() {
			Pixastic.revert(document.getElementById("channel-4-img"));

			Pixastic.process(document.getElementById("channel-4-img"), "unsharpmask", {
				threshold: $("#value-threshold-4").val()
			});

			Pixastic.process(document.getElementById("channel-4-img"), "coloradjust", {
				red : $("#value-red-4").val(),
				green : $("#value-green-4").val(),
				blue : $("#value-blue-4").val()
			});
			$("#channel-4-img").animate({ opacity: $("#value-opacity-4").val() }, 0);
			
			$("#channel-4-img").css( 'z-index', $("#value-zindex-4").val() );
			
			Pixastic.revert(document.getElementById("channel-44-img"));

			Pixastic.process(document.getElementById("channel-44-img"), "unsharpmask", {
				threshold: $("#value-threshold-4").val()
			});

			Pixastic.process(document.getElementById("channel-44-img"), "coloradjust", {
				red : $("#value-red-4").val(),
				green : $("#value-green-4").val(),
				blue : $("#value-blue-4").val()
			});
			$("#channel-44-img").animate({ opacity: $("#value-opacity-4").val() }, 0);

			$("#channel-44-img").css( 'z-index', $("#value-zindex-4").val() );

			}
		

		$(document).ready(function(){
			$("#slider-red-4").slider({
					slide: function() {
						$("#value-red-4").val((($("#slider-red-4").slider("value") / 100 * 2) - 1).toFixed(2));
						demo4();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-green-4").slider({
					slide: function() {
						$("#value-green-4").val((($("#slider-green-4").slider("value") / 100 * 2) - 1).toFixed(2));
						demo4();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-blue-4").slider({
					slide: function() {
						$("#value-blue-4").val((($("#slider-blue-4").slider("value") / 100 * 2) - 1).toFixed(2));
						demo4();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-threshold-4").slider({
					slide: function() {
						$("#value-threshold-4").val(($("#slider-threshold-4").slider("value") * 2.55).toFixed(0));
						demo4();
					}, value : 0
			}).slider("moveTo", 0);	
			$("#slider-opacity-4").slider({
					slide: function() {
						$("#value-opacity-4").val(($("#slider-opacity-4").slider("value") * 0.01).toFixed(2));
						demo4();
					}, value : 50
			}).slider("moveTo", 50);	
			$("#slider-zindex-4").slider({
					slide: function() {
						$("#value-zindex-4").val(($("#slider-zindex-4").slider("value") * 0.08).toFixed(0));
						demo4();
					}, value : 36
			}).slider("moveTo", 36);	

		});
		</script>
		<?php } 
			}
		?>
		<?php if (isset($_GET["numChannels"])) { 
			if ((int)$_GET["numChannels"] >= 5) {
		?>
		<script type="text/javascript">

		/* Channel-5 */
		function demo5() {
			Pixastic.revert(document.getElementById("channel-5-img"));

			Pixastic.process(document.getElementById("channel-5-img"), "unsharpmask", {
				threshold: $("#value-threshold-5").val()
			});

			Pixastic.process(document.getElementById("channel-5-img"), "coloradjust", {
				red : $("#value-red-5").val(),
				green : $("#value-green-5").val(),
				blue : $("#value-blue-5").val()
			});
			$("#channel-5-img").animate({ opacity: $("#value-opacity-5").val() }, 0);
			
			$("#channel-5-img").css( 'z-index', $("#value-zindex-5").val() );
			
			Pixastic.revert(document.getElementById("channel-55-img"));

			Pixastic.process(document.getElementById("channel-55-img"), "unsharpmask", {
				threshold: $("#value-threshold-5").val()
			});

			Pixastic.process(document.getElementById("channel-55-img"), "coloradjust", {
				red : $("#value-red-5").val(),
				green : $("#value-green-5").val(),
				blue : $("#value-blue-5").val()
			});
			$("#channel-55-img").animate({ opacity: $("#value-opacity-5").val() }, 0);

			$("#channel-55-img").css( 'z-index', $("#value-zindex-5").val() );

			}
		

		$(document).ready(function(){
			$("#slider-red-5").slider({
					slide: function() {
						$("#value-red-5").val((($("#slider-red-5").slider("value") / 100 * 2) - 1).toFixed(2));
						demo5();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-green-5").slider({
					slide: function() {
						$("#value-green-5").val((($("#slider-green-5").slider("value") / 100 * 2) - 1).toFixed(2));
						demo5();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-blue-5").slider({
					slide: function() {
						$("#value-blue-5").val((($("#slider-blue-5").slider("value") / 100 * 2) - 1).toFixed(2));
						demo5();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-threshold-5").slider({
					slide: function() {
						$("#value-threshold-5").val(($("#slider-threshold-5").slider("value") * 2.55).toFixed(0));
						demo5();
					}, value : 0
			}).slider("moveTo", 0);	
			$("#slider-opacity-5").slider({
					slide: function() {
						$("#value-opacity-5").val(($("#slider-opacity-5").slider("value") * 0.01).toFixed(2));
						demo5();
					}, value : 50
			}).slider("moveTo", 50);	
			$("#slider-zindex-5").slider({
					slide: function() {
						$("#value-zindex-5").val(($("#slider-zindex-5").slider("value") * 0.08).toFixed(0));
						demo5();
					}, value : 48
			}).slider("moveTo", 48);	

		});
		</script>
		<?php } 
			}
		?>

		<?php if (isset($_GET["numChannels"])) { 
			if ((int)$_GET["numChannels"] >= 6) {
		?>
		<script type="text/javascript">
		
		/* Channel-6 */
		function demo6() {
			Pixastic.revert(document.getElementById("channel-6-img"));

			Pixastic.process(document.getElementById("channel-6-img"), "unsharpmask", {
				threshold: $("#value-threshold-6").val()
			});

			Pixastic.process(document.getElementById("channel-6-img"), "coloradjust", {
				red : $("#value-red-6").val(),
				green : $("#value-green-6").val(),
				blue : $("#value-blue-6").val()
			});
			$("#channel-6-img").animate({ opacity: $("#value-opacity-6").val() }, 0);
			
			$("#channel-6-img").css( 'z-index', $("#value-zindex-6").val() );
			
			Pixastic.revert(document.getElementById("channel-66-img"));

			Pixastic.process(document.getElementById("channel-66-img"), "unsharpmask", {
				threshold: $("#value-threshold-6").val()
			});

			Pixastic.process(document.getElementById("channel-66-img"), "coloradjust", {
				red : $("#value-red-6").val(),
				green : $("#value-green-6").val(),
				blue : $("#value-blue-6").val()
			});
			$("#channel-66-img").animate({ opacity: $("#value-opacity-6").val() }, 0);

			$("#channel-66-img").css( 'z-index', $("#value-zindex-6").val() );

			}
		

		$(document).ready(function(){
			$("#slider-red-6").slider({
					slide: function() {
						$("#value-red-6").val((($("#slider-red-6").slider("value") / 100 * 2) - 1).toFixed(2));
						demo6();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-green-6").slider({
					slide: function() {
						$("#value-green-6").val((($("#slider-green-6").slider("value") / 100 * 2) - 1).toFixed(2));
						demo6();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-blue-6").slider({
					slide: function() {
						$("#value-blue-6").val((($("#slider-blue-6").slider("value") / 100 * 2) - 1).toFixed(2));
						demo6();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-threshold-6").slider({
					slide: function() {
						$("#value-threshold-6").val(($("#slider-threshold-6").slider("value") * 2.55).toFixed(0));
						demo6();
					}, value : 0
			}).slider("moveTo", 0);	
			$("#slider-opacity-6").slider({
					slide: function() {
						$("#value-opacity-6").val(($("#slider-opacity-6").slider("value") * 0.01).toFixed(2));
						demo6();
					}, value : 50
			}).slider("moveTo", 50);	
			$("#slider-zindex-6").slider({
					slide: function() {
						$("#value-zindex-6").val(($("#slider-zindex-6").slider("value") * 0.08).toFixed(0));
						demo6();
					}, value : 60
			}).slider("moveTo", 60);	

		});
		</script>
		<?php } 
			}
		?>
		<?php if (isset($_GET["numChannels"])) { 
			if ((int)$_GET["numChannels"] >= 7) {
		?>
		<script type="text/javascript">

		/* Channel-7 */
		function demo7() {
			Pixastic.revert(document.getElementById("channel-7-img"));

			Pixastic.process(document.getElementById("channel-7-img"), "unsharpmask", {
				threshold: $("#value-threshold-7").val()
			});

			Pixastic.process(document.getElementById("channel-7-img"), "coloradjust", {
				red : $("#value-red-7").val(),
				green : $("#value-green-7").val(),
				blue : $("#value-blue-7").val()
			});
			$("#channel-7-img").animate({ opacity: $("#value-opacity-7").val() }, 0);
			
			$("#channel-7-img").css( 'z-index', $("#value-zindex-7").val() );
			
			Pixastic.revert(document.getElementById("channel-77-img"));

			Pixastic.process(document.getElementById("channel-77-img"), "unsharpmask", {
				threshold: $("#value-threshold-7").val()
			});

			Pixastic.process(document.getElementById("channel-77-img"), "coloradjust", {
				red : $("#value-red-7").val(),
				green : $("#value-green-7").val(),
				blue : $("#value-blue-7").val()
			});
			$("#channel-77-img").animate({ opacity: $("#value-opacity-7").val() }, 0);

			$("#channel-77-img").css( 'z-index', $("#value-zindex-7").val() );

			}
		

		$(document).ready(function(){
			$("#slider-red-7").slider({
					slide: function() {
						$("#value-red-7").val((($("#slider-red-7").slider("value") / 100 * 2) - 1).toFixed(2));
						demo7();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-green-7").slider({
					slide: function() {
						$("#value-green-7").val((($("#slider-green-7").slider("value") / 100 * 2) - 1).toFixed(2));
						demo7();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-blue-7").slider({
					slide: function() {
						$("#value-blue-7").val((($("#slider-blue-7").slider("value") / 100 * 2) - 1).toFixed(2));
						demo7();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-threshold-7").slider({
					slide: function() {
						$("#value-threshold-7").val(($("#slider-threshold-7").slider("value") * 2.55).toFixed(0));
						demo7();
					}, value : 0
			}).slider("moveTo", 0);	
			$("#slider-opacity-7").slider({
					slide: function() {
						$("#value-opacity-7").val(($("#slider-opacity-7").slider("value") * 0.01).toFixed(2));
						demo7();
					}, value : 50
			}).slider("moveTo", 50);	
			$("#slider-zindex-7").slider({
					slide: function() {
						$("#value-zindex-7").val(($("#slider-zindex-7").slider("value") * 0.08).toFixed(0));
						demo7();
					}, value : 72
			}).slider("moveTo", 72);	

		});
		<?php } 
			}
		?>
		<?php if (isset($_GET["numChannels"])) { 
			if ((int)$_GET["numChannels"] >= 8) {
		?>
		<script type="text/javascript">

		/* Channel-8 */
		function demo8() {
			Pixastic.revert(document.getElementById("channel-8-img"));

			Pixastic.process(document.getElementById("channel-8-img"), "unsharpmask", {
				threshold: $("#value-threshold-8").val()
			});

			Pixastic.process(document.getElementById("channel-8-img"), "coloradjust", {
				red : $("#value-red-8").val(),
				green : $("#value-green-8").val(),
				blue : $("#value-blue-8").val()
			});
			$("#channel-8-img").animate({ opacity: $("#value-opacity-8").val() }, 0);
			
			$("#channel-8-img").css( 'z-index', $("#value-zindex-8").val() );
			
			Pixastic.revert(document.getElementById("channel-88-img"));

			Pixastic.process(document.getElementById("channel-88-img"), "unsharpmask", {
				threshold: $("#value-threshold-8").val()
			});

			Pixastic.process(document.getElementById("channel-88-img"), "coloradjust", {
				red : $("#value-red-8").val(),
				green : $("#value-green-8").val(),
				blue : $("#value-blue-8").val()
			});
			$("#channel-88-img").animate({ opacity: $("#value-opacity-8").val() }, 0);

			$("#channel-88-img").css( 'z-index', $("#value-zindex-8").val() );

			}
		

		$(document).ready(function(){
			$("#slider-red-8").slider({
					slide: function() {
						$("#value-red-8").val((($("#slider-red-8").slider("value") / 100 * 2) - 1).toFixed(2));
						demo8();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-green-8").slider({
					slide: function() {
						$("#value-green-8").val((($("#slider-green-8").slider("value") / 100 * 2) - 1).toFixed(2));
						demo8();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-blue-8").slider({
					slide: function() {
						$("#value-blue-8").val((($("#slider-blue-8").slider("value") / 100 * 2) - 1).toFixed(2));
						demo8();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-threshold-8").slider({
					slide: function() {
						$("#value-threshold-8").val(($("#slider-threshold-8").slider("value") * 2.55).toFixed(0));
						demo8();
					}, value : 0
			}).slider("moveTo", 0);	
			$("#slider-opacity-8").slider({
					slide: function() {
						$("#value-opacity-8").val(($("#slider-opacity-8").slider("value") * 0.01).toFixed(2));
						demo8();
					}, value : 50
			}).slider("moveTo", 50);	
			$("#slider-zindex-8").slider({
					slide: function() {
						$("#value-zindex-8").val(($("#slider-zindex-8").slider("value") * 0.08).toFixed(0));
						demo8();
					}, value : 84
			}).slider("moveTo", 84);	

		});
		</script>
		<?php } 
			}
		?>
		<?php if (isset($_GET["numChannels"])) { 
			if ((int)$_GET["numChannels"] >= 9) {
		?>
		<script type="text/javascript">

		/* Channel-9 */
		function demo9() {
			Pixastic.revert(document.getElementById("channel-9-img"));

			Pixastic.process(document.getElementById("channel-9-img"), "unsharpmask", {
				threshold: $("#value-threshold-9").val()
			});

			Pixastic.process(document.getElementById("channel-9-img"), "coloradjust", {
				red : $("#value-red-9").val(),
				green : $("#value-green-9").val(),
				blue : $("#value-blue-9").val()
			});
			$("#channel-9-img").animate({ opacity: $("#value-opacity-9").val() }, 0);
			
			$("#channel-9-img").css( 'z-index', $("#value-zindex-9").val() );
			
			Pixastic.revert(document.getElementById("channel-99-img"));

			Pixastic.process(document.getElementById("channel-99-img"), "unsharpmask", {
				threshold: $("#value-threshold-9").val()
			});

			Pixastic.process(document.getElementById("channel-99-img"), "coloradjust", {
				red : $("#value-red-9").val(),
				green : $("#value-green-9").val(),
				blue : $("#value-blue-9").val()
			});
			$("#channel-99-img").animate({ opacity: $("#value-opacity-9").val() }, 0);

			$("#channel-99-img").css( 'z-index', $("#value-zindex-9").val() );

			}
		

		$(document).ready(function(){
			$("#slider-red-9").slider({
					slide: function() {
						$("#value-red-9").val((($("#slider-red-9").slider("value") / 100 * 2) - 1).toFixed(2));
						demo9();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-green-9").slider({
					slide: function() {
						$("#value-green-9").val((($("#slider-green-9").slider("value") / 100 * 2) - 1).toFixed(2));
						demo9();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-blue-9").slider({
					slide: function() {
						$("#value-blue-9").val((($("#slider-blue-9").slider("value") / 100 * 2) - 1).toFixed(2));
						demo9();
					}, value : 50
			}).slider("moveTo", 50);
			$("#slider-threshold-9").slider({
					slide: function() {
						$("#value-threshold-9").val(($("#slider-threshold-9").slider("value") * 2.55).toFixed(0));
						demo9();
					}, value : 0
			}).slider("moveTo", 0);	
			$("#slider-opacity-9").slider({
					slide: function() {
						$("#value-opacity-9").val(($("#slider-opacity-9").slider("value") * 0.01).toFixed(2));
						demo9();
					}, value : 50
			}).slider("moveTo", 50);	
			$("#slider-zindex-9").slider({
					slide: function() {
						$("#value-zindex-9").val(($("#slider-zindex-9").slider("value") * 0.08).toFixed(0));
						demo9();
					}, value : 96
			}).slider("moveTo", 96);	

		});
		
		</script>
		<?php } 
			}
		?>
		<script type="text/javascript">
		$(document).ready(function(){
			$(".demo-options").hide();
			//toggle the componenet with class msg_body
			$(".headerdemo").click(function()
			{
				$(this).next(".demo-options").slideToggle(500);
			});
		});		
		</script>
		
	</head>
	<body>
		<div class="stack s1">
			<div class="panel p1"><span>
			<?php
				echo "<form method='get' name='frmdir' action=''><select name='dirname'>";
				if(isset($_GET['dirname'])) {
				echo '<option value="">Choose A Directory</option>';				
				}
				else {
				echo '<option value="" selected="selected">Choose A Directory</option>';				
				}
				getAllSubdirectories("multispectral");
				echo "<input type='submit' name='submitbtn' value='Select'>";
				echo "</form>";
	
				if((isset($_GET['dirname']) &&  $_GET['dirname']!= '') || (isset($_GET['filename']) &&  $_GET['filename']!= '')) {
				
					echo "<form method='get' name='frmfile' action=''><select name='filename' onchange='frmfile.submit();'>";
					if(isset($_GET['filename'])) {
					echo '<option value="">Choose A File</option>';
					}
					else {
					echo '<option value="" selected="selected">Choose A File</option>';
					}
					if(isset($_GET['dirname']) &&  $_GET['dirname']!= '') {
						getAllSubfiles($_GET['dirname']);	
					}
					if(isset($_GET['filename']) &&  $_GET['filename']!= '') {
						getAllSubfiles($_GET['fdirname']);	
					}
					
				}
				
			?>
			</span></div>
			<div class="stack s2">
			<?php if (isset($_GET["numChannels"])) { 
				if ((int)$_GET["numChannels"] >= 1) {
			?>
			
				<!-- Channel 1 -->
				<div class="panel p2">
					<div class="actiondemo">
					<div class="headerdemo">
					Channel 1 (..)
					</div>
				
					<div class="demo-options">

					<div>Red: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-red-1"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-red-1">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 70px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Green: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-green-1"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-green-1">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Blue: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-blue-1"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-blue-1">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Image Threshold: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-threshold-1"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-threshold-1">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Opacity: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-opacity-1"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-opacity-1">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Z-Index: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-zindex-1"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-zindex-1">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>

					</div>
					<?php 
					if ((int)$_GET["numChannels"] > 1) {
						echo '<img alt="" id="channel-1-img" src="'.'multispectral/'.$_GET['fdirname'].'/'.$_GET['filename'].'c0'.$_GET['endString'].'" style="width: 400px; height: 300px; z-index: 0;">';										
					}
					else {
						echo '<img alt="" id="channel-1-img" src="'.'multispectral/'.$_GET['fdirname'].'/'.$_GET['filename'].$_GET['endString'].'" style="width: 400px; height: 300px; z-index: 0;">';					
					}
					?>

					</div>				
				</div>
			<?php } 
				}
			?>
			<?php if (isset($_GET["numChannels"])) { 
				if ((int)$_GET["numChannels"] >= 2) {
			?>
				
				<!-- Channel 2 -->
				<div class="panel p3">
					<div class="actiondemo">
					<div class="headerdemo">
					Channel 2 (..)
					</div>				
				
					<div class="demo-options">

					<div>Red: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-red-2"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-red-2">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 70px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Green: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-green-2"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-green-2">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Blue: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-blue-2"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-blue-2">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Image Threshold: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-threshold-2"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-threshold-2">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Opacity: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-opacity-2"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-opacity-2">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Z-Index: <input type="text" style="width:30px;" class="demo-input" value="1" id="value-zindex-2"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-zindex-2">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>

					</div>
					<img alt="" id="channel-2-img" src="<?php echo 'multispectral/'.$_GET['fdirname'].'/'.$_GET['filename'].'c1'.$_GET['endString']; ?>" style="width: 400px; height: 300px; z-index: 1;">
					</div>				
				
				</div>
			<?php } 
				}
			?>
			<?php if (isset($_GET["numChannels"])) { 
				if ((int)$_GET["numChannels"] >= 3) {
			?>
				
				<!-- Channel 3 -->
				<div class="panel p4">
					<div class="actiondemo">
					<div class="headerdemo">
					Channel 3 (..)
					</div>				
				
					<div class="demo-options">

					<div>Red: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-red-3"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-red-3">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 70px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Green: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-green-3"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-green-3">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Blue: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-blue-3"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-blue-3">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Image Threshold: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-threshold-3"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-threshold-3">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Opacity: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-opacity-3"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-opacity-3">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Z-Index: <input type="text" style="width:30px;" class="demo-input" value="2" id="value-zindex-3"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-zindex-3">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>

					</div>
					<img alt="" id="channel-3-img" src="<?php echo 'multispectral/'.$_GET['fdirname'].'/'.$_GET['filename'].'c2'.$_GET['endString']; ?>" style="width: 400px; height: 300px; z-index: 2;">
					</div>				
				
				</div>
			<?php } 
				}
			?>
			<?php if (isset($_GET["numChannels"])) { 
				if ((int)$_GET["numChannels"] >= 4) {
			?>
				
				<!-- Channel 4 -->
				<div class="panel p2">
					<div class="actiondemo">
					<div class="headerdemo">
					Channel 4 (..)
					</div>				
				
					<div class="demo-options">

					<div>Red: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-red-4"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-red-4">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 70px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Green: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-green-4"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-green-4">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Blue: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-blue-4"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-blue-4">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Image Threshold: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-threshold-4"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-threshold-4">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Opacity: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-opacity-4"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-opacity-4">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Z-Index: <input type="text" style="width:30px;" class="demo-input" value="3" id="value-zindex-4"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-zindex-4">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>

					</div>
					<img alt="" id="channel-4-img" src="<?php echo 'multispectral/'.$_GET['fdirname'].'/'.$_GET['filename'].'c3'.$_GET['endString']; ?>" style="width: 400px; height: 300px; z-index: 3;">
					</div>				
				</div>
			<?php } 
				}
			?>
			<?php if (isset($_GET["numChannels"])) { 
				if ((int)$_GET["numChannels"] >= 5) {
			?>
				
				<!-- Channel 5 -->
				<div class="panel p3">
					<div class="actiondemo">
					<div class="headerdemo">
					Channel 5 (..)
					</div>				
				
					<div class="demo-options">

					<div>Red: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-red-5"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-red-5">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 70px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Green: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-green-5"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-green-5">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Blue: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-blue-5"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-blue-5">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Image Threshold: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-threshold-5"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-threshold-5">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Opacity: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-opacity-5"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-opacity-5">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Z-Index: <input type="text" style="width:30px;" class="demo-input" value="4" id="value-zindex-5"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-zindex-5">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>

					</div>
					<img alt="" id="channel-5-img" src="<?php echo 'multispectral/'.$_GET['fdirname'].'/'.$_GET['filename'].'c4'.$_GET['endString']; ?>" style="width: 400px; height: 300px; z-index: 4;">
					</div>				
				
				</div>
			<?php } 
				}
			?>
			<?php if (isset($_GET["numChannels"])) { 
				if ((int)$_GET["numChannels"] >= 6) {
			?>
				
				<!-- Channel 6 -->
				<div class="panel p4">
					<div class="actiondemo">
					<div class="headerdemo">
					Channel 6 (..)
					</div>				
				
					<div class="demo-options">

					<div>Red: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-red-6"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-red-6">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 70px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Green: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-green-6"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-green-6">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Blue: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-blue-6"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-blue-6">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Image Threshold: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-threshold-6"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-threshold-6">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Opacity: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-opacity-6"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-opacity-6">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Z-Index: <input type="text" style="width:30px;" class="demo-input" value="5" id="value-zindex-6"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-zindex-6">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>

					</div>
					<img alt="" id="channel-6-img" src="<?php echo 'multispectral/'.$_GET['fdirname'].'/'.$_GET['filename'].'c5'.$_GET['endString']; ?>" style="width: 400px; height: 300px; z-index: 5;">
					</div>				
				
				</div>
			<?php } 
				}
			?>
			<?php if (isset($_GET["numChannels"])) { 
				if ((int)$_GET["numChannels"] >= 7) {
			?>
				
				<!-- Channel 7 -->
				<div class="panel p2">
					<div class="actiondemo">
					<div class="headerdemo">
					Channel 7 (..)
					</div>				
				
					<div class="demo-options">

					<div>Red: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-red-7"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-red-7">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 70px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Green: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-green-7"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-green-7">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Blue: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-blue-7"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-blue-7">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Image Threshold: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-threshold-7"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-threshold-7">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Opacity: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-opacity-7"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-opacity-7">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Z-Index: <input type="text" style="width:30px;" class="demo-input" value="6" id="value-zindex-7"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-zindex-7">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>

					</div>
					<img alt="" id="channel-7-img" src="<?php echo 'multispectral/'.$_GET['fdirname'].'/'.$_GET['filename'].'c6'.$_GET['endString']; ?>" style="width: 400px; height: 300px; z-index: 6;">
					</div>				
				</div>
			<?php } 
				}
			?>
			<?php if (isset($_GET["numChannels"])) { 
				if ((int)$_GET["numChannels"] >= 8) {
			?>
				
				<!-- Channel 8 -->
				<div class="panel p3">
					<div class="actiondemo">
					<div class="headerdemo">
					Channel 8 (..)
					</div>				
				
					<div class="demo-options">

					<div>Red: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-red-8"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-red-8">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 70px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Green: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-green-8"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-green-8">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Blue: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-blue-8"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-blue-8">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Image Threshold: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-threshold-8"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-threshold-8">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Opacity: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-opacity-8"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-opacity-8">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Z-Index: <input type="text" style="width:30px;" class="demo-input" value="7" id="value-zindex-8"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-zindex-8">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>

					</div>
					<img alt="" id="channel-8-img" src="<?php echo 'multispectral/'.$_GET['fdirname'].'/'.$_GET['filename'].'c7'.$_GET['endString']; ?>" style="width: 400px; height: 300px; z-index: 7;">
					</div>				
				
				</div>
			<?php } 
				}
			?>
			<?php if (isset($_GET["numChannels"])) { 
				if ((int)$_GET["numChannels"] >= 9) {
			?>
				
				<!-- Channel 9 -->
				<div class="panel p4">
					<div class="actiondemo">
					<div class="headerdemo">
					Channel 9 (..)
					</div>				
				
					<div class="demo-options">

					<div>Red: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-red-9"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-red-9">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 70px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Green: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-green-9"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-green-9">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Blue: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-blue-9"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-blue-9">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Image Threshold: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-threshold-9"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-threshold-9">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Opacity: <input type="text" style="width:30px;" class="demo-input" value="0" id="value-opacity-9"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-opacity-9">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>
					<div>Z-Index: <input type="text" style="width:30px;" class="demo-input" value="8" id="value-zindex-9"></div>
					<div style="width:150px;margin-top:5px;margin-bottom:5px;" class="ui-slider" id="slider-zindex-9">
						<a style="outline:none;border:none;" href="#"><div class="ui-slider-handle" style="left: 69px;"></div></a><div class="ui-slider-range"></div>
					</div>

					</div>
					<img alt="" id="channel-9-img" src="<?php echo 'multispectral/'.$_GET['fdirname'].'/'.$_GET['filename'].'c8'.$_GET['endString']; ?>" style="width: 400px; height: 300px; z-index: 8;">
					</div>				
				
				</div>
			<?php } 
				}
			?>
				
			</div>
			<div class="panel p5">
			<div style="position: relative; left: 0; top: 0;">
			<?php if (isset($_GET["numChannels"])) { 
				if ((int)$_GET["numChannels"] >= 1) {
					if ((int)$_GET["numChannels"] > 1) {
						echo '<img src="'.'multispectral/'.$_GET['fdirname'].'/'.$_GET['filename'].'c0'.$_GET['endString'].'" id="channel-11-img" style="position: absolute; top: 50%; left: 50%; margin-left: -200px; z-index: 0; width: 400px; height: 300px;"/>';
					}
					else {
						echo '<img src="'.'multispectral/'.$_GET['fdirname'].'/'.$_GET['filename'].$_GET['endString'].'" id="channel-11-img" style="position: absolute; top: 50%; left: 50%; margin-left: -200px; z-index: 0; width: 400px; height: 300px;"/>';
					}
				} 
			}
			?>
			<?php if (isset($_GET["numChannels"])) { 
				if ((int)$_GET["numChannels"] >= 2) {
			?>
				<img src="<?php echo 'multispectral/'.$_GET['fdirname'].'/'.$_GET['filename'].'c1'.$_GET['endString']; ?>" id="channel-22-img" style="position: absolute; top: 50%; left: 50%; margin-left: -200px; z-index: 1; width: 400px; height: 300px;"/>
			<?php } 
				}
			?>
			<?php if (isset($_GET["numChannels"])) { 
				if ((int)$_GET["numChannels"] >= 3) {
			?>

				<img src="<?php echo 'multispectral/'.$_GET['fdirname'].'/'.$_GET['filename'].'c2'.$_GET['endString']; ?>" id="channel-33-img" style="position: absolute; top: 50%; left: 50%; margin-left: -200px; z-index: 2; width: 400px; height: 300px;"/>
			<?php } 
				}
			?>
			<?php if (isset($_GET["numChannels"])) { 
				if ((int)$_GET["numChannels"] >= 4) {
			?>
				
				<img src="<?php echo 'multispectral/'.$_GET['fdirname'].'/'.$_GET['filename'].'c3'.$_GET['endString']; ?>" id="channel-44-img" style="position: absolute; top: 50%; left: 50%; margin-left: -200px; z-index: 3; width: 400px; height: 300px;"/>
			<?php } 
				}
			?>
			<?php if (isset($_GET["numChannels"])) { 
				if ((int)$_GET["numChannels"] >= 5) {
			?>
				
				<img src="<?php echo 'multispectral/'.$_GET['fdirname'].'/'.$_GET['filename'].'c4'.$_GET['endString']; ?>" id="channel-55-img" style="position: absolute; top: 50%; left: 50%; margin-left: -200px; z-index: 4; width: 400px; height: 300px;"/>
			<?php } 
				}
			?>
			<?php if (isset($_GET["numChannels"])) { 
				if ((int)$_GET["numChannels"] >= 6) {
			?>
				
				<img src="<?php echo 'multispectral/'.$_GET['fdirname'].'/'.$_GET['filename'].'c5'.$_GET['endString']; ?>" id="channel-66-img" style="position: absolute; top: 50%; left: 50%; margin-left: -200px; z-index: 5; width: 400px; height: 300px;"/>
			<?php } 
				}
			?>
			<?php if (isset($_GET["numChannels"])) { 
				if ((int)$_GET["numChannels"] >= 7) {
			?>
				
				<img src="<?php echo 'multispectral/'.$_GET['fdirname'].'/'.$_GET['filename'].'c6'.$_GET['endString']; ?>" id="channel-77-img" style="position: absolute; top: 50%; left: 50%; margin-left: -200px; z-index: 6; width: 400px; height: 300px;"/>
			<?php } 
				}
			?>
			<?php if (isset($_GET["numChannels"])) { 
				if ((int)$_GET["numChannels"] >= 8) {
			?>
				
				<img src="<?php echo 'multispectral/'.$_GET['fdirname'].'/'.$_GET['filename'].'c7'.$_GET['endString']; ?>" id="channel-88-img" style="position: absolute; top: 50%; left: 50%; margin-left: -200px; z-index: 7; width: 400px; height: 300px;"/>
			<?php } 
				}
			?>
			<?php if (isset($_GET["numChannels"])) { 
				if ((int)$_GET["numChannels"] >= 9) {
			?>
				
				<img src="<?php echo 'multispectral/'.$_GET['fdirname'].'/'.$_GET['filename'].'c8'.$_GET['endString']; ?>" id="channel-99-img" style="position: absolute; top: 50%; left: 50%; margin-left: -200px; z-index: 8; width: 400px; height: 300px;"/>
			<?php } 
				}
			?>
				
				
			</div>
			</div>
		</div>
	</body>
</html>
