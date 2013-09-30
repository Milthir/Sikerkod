<?php
	include 'assist/debug.php';
	include 'connect.php';

	include 'assist/security.php';
	include 'assist/feedback.php';
	include 'assist/post.php';
	include 'assist/get.php';
	include 'assist/string.php';
	include 'assist/sql.php';
	include 'assist/pic.php';
	include 'assist/message_handler.php';
	include 'assist/template.php';
	include 'assist/date.php';
	include 'assist/mailer.php';
	include 'classes/forefather.php';
	include 'classes/profile.php';
	include 'classes/exports.php';


	include 'model/event.php';
	installLogger();
	
	$ADMIN_MODE = isset($_GET['admin']);

	$event->setAdminMode($ADMIN_MODE);
	
	mh_startMessageCapture(); ?>
	
<!DOCTYPE html>
<!-- saved from url=(0032)http://sikerkod.hu/?p=tanf&admin -->
<html class="cufon-active cufon-ready"><!-- ☺ --><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>» Sikerkód » Váltsd Valóra!  / Tanfolyamok</title>
	<!-- ☺ -->
	
	<!-- ☺ -->
	<link rel="stylesheet" type="text/css" href="./original_files/sikerkod.css">
	<link rel="stylesheet" type="text/css" href="./original_files/jquery-ui-1.8.10.custom.css">
	<link rel="stylesheet" type="text/css" media="screen" href="./original_files/prettyPhoto.css" title="prettyPhoto main stylesheet">
	<link rel="stylesheet" type="text/css" media="screen" href="style.css" />
	
	<link rel="shortcut icon" href="http://sikerkod.hu/favicon.png">
	
	<noscript>&lt;link href="css/noscript.css" rel="stylesheet" type="text/css" /&gt;</noscript>
	
	<!--[if IE 6]>
		<link href="css/ie6.css" rel="stylesheet" type="text/css" />
	<![endif]-->
	<!-- ☺ -->
	<script type="text/javascript" async="" src="./original_files/ga.js"></script><script type="text/javascript" src="./original_files/jquery-1.4.4.min.js"></script>
	<script type="text/javascript" src="./original_files/jquery-ui-1.8.10.custom.min.js"></script>
	<script type="text/javascript" src="./original_files/jquery.tinymce.js"></script>
	<script type="text/javascript" src="./original_files/jquery.prettyPhoto.js"></script>	

	<script type="text/javascript" src="./original_files/cufon-yui.js"></script><style type="text/css">cufon{text-indent:0!important;}@media screen,projection{cufon{display:inline!important;display:inline-block!important;position:relative!important;vertical-align:middle!important;font-size:1px!important;line-height:1px!important;}cufon cufontext{display:-moz-inline-box!important;display:inline-block!important;width:0!important;height:0!important;overflow:hidden!important;text-indent:-10000in!important;}cufon canvas{position:relative!important;}}@media print{cufon{padding:0!important;}cufon canvas{display:none!important;}}</style>
	<script type="text/javascript" src="./original_files/Monotype_Corsiva_italic_400.font.js"></script>
	<script type="text/javascript" src="./original_files/TitilliumMaps29L_400.font.js"></script>
	<script type="text/javascript" src="./original_files/custom.js"></script>
	
	<script tpye="text/javascript">
		jQuery(document).ready(function() {
			initSikerkod();
			$('a').click(function(){
				if($(this).attr("href").indexOf("#") == 0){
					
					$('html, body').animate({
						scrollTop:$($(this).attr("href")).position().top
					}, 2000);
					return false;
				}
				return true;
			});
			
			jQuery('form').submit(function()
			{
				var aa = 0;
				jQuery.each(jQuery(this).find("INPUT"),function (i,v)
				{	
					if(jQuery(v).val() == "" && !jQuery(v).hasClass("can-be-empty"))aa++;
				});
				if(aa)
				{
					if(jQuery(this).find("span.errorzone").length == 0)
						jQuery("<span/>").addClass("errorzone").css("color","red").css("font-weight","bold").html('Minden mezőt ki kell tölteni!').insertAfter(jQuery(this).find(":submit"));
				}
				return aa == 0;
			});
			
		});
	</script>
	
	<script type="text/javascript">
		  Cufon.replace('h1, h2, h3, h4, #menu-wrapper > ul > li > a > span', { fontFamily: 'TitilliumMaps29L', hover: 'true' }); 
		  Cufon.replace('.intro', { fontFamily: 'TitilliumMaps29L', hover: 'true' });  
		  Cufon.replace('h5, .breadcrumbs, blockquote p, .bcateg, .bdate p, .port1-list .more-link, .sdata-left .more-link', { fontFamily: 'TitilliumMaps29L', hover: 'true' });
		  Cufon.replace('h6', { fontFamily: 'Monotype Corsiva', hover: 'true' }); 
 	</script>
	<!-- ☺ -->
	<style>
	.page_tanf .interior-top,.page_tanf .intop-container{
		height:149px;
	}
	.page_tanf .right{
		margin-top:160px;
	}
	.page_tanf .intdec{
		top:138px;
	}
	.page_tanf #fwidth-interior{
		height:149px;
	}
	</style>
