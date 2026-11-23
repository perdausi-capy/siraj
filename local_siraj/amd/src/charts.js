// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Siraj dashboard charts: Chart.js (the copy Moodle ships) styled with the Siraj tokens.
 *
 * A chart is a canvas with data-siraj-chart='{"type": "bar"|"line", "labels": [], "series": [
 * {"label", "data", "color": "mint"|"blue"|"amber"|"red", "type"?: "line", "axis"?: "y1", "fill"?: true}]}'.
 * Charts are drawn when they scroll into view, follow light/dark mode, and skip animation for reduced motion.
 *
 * @module     local_siraj/charts
 * @copyright  2026 Capytech
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/chartjs'], function(Chart) {
    var charts = [];

    var token = function(name, fallback) {
        var v = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
        return v || fallback;
    };

    var palette = function() {
        return {
            mint: token('--si-mint', '#00C98C'),
            blue: token('--si-blue', '#3A70B8'),
            amber: '#E9A23B',
            red: token('--si-red', '#C2412F'),
            ink: token('--si-ink', '#102536'),
            muted: token('--si-muted', '#5A6975'),
            line: token('--si-line', '#E4EAED'),
            surface: token('--si-surface', '#FFFFFF')
        };
    };

    var alpha = function(hex, a) {
        var m = /^#([0-9a-f]{6})$/i.exec(hex);
        if (!m) {
            return hex;
        }
        var n = parseInt(m[1], 16);
        return 'rgba(' + (n >> 16) + ',' + ((n >> 8) & 255) + ',' + (n & 255) + ',' + a + ')';
    };

    var config = function(spec) {
        var p = palette();
        var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var hasRight = spec.series.some(function(s) {
            return s.axis === 'y1';
        });
        var datasets = spec.series.map(function(s) {
            var c = p[s.color] || p.mint;
            var type = s.type || spec.type;
            return {
                type: type,
                label: s.label,
                data: s.data,
                yAxisID: s.axis || 'y',
                backgroundColor: type === 'bar' ? alpha(c, 0.85) : (s.fill ? alpha(c, 0.14) : c),
                borderColor: c,
                borderWidth: type === 'bar' ? 0 : 2.5,
                borderRadius: type === 'bar' ? 6 : 0,
                maxBarThickness: 26,
                pointRadius: type === 'line' ? 3 : 0,
                pointHoverRadius: 5,
                pointBackgroundColor: p.surface,
                pointBorderWidth: 2,
                tension: 0.35,
                fill: !!s.fill,
                order: type === 'line' ? 0 : 1
            };
        });
        var axis = function(position, grid) {
            return {
                position: position,
                beginAtZero: true,
                border: {display: false},
                grid: {color: grid ? p.line : 'transparent', drawTicks: false},
                ticks: {color: p.muted, padding: 8, precision: 0, font: {size: 12}}
            };
        };
        var scales = {
            x: {grid: {display: false}, border: {display: false}, ticks: {color: p.muted, font: {size: 12}, maxRotation: 0,
                autoSkip: true, autoSkipPadding: 12}},
            y: axis('left', true)
        };
        if (hasRight) {
            scales.y1 = axis('right', false);
        }
        return {
            type: spec.type,
            data: {labels: spec.labels, datasets: datasets},
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: reduce ? false : {duration: 500},
                interaction: {mode: 'index', intersect: false},
                layout: {padding: {top: 6}},
                scales: scales,
                plugins: {
                    legend: {position: 'bottom', align: 'start', labels: {color: p.muted, usePointStyle: true, pointStyle: 'circle',
                        boxWidth: 8, boxHeight: 8, padding: 18, font: {size: 12.5}}},
                    tooltip: {backgroundColor: p.ink, titleColor: p.surface, bodyColor: p.surface, padding: 10, cornerRadius: 10,
                        boxPadding: 4, usePointStyle: true}
                }
            }
        };
    };

    var draw = function(canvas) {
        if (canvas.dataset.drawn) {
            return;
        }
        canvas.dataset.drawn = '1';
        var spec = JSON.parse(canvas.dataset.sirajChart);
        Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
        charts.push({chart: new Chart(canvas, config(spec)), spec: spec});
    };

    var retheme = function() {
        charts.forEach(function(item) {
            var fresh = config(item.spec);
            item.chart.data.datasets.forEach(function(ds, i) {
                var f = fresh.data.datasets[i];
                ds.backgroundColor = f.backgroundColor;
                ds.borderColor = f.borderColor;
                ds.pointBackgroundColor = f.pointBackgroundColor;
            });
            item.chart.options.scales = fresh.options.scales;
            item.chart.options.plugins = fresh.options.plugins;
            item.chart.update('none');
        });
    };

    return {
        init: function() {
            var canvases = document.querySelectorAll('canvas[data-siraj-chart]');
            if (!canvases.length) {
                return;
            }
            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function(entries) {
                    entries.forEach(function(e) {
                        if (e.isIntersecting) {
                            io.unobserve(e.target);
                            draw(e.target);
                        }
                    });
                }, {rootMargin: '200px'});
                canvases.forEach(function(c) {
                    io.observe(c);
                });
            } else {
                canvases.forEach(draw);
            }
            new MutationObserver(retheme).observe(document.documentElement, {attributes: true, attributeFilter: ['data-bs-theme']});
        }
    };
});
