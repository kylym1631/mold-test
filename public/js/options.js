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

/***/ "./resources/js/options.js":
/*!*********************************!*\
  !*** ./resources/js/options.js ***!
  \*********************************/
/***/ (() => {

eval("\n\n$(function () {\n  $('body').on('change', '.js-options-input', function () {\n    var $inp = $(this);\n    var data = {\n      key: $inp.attr('name'),\n      value: $inp.val(),\n      _token: $('input[name=_token]').val()\n    };\n    $.ajax({\n      url: '/options',\n      type: 'POST',\n      dataType: 'json',\n      data: data,\n      success: function success(res) {\n        if (res.error) {\n          toastr.error(res.error);\n        } else {\n          toastr.success('Успешно');\n        }\n      }\n    });\n  });\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9yZXNvdXJjZXMvanMvb3B0aW9ucy5qcy5qcyIsIm1hcHBpbmdzIjoiQUFBYTs7QUFFYkEsQ0FBQyxDQUFDLFlBQU07RUFDSkEsQ0FBQyxDQUFDLE1BQUQsQ0FBRCxDQUFVQyxFQUFWLENBQWEsUUFBYixFQUF1QixtQkFBdkIsRUFBNEMsWUFBWTtJQUNwRCxJQUFNQyxJQUFJLEdBQUdGLENBQUMsQ0FBQyxJQUFELENBQWQ7SUFFQSxJQUFNRyxJQUFJLEdBQUc7TUFDVEMsR0FBRyxFQUFFRixJQUFJLENBQUNHLElBQUwsQ0FBVSxNQUFWLENBREk7TUFFVEMsS0FBSyxFQUFFSixJQUFJLENBQUNLLEdBQUwsRUFGRTtNQUdUQyxNQUFNLEVBQUVSLENBQUMsQ0FBQyxvQkFBRCxDQUFELENBQXdCTyxHQUF4QjtJQUhDLENBQWI7SUFNQVAsQ0FBQyxDQUFDUyxJQUFGLENBQU87TUFDSEMsR0FBRyxFQUFFLFVBREY7TUFFSEMsSUFBSSxFQUFFLE1BRkg7TUFHSEMsUUFBUSxFQUFFLE1BSFA7TUFJSFQsSUFBSSxFQUFKQSxJQUpHO01BS0hVLE9BQU8sRUFBRSxpQkFBVUMsR0FBVixFQUFlO1FBQ3BCLElBQUlBLEdBQUcsQ0FBQ0MsS0FBUixFQUFlO1VBQ1hDLE1BQU0sQ0FBQ0QsS0FBUCxDQUFhRCxHQUFHLENBQUNDLEtBQWpCO1FBQ0gsQ0FGRCxNQUVPO1VBQ0hDLE1BQU0sQ0FBQ0gsT0FBUCxDQUFlLFNBQWY7UUFDSDtNQUNKO0lBWEUsQ0FBUDtFQWFILENBdEJEO0FBdUJILENBeEJBLENBQUQiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvb3B0aW9ucy5qcz9mNTFmIl0sInNvdXJjZXNDb250ZW50IjpbIid1c2Ugc3RyaWN0JztcclxuXHJcbiQoKCkgPT4ge1xyXG4gICAgJCgnYm9keScpLm9uKCdjaGFuZ2UnLCAnLmpzLW9wdGlvbnMtaW5wdXQnLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgY29uc3QgJGlucCA9ICQodGhpcyk7XHJcblxyXG4gICAgICAgIGNvbnN0IGRhdGEgPSB7XHJcbiAgICAgICAgICAgIGtleTogJGlucC5hdHRyKCduYW1lJyksXHJcbiAgICAgICAgICAgIHZhbHVlOiAkaW5wLnZhbCgpLFxyXG4gICAgICAgICAgICBfdG9rZW46ICQoJ2lucHV0W25hbWU9X3Rva2VuXScpLnZhbCgpLFxyXG4gICAgICAgIH07XHJcblxyXG4gICAgICAgICQuYWpheCh7XHJcbiAgICAgICAgICAgIHVybDogJy9vcHRpb25zJyxcclxuICAgICAgICAgICAgdHlwZTogJ1BPU1QnLFxyXG4gICAgICAgICAgICBkYXRhVHlwZTogJ2pzb24nLFxyXG4gICAgICAgICAgICBkYXRhLFxyXG4gICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbiAocmVzKSB7XHJcbiAgICAgICAgICAgICAgICBpZiAocmVzLmVycm9yKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgdG9hc3RyLmVycm9yKHJlcy5lcnJvcik7XHJcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgICAgIHRvYXN0ci5zdWNjZXNzKCfQo9GB0L/QtdGI0L3QvicpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcbiAgICB9KTtcclxufSk7Il0sIm5hbWVzIjpbIiQiLCJvbiIsIiRpbnAiLCJkYXRhIiwia2V5IiwiYXR0ciIsInZhbHVlIiwidmFsIiwiX3Rva2VuIiwiYWpheCIsInVybCIsInR5cGUiLCJkYXRhVHlwZSIsInN1Y2Nlc3MiLCJyZXMiLCJlcnJvciIsInRvYXN0ciJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./resources/js/options.js\n");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval-source-map devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./resources/js/options.js"]();
/******/ 	
/******/ })()
;