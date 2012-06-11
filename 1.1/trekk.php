<?php
/*
Plugin Name: Trekking Suedtirol (trekk)
Plugin URI: http://www.diewebmaster.it/wordpress-plugin-digitales-wandernetz-suedtirol/
Description: Dieses Plugin erm&ouml;glicht die einfache Integration von S&uuml;dtiroler Wandertouren in deinen Wordpressblog. Daf&uuml;r greift das Plugin auf die Schnittstelle von trekking.suedtirol.info zur&uuml;ck.
Author: Dietmar Mitterer-Zublasing
Author URI: http://www.compusol.it/
Version: 1.1
License: This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

Copyright 2008 Compusol des P.I. Mitterer-Zublasing Dietmar

Achtung ich &uuml;berneheme keine Haftung f&uuml;r wenn durch dein Einsatz dieses Plugins Sch&auml;den finanzieller oder ideeller oder jeglicher anderer Art entstehen. Der Einsatz dieses Plugin erfolgt auf eigene Gefahr!

Installationshinweise siehe: http://weblog.dietmar.biz/archives/157


*/

// Replace trekk Tags in Content with Amazon Links
function trekkContent($inhalt)
{
	$spos=0;
	$epos=0;
	
	$neuer_inhalt=$inhalt;
	
	while($spos=strpos($neuer_inhalt, '[trekk]'))		// find each trekk tag
	{
		$epos=strpos($neuer_inhalt, '[/trekk]');
		
		if($spos>0 & $epos>0)
		{
			$sub=substr($neuer_inhalt, $spos, $epos-$spos);	// extract the begin tag and parameters from contents
			$sub=str_replace('[trekk]', '', $sub);	// remove the begin tag
			

			list($Typ,$TourId,$Position,$Umbruchart,$Sprache,$Groesse,$Benutzer_Id)=split(":", $sub);	
			
			list($xPos,$yPos,$Zoom)=split(",", $Position);
			list($Breite,$Hoehe)=split(",", $Groesse);
			
			// set default values for missing parameters
			if($Typ=='') $Typ='gesamt';
			if($Umbruchart=='') $Umbruchart='none';
			if($Benutzer_Id=='') $Benutzer_Id=get_option('trekk_BenutzerID');
			if($Breite=='') $Breite=get_option('trekk_Breite');
			if($Hoehe=='') $Hoehe=get_option('trekk_Hoehe');
			if($Sprache=='') $Sprache="de";
			
			// build the link based on the ad type
			switch($Typ)
			{
				case 'gesamt':
					$link = '<span class="trekk" style="float:'.$Umbruchart.';margin:5px;">'
							. '<iframe src="http://www.trekking.suedtirol.info/extern/atlas.php?id='.$Benutzer_Id.'&lang='.$Sprache.'" name="trekking_atlas" marginwidth="0" marginheight="0" frameborder="0" width="'.$Breite.'" height="'.$Hoehe.'" scrolling="no" hspace="0" vspace="0"></iframe>'
							. '</span>';
					break;					
				case 'teil':
					$link = '<span class="trekk" style="float:'.$Umbruchart.';margin:5px;">'
							. '<iframe src="http://www.trekking.suedtirol.info/extern/atlas.php?id='.$Benutzer_Id.'&lang='.$Sprache.'&xPos='.$xPos.'&yPos='.$yPos.'&zoom='.$Zoom.'" name="trekking_atlas" marginwidth="0" marginheight="0" frameborder="0" width="'.$Breite.'" height="'.$Hoehe.'" scrolling="no" hspace="0" vspace="0"></iframe>'
							. '</span>';
					break;	
				case 'tour':
					$link = '<span class="trekk" style="float:'.$Umbruchart.';margin:5px;">'
							. '<iframe src="http://www.trekking.suedtirol.info/extern/atlas.php?id='.$Benutzer_Id.'&lang='.$Sprache.'&tourId2='.$TourId.'" name="trekking_atlas" marginwidth="0" marginheight="0" frameborder="0" width="'.$Breite.'" height="'.$Hoehe.'" scrolling="no" hspace="0" vspace="0"></iframe>'
							.'<p><a  style="cursor:pointer" onclick="Info=window.open(\'http://www.trekking.suedtirol.info/extern/tourinfo.php?id='.$Benutzer_Id.'&lang='.$Sprache.'&tourId2='.$TourId.'\', \'\',\'toolbar=0,location=0,directories=0,status=0,menubar=0, scrollbars=0, resizable=0,width=650, height=600, left=20,top=20\'); return false;">H&ouml;henprofil und Details</a><p>'
							. '</span>';
					break;	
				case 'mytour':
					$link = '<span class="trekk" style="float:'.$Umbruchart.';margin:5px;">'
							. '<iframe src="http://www.trekking.suedtirol.info/extern/atlas.php?id='.$Benutzer_Id.'&lang='.$Sprache.'&myTourId='.$TourId.'" name="trekking_atlas" marginwidth="0" marginheight="0" frameborder="0" width="'.$Breite.'" height="'.$Hoehe.'" scrolling="no" hspace="0" vspace="0"></iframe>'
							.'<p><a style="cursor:pointer" onclick="Info=window.open(\'http://www.trekking.suedtirol.info/extern/tourinfo.php?id='.$Benutzer_Id.'&lang='.$Sprache.'&myTourId='.$TourId.'\', \'\',\'toolbar=0,location=0,directories=0,status=0,menubar=0, scrollbars=0, resizable=0,width=650, height=600, left=20,top=20\'); return false;">H&ouml;henprofil und Details</a><p>'							
							. '</span>';
					break;					
			}
	
			$neuer_inhalt=str_replace('[trekk]'.$sub.'[/trekk]', $link, $neuer_inhalt);
			
		}
	}
	
	return $neuer_inhalt;
}