</head>
<!-- ☺ -->
<body class="page_tanf">
<div class="int-wrapper" id="backtop">
    
    <div id="fwidth-interior">
        <div class="int-wrapper">
            <!-- start interior top area -->               
            <div class="interior-top">
                <div class="intop-container">
                    <div class="sinfo-wrapper">
                        <div class="sinfo">
                            <div class="sdata-left">
                            	<h6><cufon class="cufon cufon-canvas" alt="„A " style="width: 18px; height: 20px;"><canvas width="42" height="25" style="width: 42px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>„A </cufontext></cufon><cufon class="cufon cufon-canvas" alt="cél " style="width: 24px; height: 20px;"><canvas width="48" height="25" style="width: 48px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>cél </cufontext></cufon><cufon class="cufon cufon-canvas" alt="cselekvés " style="width: 69px; height: 20px;"><canvas width="93" height="25" style="width: 93px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>cselekvés </cufontext></cufon><cufon class="cufon cufon-canvas" alt="nélkül " style="width: 50px; height: 20px;"><canvas width="73" height="25" style="width: 73px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>nélkül </cufontext></cufon><cufon class="cufon cufon-canvas" alt="csak " style="width: 36px; height: 20px;"><canvas width="60" height="25" style="width: 60px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>csak </cufontext></cufon><cufon class="cufon cufon-canvas" alt="álom. " style="width: 44px; height: 20px;"><canvas width="68" height="25" style="width: 68px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>álom. </cufontext></cufon><cufon class="cufon cufon-canvas" alt="A " style="width: 18px; height: 20px;"><canvas width="42" height="25" style="width: 42px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>A </cufontext></cufon><cufon class="cufon cufon-canvas" alt="cselekvés " style="width: 69px; height: 20px;"><canvas width="93" height="25" style="width: 93px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>cselekvés </cufontext></cufon><cufon class="cufon cufon-canvas" alt="cél " style="width: 24px; height: 20px;"><canvas width="48" height="25" style="width: 48px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>cél </cufontext></cufon><cufon class="cufon cufon-canvas" alt="nélkül " style="width: 50px; height: 20px;"><canvas width="73" height="25" style="width: 73px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>nélkül </cufontext></cufon><cufon class="cufon cufon-canvas" alt="csak " style="width: 36px; height: 20px;"><canvas width="60" height="25" style="width: 60px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>csak </cufontext></cufon><cufon class="cufon cufon-canvas" alt="időtöltés. " style="width: 71px; height: 20px;"><canvas width="95" height="25" style="width: 95px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>időtöltés. </cufontext></cufon><cufon class="cufon cufon-canvas" alt="A " style="width: 18px; height: 20px;"><canvas width="42" height="25" style="width: 42px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>A </cufontext></cufon><cufon class="cufon cufon-canvas" alt="céllal " style="width: 42px; height: 20px;"><canvas width="66" height="25" style="width: 66px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>céllal </cufontext></cufon><cufon class="cufon cufon-canvas" alt="történő " style="width: 57px; height: 20px;"><canvas width="81" height="25" style="width: 81px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>történő </cufontext></cufon><cufon class="cufon cufon-canvas" alt="cselekvés " style="width: 69px; height: 20px;"><canvas width="93" height="25" style="width: 93px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>cselekvés </cufontext></cufon><cufon class="cufon cufon-canvas" alt="megváltoztatja " style="width: 114px; height: 20px;"><canvas width="138" height="25" style="width: 138px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>megváltoztatja </cufontext></cufon><cufon class="cufon cufon-canvas" alt="a " style="width: 14px; height: 20px;"><canvas width="37" height="25" style="width: 37px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>a </cufontext></cufon><cufon class="cufon cufon-canvas" alt="világot.” " style="width: 60px; height: 20px;"><canvas width="84" height="25" style="width: 84px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>világot.” </cufontext></cufon><cufon class="cufon cufon-canvas" alt="(Joe " style="width: 34px; height: 20px;"><canvas width="57" height="25" style="width: 57px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>(Joe </cufontext></cufon><cufon class="cufon cufon-canvas" alt="Arthur " style="width: 55px; height: 20px;"><canvas width="79" height="25" style="width: 79px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>Arthur </cufontext></cufon><cufon class="cufon cufon-canvas" alt="Barker)" style="width: 54px; height: 20px;"><canvas width="78" height="25" style="width: 78px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>Barker)</cufontext></cufon></h6>
								<a href="http://sikerkod.hu/?p=tanf&admin#" onClick="jQuery(&#39;#tanfdlg_next&#39;).dialog(&#39;open&#39;)" class="more-link"><span class="more-text"><cufon class="cufon cufon-canvas" alt="következő " style="width: 106px; height: 22px;"><canvas width="123" height="30" style="width: 123px; height: 30px; top: -6px; left: -1px;"></canvas><cufontext>következő </cufontext></cufon><cufon class="cufon cufon-canvas" alt="tanfolyam" style="width: 101px; height: 22px;"><canvas width="105" height="30" style="width: 105px; height: 30px; top: -6px; left: -1px;"></canvas><cufontext>tanfolyam</cufontext></cufon></span></a>
															
							</div>
							
                        </div>
                    </div>
                    <img src="./original_files/intop_tanf.jpg" alt="" class="the-image">
                </div>
            </div>          
            <!-- end interior top area -->                            
        </div>

    </div>
        
    <div class="left">
        <!-- start top menu -->
        <div id="menu-wrapper">
                <div id="logo"><a href="http://sikerkod.hu/?p=main"><img src="./original_files/logo.png" alt=""></a></div>
                                <ul class="sf-menu sf-vertical sf-js-enabled">
                    <li><a href="http://sikerkod.hu/?p=main"><span><cufon class="cufon cufon-canvas" alt="Kezdőlap" style="width: 69px; height: 17px;"><canvas width="78" height="23" style="width: 78px; height: 23px; top: -5px; left: -1px;"></canvas><cufontext>Kezdőlap</cufontext></cufon></span><strong>A Sikerkódról</strong></a></li>
                    <li><a href="http://sikerkod.hu/?p=tanf"><span><cufon class="cufon cufon-canvas" alt="Tanfolyamok" style="width: 98px; height: 17px;"><canvas width="107" height="23" style="width: 107px; height: 23px; top: -5px; left: -1px;"></canvas><cufontext>Tanfolyamok</cufontext></cufon></span><strong>Bemutatás, időpontok, jegyrendelés</strong></a></li>         
                   <!-- <li><a href="?p=shop"><span>Könyvtár</span><strong>Könyvek - amiket ajánlunk</strong></a></li>-->
                    <li><a href="http://sikerkod.hu/?p=rolunk"><span><cufon class="cufon cufon-canvas" alt="Rólunk" style="width: 53px; height: 17px;"><canvas width="62" height="23" style="width: 62px; height: 23px; top: -5px; left: -1px;"></canvas><cufontext>Rólunk</cufontext></cufon></span><strong>Bemutatkozunk</strong></a>
						<ul style="display: none; visibility: hidden;"><li><a href="http://sikerkod.hu/?p=kapcsolat">Kapcsolat</a></li></ul>
					</li>
                    <li><a href="http://sikerkod.hu/?p=blog"><span><cufon class="cufon cufon-canvas" alt="Blog" style="width: 34px; height: 17px;"><canvas width="42" height="23" style="width: 42px; height: 23px; top: -5px; left: -1px;"></canvas><cufontext>Blog</cufontext></cufon></span></a></li> 
                    <li><a href="http://sikerkod.hu/?p=ceg"><span><cufon class="cufon cufon-canvas" alt="Cégeknek " style="width: 77px; height: 17px;"><canvas width="90" height="23" style="width: 90px; height: 23px; top: -5px; left: -1px;"></canvas><cufontext>Cégeknek </cufontext></cufon><cufon class="cufon cufon-canvas" alt="ajánljuk" style="width: 60px; height: 17px;"><canvas width="69" height="23" style="width: 69px; height: 23px; top: -5px; left: -1px;"></canvas><cufontext>ajánljuk</cufontext></cufon></span></a></li> 
                    <li><a href="http://sikerkod.hu/?p=blog&c=6"><span><cufon class="cufon cufon-canvas" alt="Sikerek" style="width: 55px; height: 17px;"><canvas width="64" height="23" style="width: 64px; height: 23px; top: -5px; left: -1px;"></canvas><cufontext>Sikerek</cufontext></cufon></span></a></li> 
                                                                                                  
                </ul>
                                <div class="menu-add"></div>                   
        </div>
        <!-- end top menu --> 
        
        <!-- start sidebar -->
        <div class="sidebar">
        				
			<div class="box">
				
				<iframe src="./original_files/likebox.html" scrolling="no" frameborder="0" style="border:none; border-style: none; overflow:hidden; width:250px; height:650px;" allowtransparency="true"></iframe>
			</div>
			            
        </div>
        <!-- end sidebar -->    
    </div>
   
    <div class="right">
      <div class="unit">
		<div class="calendar">
			<div class="days">
				<div class="dayNames">
					<p>Hétfő</p>
				</div>
				<div class="dayNames">
					<p>Kedd</p>
				</div>
				<div class="dayNames">
					<p>Szerda</p>
				</div>
				<div class="dayNames">
					<p>Csütörtök</p>
				</div>
				<div class="dayNames">
					<p>Péntek</p>
				</div>
				<div class="dayNames">
					<p>Szombat</p>
				</div>
				<div class="dayNames">
					<p>Vasárnap</p>
				</div>
			</div>
			 
	<? startCapture(); ?>
		<form method="post" action="">{hidden_zone}
				Dátum: {field_date} <br />
				Kezdő idő: {field_startTime} <br /> 
				Befejező idő: {field_endTime} <br /> 
				Név: {field_name} <br />
				Email: {field_email} <br />
				Számlázási cím: {field_billAddr} <br />
				Telefonszám: {field_phoneNumber} <br />
				<input type="submit" class="submit-btn" value="Találka módosítása" size="100"/>	
			</form>
			<br />
	<?
		$event->process();
		$event->setTemplate(PROFILE_TEMPLATE_MAIN,new template(endCapture()));
		//$arr = $event->getProfileView();
		$arr = $event->getProfileView(array("date" => "ASC", "startTime" => "ASC"));
		/*$days = array(  "Mon" => array(),
						"Tue" => array(),
						"Wed" => array(),
						"Thu" => array(),
						"Fri" => array(),
						"Sat" => array(),
						"Sun" => array()
					); 
					
		foreach($arr as $element) {
			array_push($days[(string)date("D",$element['date'])], array($element['startTime'], $element['endTime'], $element['accepted'], $element['token']));
			echo '<script>alert("'.date("D",$element['date']).'");</script>';
		}*/
		?>
			<div class="dater">
				<?
					$day = date("D",$arr[0]['date']);
					echo '<div class="column">';
					$days = array(  "Mon",
						"Tue" ,
						"Wed",
						"Thu",
						"Fri",
						"Sat",
						"Sun"
					); 
					$j = 0;
					for($i = 0; $i < sizeof($arr);) {
					$date = new DateTime($arr[$i]['date']);
						if ($days[$j] == date("D",$date->getTimestamp())/*date("D",$arr[$i]['date'])*/ ) {
									if ($arr[$i]['accepted'] == 1 ) {
										echo '<div class="orangeBox box">
										<p>'.$arr[$i]['startTime'].' - '.$arr[$i]['endTime'].'</p>
										</div>';
									}
									else {
										echo '<div class="greyBox box">
										<p>'.$arr[$i]['startTime'].' - '.$arr[$i]['endTime'].'</p>
										</div>';
									}	
									$i++;
						
						}
						else {
							$j++;
							echo '</div>';
							echo '<div class="column">';
						}
					}
					//echo '</div>';
					for($j; $j < sizeof($days); $j++) {
						echo '<div class="column">';
						echo '</div>';
					}
					
					echo '</div>';
				
				
					/*$s = "<!-- Kezdet -->";
					foreach($days as $item) {
						$s .= '<div class="column">';
							if(sizeof($item) > 0) {
								foreach($item as $meeting) {
									$s .= '<div class="';
									if ($meeting[2] == 1 ) {
										$s .= 'orangeBox box">
										<p>'.$meeting[0].' - '.$meeting[1].'</p>
										</div>';
									}
									else {
										$s .= 'greyBox box">
										<p>'.$meeting[0].' - '.$meeting[1].'</p>
										</div>';
									}	
								}
							}
						$s .= "</div>";
					}
					$s .= "<!-- Vég -->";
				echo $s; */ 
				?>
			</div>
		</div>	
	</div>
	
	<div class="calendarFooter">
		<div class="orangeSmallBox">
		</div>
		<p class="orange textBottom">Foglalt időpont</p>
		<div class="greenSmallBox">
		</div>
		<p class="green textBottom">Kiválasztott időpont</p>
		<div class="greySmallBox">
		</div>
		<p class="grey textBottom">Szabad időpont</p>
		<br />
		<p>
		<?
		if ($ADMIN_MODE) {
			//print_r($arr);
			//$event->setArg('id',-1);
			$event->show();
			}
		?>
		</p> 
	</div>	
	
                                                           
    <!-- </div> -->  

