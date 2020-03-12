(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["/js/main"],{

/***/ "./node_modules/handlebars/lib/index.js":
/*!**********************************************!*\
  !*** ./node_modules/handlebars/lib/index.js ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// USAGE:
// var handlebars = require('handlebars');
/* eslint-disable no-var */

// var local = handlebars.create();

var handlebars = __webpack_require__(/*! ../dist/cjs/handlebars */ "./node_modules/handlebars/dist/cjs/handlebars.js")['default'];

var printer = __webpack_require__(/*! ../dist/cjs/handlebars/compiler/printer */ "./node_modules/handlebars/dist/cjs/handlebars/compiler/printer.js");
handlebars.PrintVisitor = printer.PrintVisitor;
handlebars.print = printer.print;

module.exports = handlebars;

// Publish a Node.js require() handler for .handlebars and .hbs files
function extension(module, filename) {
  var fs = __webpack_require__(/*! fs */ "./node_modules/node-libs-browser/mock/empty.js");
  var templateString = fs.readFileSync(filename, 'utf8');
  module.exports = handlebars.compile(templateString);
}
/* istanbul ignore else */
if ( true && (void 0)) {
  (void 0)['.handlebars'] = extension;
  (void 0)['.hbs'] = extension;
}


/***/ }),

/***/ "./node_modules/node-libs-browser/mock/empty.js":
/*!******************************************************!*\
  !*** ./node_modules/node-libs-browser/mock/empty.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {



/***/ }),

/***/ "./node_modules/process/browser.js":
/*!*****************************************!*\
  !*** ./node_modules/process/browser.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// shim for using process in browser
var process = module.exports = {};

// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.

var cachedSetTimeout;
var cachedClearTimeout;

function defaultSetTimout() {
    throw new Error('setTimeout has not been defined');
}
function defaultClearTimeout () {
    throw new Error('clearTimeout has not been defined');
}
(function () {
    try {
        if (typeof setTimeout === 'function') {
            cachedSetTimeout = setTimeout;
        } else {
            cachedSetTimeout = defaultSetTimout;
        }
    } catch (e) {
        cachedSetTimeout = defaultSetTimout;
    }
    try {
        if (typeof clearTimeout === 'function') {
            cachedClearTimeout = clearTimeout;
        } else {
            cachedClearTimeout = defaultClearTimeout;
        }
    } catch (e) {
        cachedClearTimeout = defaultClearTimeout;
    }
} ())
function runTimeout(fun) {
    if (cachedSetTimeout === setTimeout) {
        //normal enviroments in sane situations
        return setTimeout(fun, 0);
    }
    // if setTimeout wasn't available but was latter defined
    if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) {
        cachedSetTimeout = setTimeout;
        return setTimeout(fun, 0);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedSetTimeout(fun, 0);
    } catch(e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
            return cachedSetTimeout.call(null, fun, 0);
        } catch(e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
            return cachedSetTimeout.call(this, fun, 0);
        }
    }


}
function runClearTimeout(marker) {
    if (cachedClearTimeout === clearTimeout) {
        //normal enviroments in sane situations
        return clearTimeout(marker);
    }
    // if clearTimeout wasn't available but was latter defined
    if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) {
        cachedClearTimeout = clearTimeout;
        return clearTimeout(marker);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedClearTimeout(marker);
    } catch (e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
            return cachedClearTimeout.call(null, marker);
        } catch (e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
            // Some versions of I.E. have different rules for clearTimeout vs setTimeout
            return cachedClearTimeout.call(this, marker);
        }
    }



}
var queue = [];
var draining = false;
var currentQueue;
var queueIndex = -1;

function cleanUpNextTick() {
    if (!draining || !currentQueue) {
        return;
    }
    draining = false;
    if (currentQueue.length) {
        queue = currentQueue.concat(queue);
    } else {
        queueIndex = -1;
    }
    if (queue.length) {
        drainQueue();
    }
}

function drainQueue() {
    if (draining) {
        return;
    }
    var timeout = runTimeout(cleanUpNextTick);
    draining = true;

    var len = queue.length;
    while(len) {
        currentQueue = queue;
        queue = [];
        while (++queueIndex < len) {
            if (currentQueue) {
                currentQueue[queueIndex].run();
            }
        }
        queueIndex = -1;
        len = queue.length;
    }
    currentQueue = null;
    draining = false;
    runClearTimeout(timeout);
}

process.nextTick = function (fun) {
    var args = new Array(arguments.length - 1);
    if (arguments.length > 1) {
        for (var i = 1; i < arguments.length; i++) {
            args[i - 1] = arguments[i];
        }
    }
    queue.push(new Item(fun, args));
    if (queue.length === 1 && !draining) {
        runTimeout(drainQueue);
    }
};

// v8 likes predictible objects
function Item(fun, array) {
    this.fun = fun;
    this.array = array;
}
Item.prototype.run = function () {
    this.fun.apply(null, this.array);
};
process.title = 'browser';
process.browser = true;
process.env = {};
process.argv = [];
process.version = ''; // empty string to avoid regexp issues
process.versions = {};

function noop() {}

process.on = noop;
process.addListener = noop;
process.once = noop;
process.off = noop;
process.removeListener = noop;
process.removeAllListeners = noop;
process.emit = noop;
process.prependListener = noop;
process.prependOnceListener = noop;

process.listeners = function (name) { return [] }

process.binding = function (name) {
    throw new Error('process.binding is not supported');
};

process.cwd = function () { return '/' };
process.chdir = function (dir) {
    throw new Error('process.chdir is not supported');
};
process.umask = function() { return 0; };


/***/ }),