// Admin Options Page
function trekkOptionsPage()
{

	if(isset($_POST['trekkUpdate']))
	{
		$Benutzer_Id=$_POST["BenutzerID"];
		$Breite=$_POST["Breite"];
		$Hoehe=$_POST["Hoehe"];
		
		
		update_option('trekk_BenutzerID', $Benutzer_Id);
		update_option('trekk_Breite', $Breite);
		update_option('trekk_Hoehe', $Hoehe);

?>
<div class="updated fade" id="message" style="background-color: rgb(207, 235, 247);"><p><strong>Options saved.</strong></p></div>
<?
	}
	else
	{
		$Benutzer_Id=get_option('trekk_BenutzerID');
		$Breite=get_option('trekk_Breite');
		$Hoehe=get_option('trekk_Hoehe');
	}

?>
	<div class="wrap">
		<h2>trekk</h2>
		<form method="POST">
			<table class="optiontable">
				<tr valign="top">
					<th>Standard Benutzer ID zum Einbinden der Karte:</th>
					<td><input id="BenutzerID" name="BenutzerID" type="text" value="<? echo $Benutzer_Id; ?>"><br>
					Die Benutzer ID bekommst du indem du dich unter www.trekking.suedtirol.info anmeldest und dann im Administrationsmen&uuml; nachschaust.</td>
				</tr>
				<tr valign="top">
					<th>Standard Breite der Karte:</th>
					<td><input id="Breite" name="Breite" type="text" value="<? echo $Breite; ?>"><br>
					</td>
				</tr>
				<tr valign="top">
					<th>Standard H&ouml;he der Karte:</th>
					<td><input id="Hoehe" name="Hoehe" type="text" value="<? echo $Hoehe; ?>"><br>
					</td>
				</tr>
			
				

				<tr valign="top">
					<td>&nbsp;</td>
					<td><input name="trekkUpdate" type="submit" value="Speichern"></td>
				</tr>
			</table>
		</form>
		<h3>Einbindung der Karte</h3>
		<p>Um die Karte in deinen Weblog einzubinden, musst du folgenden Tag in deinem Wordpressartikel eingeben:</p>
		<code>[trekk]Typ:TourId:Ausschnitt:Umbruchart:Sprache:Gr&ouml;&szlig;e:Benutzer_Id[/trekk]</code>
		<p>wobei viele Parameter optinal sind...</p>
		<ul>
			<li><b>Typ:</b> Hier musst du: <b>gesamt</b>, <b>teil</b>, <b>tour</b> oder <b>mytour</b> eingeben. Davon abh&auml;ngig wird die gesamte <b>S&uuml;dtirolkarte</b>, ein <b>Ausschnitt</b> der S&uuml;dtirolkarte, eine <b>vordefinierten Tour</b> oder eine  <b>benutzerdefinierten Tour</b> eingef&uuml;gt. Eine Beschreibung wie du benutzerdefinierte Touren erzeugen kannst, findest du unter: <a href="http://weblog.dietmar.biz/archives/123" target="_blank">http://weblog.dietmar.biz/archives/123</a>.</li>
			<li><b>TourId:</b> Diesen Parameter musst du nur angeben, wenn du den Typ auf <b>tour</b> oder auf <b>mytour</b> eingestellt hast. Der Parameter muss die ID der Tour bzw. deiner benutzerdefinierten Tour enthalten.</li>
			<li><b>Ausschnitt:</b> Diesen Parameter musst du nur angeben, wenn du den Typ <b>teil</b> gew&auml;hlt hast. Der Parameter bestimmt den Mittelpunkt und den Vergr&ouml;&szlig;erungsfaktor des gew&uuml;nschten Kartenausschnittes. Er muss in der Form: <b>xPos,yPos,Zoom</b> angegeben werden, wobei Zoom eine optionale Angabe ist. Achte auf die zwei Beistriche! Beispiel: <b>674000,5138686,5</b>.
				<ul>
					<li><b>xPos:</b> Die Parameter xPos und yPos bestimmen den Mittelpunkt des gew&uuml;nschten Kartenausschnittes. Die Werte xPos und yPos m&uuml;ssen sich innerhalb von S&uuml;dtirol befinden. Die Werte xPos und yPos kannst du von der Karte: www.trekking.suedtirol.info ablesen. Einfach mit der Maus auf einen Punkt zeigen, dann werden dir die Koordinaten am unteren Rand der Karte (dort wo UTM steht) angezeigt. E entspricht xPos, N entspricht yPos.</li>
					<li><b>yPos:</b> Siehe Beschreibung xPos.</li>
					<li><b>Zoom:</b> Mit diesem Parameter kannst du den Vergr&ouml;&szlig;erungsfaktor des Ausschnittes bestimmen. Wenn du hier nichts eingibst dann wird 6 verwendet. Insgesamt gibt es 10 Stufen von 0 bis 10.</li>
				</ul>
			</li>
			<li><b>Umbruchart:</b> Hier kannst du <b>left</b>, <b>right</b> oder <b>none</b> eingeben. Dementsprechend wird die gesamte Karte <b>links</b>, <b>rechts</b> oder <b>nicht</b> gefloatet. Standardwert ist none. Interessant ist dieser Parameter im Zusammenspiel mit Breite und H&ouml;he.</li>
			<li><b>Sprache:</b> Hier kannst du <b>de</b> oder <b>it</b> eingeben. Standardwert ist de.</li>
			<li><b>Gr&ouml;&szlig;e:</b> Die Gr&ouml;&szlig;e der Karte wird  durch die Standardeinstellungen f&uuml;r Breite und H&ouml;he hier in den Einstellungen definiert. M&ouml;chtest du bei einer einzelnen Karte davon abweichende Werte setzen, dann kannst du das &uuml;ber diesen Parameter machen. Achtung er muss in der Form: <b>Breite,H&ouml;he</b> angegeben werden. Achte auf den Beistrich! Beispiel: <b>350,200</b>.</li>
			<li><b>Benutzer_Id:</b> Die Benutzer_Id f&uuml;r die Einbindung der Karte wird durch die Standardeinstellung hier in den Einstellungen definiert. M&ouml;chtest du bei einer einzelnen Karte eine andere Benutzer_Id definieren, dann kannst du das &uuml;ber diesen Parameter machen. Eine Benutzer_ID kannst du dir besorgen indem duch dich auf der Website: www.trekking.suedtirol.info registrierst!</li>
		</ul>
		<p></p>
		<h4>Beispiel</h4>

		<p>1. Ganze S&uuml;dtirol Karte einf&uuml;gen</p>
		<code>[trekk]gesamt[/trekk]</code>
		<p>Hierbei werden folgende Standardwerte verwendet: Sprache=de, Gr&ouml;&szlig;e wie hier in den Einstellungen definiert, Benutzer_Id wie hier in den Einstellungen definiert</p><hr>
		

		
		<p>2. Ausschnitt S&uuml;dtirol Karte einf&uuml;gen</p>
		<code>[trekk]teil::674000,5138686,5[/trekk]</code>
		<p>Der Ausschnitt um den Kalterer See wird eingebunden. Mittelpunkt der Karte ist xpos=674000, yPos=5138686. Der Vergr&ouml;&szlig;erungsfaktor wird auf 5 gesetzt. Achtung da der Parameter tour nicht angegeben wurde, m&uuml;ssen nach dem Parameter teil zwei Doppelpunkte folgen!</p><hr>

		<p>3. Vordefinierte Wandertour einf&uuml;gen</p>
		<code>[trekk]tour:107[/trekk]</code>
		<p>Die vordefinierte Tour mit der Id=107 wird samt Link zum H&ouml;henprofil eingebunden.</p><hr>

		<p>4. Benutzerdefinierte Tour einf&uuml;gen</p>
		<code>[trekk]mytour:2468[/trekk]</code>
		<p>Die benutzerdefinierte Tour mit der Id=2468 wird samt Link zum H&ouml;henprofil eingebunden.</p><hr>

		
		
		
		<p>5. Gr&ouml;&szlig;e der Karte und Textfluss der Karte bestimmen</p>
		<code>[trekk]mytour:2468::left:it:200,150[/trekk]</code>
		<p>Die benutzerdefinierte Tour mit der Id=2468 wird eingebunden. Dabei wird die Gr&ouml;&szlig;e abweichend von den Angaben hier in den Einstellungen auf 200 Pixel Breite und 150 Pixel H&ouml;he definiert, die Karte wird links gefloatet und es wird die italienische Karte eingef&uuml;gt. Achte darauf, dass du f&uuml;r Parameter die du ausl&auml;st trotzdem den Doppelpunkt machen musst! Die Angabe der Umbruchart muss sich z.B. immer an 4ter Stelle befinden!</p><hr>

		<h4>Formatierung der S&uuml;dtirolkarte und des Links zum Wander-H&ouml;henprofil</h4>
		<p>Das iframe, welches die Karte einbindet, und auch der Link zum H&ouml;henprofil befinden sich in einem SPAN-Tag, dem die CSS-Klasse <b>trekk</b> zugewiesen ist.</p>
		<p>Du kannst darum die Karte mittels CSS wir folgt formatieren: Beispiel (roter Rahmen um Karte): </p>
		<code>.trekk iframe {border:1px solid red}</code>
		<p>Den Link zum H&ouml;henprofil kannst du auch mittels CSS wie folgt formatieren: Beispiel (Schriftfarbe rot, Unterstreichung): </p>
		<code>.trekk a {color:red; text-decoration:underline}</code>
		<p>Achtung ohne diesen Anweisungen in deiner CSS-Datei wird keine Unterstreichung angezeigt, da der Link kein href-Attribut besitzt!</p>
		
	</div>
	<div class="wrap">
		<h2>Weitere Informationen</h2>
		<p>Eventuelle Updates findest du hier:  <a href="http://weblog.dietmar.biz/archives/157" target="_blank">http://weblog.dietmar.biz/archives/157</a>.<p>
		<p>Die Homepage des Autors findest du unter: <a href="http://www.compusol.it" target="_blank">www.compusol.it</a></p>
	</div>
<?
}

// Add Options Page
function trekkAdminSetup()
{
	add_options_page('Trekking S&uuml;dtirol (trekk)', 'Trekking S&uuml;dtirol', 8, basename(__FILE__), 'trekkOptionsPage');	
}

// Load trekk Actions
if (function_exists('add_action'))
{
	add_action('the_content', 'trekkContent');
	add_action('admin_menu', 'trekkAdminSetup');
}

?>