</div>

<div class="intdec"></div>

<!-- start footer -->
<div id="footer">
	<div class="footer-wrapper">
    	<!-- start 4 footer widgets wrapper -->
    	<div class="fbox-wrapper">
        	<div class="fbox flogo">
                <div class="fbox-content">
					<img src="./original_files/grey_logo.jpg" alt="">
                    <p>Váltsd Valóra!</p>
                </div>
            </div>		

        	<!--<div class="fbox">
            	<h6>Spin! :)</h6>
                <div class="fbox-content">
                    <iframe title="YouTube video player" width="210" src="http://www.youtube.com/embed/oP59tQf_njc" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>	
			<div class="fbox">
            	<h6>power of will</h6>
                <div class="fbox-content">
                    <iframe title="YouTube video player" width="210" src="http://www.youtube.com/embed/VDvr08sCPOc" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>-->	
        	<div class="fbox">
            	<h6><cufon class="cufon cufon-canvas" alt="Gyakori " style="width: 63px; height: 20px;"><canvas width="87" height="25" style="width: 87px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>Gyakori </cufontext></cufon><cufon class="cufon cufon-canvas" alt="kérdések" style="width: 61px; height: 20px;"><canvas width="81" height="25" style="width: 81px; height: 25px; top: -2px; left: -4px;"></canvas><cufontext>kérdések</cufontext></cufon></h6>
                <div class="fbox-content">
                    <ul>
                        <li><a href="http://sikerkod.hu/?p=vasarlas">A vásárlás menete</a></li>
                        <li><a href="http://sikerkod.hu/?p=felhfelt">Felhasználás feltételei</a></li>
                        <li><a href="http://sikerkod.hu/?p=adatvedelem">Adatvédelem</a></li>       
                        <li><a href="http://sikerkod.hu/?p=kapcsolat">Kapcsolat</a></li>
                   <!--     <li><a href="?p=kerdesek">Kérdések és válaszok</a></li>-->
					</ul>
                </div>
            </div>	                                    
		</div>    
    	<!-- end 4 footer widgets wrapper -->                
    </div>