/***/ "./node_modules/setimmediate/setImmediate.js":
/*!***************************************************!*\
  !*** ./node_modules/setimmediate/setImmediate.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global, process) {(function (global, undefined) {
    "use strict";

    if (global.setImmediate) {
        return;
    }

    var nextHandle = 1; // Spec says greater than zero
    var tasksByHandle = {};
    var currentlyRunningATask = false;
    var doc = global.document;
    var registerImmediate;

    function setImmediate(callback) {
      // Callback can either be a function or a string
      if (typeof callback !== "function") {
        callback = new Function("" + callback);
      }
      // Copy function arguments
      var args = new Array(arguments.length - 1);
      for (var i = 0; i < args.length; i++) {
          args[i] = arguments[i + 1];
      }
      // Store and register the task
      var task = { callback: callback, args: args };
      tasksByHandle[nextHandle] = task;
      registerImmediate(nextHandle);
      return nextHandle++;
    }

    function clearImmediate(handle) {
        delete tasksByHandle[handle];
    }

    function run(task) {
        var callback = task.callback;
        var args = task.args;
        switch (args.length) {
        case 0:
            callback();
            break;
        case 1:
            callback(args[0]);
            break;
        case 2:
            callback(args[0], args[1]);
            break;
        case 3:
            callback(args[0], args[1], args[2]);
            break;
        default:
            callback.apply(undefined, args);
            break;
        }
    }

    function runIfPresent(handle) {
        // From the spec: "Wait until any invocations of this algorithm started before this one have completed."
        // So if we're currently running a task, we'll need to delay this invocation.
        if (currentlyRunningATask) {
            // Delay by doing a setTimeout. setImmediate was tried instead, but in Firefox 7 it generated a
            // "too much recursion" error.
            setTimeout(runIfPresent, 0, handle);
        } else {
            var task = tasksByHandle[handle];
            if (task) {
                currentlyRunningATask = true;
                try {
                    run(task);
                } finally {
                    clearImmediate(handle);
                    currentlyRunningATask = false;
                }
            }
        }
    }

    function installNextTickImplementation() {
        registerImmediate = function(handle) {
            process.nextTick(function () { runIfPresent(handle); });
        };
    }

    function canUsePostMessage() {
        // The test against `importScripts` prevents this implementation from being installed inside a web worker,
        // where `global.postMessage` means something completely different and can't be used for this purpose.
        if (global.postMessage && !global.importScripts) {
            var postMessageIsAsynchronous = true;
            var oldOnMessage = global.onmessage;
            global.onmessage = function() {
                postMessageIsAsynchronous = false;
            };
            global.postMessage("", "*");
            global.onmessage = oldOnMessage;
            return postMessageIsAsynchronous;
        }
    }

    function installPostMessageImplementation() {
        // Installs an event handler on `global` for the `message` event: see
        // * https://developer.mozilla.org/en/DOM/window.postMessage
        // * http://www.whatwg.org/specs/web-apps/current-work/multipage/comms.html#crossDocumentMessages

        var messagePrefix = "setImmediate$" + Math.random() + "$";
        var onGlobalMessage = function(event) {
            if (event.source === global &&
                typeof event.data === "string" &&
                event.data.indexOf(messagePrefix) === 0) {
                runIfPresent(+event.data.slice(messagePrefix.length));
            }
        };

        if (global.addEventListener) {
            global.addEventListener("message", onGlobalMessage, false);
        } else {
            global.attachEvent("onmessage", onGlobalMessage);
        }

        registerImmediate = function(handle) {
            global.postMessage(messagePrefix + handle, "*");
        };
    }

    function installMessageChannelImplementation() {
        var channel = new MessageChannel();
        channel.port1.onmessage = function(event) {
            var handle = event.data;
            runIfPresent(handle);
        };

        registerImmediate = function(handle) {
            channel.port2.postMessage(handle);
        };
    }

    function installReadyStateChangeImplementation() {
        var html = doc.documentElement;
        registerImmediate = function(handle) {
            // Create a <script> element; its readystatechange event will be fired asynchronously once it is inserted
            // into the document. Do so, thus queuing up the task. Remember to clean up once it's been called.
            var script = doc.createElement("script");
            script.onreadystatechange = function () {
                runIfPresent(handle);
                script.onreadystatechange = null;
                html.removeChild(script);
                script = null;
            };
            html.appendChild(script);
        };
    }

    function installSetTimeoutImplementation() {
        registerImmediate = function(handle) {
            setTimeout(runIfPresent, 0, handle);
        };
    }

    // If supported, we should attach to the prototype of global, since that is where setTimeout et al. live.
    var attachTo = Object.getPrototypeOf && Object.getPrototypeOf(global);
    attachTo = attachTo && attachTo.setTimeout ? attachTo : global;

    // Don't get fooled by e.g. browserify environments.
    if ({}.toString.call(global.process) === "[object process]") {
        // For Node.js before 0.9
        installNextTickImplementation();

    } else if (canUsePostMessage()) {
        // For non-IE10 modern browsers
        installPostMessageImplementation();

    } else if (global.MessageChannel) {
        // For web workers, where supported
        installMessageChannelImplementation();

    } else if (doc && "onreadystatechange" in doc.createElement("script")) {
        // For IE 6–8
        installReadyStateChangeImplementation();

    } else {
        // For older browsers
        installSetTimeoutImplementation();
    }

    attachTo.setImmediate = setImmediate;
    attachTo.clearImmediate = clearImmediate;
}(typeof self === "undefined" ? typeof global === "undefined" ? this : global : self));

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js"), __webpack_require__(/*! ./../process/browser.js */ "./node_modules/process/browser.js")))

