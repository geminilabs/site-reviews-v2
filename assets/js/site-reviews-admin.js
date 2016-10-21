"use strict";var x=jQuery.noConflict(),GLSR={pinned:{}};GLSR.colorControls=function(){"object"==typeof x.wp&&"function"==typeof x.wp.wpColorPicker&&x(document).find('input[type="text"].color-picker-hex').each(function(){var e=x(this),n=e.data("colorpicker")||{};e.wpColorPicker(n)})},GLSR.dismissNotices=function(){x(".notice.is-dismissible").each(function(){var e=x(this);e.fadeTo(100,0,function(){e.slideUp(100,function(){e.remove()})})})},GLSR.insertNotices=function(e){e=e||!1,e&&(x("#glsr-notices").length||(x("#message.notice").remove(),x("form#post").before('<div id="glsr-notices" />')),x("#glsr-notices").html(e),x(document).trigger("wp-updates-notice-added"))},GLSR.normalizeValue=function(e){return["true","on","1"].indexOf(e)>-1||!(["false","off","0"].indexOf(e)>-1)&&e},GLSR.normalizeValues=function(e){return e.map(GLSR.normalizeValue)},GLSR.onClearLog=function(e){e.preventDefault();var n=x(this);if(!n.is(":disabled")){var t={action:site_reviews.action,request:{action:"clear-log"}};n.prop("disabled",!0),x.post(site_reviews.ajaxurl,t,function(e){GLSR.insertNotices(e.notices),x("#log-file").val(e.log),n.prop("disabled",!1)},"json")}},GLSR.onFieldChange=function(){var e=x(this).closest("form").find("[data-depends]");if(e.length)for(var n=this.getAttribute("name"),t=this.getAttribute("type"),i=0;i<e.length;i++)try{var o,s=JSON.parse(e[i].getAttribute("data-depends"));if(s.name!==n)continue;o="checkbox"===t?!!this.checked:x.isArray(s.value)?x.inArray(GLSR.normalizeValue(this.value),GLSR.normalizeValues(s.value))!==-1:GLSR.normalizeValue(s.value)===GLSR.normalizeValue(this.value),GLSR.toggleHiddenField(e[i],o)}catch(a){continue}},GLSR.pointers=function(e){x(e.target).pointer({content:e.options.content,position:e.options.position,close:function(){x.post(ajaxurl,{pointer:e.id,action:"dismiss-wp-pointer"})}}).pointer("open").pointer("sendToTop"),x(document).on("wp-window-resized",function(){x(e.target).pointer("reposition")})},GLSR.toggleHiddenField=function(e,n){var t=x(e).closest(".glsr-field");t.length&&(n?t.removeClass("hidden"):t.addClass("hidden"))},GLSR.pinned.events=function(){var e=x("#pinned-status-select");x("a.cancel-pinned-status").on("click",function(n){n.preventDefault(),e.slideUp("fast").siblings("a.edit-pinned-status").show().focus(),e.find("select").val("0"===x("#hidden-pinned-status").val()?1:0)}),x("a.edit-pinned-status").on("click",function(n){n.preventDefault(),e.is(":hidden")&&(e.slideDown("fast",function(){e.find("select").focus()}),x(this).hide())}),x("a.save-pinned-status").on("click",function(n){n.preventDefault(),e.slideUp("fast").siblings("a.edit-pinned-status").show().focus(),GLSR.pinned.save(x(this))}),x("table").on("click","td.sticky i",GLSR.pinned.onToggle)},GLSR.pinned.onToggle=function(){var e=x(this),n={action:site_reviews.action,request:{action:"toggle-pinned",id:e[0].getAttribute("data-id")}};x.post(site_reviews.ajaxurl,n,function(n){n.pinned?e.addClass("pinned"):e.removeClass("pinned")})},GLSR.pinned.save=function(e){var n={action:site_reviews.action,request:{action:"toggle-pinned",id:x("#post_ID").val(),pinned:x("#pinned-status").val()}};x.post(site_reviews.ajaxurl,n,function(n){x("#pinned-status").val(0|!n.pinned),x("#hidden-pinned-status").val(0|n.pinned),x("#pinned-status-text").text(n.pinned?e.data("yes"):e.data("no")),GLSR.insertNotices(n.notices)})},x(function(){x("form").on("change",":input",GLSR.onFieldChange),x("form").on("click","#clear-log",GLSR.onClearLog),GLSR.colorControls(),GLSR.pinned.events(),x.each(site_reviews_pointers.pointers,function(e,n){GLSR.pointers(n)})});