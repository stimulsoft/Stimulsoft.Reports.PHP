/*
Stimulsoft.Reports.JS
Version: 2023.1.7
Build date: 2023.02.10
License: https://www.stimulsoft.com/en/licensing/reports
*/
!function(t){var f;"object"==typeof exports&&"undefined"!=typeof module?module.exports=(f=require("./stimulsoft.viewer.pack"),Object.assign(f,t(f.Stimulsoft))):"function"==typeof define&&define.amd?define(["./stimulsoft.viewer.pack"],f=>Object.assign(f,t(f.Stimulsoft))):Object.assign(window,t(window.Stimulsoft))}(function(f){var t={Stimulsoft:f||{}};if(f&&(f.__engineVersion&&"2023.1.7"!==f.__engineVersion?console.warn("Scripts versions mismatch: engine ver. = %s; designer ver. = 2023.1.7",f.__engineVersion):"2023.1.7"!==f.__reportsVersion&&console.warn("Scripts versions mismatch: reports ver. = %s; designer ver. = 2023.1.7",f.__reportsVersion)),
,t.Stimulsoft.decodePackedData&&t.Stimulsoft.Viewer)for(const b of["designerScript","blocklyScript"])t.Stimulsoft[b]&&Object.assign(t,t.Stimulsoft.decodePackedData(t.Stimulsoft[b])(t.Stimulsoft)),delete t.Stimulsoft[b];return t});