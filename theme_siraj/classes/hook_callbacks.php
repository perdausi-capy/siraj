<?php
/**
 * Hook callbacks for theme_siraj.
 *
 * @package   theme_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_siraj;

/**
 * Page head additions for the Siraj theme.
 */
class hook_callbacks {
    /**
     * Pages that are safe to fetch ahead of a click: they only show information. Activity pages are excluded
     * on purpose (opening them can mark an activity as viewed/completed), and so are Siraj course and
     * Learning mode pages (they record course views and last access).
     */
    private const PREFETCH = ['/local/siraj/index.php', '/local/siraj/learning.php', '/local/siraj/explore.php', '/local/siraj/achievements.php', '/my/courses.php', '/course/index.php', '/calendar/view.php*', '/badges/mybadges.php'];

    /**
     * Font preloads, saved light/dark mode before first paint, and link prefetching.
     *
     * @param \core\hook\output\before_standard_head_html_generation $hook
     */
    public static function before_standard_head_html_generation(
        \core\hook\output\before_standard_head_html_generation $hook): void {
        global $PAGE, $CFG;
        if ($PAGE->theme->name !== 'siraj') {
            return;
        }
        $html = '';
        // Arabic pages also preload the Arabic font, so text never shows in a stand-in font first and then swaps.
        $fonts = ['inter-latin.woff2', 'instrumentserif-latin.woff2'];
        if (right_to_left()) {
            $fonts = ['plexarabic-400.woff2', 'plexarabic-600.woff2', 'inter-latin.woff2'];
        }
        foreach ($fonts as $font) {
            $url = $PAGE->theme->font_url($font, 'theme');
            if ($url) {
                $html .= '<link rel="preload" href="' . s($url->out(false)) . '" as="font" type="font/woff2" crossorigin>';
            }
        }
        // Light/dark and the collapsed sidebar live in small cookies: Moodle clears the browser's localStorage whenever
        // its JavaScript version changes (every upgrade or cache purge), which would reset everyone's choice.
        $html .= '<script>(function(){function c(k){var m=document.cookie.match(new RegExp("(?:^|; )"+k+"=([^;]*)"));'
            . 'if(m){return m[1];}try{return localStorage.getItem(k);}catch(e){return null;}}'
            . 'var t=c("siraj-theme");if(t==="dark"||t==="light"){document.documentElement.setAttribute("data-bs-theme",t);}'
            . 'if(c("siraj-collapsed")!=="0"){document.documentElement.classList.add("siraj-collapsed-early");}'
            // First page after signing in: play the welcome entrance once (the sign-in form leaves a flag).
            . 'try{if(sessionStorage.getItem("siraj-welcome")&&!document.querySelector("body")){var w=sessionStorage.getItem("siraj-welcome");'
            . 'if(Date.now()-w<60000){document.documentElement.classList.add("siraj-welcome");}}}catch(e){}'
            // Arriving after a language switch: start on the Siraj language screen and fade it away.
            . 'try{var l=JSON.parse(sessionStorage.getItem("siraj-lang")||"null");sessionStorage.removeItem("siraj-lang");'
            . 'if(l&&Date.now()-l.t<15000){var h=document.documentElement;h.classList.add("siraj-langin");'
            . 'h.style.setProperty("--siraj-lang-word",JSON.stringify(l.w));h.style.setProperty("--siraj-lang-sub",JSON.stringify(l.s));'
            . 'setTimeout(function(){h.classList.remove("siraj-langin");},1400);}}catch(e){}})();</script>';
        if (isloggedin() && !isguestuser()) {
            $path = parse_url($CFG->wwwroot, PHP_URL_PATH) ?? '';
            $rules = ['prefetch' => [[
                'where' => ['and' => [
                    ['or' => array_map(fn($p) => ['href_matches' => $path . $p], self::PREFETCH)],
                    ['not' => ['href_matches' => '*sesskey=*']],
                ]],
                'eagerness' => 'conservative',  // starts on mousedown/touch, so it never competes with the page in use
            ]]];
            $html .= '<script type="speculationrules">' . json_encode($rules, JSON_UNESCAPED_SLASHES) . '</script>';
        }
        $hook->add_html($html);
    }
}