/***/ }),

/***/ "./node_modules/timers-browserify/main.js":
/*!************************************************!*\
  !*** ./node_modules/timers-browserify/main.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {var scope = (typeof global !== "undefined" && global) ||
            (typeof self !== "undefined" && self) ||
            window;
var apply = Function.prototype.apply;

// DOM APIs, for completeness

exports.setTimeout = function() {
  return new Timeout(apply.call(setTimeout, scope, arguments), clearTimeout);
};
exports.setInterval = function() {
  return new Timeout(apply.call(setInterval, scope, arguments), clearInterval);
};
exports.clearTimeout =
exports.clearInterval = function(timeout) {
  if (timeout) {
    timeout.close();
  }
};

function Timeout(id, clearFn) {
  this._id = id;
  this._clearFn = clearFn;
}
Timeout.prototype.unref = Timeout.prototype.ref = function() {};
Timeout.prototype.close = function() {
  this._clearFn.call(scope, this._id);
};

// Does not start the time, just sets up the members needed.
exports.enroll = function(item, msecs) {
  clearTimeout(item._idleTimeoutId);
  item._idleTimeout = msecs;
};

exports.unenroll = function(item) {
  clearTimeout(item._idleTimeoutId);
  item._idleTimeout = -1;
};

exports._unrefActive = exports.active = function(item) {
  clearTimeout(item._idleTimeoutId);

  var msecs = item._idleTimeout;
  if (msecs >= 0) {
    item._idleTimeoutId = setTimeout(function onTimeout() {
      if (item._onTimeout)
        item._onTimeout();
    }, msecs);
  }
};

// setimmediate attaches itself to the global object
__webpack_require__(/*! setimmediate */ "./node_modules/setimmediate/setImmediate.js");
// On some exotic environments, it's not clear which object `setimmediate` was
// able to install onto.  Search each possibility in the same order as the
// `setimmediate` library.
exports.setImmediate = (typeof self !== "undefined" && self.setImmediate) ||
                       (typeof global !== "undefined" && global.setImmediate) ||
                       (this && this.setImmediate);
