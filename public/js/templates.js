/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/js/templates.js":
/*!***********************************!*\
  !*** ./resources/js/templates.js ***!
  \***********************************/
/***/ (() => {

eval("\n\n$(function () {\n  $('body').on('change', '.js-choose-template', function () {\n    var link = '/templates/document-preview' + window.location.search;\n    var tplIds = [];\n    $('.js-choose-template:checked').each(function () {\n      var val = $(this).val();\n      tplIds.push(val);\n    });\n    link += '&' + tplIds.map(function (id) {\n      return 'tpl_id[]=' + id;\n    }).join('&');\n\n    if (tplIds.length) {\n      $('.js-generate-docs-link').show().attr('href', encodeURI(link));\n    } else {\n      $('.js-generate-docs-link').hide();\n    }\n  });\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9yZXNvdXJjZXMvanMvdGVtcGxhdGVzLmpzLmpzIiwibWFwcGluZ3MiOiJBQUFhOztBQUViQSxDQUFDLENBQUMsWUFBTTtFQUVKQSxDQUFDLENBQUMsTUFBRCxDQUFELENBQVVDLEVBQVYsQ0FBYSxRQUFiLEVBQXVCLHFCQUF2QixFQUE4QyxZQUFZO0lBQ3RELElBQUlDLElBQUksR0FBRyxnQ0FBZ0NDLE1BQU0sQ0FBQ0MsUUFBUCxDQUFnQkMsTUFBM0Q7SUFDQSxJQUFNQyxNQUFNLEdBQUcsRUFBZjtJQUVBTixDQUFDLENBQUMsNkJBQUQsQ0FBRCxDQUFpQ08sSUFBakMsQ0FBc0MsWUFBWTtNQUM5QyxJQUFNQyxHQUFHLEdBQUdSLENBQUMsQ0FBQyxJQUFELENBQUQsQ0FBUVEsR0FBUixFQUFaO01BQ0FGLE1BQU0sQ0FBQ0csSUFBUCxDQUFZRCxHQUFaO0lBQ0gsQ0FIRDtJQUtBTixJQUFJLElBQUksTUFBTUksTUFBTSxDQUFDSSxHQUFQLENBQVcsVUFBQUMsRUFBRSxFQUFJO01BQzNCLE9BQU8sY0FBY0EsRUFBckI7SUFDSCxDQUZhLEVBRVhDLElBRlcsQ0FFTixHQUZNLENBQWQ7O0lBSUEsSUFBSU4sTUFBTSxDQUFDTyxNQUFYLEVBQW1CO01BQ2ZiLENBQUMsQ0FBQyx3QkFBRCxDQUFELENBQTRCYyxJQUE1QixHQUFtQ0MsSUFBbkMsQ0FBd0MsTUFBeEMsRUFBZ0RDLFNBQVMsQ0FBQ2QsSUFBRCxDQUF6RDtJQUNILENBRkQsTUFFTztNQUNIRixDQUFDLENBQUMsd0JBQUQsQ0FBRCxDQUE0QmlCLElBQTVCO0lBQ0g7RUFDSixDQWxCRDtBQW9CSCxDQXRCQSxDQUFEIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vLy4vcmVzb3VyY2VzL2pzL3RlbXBsYXRlcy5qcz9hMDc0Il0sInNvdXJjZXNDb250ZW50IjpbIid1c2Ugc3RyaWN0JztcclxuXHJcbiQoKCkgPT4ge1xyXG5cclxuICAgICQoJ2JvZHknKS5vbignY2hhbmdlJywgJy5qcy1jaG9vc2UtdGVtcGxhdGUnLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgbGV0IGxpbmsgPSAnL3RlbXBsYXRlcy9kb2N1bWVudC1wcmV2aWV3JyArIHdpbmRvdy5sb2NhdGlvbi5zZWFyY2g7XHJcbiAgICAgICAgY29uc3QgdHBsSWRzID0gW107XHJcblxyXG4gICAgICAgICQoJy5qcy1jaG9vc2UtdGVtcGxhdGU6Y2hlY2tlZCcpLmVhY2goZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBjb25zdCB2YWwgPSAkKHRoaXMpLnZhbCgpO1xyXG4gICAgICAgICAgICB0cGxJZHMucHVzaCh2YWwpO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICBsaW5rICs9ICcmJyArIHRwbElkcy5tYXAoaWQgPT4ge1xyXG4gICAgICAgICAgICByZXR1cm4gJ3RwbF9pZFtdPScgKyBpZDtcclxuICAgICAgICB9KS5qb2luKCcmJyk7XHJcblxyXG4gICAgICAgIGlmICh0cGxJZHMubGVuZ3RoKSB7XHJcbiAgICAgICAgICAgICQoJy5qcy1nZW5lcmF0ZS1kb2NzLWxpbmsnKS5zaG93KCkuYXR0cignaHJlZicsIGVuY29kZVVSSShsaW5rKSk7XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgJCgnLmpzLWdlbmVyYXRlLWRvY3MtbGluaycpLmhpZGUoKTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuXHJcbn0pOyJdLCJuYW1lcyI6WyIkIiwib24iLCJsaW5rIiwid2luZG93IiwibG9jYXRpb24iLCJzZWFyY2giLCJ0cGxJZHMiLCJlYWNoIiwidmFsIiwicHVzaCIsIm1hcCIsImlkIiwiam9pbiIsImxlbmd0aCIsInNob3ciLCJhdHRyIiwiZW5jb2RlVVJJIiwiaGlkZSJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./resources/js/templates.js\n");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval-source-map devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./resources/js/templates.js"]();
/******/ 	
/******/ })()
;