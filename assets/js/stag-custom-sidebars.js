// Generated by CoffeeScript 1.6.3
(function(){var e,t;e=jQuery;t=function(){function t(){this.widget_wrap=e(".widget-liquid-right");this.widget_area=e("#widgets-right");this.widget_add=e("#tmpl-stag-add-widget");this.create_form();this.add_elements();this.events()}t.prototype.create_form=function(){this.widget_wrap.append(this.widget_add.html());this.widget_name=this.widget_wrap.find('input[name="stag-add-widget"]');this.nonce=this.widget_wrap.find('input[name="scs-delete-nonce"]').val()};t.prototype.add_elements=function(){this.widget_area.find(".sidebar-stag-custom").append('<span class="scs-area-delete">&#10006;</span>');this.widget_area.find(".sidebar-stag-custom").each(function(){var t,n;n=e(this).find(".widgets-sortables");t=n.attr("id").replace("sidebar-","");n.append("<div class='sidebar-description'><p class='description'>"+objectL10n.shortcode+": <code>[stag_sidebar id='"+t+"']</code></p></div>")})};t.prototype.events=function(){this.widget_wrap.on("click",".scs-area-delete",e.proxy(this.delete_sidebar,this))};t.prototype.delete_sidebar=function(t){var n,r,i,s,o;s=e(t.currentTarget).parents(".widgets-holder-wrap:eq(0)");i=s.find(".sidebar-name h3");r=i.find(".spinner");o=e.trim(i.text());n=this;e.ajax({type:"POST",url:window.ajaxurl,data:{action:"stag_ajax_delete_custom_sidebar",name:o,_wpnonce:n.nonce},beforeSend:function(){r.addClass("activate")},success:function(t){t==="sidebar-deleted"&&s.slideUp(200,function(){e(".widget-control-remove",s).trigger("click");s.remove();n.widget_area.find(".widgets-holder-wrap .widgets-sortables").each(function(t){e(this).attr({id:"sidebar-"+(t+1)})});wpWidgets.saveOrder()})}})};return t}();e(function(){var e;return e=new t})}).call(this);