exports.clearImmediate = (typeof self !== "undefined" && self.clearImmediate) ||
                         (typeof global !== "undefined" && global.clearImmediate) ||
                         (this && this.clearImmediate);

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./node_modules/webpack/buildin/global.js":
/*!***********************************!*\
  !*** (webpack)/buildin/global.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || new Function("return this")();
} catch (e) {
	// This works if the window reference is available
	if (typeof window === "object") g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),

/***/ "./resources/assets/js/ajax.js":
/*!*************************************!*\
  !*** ./resources/assets/js/ajax.js ***!
  \*************************************/
/*! exports provided: getReq, saveRating */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getReq", function() { return getReq; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "saveRating", function() { return saveRating; });
var getReq = function getReq(url) {
  return fetch(url, {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    }
  }).then(function (response) {
    return response.json();
  });
};
var saveRating = function saveRating(url, data) {
  console.log(url, data);
  return fetch(url, {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
  }).then(function (response) {
    return response.json().then(function (data) {
      return {
        status: response.status,
        data: data
      };
    });
  });
};

/***/ }),

/***/ "./resources/assets/js/main.js":
/*!*************************************!*\
  !*** ./resources/assets/js/main.js ***!
  \*************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(__webpack_provided_window_dot_jQuery, $, Handlebars) {/* harmony import */ var _ajax_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ajax.js */ "./resources/assets/js/ajax.js");

window.$ = __webpack_provided_window_dot_jQuery = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");
window.croppie = __webpack_require__(/*! croppie */ "./node_modules/croppie/croppie.js");
window.Handlebars = __webpack_require__(/*! handlebars */ "./node_modules/handlebars/lib/index.js");

__webpack_require__(/*! jquery-validation */ "./node_modules/jquery-validation/dist/jquery.validate.js");

