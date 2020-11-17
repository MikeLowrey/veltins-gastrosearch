/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/css/app.css":
/*!*******************************!*\
  !*** ./resources/css/app.css ***!
  \*******************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/js/app.js":
/*!*****************************!*\
  !*** ./resources/js/app.js ***!
  \*****************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { if (typeof Symbol === "undefined" || !(Symbol.iterator in Object(arr))) return; var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

// require('./bootstrap');
var searchInput = 'location';
autocomplete = new google.maps.places.Autocomplete(document.getElementById(searchInput), {
  types: ['geocode']
}); // Set initial restrict to the greater list of countries.

autocomplete.setComponentRestrictions({
  country: ["de"]
});
google.maps.event.addListener(autocomplete, 'place_changed', function () {
  var near_place = autocomplete.getPlace();
  window.t = near_place;
  console.log("near_place", near_place);
  document.getElementById('latitude').value = near_place.geometry.location.lat();
  document.getElementById('longitude').value = near_place.geometry.location.lng();
  document.getElementById('place-id').value = near_place.place_id;
  document.getElementById('formatted-address').value = near_place.formatted_address;
});

document.getElementById('location').onchange = function () {
  document.getElementById('latitude').value = '';
  document.getElementById('longitude').value = '';
  document.getElementById('place-id').value = '';
};

document.getElementById('location').oninput = function () {
  if (document.getElementById('location').value.length == 0) {
    document.getElementById("zip").removeAttribute("disabled");
    document.getElementById("type1").removeAttribute("disabled");
  } else {
    document.getElementById("zip").setAttribute("disabled", "disabled");
    document.getElementById("zip").value = "";
    document.getElementById("type1").setAttribute("disabled", "disabled");
    document.getElementById("type1").selectedIndex = "0";
  }
};

document.getElementById("zip").oninput = function () {
  if (document.getElementById("zip").value.length > 0) {
    document.getElementById('location').setAttribute("disabled", "disabled");
    document.getElementById("location").value = "";
    document.getElementById('radius').setAttribute("disabled", "disabled");
    document.getElementById('type2').setAttribute("disabled", "disabled");
    document.getElementById("type2").selectedIndex = "0";
  } else {
    document.getElementById('location').removeAttribute("disabled");
    document.getElementById('radius').removeAttribute("disabled");
    document.getElementById('type2').removeAttribute("disabled", "disabled");
  }
};

document.getElementById('type1').oninput = function () {
  if (document.getElementById("type1").options.selectedIndex !== 0) {
    document.getElementById("location").setAttribute("disabled", "disabled");
    document.getElementById("radius").setAttribute("disabled", "disabled");
    document.getElementById("type2").setAttribute("disabled", "disabled");
  } else {
    document.getElementById("location").removeAttribute("disabled");
    document.getElementById("radius").removeAttribute("disabled");
    document.getElementById("type2").removeAttribute("disabled");
  }
};

document.getElementById('type2').oninput = function () {
  if (document.getElementById("type2").options.selectedIndex !== 0) {
    document.getElementById("zip").setAttribute("disabled", "disabled");
    document.getElementById("type1").setAttribute("disabled", "disabled");
  } else {
    document.getElementById("zip").removeAttribute("disabled");
    document.getElementById("type1").removeAttribute("disabled");
  }
};
/*var ext = '';
document.getElementById("select-ext").onchange = function() {
    ext = document.getElementById("select-ext").options[document.getElementById("select-ext").options.selectedIndex].value;
    console.log("ext", ext)
    console.log("ext", document.getElementById("download-csv").href)
    if (document.getElementById("download-csv").href.length == 0) {
        alert();
    }
    document.getElementById("select-ext").setAttribute("disabled", "disabled");
    document.getElementById("download-csv").href = document.getElementById("download-csv").href + "." + ext
    document.getElementById("download-csv").removeAttribute("disabled");
}
*/

/*
document.getElementById("download-csv").onclick = function() {
    let link = document.getElementById("download-csv").href;
    console.log("link", link);
    location.href = link;
    return false;
}
*/


