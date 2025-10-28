<?php
// ----------------------------------------------------------------
// obligate check for phpwcms constants
if (!defined('PHPWCMS_ROOT')) {
	die("You Cannot Access This Script Directly, Have a Nice Day.");
}
// ----------------------------------------------------------------
setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'de', 'ge');
// Module/weatherwarnings
$ModulPath 		= $phpwcms['modules']['weatherwarnings']['path']; 
$fomname 		= 'carsearch';
$ModulLS		="\n\r";

require_once __DIR__ . '/lang/en.lang.php';
if (is_file(__DIR__ . '/lang/' . $phpwcms['default_lang'] . '.lang.php')) {
	require_once __DIR__ . '/lang/' . $phpwcms['default_lang'] . '.lang.php';
}

//Platzhalter
$_Weather_load 	= strpos($content['all'], '{WEATHERWARNINGS}');

//lade Klassen und functionen
require_once $ModulPath.'Classes/class.weatherwarning.php';

if($_Weather_load != False)
{
	//load javasrcipt file
	$GLOBALS['block']['custom_htmlhead']['weatherwarnings-js'] = '<script src="'.$phpwcms['modules']['weatherwarnings']['dir'].'template/js/script.js" type="text/javascript"></script>';
	$GLOBALS['block']['custom_htmlhead']['weatherwarnings-css'] = '<link rel="stylesheet" type="text/css" href="'.$phpwcms['modules']['weatherwarnings']['dir'].'template/css/style.css" />';

	$TEMPHTML ='';
	$warning = new weatherwarings();
	
	//add standorte bernburg
	$warning->addWarnCellID(815089030);
	$warning->addWarnCellID(115089000, 'kreis'); //salzlandkreis
	//add Ballenstedt
	$warning->addWarnCellID(815085040);
	//add eisleben	
	$warning->addWarnCellID(815087130);

	//add Koethen	
	$warning->addWarnCellID(815082180);
	$warning->addWarnCellID(115082000, 'kreis'); //Kreis Anhalt-Bitterfeld

	//add Koennern	
	$warning->addWarnCellID(815089195);
	//add Güsten	
	$warning->addWarnCellID(815089165);
	
	//Quedlingburg 815085235
	$warning->addWarnCellID(815085235);	
	$warning->addWarnCellID(115085000, 'kreis'); //Kreis Harz
	$warning->addWarnCellID(915085001, 'kreis'); //Kreis Harz - Tiefland
	$warning->addWarnCellID(903000008, 'kreis'); //Harz

	if(($data = $warning->getWarningDatas()) === FALSE){
		foreach($warning->getErrors() as $index => $message){
			$TEMPHTML .= '<div class="error">'.$message.'</div>';
		}
	}

	if(!empty($data) 
		&& is_array($data)
	 	&& count($data) > 0){
		
		$TEMPHTML .= '<div id="weatherwarnings" class="default">'.$ModulLS;		
		$TEMPHTML .= '<div class="row">'.$ModulLS;
		$TEMPHTML .= '<div class="container">'.$ModulLS;
			$TEMPHTML .= '<div class="row">'.$ModulLS;
				$TEMPHTML .= '<div class="header">'.$ModulLS.'<h3>'.$ModulLS;
				$TEMPHTML .= ' Aktuelle Wetterwarnungen ';
				$TEMPHTML .= '</h3>'.$ModulLS.'</div>'.$ModulLS;
			$TEMPHTML .= '</div>'.$ModulLS; //close row
			$TEMPHTML .= '<div class="row">'.$ModulLS;
			foreach($data as $event => $feature){
				
				if(!empty($feature['regions'])
				&& count($feature['regions']) > 0){

					$TEMPHTML .= '<div class="flex-item">'.$ModulLS;
						$TEMPHTML .= '<div class="warn-header"><div class="row"><h4>'.$feature['HEADLINE'] . '</h4></div></div>'.$ModulLS;
						$TEMPHTML .= '<div class="warn-body">'.$ModulLS;
							$TEMPHTML .= '<div class="row">'.$ModulLS;
							if(is_file($phpwcms['modules']['weatherwarnings']['path'].'/template/icons/'.$feature['EC_GROUP'].'.png')){
								$TEMPHTML .= '<div class="warn-icons"><img src="'.$phpwcms['modules']['weatherwarnings']['dir'].'/template/icons/'.$feature['EC_GROUP'].'.png" /></div>'.$ModulLS;
							}
								$TEMPHTML .= '<div class="warn-content">'.$ModulLS;
										$TEMPHTML .= '<div class="warn-description">'.$feature['DESCRIPTION'].'</div>'.$ModulLS;													
										$TEMPHTML .= '<div class="warn-times">'.$ModulLS;						
											$TEMPHTML .= '<div class="warn-time-start">Gültig von: '.$BLM['weekdays'][date('w',strtotime($feature['ONSET']))] 
													.', den '. date('d.m.y H:i',strtotime($feature['ONSET'])) .' Uhr</div>'.$ModulLS;
											$TEMPHTML .= '<div class="warn-time-end">bis: '. $BLM['weekdays'][date('w',strtotime($feature['EXPIRES']))] 
													. ', den '.date('d.m.y H:i',strtotime($feature['EXPIRES'])).' Uhr</div>'.$ModulLS;
										$TEMPHTML .= '</div>'.$ModulLS; //close warn-content	
								$TEMPHTML .= '</div>'.$ModulLS; //close warn-content				
							$TEMPHTML .= '</div>'.$ModulLS; //close row
							$TEMPHTML .= '<div class="row">'.$ModulLS;
								$TEMPHTML .= '<div class="warn-instruction">'.$feature['INSTRUCTION'].'</div>'.$ModulLS;
							$TEMPHTML .= '</div>'; //close row
						$TEMPHTML .= '</div>'.$ModulLS; //close warn-body
						$TEMPHTML .= '<div class="regionsBlock">';
							$TEMPHTML .= '<div class="row">'.$ModulLS;
							$TEMPHTML .= '<div class="label">Betroffene Regionen:</div>';
							$TEMPHTML .= '<div class="regions">';
								$TEMPHTML .= '<div class="row">'.$ModulLS;
								foreach($feature['regions'] as $index => $region){
									$TEMPHTML .= '<div class="region">'.$region.'</div>';
								}
								$TEMPHTML .= '</div>'; //close row
							$TEMPHTML .= '</div>'; //close regions
							$TEMPHTML .= '</div>'; //close row
						$TEMPHTML .= '</div>'; //close regionsBlock
						$TEMPHTML .= '<div class="warn-footer">'.$ModulLS;
							$TEMPHTML .= '<div class="row">'.$ModulLS;	
								$TEMPHTML .= '<div class="warn-sender">'.$feature['SENDERNAME'].'</div>'.$ModulLS;
								$TEMPHTML .= '<div class="warn-cell-id">'.$feature['WARNCELLID'].'</div>'.$ModulLS;
							$TEMPHTML .= '</div>'.$ModulLS; //close row
						$TEMPHTML .= '</div>'.$ModulLS; //close warn-footer
					$TEMPHTML .= '</div>'.$ModulLS; //close flex-item
				}			
			}
			$TEMPHTML .= '</div>'.$ModulLS; //close row
			$TEMPHTML .= '<div class="row">'.$ModulLS;
				$TEMPHTML .= '<button id="close-warnings" class="btn btn-default">Wetterwarnungen schließen</button>';
			$TEMPHTML .= '</div>'.$ModulLS; //close row
		$TEMPHTML .= '</div>'.$ModulLS; //close Container
		$TEMPHTML .= '</div>'.$ModulLS; //close row
		
		$TEMPHTML .= '</div>'.$ModulLS; //close #weatherwarnings		
	}

	//Übergabe an CMS-Template
	$content['all'] = str_replace('{WEATHERWARNINGS}', $TEMPHTML, $content['all']);

}