$(document).ready(function () {
  console.log("DOCUMENT READY");
  $('#doctorForm').bind('keypress keydown keyup', function (e) {
    if (e.keyCode === 13) {
      e.preventDefault();
    }
  });
  /* TOGGLE MODAL */

  $(".openModal").on("click", function () {
    openModal($(this).attr('data-modal'));
  });
  $(".closeModal").on("click", function () {
    closeModal();
  });
  /* MANAGE AVATAR */

  var $croppieEl = initCroppie(210, 210);
  $('#avatarInput').on("change", function () {
    updateCroppie($croppieEl, this);
  });
  $('#saveAvatar').on("click", function () {
    saveCroppie($croppieEl);
  });
  $(".selectAvatar").on("click", function () {
    selectAvatar($(this).attr("data-avatar"));
  });
  /* MANAGE WEEKDAYS SELECT */

  $(".weekdaySelect").on("change", function () {
    if (parseInt($(this).val(), 10) > 1) {
      $(this).closest("div").find("input, button").attr("disabled", true);
    } else {
      $(this).closest("div").find("input, button").attr("disabled", false);
    }
  });
  /* MANAGE NEW WEEKDAY ROW */

  $(".addWeekdayRow").on("click", function () {
    var $insertAfter = $(this).closest("div");
    $(this).prop('disabled', true).addClass("invisible");
    addNewWeekdayRow($insertAfter, $insertAfter.data("weekday"));
  });
  $(document).on("click", ".removeWeekdayRow", function () {
    var $row = $(this).closest("div");
    $row.prev().find(".addWeekdayRow").prop('disabled', false).removeClass("invisible");
    $row.remove();
  });
  /* SEARCH PROERTIES OR SERVICES AND FILL IN LIST */

  searchCustomOptions();
  /* MANAGE PHOTO UPLOAD */

  $(".photoInput").on("change", function (e) {
    var file = e.target.files[0];
    var photoInput = $(this);
    var reader = new FileReader();

    reader.onloadend = function () {
      photoInput.css({
        "background-image": "url(" + reader.result + ")"
      }).removeClass("empty");
    };

    if (file) {
      reader.readAsDataURL(file);
    }
  });
  $(".photoInput .closeButton").on("click", function () {
    var $photoInput = $(this).closest(".photoInput");
    $photoInput.find("input").val(null);
    $photoInput.addClass("empty");
  });
  $("#gdpr_agreed").on("change", function () {
    if ($(this).prop("checked")) {
      $("#submit_form").prop("disabled", false);
    } else {
      $("#submit_form").prop("disabled", true);
    }
  });
  /* VALIDATE FORM */

  validateForm();
  /* ADD MAP BOX TO DOCTOR'S PROFILE PAGE */

  if ($("body").hasClass("doctor")) {
    console.log(window.location);
    var map = createMapbox($("#map").data("lat"), $("#map").data("lng"));
    var userMarker = new google.maps.Marker({
      position: new google.maps.LatLng($("#map").data("lat"), $("#map").data("lng")),
      map: map,
      icon: window.location.origin + '/images/marker.png',
      zIndex: google.maps.Marker.MAX_ZINDEX + 1,
      animation: google.maps.Animation.BOUNCE
    });
  }
  /* ADD RATING TO DOCTOR */


  manageStarsRating();
  $("#rateDoctorForm").on("submit", function (e) {
    e.preventDefault();
    var rating = rateDoctor();
    var comment = $("#comment").val();
    var userId = $("#userId").val();
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    Object(_ajax_js__WEBPACK_IMPORTED_MODULE_0__["saveRating"])($(this).attr("action"), {
      comment: comment,
      rating: rating,
      userId: userId
    }).then(function (response) {
      if (response.status === 201) {
        $('.successMsg').removeClass('hidden');
        $('#rateDoctorForm').addClass('hidden');
      }
    });
  });
});
$(window).on("resize", function () {
  console.log("RESIZE");
});
$(window).on("scroll", function () {
  makeStickyHeader();
});

var makeStickyHeader = function makeStickyHeader() {
  var scroll = $(window).scrollTop();

  if (scroll > 0) {
    $("header").addClass("sticky");
  } else {
    $("header").removeClass("sticky");
  }
};

var initCroppie = function initCroppie(width, height) {
  var $croppieEl = $('.croppie').croppie({
    viewport: {
      width: width,
      height: height,
      type: 'circle'
    },
    boundary: {
      width: '100%',
      height: height + 40
    }
  });
  return $croppieEl;
};

var updateCroppie = function updateCroppie($croppieEl, fileInput) {
  if (fileInput.files && fileInput.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      $croppieEl.croppie('bind', {
        url: e.target.result
      });
    };

    reader.readAsDataURL(fileInput.files[0]);
  }
};

var saveCroppie = function saveCroppie($croppieEl) {
  if (typeof $(".cr-image").attr("src") !== "undefined") {
    $croppieEl.croppie('result', {
      type: 'canvas',
      circle: false,
      size: 'original'
    }).then(function (img) {
      updateAvatar(img);
      closeModal();
      $('#doc_profile_pic').val(img);
      $(".avatar").removeClass("empty");
    });
  }
};

var selectAvatar = function selectAvatar(url) {
  updateAvatar(url);
  closeModal();
  $('#doc_profile_pic2').val(url);
  $(".avatar").removeClass("empty");
};

var updateAvatar = function updateAvatar(img) {
  $('.avatar').css({
    'background-image': 'url(' + img + ')'
  });
};

var closeModal = function closeModal() {
  $(".modal").removeClass("open");
};

var openModal = function openModal(modal) {
  $(".modal").removeClass("open");
  $("#" + modal).addClass("open");
};

var addNewWeekdayRow = function addNewWeekdayRow($insertAfter, weekday) {
  var source = $("#weekdayRowTemplate").html();
  var template = Handlebars.compile(source);
  var rowHtml = template({
    weekday: weekday
  });
  $(rowHtml).insertAfter($insertAfter);
};

