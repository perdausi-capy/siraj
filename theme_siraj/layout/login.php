<?php
/**
 * Siraj sign-in page: a looping city video behind the brand headline and Moodle's login form in a glass card.
 *
 * @package   theme_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$sitename = format_string($SITE->fullname, true, ['context' => context_course::instance(SITEID), 'escape' => false]);
$primary = new core\navigation\output\primary($PAGE);
$primarymenu = $primary->export_for_template($PAGE->get_renderer('core'));

$templatecontext = [
    'sitename' => $sitename,
    'output' => $OUTPUT,
    'bodyattributes' => $OUTPUT->body_attributes(['siraj-login-page']),
    'tagline' => get_config('theme_siraj', 'logintagline') ?: get_string('tagline', 'theme_siraj'),
    'subline' => get_config('theme_siraj', 'loginsubline') ?: get_string('subline', 'theme_siraj'),
    'panellogo' => theme_siraj_panel_logo_url(),
    'flame' => \theme_siraj\icons::flame('si-brand-flame'),
    'icon_sun' => \theme_siraj\icons::svg('sun', 'si-show-light'),
    'icon_moon' => \theme_siraj\icons::svg('moon', 'si-show-dark'),
    'icon_layers' => \theme_siraj\icons::svg('layers'),
    'icon_award' => \theme_siraj\icons::svg('award'),
    'icon_globe' => \theme_siraj\icons::svg('globe'),
    'icon_check' => \theme_siraj\icons::svg('check'),
    'icon_mail' => \theme_siraj\icons::svg('mail'),
    // Forgotten password: the same card, with a heading for each step (see the template).
    'forgot' => $PAGE->pagetype === 'login-forgot_password',
    'posted' => ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST',
    'loginurl' => get_login_url(),
    'langmenu' => $primarymenu['lang'],
    'year' => date('Y'),
    'languages' => theme_siraj_login_languages(),
    // Background video (Pexels, free licence; see video/CREDITS.txt). Loaded by JS on wider screens only.
    'videowebm' => (new moodle_url('/theme/siraj/video/siraj-dubai-sunrise.webm'))->out(false),
    'videomp4' => (new moodle_url('/theme/siraj/video/siraj-dubai-sunrise.mp4'))->out(false),
    'videoposter' => (new moodle_url('/theme/siraj/video/siraj-dubai-sunrise.jpg'))->out(false),
];

$templatecontext['haslanguages'] = count($templatecontext['languages']) > 1;
$templatecontext['currentshort'] = '';
foreach ($templatecontext['languages'] as $l) {
    if ($l['current']) {
        $templatecontext['currentshort'] = $l['short'];
    }
}
echo $OUTPUT->render_from_template('theme_siraj/login', $templatecontext);

/**
 * Installed languages for the sign-in page menu (plain links, no JavaScript needed).
 *
 * @return array
 */
function theme_siraj_login_languages(): array {
    global $PAGE;
    $out = [];
    $current = current_language();
    foreach (get_string_manager()->get_list_of_translations() as $code => $name) {
        $url = new moodle_url($PAGE->url, ['lang' => $code]);
        $out[] = ['code' => $code, 'current' => $code === $current,
            'name' => trim(preg_replace('/[\s\x{200E}\x{200F}]*\(' . preg_quote($code, '/') . '\)[\s\x{200E}\x{200F}]*$/u', '',
                html_entity_decode($name, ENT_QUOTES, 'UTF-8'))),
            'short' => strpos($code, 'ar') === 0 ? 'ع' : core_text::strtoupper(substr($code, 0, 2)),
            'url' => $url->out(false)];
    }
    return $out;
}
