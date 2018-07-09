/*!
 * Star Rating
 * @version: 2.2.0
 * @author: Paul Ryley (http://geminilabs.io)
 * @url: https://github.com/geminilabs/star-rating.js
 * @license: MIT
 */
!function(t,i,e){"use strict";var r=function(e,t){this.selects="[object String]"==={}.toString.call(e)?i.querySelectorAll(e):[e],this.destroy=function(){this.widgets.forEach(function(e){e.destroy_()})},this.rebuild=function(){this.widgets.forEach(function(e){e.rebuild_()})},this.widgets=[];for(var r=0;r<this.selects.length;r++)if("SELECT"===this.selects[r].tagName){var n=new s(this.selects[r],t);void 0!==n.direction&&this.widgets.push(n)}},s=function(e,t){this.el=e,this.options_=this.extend_({},this.defaults,t||{},JSON.parse(e.getAttribute("data-options"))),this.setStarCount_(),this.stars<1||this.stars>this.options_.maxStars||this.init_()};s.prototype={defaults:{classname:"gl-star-rating",clearable:!0,initialText:"Select a Rating",maxStars:10,onClick:null,showText:!0},init_:function(){this.initEvents_(),this.current=this.selected=this.getSelectedValue_(),this.wrapEl_(),this.buildWidgetEl_(),this.setDirection_(),this.setValue_(this.current),this.handleEvents_("add")},buildLabelEl_:function(){this.options_.showText&&(this.textEl=this.insertSpanEl_(this.widgetEl,{class:this.options_.classname+"-text"},!0))},buildWidgetEl_:function(){var e=this.getOptionValues_(),t=this.insertSpanEl_(this.el,{class:this.options_.classname+"-stars"},!0);for(var r in e)if(e.hasOwnProperty(r)){var n=this.createSpanEl_({"data-value":r,"data-text":e[r]});t.innerHTML+=n.outerHTML}this.widgetEl=t,this.buildLabelEl_()},changeTo_:function(e){(e<0||""===e)&&(e=0),e>this.stars&&(e=this.stars),this.widgetEl.classList.remove("s"+10*this.current),this.widgetEl.classList.add("s"+10*e),this.options_.showText&&(this.textEl.textContent=e<1?this.options_.initialText:this.widgetEl.childNodes[e-1].dataset.text),this.current=e},createSpanEl_:function(e){var t=i.createElement("span");for(var r in e=e||{})e.hasOwnProperty(r)&&t.setAttribute(r,e[r]);return t},destroy_:function(){this.handleEvents_("remove");var e=this.el.parentNode;e.parentNode.replaceChild(this.el,e)},eventListener_:function(t,r,e){e.forEach(function(e){t[r+"EventListener"](e,this.events[e])}.bind(this))},extend_:function(){var e=[].slice.call(arguments),r=e[0],n=e.slice(1);return Object.keys(n).forEach(function(e){for(var t in n[e])n[e].hasOwnProperty(t)&&(r[t]=n[e][t])}),r},getIndexFromPosition_:function(e){var t={},r=this.widgetEl.offsetWidth;return t.ltr=Math.max(e-this.offsetLeft,1),t.rtl=r-t.ltr,Math.min(Math.ceil(t[this.direction]/Math.round(r/this.stars)),this.stars)},getOptionValues_:function(){for(var e=this.el,t={},r={},n=0;n<e.length;n++)this.isValueEmpty_(e[n])||(t[e[n].value]=e[n].text);return Object.keys(t).sort().forEach(function(e){r[e]=t[e]}),r},getSelectedValue_:function(){return parseInt(this.el.options[Math.max(this.el.selectedIndex,0)].value)||0},handleEvents_:function(e){var t=this.el.closest("form");this.eventListener_(this.el,e,["change","keydown"]),this.eventListener_(this.widgetEl,e,["mousedown","mouseleave","mousemove","mouseover","touchend","touchmove","touchstart"]),t&&this.eventListener_(t,e,["reset"])},initEvents_:function(){this.events={change:this.onChange_.bind(this),keydown:this.onKeydown_.bind(this),mousedown:this.onMousedown_.bind(this),mouseleave:this.onMouseleave_.bind(this),mousemove:this.onMousemove_.bind(this),mouseover:this.onMouseover_.bind(this),reset:this.onReset_.bind(this),touchend:this.onMousedown_.bind(this),touchmove:this.onMousemove_.bind(this),touchstart:this.onMouseover_.bind(this)}},insertSpanEl_:function(e,t,r){var n=this.createSpanEl_(t);return e.parentNode.insertBefore(n,!0===r?e.nextSibling:e),n},isValueEmpty_:function(e){return null===e.getAttribute("value")||""===e.value},onChange_:function(){this.changeTo_(this.getSelectedValue_())},onKeydown_:function(e){if(-1!==["ArrowLeft","ArrowRight"].indexOf(e.key)){var t="ArrowLeft"===e.key?-1:1;"rtl"===this.direction&&(t*=-1),this.setValue_(Math.min(Math.max(this.getSelectedValue_()+t,0),this.stars))}},onMousedown_:function(e){e.preventDefault();var t=this.getIndexFromPosition_(e.pageX);if(0!==this.current&&parseFloat(this.selected)===t&&this.options_.clearable)return this.onReset_();this.setValue_(t),"function"==typeof this.options_.onClick&&this.options_.onClick.call(this,this.el)},onMouseleave_:function(e){e.preventDefault(),this.changeTo_(this.selected)},onMousemove_:function(e){e.preventDefault(),this.changeTo_(this.getIndexFromPosition_(e.pageX))},onMouseover_:function(e){e.preventDefault();var t=this.widgetEl.getBoundingClientRect();this.offsetLeft=t.left+i.body.scrollLeft},onReset_:function(){var e=this.el.querySelector("[selected]"),t=e?e.value:"";this.el.value=t,this.selected=parseInt(t)||0,this.changeTo_(t)},rebuild_:function(){this.el.parentNode.classList.contains(this.options_.classname)&&this.destroy_(),this.init_()},setDirection_:function(){var e=this.el.parentNode;this.direction=t.getComputedStyle(e,null).getPropertyValue("direction"),e.classList.add(this.options_.classname+"-"+this.direction)},setValue_:function(e){this.el.value=e,this.selected=e,this.changeTo_(e)},setStarCount_:function(){for(var e=this.el,t=this.stars=0;t<e.length;t++)if(!this.isValueEmpty_(e[t])){if(isNaN(parseFloat(e[t].value))||!isFinite(e[t].value))return void(this.stars=0);this.stars++}},wrapEl_:function(){this.insertSpanEl_(this.el,{class:this.options_.classname,"data-star-rating":""}).appendChild(this.el)}},"function"==typeof define&&define.amd?define([],function(){return r}):"object"==typeof module&&module.exports?module.exports=r:t.StarRating=r}(window,document);var GLSR={addClass:function(e,t){e.classList?e.classList.add(t):GLSR.hasClass(e,t)||(e.className+=" "+t)},convertValue:function(e){return GLSR.isNumeric(e)?parseFloat(e):"true"===e||"false"!==e&&(""!==e&&null!==e?e:void 0)},getAjax:function(e,t){var r=window.XMLHttpRequest?new XMLHttpRequest:new ActiveXObject("Microsoft.XMLHTTP");return r.open("GET",e),r.onreadystatechange=function(){3<r.readyState&&200===r.status&&t(r.responseText)},r.setRequestHeader("X-Requested-With","XMLHttpRequest"),r.send(),r},hasClass:function(e,t){return e.classList?e.classList.contains(t):new RegExp("\\b"+t+"\\b").test(e.className)},inArray:function(e,t){for(var r=t.length;r--;)if(t[r]===e)return!0;return!1},isNumeric:function(e){return!(isNaN(parseFloat(e))||!isFinite(e))},isString:function(e){return"[object String]"===Object.prototype.toString.call(e)},on:function(t,e,r){GLSR.isString(e)&&(e=document.querySelectorAll(e)),[].forEach.call(e,function(e){e.addEventListener(t,r)})},off:function(t,e,r){GLSR.isString(e)&&(e=document.querySelectorAll(e)),[].forEach.call(e,function(e){e.removeEventListener(t,r)})},parseFormData:function(e,o){o=!!o||!1;for(var t=/[^\[\]]+/g,r={},a={},l=function(e,t,r,n){var i=r.shift();if(n=n?n+"."+i:i,r.length)t[i]||(t[i]={}),l(e,t[i],r,n);else{var s=o?GLSR.convertValue(e.value):e.value;if(n in a&&"radio"!==e.type&&!t[i].isArray()?t[i]=i in t?[t[i]]:[]:a[n]=!0,GLSR.inArray(e.type,["radio","checkbox"])&&!e.checked)return;t[i]?t[i].push(s):t[i]=s}},n=0;n<e.length;n++){var i=e[n];if(i.name&&!i.disabled&&!GLSR.inArray(i.type,["file","reset","submit","button"])){var s=i.name.match(t);s.length||(s=[i.name]),l(i,r,s)}}return r},postAjax:function(e,t,r){var n="string"!=typeof t?GLSR.serialize(t):t,i=window.XMLHttpRequest?new XMLHttpRequest:new ActiveXObject("Microsoft.XMLHTTP");return i.open("POST",e),i.onreadystatechange=function(){3<i.readyState&&200===i.status&&r(JSON.parse(i.responseText))},i.setRequestHeader("X-Requested-With","XMLHttpRequest"),i.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8"),i.send(n),i},removeClass:function(e,t){e.classList?e.classList.remove(t):e.className=e.className.replace(new RegExp("\\b"+t+"\\b","g"),"")},serialize:function(e,t){var r=[];for(var n in e)if(e.hasOwnProperty(n)){var i=t?t+"["+n+"]":n,s=e[n];r.push("object"==typeof s?GLSR.serialize(s,i):encodeURIComponent(i)+"="+encodeURIComponent(s))}return r.join("&")},toggleClass:function(e,t){GLSR.hasClass(e,t)?GLSR.removeClass(e,t):GLSR.addClass(e,t)},insertAfter:function(e,t,r){var n=GLSR.createEl(t,r);return e.parentNode.insertBefore(n,e.nextSibling),n},appendTo:function(e,t,r){var n=GLSR.createEl(t,r);return e.appendChild(n),n},createEl:function(e,t){var r="string"==typeof e?document.createElement(e):e;for(var n in t=t||{})t.hasOwnProperty(n)&&r.setAttribute(n,t[n]);return r},SCROLL_TIME:468,activeForm:null,recaptcha:{},buildFormData:function(e){return void 0===e&&(e=""),{action:site_reviews.action,request:GLSR.parseFormData(GLSR.activeForm),"g-recaptcha-response":e}},clearFieldError:function(e){var t=e.closest(".glsr-field");if(null!==t){var r=t.querySelector(".glsr-field-errors");GLSR.removeClass(t,"glsr-has-error"),null!==r&&r.parentNode.removeChild(r)}},clearFormErrors:function(){var e=GLSR.activeForm;GLSR.clearFormMessages();for(var t=0;t<e.length;t++)GLSR.clearFieldError(e[t])},clearFormMessages:function(){var e=GLSR.activeForm.querySelector(".glsr-form-messages");e&&(e.innerHTML="")},createExceprts:function(e){for(var t=(e=e||document).querySelectorAll(".glsr-hidden-text"),r=0;r<t.length;r++){var n=GLSR.insertAfter(t[r],"span",{class:"glsr-read-more"});GLSR.appendTo(n,"a",{href:"#","data-text":t[r].getAttribute("data-show-less")}).innerHTML=t[r].getAttribute("data-show-more")}GLSR.on("click",".glsr-read-more a",GLSR.onClickReadMore)},createStarRatings:function(){new StarRating("select.glsr-star-rating",{clearable:!1,showText:!1,onClick:GLSR.clearFieldError})},enableSubmitButton:function(){GLSR.activeForm.querySelector('[type="submit"]').removeAttribute("disabled")},getSelectorOfElement:function(e){if(e&&e.nodeType===e.ELEMENT_NODE)return e.nodeName.toLowerCase()+(e.id?"#"+e.id.trim():"")+(e.className?"."+e.className.trim().replace(/\s+/g,"."):"")},now:function(){return window.performance&&window.performance.now?window.performance.now():Date.now()},onClickPagination:function(n){n.preventDefault();var i=this.closest(".glsr-reviews"),s=GLSR.getSelectorOfElement(i);GLSR.addClass(i,"glsr-hide"),GLSR.getAjax(this.href,function(e){var t=document.implementation.createHTMLDocument("new");t.documentElement.innerHTML=e;var r=s?t.querySelectorAll(s):"";if(1===r.length)return i.innerHTML=r[0].innerHTML,GLSR.scrollToTop(i),GLSR.removeClass(i,"glsr-hide"),GLSR.on("click",".glsr-ajax-navigation a",GLSR.onClickPagination),window.history.pushState(null,"",n.target.href),void GLSR.createExceprts(i);window.location=n.target.href})},onClickReadMore:function(e){e.preventDefault();var t=e.target,r=t.parentNode.previousSibling,n=t.getAttribute("data-text");GLSR.toggleClass(r,"glsr-hidden"),GLSR.toggleClass(r,"glsr-visible"),t.setAttribute("data-text",t.innerText),t.innerText=n}};GLSR.recaptcha.addListeners=function(){var e=GLSR.recaptcha.overlay();"[object HTMLDivElement]"===Object.prototype.toString.call(e)&&(e.addEventListener("click",GLSR.enableSubmitButton,!1),window.addEventListener("keyup",GLSR.recaptcha.onKeyup.bind(e),!1))},GLSR.recaptcha.execute=function(){var e=GLSR.recaptcha.id();return-1!==e?grecaptcha.execute(e):GLSR.submitForm(!1)},GLSR.recaptcha.id=function(){return GLSR.recaptcha.search(function(e,t){if("[object HTMLDivElement]"===Object.prototype.toString.call(e))return e.closest("form")===GLSR.activeForm?t:void 0})},GLSR.recaptcha.onKeyup=function(e){27===e.keyCode&&(GLSR.enableSubmitButton(),GLSR.recaptcha.removeListeners(this))},GLSR.recaptcha.overlay=function(){return GLSR.recaptcha.search(function(e){if("[object Object]"===Object.prototype.toString.call(e)){for(var t in e)if(e.hasOwnProperty(t)&&"[object HTMLDivElement]"===Object.prototype.toString.call(e[t])&&""===e[t].className)return e[t].firstChild;return!1}})},GLSR.recaptcha.removeListeners=function(e){e.removeEventListener("click",GLSR.enableSubmitButton,!1),window.removeEventListener("keyup",GLSR.recaptcha.onKeyup,!1)},GLSR.recaptcha.reset=function(){var e=GLSR.recaptcha.id();-1!==e&&grecaptcha.reset(e)},GLSR.recaptcha.search=function(e){var t=-1;if(window.hasOwnProperty("___grecaptcha_cfg")){var r,n,i=window.___grecaptcha_cfg.clients;for(r in i)for(n in i[r])if(t=e(i[r][n],r))return t}return t},GLSR.setDirection=function(){for(var e=document.querySelectorAll(".glsr-widget, .glsr-shortcode"),t=0;t<e.length;t++){var r=window.getComputedStyle(e[t],null).getPropertyValue("direction");e[t].classList.add("glsr-"+r)}},GLSR.scrollToTop=function(e,t){var r;t=t||16;for(var n=0;n<site_reviews.ajaxpagination.length;n++)(r=document.querySelector(site_reviews.ajaxpagination[n]))&&"fixed"===window.getComputedStyle(r).getPropertyValue("position")&&(t+=r.clientHeight);var i=e.getBoundingClientRect().top-t;0<i||("requestAnimationFrame"in window!=!1?GLSR.scrollToTopStep({endY:i,offset:window.pageYOffset,startTime:GLSR.now(),startY:e.scrollTop}):window.scroll(0,window.pageYOffset+i))},GLSR.scrollToTopStep=function(e){var t=(GLSR.now()-e.startTime)/GLSR.SCROLL_TIME;t=1<t?1:t;var r=.5*(1-Math.cos(Math.PI*t)),n=e.startY+(e.endY-e.startY)*r;window.scroll(0,e.offset+n),n!==e.endY&&window.requestAnimationFrame(GLSR.scrollToTopStep.bind(window,e))},GLSR.showFormErrors=function(e){var t,r;if(e)for(var n in e)if(e.hasOwnProperty(n)){t=GLSR.activeForm.querySelector('[name="'+n+'"]').closest(".glsr-field"),GLSR.addClass(t,"glsr-has-error"),null===(r=t.querySelector(".glsr-field-errors"))&&(r=GLSR.appendTo(t,"span",{class:"glsr-field-errors"}));for(var i=0;i<e[n].errors.length;i++)null!==e[n].errors[i]&&(r.innerHTML+='<span class="glsr-field-error">'+e[n].errors[i]+"</span>")}},GLSR.showFormMessage=function(e){var t=GLSR.activeForm.querySelector('input[name="form_id"]'),r=GLSR.activeForm.querySelector(".glsr-form-messages");null===r&&(r=GLSR.insertAfter(t,"div",{class:"glsr-form-messages"})),e.errors?GLSR.addClass(r,"gslr-has-errors"):GLSR.removeClass(r,"gslr-has-errors"),r.innerHTML="<p>"+e.message+"</p>"},GLSR.submitForm=function(e){GLSR.activeForm.querySelector('[type="submit"]').setAttribute("disabled",""),GLSR.addClass(GLSR.activeForm,"glsr-loading"),GLSR.postAjax(site_reviews.ajaxurl,GLSR.buildFormData(e),function(e){if(!0===e.recaptcha)return GLSR.recaptcha.execute();"reset"===e.recaptcha&&GLSR.recaptcha.reset(),!1===e.errors&&(GLSR.recaptcha.reset(),GLSR.activeForm.reset()),GLSR.showFormErrors(e.errors),GLSR.showFormMessage(e),GLSR.enableSubmitButton(),GLSR.removeClass(GLSR.activeForm,"glsr-loading"),e.form=GLSR.activeForm,document.dispatchEvent(new CustomEvent("site-reviews/after/submission",{detail:e})),GLSR.activeForm=null})},GLSR.on("change","form.glsr-submit-review-form",function(e){GLSR.clearFieldError(e.target)}),GLSR.on("submit","form.glsr-submit-review-form",function(e){GLSR.hasClass(this,"no-ajax")||(e.preventDefault(),GLSR.activeForm=this,GLSR.recaptcha.addListeners(),GLSR.clearFormErrors(),GLSR.submitForm())}),GLSR.on("click",'.glsr-field [type="submit"]',function(){this.closest("form").onsubmit=null,HTMLFormElement.prototype._submit=HTMLFormElement.prototype.submit,HTMLFormElement.prototype.submit=function(){var e=this.querySelector("#g-recaptcha-response");null===e||null===this.querySelector(".glsr-field")?this._submit():GLSR.submitForm(e.value)}}),GLSR.on("click",".glsr-ajax-navigation a",GLSR.onClickPagination),GLSR.init=function(){GLSR.setDirection(),GLSR.createExceprts(),GLSR.createStarRatings()},GLSR.onLoaded=function(){document.removeEventListener("DOMContentLoaded",GLSR.onLoaded),window.removeEventListener("load",GLSR.onLoaded),GLSR.init()},"complete"===document.readyState||"loading"!==document.readyState&&!document.documentElement.doScroll?window.setTimeout(GLSR.init):(document.addEventListener("DOMContentLoaded",GLSR.onLoaded),window.addEventListener("load",GLSR.onLoaded));