var searchCustomOptions = function searchCustomOptions() {
  $(".searchOptions").on("input", function () {
    var type = $(this).data("type");
    var serachUrl = type === "properties" ? "/get-properties?name=" + $(this).val() + "&category_id=" + $(this).data("category") : "/get-services?name=" + $(this).val();
    var $optionInput = $(this);
    Object(_ajax_js__WEBPACK_IMPORTED_MODULE_0__["getReq"])(serachUrl).then(function (response) {
      var source = $("#optionsTemplate").html();
      var template = Handlebars.compile(source);
      var optionsHtml = template({
        options: response
      });
      $optionInput.next().html(optionsHtml).slideDown();
    });
  });
  $(".searchOptions").keyup(function (e) {
    var $highlighted = $('.customOptions .highlighted');
    var $li = $('.customOptions ul li');
    var type = $(this).data("type");

    if (e.keyCode === 40) {
      $highlighted.removeClass('highlighted').next().addClass('highlighted');

      if ($highlighted.next().length === 0) {
        $li.eq(0).addClass('highlighted');
      }
    } else if (e.keyCode === 38) {
      $highlighted.removeClass('highlighted').prev().addClass('highlighted');

      if ($highlighted.prev().length === 0) {
        $li.eq(-1).addClass('highlighted');
      }
    } else if (e.keyCode === 13) {
      if ($highlighted.length === 0) {
        addCustomOption($(this), type);
      } else {
        selectCustomOption($highlighted, type);
      }
    }
  });
  $(document).on("click", ".customOptions li", function () {
    var type = $(this).closest(".formRow").find(".searchOptions").data("type");
    selectCustomOption($(this), type);
  });
};

var addCustomOption = function addCustomOption($option, type) {
  var source = type === "properties" ? $("#propertyInputTemplate").html() : $("#serviceInputTemplate").html();
  var template = Handlebars.compile(source);
  var rowHtml = template({
    id: $option.val(),
    categoryId: $option.data("category"),
    name: $option.val()
  });
  $(rowHtml).insertBefore($option.closest(".formRow"));
  hideCustomOptions($option.next());
};

var selectCustomOption = function selectCustomOption($option, type) {
  var source = type === "properties" ? $("#propertyInputTemplate").html() : $("#serviceInputTemplate").html();
  var template = Handlebars.compile(source);
  var rowHtml = template({
    id: $option.data("option-id"),
    categoryId: $option.data("category-id"),
    name: $option.data("option-name")
  });
  $(rowHtml).insertBefore($option.closest(".formRow"));
  hideCustomOptions($option.closest(".customOptions"));
};

var hideCustomOptions = function hideCustomOptions($el) {
  $el.closest(".formRow").find("input").val("");
  $el.hide();
};

var validateForm = function validateForm() {
  $("form").validate({
    rules: {
      name: {
        required: true
      },
      description: {
        required: true
      },
      email: {
        email: true
      },
      password: {
        required: true,
        minlength: 6
      },
      password_confirmation: {
        required: true,
        minlength: 6,
        equalTo: "#password"
      },
      street: {
        required: true
      },
      post_code: {
        minlength: 5
      },
      city: {
        required: true
      },
      phone: {
        required: true
      }
    },
    messages: {
      name: "Zadejte jméno",
      description: "Zadejte popis",
      email: "Zadejte email ve správném formátu",
      password: "Zadejte heslo",
      password_confirmation: "Hesla se neshoduji",
      street: "Zadejte ulici",
      post_code: "Zadejte PSČ",
      city: "Zadejte město",
      phone: "Zadejte telefonní číslo"
    },
    highlight: function highlight(element) {
      $(element).addClass("error");
    },
    unhighlight: function unhighlight(element) {
      $(element).removeClass("error");
    }
  });
};

var createMapbox = function createMapbox(lat, lng) {
  var styles = mapboxStyle();
  var mapsLatLgn = new google.maps.LatLng(lat, lng);
  var mapOptions = {
    center: mapsLatLgn,
    zoom: 15,
    disableDefaultUI: true,
    scrollwheel: false,
    zoomControl: true,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    styles: styles
  };
  return new google.maps.Map(document.getElementById('map'), mapOptions);
};