</div>

<div class="fbottom">
	<div class="fbottom-wrapper">
    	<p><strong>Copyright © 2011 - Sikerkód Kft. Minden jog fentartva.</strong><br> A weblapot a <a href="http://vadpocok.hu/">« Vadpocok Művek »</a> készítette. Tanácsad és márkát épít: <a href="http://www.mosaicbrand.com/">Mosaic Brand</a>. <i>Neked is ;)</i></p>
        <div class="backtotop"><a href="http://sikerkod.hu/?p=tanf&admin#backtop">ugrás a lap tetejére</a></div>        
    </div>
</div>
<!-- end footer -->

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-19181655-3']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>


<!-- ☺☺☺ -->

<!-- 2011 (c) VadpocoK! Műve(K)! - http://vadpocok.hu -->
<div style="display: none; z-index: 1000; outline: 0px; position: absolute;" class="ui-dialog ui-widget ui-widget-content ui-corner-all  ui-draggable ui-resizable" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-tanfdlg_next"><div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix"><span class="ui-dialog-title" id="ui-dialog-title-tanfdlg_next">Tanfolyam információk</span><a href="http://sikerkod.hu/?p=tanf&admin#" class="ui-dialog-titlebar-close ui-corner-all" role="button"><span class="ui-icon ui-icon-closethick">close</span></a></div><div class="dlg ui-dialog-content ui-widget-content" id="tanfdlg_next" style="display: block;"><h3><cufon class="cufon cufon-canvas" alt="Alaptanfolyam " style="width: 171px; height: 28px;"><canvas width="194" height="37" style="width: 194px; height: 37px; top: -8px; left: -1px;"></canvas><cufontext>Alaptanfolyam </cufontext></cufon><cufon class="cufon cufon-canvas" alt="- " style="width: 17px; height: 28px;"><canvas width="41" height="37" style="width: 41px; height: 37px; top: -8px; left: -1px;"></canvas><cufontext>- </cufontext></cufon><cufon class="cufon cufon-canvas" alt="Makó" style="width: 64px; height: 28px;"><canvas width="79" height="37" style="width: 79px; height: 37px; top: -8px; left: -1px;"></canvas><cufontext>Makó</cufontext></cufon></h3>
									
									<strong>Dátum: </strong>2013. 
							október						
							12-13.<br><br>
							<strong>Időtartam: </strong>Mindkét napon (Sz-V) 9 - 17 óráig.<br><br>
									<strong>Helyszín: </strong>6900 Makó, Széchenyi tér 10.<br><br>
									<strong>Jegyárusítás: </strong><p>Jegyek online vásárolhatók.</p><br><br>
									<strong>Jegyár: </strong>19 500 Ft / db<br><br>
																
									<div id="rendeles-gombok">
										<a href="http://sikerkod.hu/?p=tanf&t=50"><img src="./original_files/online-jegyrendeles.jpg"></a>
										<img src="./original_files/telefonos-jegyrendeles.jpg">
									</div>

					</div><div class="ui-resizable-handle ui-resizable-n" style=""></div><div class="ui-resizable-handle ui-resizable-e" style=""></div><div class="ui-resizable-handle ui-resizable-s" style=""></div><div class="ui-resizable-handle ui-resizable-w" style=""></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se ui-icon-grip-diagonal-se" style="z-index: 1001;"></div><div class="ui-resizable-handle ui-resizable-sw" style="z-index: 1002;"></div><div class="ui-resizable-handle ui-resizable-ne" style="z-index: 1003;"></div><div class="ui-resizable-handle ui-resizable-nw" style="z-index: 1004;"></div></div><div style="display: none; z-index: 1000; outline: 0px; position: absolute;" class="ui-dialog ui-widget ui-widget-content ui-corner-all  ui-draggable ui-resizable" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-tanfdlg_50"><div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix"><span class="ui-dialog-title" id="ui-dialog-title-tanfdlg_50">Tanfolyam információk</span><a href="http://sikerkod.hu/?p=tanf&admin#" class="ui-dialog-titlebar-close ui-corner-all" role="button"><span class="ui-icon ui-icon-closethick">close</span></a></div><div class="dlg ui-dialog-content ui-widget-content" id="tanfdlg_50" style="display: block;"><h3><cufon class="cufon cufon-canvas" alt="Alaptanfolyam " style="width: 171px; height: 28px;"><canvas width="194" height="37" style="width: 194px; height: 37px; top: -8px; left: -1px;"></canvas><cufontext>Alaptanfolyam </cufontext></cufon><cufon class="cufon cufon-canvas" alt="- " style="width: 17px; height: 28px;"><canvas width="41" height="37" style="width: 41px; height: 37px; top: -8px; left: -1px;"></canvas><cufontext>- </cufontext></cufon><cufon class="cufon cufon-canvas" alt="Makó" style="width: 64px; height: 28px;"><canvas width="79" height="37" style="width: 79px; height: 37px; top: -8px; left: -1px;"></canvas><cufontext>Makó</cufontext></cufon></h3>
							<strong>Dátum: </strong>2013. 
							október
							12-13.<br><br>
							<strong>Időtartam: </strong>
							
							
								9-17 óráig
							<br><br>
							<strong>Helyszín: </strong>6900 Makó, Széchenyi tér 10.<br><br>
							<strong>Jegyárusítás: </strong><p>Jegyek online vásárolhatók.</p><br><br>
							<strong>Jegyár: </strong>19 500 Ft / db<br><br>
							
							
							
							<div id="rendeles-gombok">
								<a href="http://sikerkod.hu/?p=tanf&admin=&t=50"><img src="./original_files/online-jegyrendeles.jpg"></a>
								<img src="./original_files/telefonos-jegyrendeles.jpg">
							</div>

			</div><div class="ui-resizable-handle ui-resizable-n" style=""></div><div class="ui-resizable-handle ui-resizable-e" style=""></div><div class="ui-resizable-handle ui-resizable-s" style=""></div><div class="ui-resizable-handle ui-resizable-w" style=""></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se ui-icon-grip-diagonal-se" style="z-index: 1001;"></div><div class="ui-resizable-handle ui-resizable-sw" style="z-index: 1002;"></div><div class="ui-resizable-handle ui-resizable-ne" style="z-index: 1003;"></div><div class="ui-resizable-handle ui-resizable-nw" style="z-index: 1004;"></div></div><div style="display: none; z-index: 1000; outline: 0px; position: absolute;" class="ui-dialog ui-widget ui-widget-content ui-corner-all  ui-draggable ui-resizable" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-tanfdlg_49"><div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix"><span class="ui-dialog-title" id="ui-dialog-title-tanfdlg_49">Tanfolyam információk</span><a href="http://sikerkod.hu/?p=tanf&admin#" class="ui-dialog-titlebar-close ui-corner-all" role="button"><span class="ui-icon ui-icon-closethick">close</span></a></div><div class="dlg ui-dialog-content ui-widget-content" id="tanfdlg_49" style="display: block;"><h3><cufon class="cufon cufon-canvas" alt="Alaptanfolyam " style="width: 171px; height: 28px;"><canvas width="194" height="37" style="width: 194px; height: 37px; top: -8px; left: -1px;"></canvas><cufontext>Alaptanfolyam </cufontext></cufon><cufon class="cufon cufon-canvas" alt="- " style="width: 17px; height: 28px;"><canvas width="41" height="37" style="width: 41px; height: 37px; top: -8px; left: -1px;"></canvas><cufontext>- </cufontext></cufon><cufon class="cufon cufon-canvas" alt="Budapest" style="width: 109px; height: 28px;"><canvas width="128" height="37" style="width: 128px; height: 37px; top: -8px; left: -1px;"></canvas><cufontext>Budapest</cufontext></cufon></h3>
							<strong>Dátum: </strong>2013. 
							december
							28-29.<br><br>
							<strong>Időtartam: </strong>
							
							
								10-18 óráig
							<br><br>
							<strong>Helyszín: </strong>Tulip Inn Millennium Budapest, Üllői út 94-98.<br><br>
							<strong>Jegyárusítás: </strong><p>Jegyek online rendelhetők vagy telefonon (06 30 398 1399) igényelhetők!</p><br><br>
							<strong>Jegyár: </strong>19 500 Ft / db<br><br>
							
							
							
							<div id="rendeles-gombok">
								<a href="http://sikerkod.hu/?p=tanf&admin=&t=49"><img src="./original_files/online-jegyrendeles.jpg"></a>
								<img src="./original_files/telefonos-jegyrendeles.jpg">
							</div>

			</div><div class="ui-resizable-handle ui-resizable-n" style=""></div><div class="ui-resizable-handle ui-resizable-e" style=""></div><div class="ui-resizable-handle ui-resizable-s" style=""></div><div class="ui-resizable-handle ui-resizable-w" style=""></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se ui-icon-grip-diagonal-se" style="z-index: 1001;"></div><div class="ui-resizable-handle ui-resizable-sw" style="z-index: 1002;"></div><div class="ui-resizable-handle ui-resizable-ne" style="z-index: 1003;"></div><div class="ui-resizable-handle ui-resizable-nw" style="z-index: 1004;"></div></div></body></html>


 

<? mh_endMessageCapture(); ?>
