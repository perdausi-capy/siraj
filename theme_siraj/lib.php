<?php
/**
 * Siraj theme functions.
 *
 * @package   theme_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/theme/boost/lib.php');

/**
 * The brand presets. Each client gets one; the theme code is the same for everyone.
 *
 * @return array preset key => display name
 */
function theme_siraj_presets(): array {
    return ['siraj' => 'Siraj (default)'];
}

/**
 * Current preset key.
 *
 * @param theme_config|null $theme
 * @return string
 */
function theme_siraj_current_preset($theme = null): string {
    $theme = $theme ?? theme_config::load('siraj');
    $preset = $theme->settings->brandpreset ?? 'siraj';
    return array_key_exists($preset, theme_siraj_presets()) ? $preset : 'siraj';
}

/**
 * Brand variables from the preset, then the admin's own pre-SCSS.
 *
 * @param theme_config $theme
 * @return string
 */
function theme_siraj_get_pre_scss($theme) {
    global $CFG;
    $preset = theme_siraj_current_preset($theme);
    $scss = file_get_contents($CFG->dirroot . "/theme/siraj/scss/preset/{$preset}.scss");
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }
    return $scss;
}

/**
 * Boost's default preset plus the Siraj layer.
 *
 * @param theme_config $theme
 * @return string
 */
function theme_siraj_get_main_scss_content($theme) {
    global $CFG;
    $scss = file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    foreach (['tokens', 'shell', 'components', 'login', 'pages', 'dashboards', 'dark', 'polish', 'blocks', 'concepta', 'moodlepages'] as $part) {
        $scss .= file_get_contents($CFG->dirroot . "/theme/siraj/scss/siraj/{$part}.scss");
    }
    $scss .= \theme_siraj\icons::block_icon_css();
    if (!empty($theme->settings->scss)) {
        $scss .= $theme->settings->scss;
    }
    return $scss;
}

/**
 * Logo for the dark login panel (usually a white version of the client logo).
 *
 * @return string|null
 */
function theme_siraj_panel_logo_url(): ?string {
    $file = get_config('theme_siraj', 'panellogo');
    if (empty($file)) {
        return null;
    }
    return moodle_url::make_pluginfile_url(context_system::instance()->id, 'theme_siraj', 'panellogo',
        theme_get_revision(), '/', ltrim($file, '/'))->out(false);
}

/**
 * Serve theme files.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_siraj_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel == CONTEXT_SYSTEM && $filearea === 'panellogo') {
        $theme = theme_config::load('siraj');
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve('panellogo', $args, $forcedownload, $options);
    }
    send_file_not_found();
}