var mapboxStyle = function mapboxStyle() {
  return [{
    "featureType": "administrative",
    "elementType": "labels.text.fill",
    "stylers": [{
      "color": "#444444"
    }]
  }, {
    "featureType": "landscape",
    "elementType": "all",
    "stylers": [{
      "color": "#f2f2f2"
    }]
  }, {
    "featureType": "poi",
    "elementType": "all",
    "stylers": [{
      "visibility": "off"
    }]
  }, {
    "featureType": "poi",
    "elementType": "geometry.fill",
    "stylers": [{
      "visibility": "on"
    }, {
      "color": "#b7b7b7"
    }]
  }, {
    "featureType": "poi",
    "elementType": "labels.text.fill",
    "stylers": [{
      "visibility": "on"
    }, {
      "color": "#414141"
    }]
  }, {
    "featureType": "poi.park",
    "elementType": "geometry.fill",
    "stylers": [{
      "visibility": "on"
    }, {
      "color": "#dfdfdf"
    }]
  }, {
    "featureType": "poi.park",
    "elementType": "labels.text.fill",
    "stylers": [{
      "visibility": "on"
    }, {
      "color": "#565656"
    }]
  }, {
    "featureType": "road",
    "elementType": "all",
    "stylers": [{
      "saturation": -100
    }, {
      "lightness": 45
    }]
  }, {
    "featureType": "road.highway",
    "elementType": "all",
    "stylers": [{
      "visibility": "simplified"
    }]
  }, {
    "featureType": "road.arterial",
    "elementType": "labels.icon",
    "stylers": [{
      "visibility": "off"
    }]
  }, {
    "featureType": "transit",
    "elementType": "all",
    "stylers": [{
      "visibility": "off"
    }]
  }, {
    "featureType": "water",
    "elementType": "all",
    "stylers": [{
      "color": "#46bcec"
    }, {
      "visibility": "on"
    }]
  }, {
    "featureType": "water",
    "elementType": "labels.text.fill",
    "stylers": [{
      "visibility": "on"
    }, {
      "color": "#ffffff"
    }]
  }, {
    "featureType": "water",
    "elementType": "labels.text.stroke",
    "stylers": [{
      "visibility": "off"
    }]
  }];
};

var manageStarsRating = function manageStarsRating() {
  $('.ratingForm li').on('mouseover', function () {
    var onStar = parseInt($(this).data('value'), 10); // The star currently mouse on
    // Now highlight all the stars that's not after the current hovered star

    $(this).parent().children('li.star').each(function (e) {
      if (e < onStar) {
        $(this).addClass('hover');
      } else {
        $(this).removeClass('hover');
      }
    });
  }).on('mouseout', function () {
    $(this).parent().children('li.star').each(function (e) {
      $(this).removeClass('hover');
    });
  });
  $('.ratingForm li').on('click', function () {
    var onStar = parseInt($(this).data('value'), 10); // The star currently selected

    var stars = $(this).parent().children('li.star');
    $(this).closest(".ratingForm").attr("data-score", onStar);

    for (var i = 0; i < stars.length; i++) {
      $(stars[i]).removeClass('selected');
    }

    for (var _i = 0; _i < onStar; _i++) {
      $(stars[_i]).addClass('selected');
    }
  });
};

var rateDoctor = function rateDoctor() {
  var $forms = $(".ratingForm");
  var rating = [];
  $forms.each(function () {
    rating.push({
      'id': $(this).data('item-id'),
      'score': $(this).data('score')
    });
  });
  return rating;
};
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js"), __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js"), __webpack_require__(/*! handlebars */ "./node_modules/handlebars/lib/index.js")))

/***/ }),

/***/ "./resources/assets/sass/main.scss":
/*!*****************************************!*\
  !*** ./resources/assets/sass/main.scss ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 0:
/*!*****************************************************************************!*\
  !*** multi ./resources/assets/js/main.js ./resources/assets/sass/main.scss ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! /Users/lukasvacovsky/Htdocs/drmouse-api/resources/assets/js/main.js */"./resources/assets/js/main.js");
module.exports = __webpack_require__(/*! /Users/lukasvacovsky/Htdocs/drmouse-api/resources/assets/sass/main.scss */"./resources/assets/sass/main.scss");


/***/ })

},[[0,"/js/manifest","/js/vendor"]]]);