document.getElementById("searchSubmit").onclick = function () {
  var zip = document.getElementById("zip").value;

  if (zip.length >= 2) {
    var type = document.getElementById("type1").options[document.getElementById("type1").options.selectedIndex].value;
  } else {
    var type = document.getElementById("type2").options[document.getElementById("type2").options.selectedIndex].value;
  }

  if (type == "0") {
    document.querySelector('#custom-error-alert').classList.add("in");
    setTimeout(function () {
      document.querySelector('#custom-error-alert').classList.remove("in");
    }, 3000);
    return;
  }

  document.getElementById("myTable").getElementsByTagName('tbody')[0].innerHTML = "";

  if (zip != '') {
    var _url = 'api/searchbyzip/' + zip + '/' + type;

    callApi(_url);
    return;
  } // clear outpulist and list element


  document.getElementById("myList").innerHTML = '';
  var lat = document.getElementById('latitude').value;
  var lng = document.getElementById('longitude').value;
  var placeid = document.getElementById('place-id').value;

  if (type == "0" || document.getElementById('location').value == "") {
    document.querySelector('#custom-error-alert').classList.add("in");
    setTimeout(function () {
      document.querySelector('#custom-error-alert').classList.remove("in");
    }, 3000);
    return;
  }

  var radius = document.getElementById('radius').value;
  var formatted_address = encodeURI(document.getElementById('formatted-address').value);
  console.log("formatted_address", formatted_address); // type , radius

  var url = "api/call" + "?lat=" + lat + "&lng=" + lng + "&type=" + type + "&radius=" + radius + "&placeid=" + placeid + "&formattedaddress=" + formatted_address;
  callApi(url);
  return false;
};

function callApi(url) {
  document.getElementById("searchSubmit").classList.add("d-none");
  document.getElementsByClassName("lds-ripple")[0].classList.remove("d-none");
  fetch(url).then(function (response) {
    return response.json();
  }).then(function (data) {
    console.log("data response", data);
    window.t = data;
    document.getElementById("searchSubmit").classList.remove("d-none");
    document.getElementsByClassName("lds-ripple")[0].classList.add("d-none");
    BuildTable(data);
    document.getElementById("hits").innerHTML = data.results.length + " Treffer!";

    if (typeof data.referenz !== "undefined" && data.referenz !== '') {
      // document.getElementById("download-sheet-input").style.display = "block"; // inline
      document.getElementById("download-csv").classList.remove("d-none");
      document.getElementById("download-csv").href = "/download/" + data.referenz + "/" + type2.value;
    } else if (data.results.length > 0 && data.referenz == '') {
      // document.getElementById("download-sheet-input").style.display = "block"; // inline
      document.getElementById("download-csv").classList.remove("d-none");
      document.getElementById("download-csv").href = "/download/generate/" + zip.value + "/" + type1.value;
      console.log("zip", zip);
    } else {
      document.getElementById("download-csv").classList.add("d-none"); //.style.display = "none";
    }
  });
}

function BuildTable(obj) {
  if (obj.length == 0) {
    return;
  }

  Object.entries(obj.results).forEach(function (_ref) {
    var _ref2 = _slicedToArray(_ref, 2),
        key = _ref2[0],
        value = _ref2[1];

    var time = new Date(value.updated_at);
    var timeString = time.getDate() + '.' + (time.getUTCMonth() + 1) + '.' + time.getUTCFullYear();
    var table = document.getElementById("myTable").getElementsByTagName('tbody')[0];
    var row = table.insertRow();
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);
    var cell5 = row.insertCell(4);
    var cell6 = row.insertCell(5);
    cell1.innerHTML = value.name.substring(0, 40);
    cell2.innerHTML = "".concat(value.street, " ").concat(value.street_number);
    cell3.innerHTML = "".concat(value.zip);
    cell4.innerHTML = "".concat(value.place);
    cell5.innerHTML = "".concat(value.phone !== null ? value.phone : '-');
    cell6.innerHTML = "<span class=\"popup\" onclick=\"showPopUp(".concat(value.id, ")\"><svg class=\"info-icon\" aria-hidden=\"true\" focusable=\"false\" data-prefix=\"fas\" data-icon=\"info-circle\" class=\"svg-inline--fa fa-info-circle fa-w-16\" role=\"img\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\"><path fill=\"currentColor\" d=\"M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 110c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z\"></path></svg>\n        <span class=\"popuptext\" id=\"popup-").concat(value.id, "\">Gecrawlt</br>").concat(timeString, "</span>\n      </span>");
  });
}

function BuildList(obj) {
  Object.entries(obj.results).forEach(function (_ref3) {
    var _ref4 = _slicedToArray(_ref3, 2),
        key = _ref4[0],
        value = _ref4[1];

    console.log("".concat(key, ": ").concat(value.name));
    var node = document.createElement("LI");
    var textnode = document.createTextNode(value.name + "-" + value.place_id + "-" + value.formatted_address + "-" + value.types); // let textnode = document.createTextNode(value.join());

    node.appendChild(textnode);
    document.getElementById("myList").appendChild(node);
  });
}

/***/ }),

/***/ 0:
/*!***********************************************************!*\
  !*** multi ./resources/js/app.js ./resources/css/app.css ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! /home/martin/Dokumente/dev/www/laravel/customers/kreativkarusell/veltins-places/veltins/resources/js/app.js */"./resources/js/app.js");
module.exports = __webpack_require__(/*! /home/martin/Dokumente/dev/www/laravel/customers/kreativkarusell/veltins-places/veltins/resources/css/app.css */"./resources/css/app.css");


/***/ })

/******/ });