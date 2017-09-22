<?php

$lang = &$GLOBALS['TL_LANG']['tl_module'];

/**
 * Fields
 */
$lang['use_news_lists']             = ['Nachrichtenlisten verwenden', 'Verwenden Sie individuelle Nachrichtenlisten für die Nachrichtenliste.'];
$lang['newsListMode']               = ['Nachrichtenlisten-Modus', 'Wählen Sie hier aus, wie die zu verwendenden Nachrichtenlisten geholt werden sollen.'];
$lang['news_lists']                 = ['Nachrichtenlisten', 'Bitte wählen Sie ein oder mehrere Nachrichtenlisten aus.'];
$lang['add_related_news']           = ['Verwandte Nachrichten anzeigen', 'Zeige verwandte Nachrichten für Beiträge an.'];
$lang['relatedNewsModules']         = ['Nachrichtenlisten', 'Bitte wählen Sie eines oder mehrere Nachrichtenlisten-Module für die Anzeige der verwandten Beiträge.'];
$lang['skipPreviousNews']           = ['Bereits angezeigte Nachrichten ausschließen', 'Schließen Sie bereits angezeigte Nachrichten aus.'];
$lang['news_readers_survey_result'] = ['Leser-Umfrage-Ergebnis'];
$lang['news_slick_box_selector']    = ['Typ auswählen'];
$lang['newsInfoBoxModule']          = ['Nachrichten Info-Box Modul', 'Definieren Sie ein Nachrichten Info-Box Modul.'];
$lang['addNewsTagFilter']           = ['Filterung durch Schlagworte erlauben', 'Wählen Sie diese Option, wenn die Liste nach Schlagworten filterbar sein soll.'];
$lang['newsTagFilterJumpTo']        = ['Weiterleitungsseite für Schlagwort-Links', 'Wählen Sie hier eine Seite aus, zu der beim Anklicken von Schlagwort-Links weitergeleitet werden soll.'];
$lang['addCustomSort']              = ['Sortierung überschreiben'];
$lang['sortClause']                 = ['ORDER-Bedingung überschreiben', 'Geben Sie hier die neue ORDER BY-Bedingung kommasepariert ein.(z.B.: tl_news.date DESC)'];
$lang['useTeaserImage']             = ['Teaserbild verwenden', 'Sofern vorhanden, verwende das Teaserbild anstatt des Beitragsbildes.'];
$lang['posterSRC']                  = ['Vorschaubild', 'Das Bild statt des ersten Frame des Videos vor dem Abspielen anzeigen.'];
$lang['newsListFilterModule']       = ['Nachrichtenliste-Filter-Modul', 'Wählen Sie ein Modul aus, dass die Filterung der Nachrichtenliste steuert.'];
/**
 * Legends
 */
$lang['news_readers_survey_result_legend'] = 'Leser-Umfrage-Ergebnis';
$lang['news_related_legend']               = 'Verwandte Nachrichten-Einstellungen';
$lang['tags_legend']                       = 'Schlagwort-Einstellungen';
$lang['news_info_box_legend']              = 'Infobox-Einstellungen';

/**
 * Reference
 */
$lang['reference']['newsBundle'] = [
    \HeimrichHannot\NewsBundle\Backend\NewsList::MODE_MANUAL    => 'Manuelle Auswahl',
    \HeimrichHannot\NewsBundle\Backend\NewsList::MODE_AUTO_ITEM => 'Auto-Item',
];