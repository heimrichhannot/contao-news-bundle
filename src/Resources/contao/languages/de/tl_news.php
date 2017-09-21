<?php

$lang = &$GLOBALS['TL_LANG']['tl_news'];

/**
 * Fields
 */
$lang['teaser_short']            = ['Teasertext (kurz)', 'Der Teasertext kann in einer Nachrichtenliste anstatt des ganzen Beitrags angezeigt werden.'];
$lang['writers']                 = ['Verfasser auswählen', 'Wählen Sie einen oder mehrere Verfasser aus der Mitgliederdatenbank aus.'];
$lang['tags']                    = ['Schlagwörter', 'Vergeben Sie ein oder mehrere Schlagwörter.'];
$lang['addContactBox']           = ['Kontaktbox hinzufügen', 'Fügen Sie dem Beitrag einen Ansprechpartner / Kontaktperson hinzu.'];
$lang['contactBox_header']       = ['Überschrift Kontaktbox', 'Vergeben Sie eine Überschrift für die Kontaktbox.'];
$lang['contactBox_members']      = ['Kontakt hinzufügen', 'Wählen Sie einen Kontaktperson aus der Mitgliederverwaltung aus (E-Mail Addresse).'];
$lang['contactBox_links']        = ['Links hinzufügen', 'Fügen Sie mehrere Links zur Kontaktbox hinzu.'];
$lang['contactBox_link']         = ['Link', 'Geben Sie einen Link an.'];
$lang['contactBox_linkText']     = ['Linktitel', 'Geben Sie einen Linktitel an.'];
$lang['add_teaser_image']        = ['Ein Teaserbild hinzufügen', 'Dem Beitrag ein individuelles Bild für den Teaser hinzufügen.'];
$lang['add_readers_survey']      = ['Leser-Umfrage hinzufügen'];
$lang['readers_survey_question'] = ['Frage', 'Bitte geben Sie eine Frage ein.'];
$lang['readers_survey_answers']  = ['Antworten'];
$lang['readers_survey']          = ['Leser-Umfrage'];
$lang['news_answers']            = ['Antwort'];
$lang['readers_survey_answer']   = ['Antwort', 'Bitte geben Sie eine Antwort ein.'];
$lang['infoBox']                 = ['Infobox', 'Fügen Sie dem Beitrag eine Infobox hinzu.'];
$lang['infoBox_header']          = ['Infobox-Überschrift hinzufügen', 'Vergeben Sie der Infobox eine Überschrift.'];
$lang['infoBox_text']            = ['Infobox-Text', 'Vergeben Sie der Infobox einen Text.'];
$lang['infoBox_link']            = ['Infobox-Link hinzufügen', 'Fügen Sie der Infobox einen Link hinzu.'];
$lang['infoBox_link_text']       = ['Infobox-Link-Text hinzufügen', 'Vergeben Sie einen Linktext für den Link.'];
$lang['infoBox_header']          = ['Infobox-Überschrift hinzufügen'];
$lang['add_related_news']        = ['Verwandte Nachrichten hinzufügen', 'Verknüpfen Sie verwandte Nachrichten für diesen Beitrag.'];
$lang['related_news']            = [
    'Verwandte Nachrichten',
    'Erhalten Sie eine Auswahl von verwandten Nachrichten indem Sie deren Überschriften in dieses Feld eingeben. (Nachrichten können per Drag & Drop in soriert werden)',
];
$lang['pageTitle']               = ['Seitentitel', 'Vergeben Sie einen individuellen Seitentitel &lt;title&gt; für diese Nachricht. (Standard: Titel)'];
$lang['metaDescription']         = ['Meta-Description', 'Vergeben Sie eine individuellen &lt;meta name="description"&gt; für diese Nachricht. (Standard: Teasertext)'];
$lang['metaKeywords']            = ['Meta-Keywords', 'Vergeben Sie individuelle &lt;meta type="keywords"&gt; für diese Nachricht. (Standard: Keine)'];
$lang['twitterCard']             = ['Twitter Karte', 'Wählen Sie eine Twitter Karte aus, die bei Tweets angehängt werden soll.'];
$lang['twitterCreator']          = ['Author Twitter @username', 'Geben Sie den Twitter @username des Authors dieses Artikels ans.'];
$lang['player']                  = ['Video-/Audio-Dateien', 'Hier können Sie die Video-/Audio-Datei hinzufügen.'];
$lang['playerSRC']               = ['Video/Audio-Datei', 'Hier können Sie die Url zur Video/Audio-Datei bzw. – wenn Sie verschiedene Codecs verwenden – die Video-/Audio-Dateien hinzufügen.'];
$lang['playerUrl']               = ['Video/Audio-Url', 'Hier können Sie die Url zur Video/Audio-Datei bzw. – wenn Sie verschiedene Codecs verwenden – die Video-/Audio-Dateien (durch Pipe | getrennt) hinzufügen.'];
$lang['posterSRC']               = ['Vorschaubild', 'Das Bild statt des ersten Frame des Videos vor dem Abspielen anzeigen.'];

/**
 * Legends
 */
$lang['writers_legend']        = 'Verfasser';
$lang['tags_legend']           = 'Schlagwörter';
$lang['contact_box_legend']    = 'Kontakt';
$lang['readers_survey_legend'] = 'Leser-Umfrage';
$lang['info_box_legend']       = 'Infobox';
$lang['related_news_legend']   = 'Verwandte Nachrichten';
$lang['meta_legend']           = 'Metadaten';
$lang['twitter_legend']        = 'Twitter';
$lang['player_legend']         = 'Player-Einstellungen';

/**
 * Placeholders
 */
$lang['placeholders']['contactBox_members'] = 'E-Mail eingeben';
$lang['placeholders']['metaKeywords']       = 'Keywords eingeben';

/**
 * References
 */

$lang['reference']['twitterCardTypes']['summary']             = 'Summary Card [summary]';
$lang['reference']['twitterCardTypes']['summary_large_image'] = 'Summary Card with Large Image [summary_large_image]';
$lang['reference']['twitterCardTypes']['player']              = 'Player Card [player]';

$lang['reference']['infoBox']['none'] = 'Keine';
$lang['reference']['infoBox']['text'] = 'Text';

$lang['reference']['player']['none']     = 'Keine';
$lang['reference']['player']['internal'] = 'Datei';
$lang['reference']['player']['external'] = 'Externe URL';