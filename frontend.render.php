<?php
// ----------------------------------------------------------------
// obligate check for phpwcms constants
if (!defined('PHPWCMS_ROOT')) {
	die("You Cannot Access This Script Directly, Have a Nice Day.");
}
// ----------------------------------------------------------------
setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'de', 'ge');
// Module/weatherwarnings
$ModulPath = $phpwcms['modules']['weatherwarnings']['path'];
$ModulLS   = "\n\r";

require_once __DIR__ . '/lang/en.lang.php';
if (is_file(__DIR__ . '/lang/' . $phpwcms['default_lang'] . '.lang.php')) {
	require_once __DIR__ . '/lang/' . $phpwcms['default_lang'] . '.lang.php';
}

//Platzhalter
$_Weather_load = strpos($content['all'], '{WEATHERWARNINGS}');

//lade Klassen und Funktionen
require_once $ModulPath . 'Classes/class.weatherwarning.php';

if ($_Weather_load !== false) {

	// CSS + JS in den <head>
	$GLOBALS['block']['custom_htmlhead']['weatherwarnings-css'] = '<link rel="stylesheet" type="text/css" href="' . $phpwcms['modules']['weatherwarnings']['dir'] . 'template/css/style.css" />';
	$GLOBALS['block']['custom_htmlhead']['weatherwarnings-js']  = '<script src="' . $phpwcms['modules']['weatherwarnings']['dir'] . 'template/js/script.js" type="text/javascript"></script>';

	$TEMPHTML = '';
	$warning  = new weatherwarings();

	// Standorte Bernburg + Salzlandkreis
	$warning->addWarnCellID(815089030);
	$warning->addWarnCellID(115089000, 'kreis');
	// Ballenstedt
	$warning->addWarnCellID(815085040);
	// Eisleben
	$warning->addWarnCellID(815087130);
	// Köthen + Anhalt-Bitterfeld
	$warning->addWarnCellID(815082180);
	$warning->addWarnCellID(115082000, 'kreis');
	// Könnern
	$warning->addWarnCellID(815089195);
	// Güsten
	$warning->addWarnCellID(815089165);
	// Quedlinburg + Harz
	$warning->addWarnCellID(815085235);
	$warning->addWarnCellID(115085000, 'kreis');
	$warning->addWarnCellID(915085001, 'kreis');
	$warning->addWarnCellID(903000008, 'kreis');

	if (($data = $warning->getWarningDatas()) === false) {
		foreach ($warning->getErrors() as $message) {
			$TEMPHTML .= '<div class="error">' . htmlspecialchars($message) . '</div>' . $ModulLS;
		}
	}

	if (!empty($data) && is_array($data) && count($data) > 0) {

		$TEMPHTML .= '<div id="weatherwarnings" class="default">' . $ModulLS;
		$TEMPHTML .= '<div class="container">' . $ModulLS;

		// Header
		$TEMPHTML .= '<div class="header"><h3>Aktuelle Wetterwarnungen</h3></div>' . $ModulLS;

		// Cards
		$TEMPHTML .= '<div class="warn-cards">' . $ModulLS;

		foreach ($data as $event => $feature) {

			if (empty($feature['regions']) || count($feature['regions']) === 0) {
				continue;
			}

			$iconPath = $phpwcms['modules']['weatherwarnings']['path'] . '/template/icons/' . $feature['EC_GROUP'] . '.png';
			$iconUrl  = $phpwcms['modules']['weatherwarnings']['dir'] . '/template/icons/' . $feature['EC_GROUP'] . '.png';

			$TEMPHTML .= '<div class="flex-item">' . $ModulLS;

			// Card header
			$TEMPHTML .= '<div class="warn-header"><div class="row"><h4>' . htmlspecialchars($feature['HEADLINE']) . '</h4></div></div>' . $ModulLS;

			// Card body
			$TEMPHTML .= '<div class="warn-body">' . $ModulLS;
			$TEMPHTML .= '<div class="row">' . $ModulLS;

			if (is_file($iconPath)) {
				$TEMPHTML .= '<div class="warn-icons"><img src="' . $iconUrl . '" alt="' . htmlspecialchars($feature['EC_GROUP']) . '" /></div>' . $ModulLS;
			}

			$TEMPHTML .= '<div class="warn-content">' . $ModulLS;
			$TEMPHTML .= '<div class="warn-description">' . nl2br(htmlspecialchars($feature['DESCRIPTION'])) . '</div>' . $ModulLS;
			$TEMPHTML .= '<div class="warn-times">' . $ModulLS;
			$TEMPHTML .= '<div class="warn-time-start">Gültig von: '
				. $BLM['weekdays'][date('w', strtotime($feature['ONSET']))]
				. ', den ' . date('d.m.y H:i', strtotime($feature['ONSET'])) . ' Uhr</div>' . $ModulLS;
			$TEMPHTML .= '<div class="warn-time-end">bis: '
				. $BLM['weekdays'][date('w', strtotime($feature['EXPIRES']))]
				. ', den ' . date('d.m.y H:i', strtotime($feature['EXPIRES'])) . ' Uhr</div>' . $ModulLS;
			$TEMPHTML .= '</div>' . $ModulLS; // .warn-times
			$TEMPHTML .= '</div>' . $ModulLS; // .warn-content

			$TEMPHTML .= '</div>' . $ModulLS; // .row (icon + content)

			if (!empty($feature['INSTRUCTION'])) {
				$TEMPHTML .= '<div class="row">' . $ModulLS;
				$TEMPHTML .= '<div class="warn-instruction">' . nl2br(htmlspecialchars($feature['INSTRUCTION'])) . '</div>' . $ModulLS;
				$TEMPHTML .= '</div>' . $ModulLS;
			}

			$TEMPHTML .= '</div>' . $ModulLS; // .warn-body

			// Regions
			$TEMPHTML .= '<div class="regionsBlock">' . $ModulLS;
			$TEMPHTML .= '<div class="row">' . $ModulLS;
			$TEMPHTML .= '<div class="label">Betroffene Regionen:</div>' . $ModulLS;
			$TEMPHTML .= '<div class="regions"><div class="row">' . $ModulLS;
			foreach ($feature['regions'] as $region) {
				$TEMPHTML .= '<div class="region">' . htmlspecialchars($region) . '</div>';
			}
			$TEMPHTML .= '</div></div>' . $ModulLS; // .row .regions
			$TEMPHTML .= '</div>' . $ModulLS; // .row
			$TEMPHTML .= '</div>' . $ModulLS; // .regionsBlock

			// Footer
			$TEMPHTML .= '<div class="warn-footer">' . $ModulLS;
			$TEMPHTML .= '<div class="row">' . $ModulLS;
			$TEMPHTML .= '<div class="warn-sender">' . htmlspecialchars($feature['SENDERNAME']) . '</div>' . $ModulLS;
			$TEMPHTML .= '<div class="warn-cell-id">' . htmlspecialchars($feature['WARNCELLID']) . '</div>' . $ModulLS;
			$TEMPHTML .= '</div>' . $ModulLS; // .row
			$TEMPHTML .= '</div>' . $ModulLS; // .warn-footer

			$TEMPHTML .= '</div>' . $ModulLS; // .flex-item
		}

		$TEMPHTML .= '</div>' . $ModulLS; // .warn-cards

		// Close button
		$TEMPHTML .= '<div class="warn-close-row">' . $ModulLS;
		$TEMPHTML .= '<button id="close-warnings" class="btn btn-default">Wetterwarnungen ausblenden</button>' . $ModulLS;
		$TEMPHTML .= '</div>' . $ModulLS;

		$TEMPHTML .= '</div>' . $ModulLS; // .container
		$TEMPHTML .= '</div>' . $ModulLS; // #weatherwarnings
	}

	// Übergabe an CMS-Template
	$content['all'] = str_replace('{WEATHERWARNINGS}', $TEMPHTML, $content['all']);
}
