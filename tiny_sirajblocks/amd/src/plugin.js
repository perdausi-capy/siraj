// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Siraj blocks: a toolbar button (and Insert menu item) that inserts ready-designed content blocks.
 * The blocks are plain HTML with sb-* classes, styled by styles.css (pages) and editor_styles.css (the editor),
 * so authors can edit every word and the look follows the theme in light, dark and Arabic.
 *
 * Note: amd/build/plugin.min.js is this file hand-converted to AMD (no build step in this repo); keep them in step.
 *
 * @module     tiny_sirajblocks/plugin
 * @copyright  2026 Capytech
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getTinyMCE} from 'editor_tiny/loader';
import {getPluginMetadata, getButtonImage, addToolbarButton, addMenubarItem} from 'editor_tiny/utils';
import {getStrings} from 'core/str';

const component = 'tiny_sirajblocks';
const KEYS = ['buttontitle', 'insertmenu', 'b_banner', 'b_callout', 'b_tip', 'b_warning', 'b_keypoints', 'b_steps',
    'b_dodont', 'b_scenario', 'b_quote', 'b_stats', 'b_faq', 'b_columns', 'b_summary', 't_banner_eyebrow', 't_banner_title',
    't_banner_lead', 't_callout_title', 't_callout_text', 't_tip_title', 't_tip_text', 't_warning_title', 't_warning_text',
    't_keypoints_title', 't_point', 't_steps_step', 't_steps_text', 't_do', 't_dont', 't_do_item', 't_dont_item',
    't_scenario_label', 't_scenario_text', 't_scenario_question', 't_quote_text', 't_quote_cite', 't_stat_value',
    't_stat_label', 't_faq_question', 't_faq_answer', 't_col_title', 't_col_text', 't_summary_title', 't_summary_text'];

const esc = (s) => String(s).replace(/[&<>"]/g, (c) => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;'})[c]);

/**
 * The blocks: [string key for the menu, html].
 *
 * @param {Object} t Strings by key
 * @returns {Array}
 */
const blocks = (t) => {
    const s = (k) => esc(t[k]);
    const note = (kind, title, text) => `<div class="sb sb-note sb-${kind}"><p class="sb-title">${s(title)}</p><p>${s(text)}</p></div>`;
    return [
        ['b_banner', `<div class="sb sb-banner"><p class="sb-eyebrow">${s('t_banner_eyebrow')}</p>` +
            `<h2>${s('t_banner_title')}</h2><p class="sb-lead">${s('t_banner_lead')}</p></div>`],
        ['b_callout', note('info', 't_callout_title', 't_callout_text')],
        ['b_tip', note('tip', 't_tip_title', 't_tip_text')],
        ['b_warning', note('warn', 't_warning_title', 't_warning_text')],
        ['b_keypoints', `<div class="sb sb-keypoints"><p class="sb-title">${s('t_keypoints_title')}</p>` +
            `<ul><li>${s('t_point')}</li><li>${s('t_point')}</li><li>${s('t_point')}</li></ul></div>`],
        ['b_steps', `<ol class="sb sb-steps">` + [1, 2, 3].map((n) =>
            `<li><p class="sb-title">${s('t_steps_step')} ${n}</p><p>${s('t_steps_text')}</p></li>`).join('') + `</ol>`],
        ['b_dodont', `<div class="sb sb-dodont"><div class="sb-do"><p class="sb-title">${s('t_do')}</p>` +
            `<ul><li>${s('t_do_item')}</li><li>${s('t_do_item')}</li></ul></div>` +
            `<div class="sb-dont"><p class="sb-title">${s('t_dont')}</p>` +
            `<ul><li>${s('t_dont_item')}</li><li>${s('t_dont_item')}</li></ul></div></div>`],
        ['b_scenario', `<div class="sb sb-scenario"><p class="sb-title">${s('t_scenario_label')}</p>` +
            `<p>${s('t_scenario_text')}</p><p class="sb-ask">${s('t_scenario_question')}</p></div>`],
        ['b_quote', `<blockquote class="sb sb-quote"><p>${s('t_quote_text')}</p><p class="sb-cite">${s('t_quote_cite')}</p></blockquote>`],
        ['b_stats', `<div class="sb sb-stats">` + [1, 2, 3].map(() =>
            `<div class="sb-stat"><p class="sb-value">${s('t_stat_value')}</p><p>${s('t_stat_label')}</p></div>`).join('') + `</div>`],
        ['b_faq', `<details class="sb sb-faq"><summary>${s('t_faq_question')}</summary><p>${s('t_faq_answer')}</p></details>`],
        ['b_columns', `<div class="sb sb-columns">` + [1, 2].map(() =>
            `<div class="sb-col"><h3>${s('t_col_title')}</h3><p>${s('t_col_text')}</p></div>`).join('') + `</div>`],
        ['b_summary', note('summary', 't_summary_title', 't_summary_text')],
    ];
};

export default new Promise(async(resolve) => {
    const [tinyMCE, metadata, image, strs] = await Promise.all([
        getTinyMCE(),
        getPluginMetadata(component, 'plugin'),
        getButtonImage('icon', component),
        getStrings(KEYS.map((key) => ({key, component}))),
    ]);
    const t = {};
    KEYS.forEach((k, i) => {
        t[k] = strs[i];
    });

    tinyMCE.PluginManager.add(`${component}/plugin`, (editor) => {
        const items = () => blocks(t).map(([key, html]) => ({
            type: 'menuitem',
            text: t[key],
            // The block, then an empty paragraph so writing continues after it.
            onAction: () => editor.insertContent(html + '<p><br></p>'),
        }));
        editor.ui.registry.addIcon(component, image.html);
        editor.ui.registry.addMenuButton(component, {icon: component, tooltip: t.buttontitle, fetch: (done) => done(items())});
        editor.ui.registry.addNestedMenuItem(component, {icon: component, text: t.insertmenu, getSubmenuItems: items});
        return metadata;
    });

    resolve([`${component}/plugin`, {
        configure: (config) => ({
            toolbar: addToolbarButton(config.toolbar, 'content', component),
            menu: addMenubarItem(config.menu, 'insert', component),
        }),
    }]);
});
