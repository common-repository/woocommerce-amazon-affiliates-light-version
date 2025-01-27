// Initialization and events code for the app
WooZoneLite_debugbar = (function ($) {
	"use strict";

	var DEBUG				= true;

	var mod_alias 			= 'woozonelite debugbar/ ',
		ajaxurl				= woozonelite_debugbar_vars.ajax_url,

		maincontainer		= null,
		mainbox 			= null,

		bar_settings 		= {},

		wpadminbar_height 	= 0,
		box_minheight 		= 30,
		box_maxheight 		= $(window).height() - 50,

		box_row_key_pinned 	= 'woozonelite-debugbar-row-pinned',
		box_row_key_height 	= 'woozonelite-debugbar-row-height';


	// init function, autoload
	(function init() {
		
		// load the triggers
		$(document).ready(function(){
			console.log( 'WooZoneLite debugbar script is loaded!' );

			wpadminbar_height = $('#wpadminbar').outerHeight();

			maincontainer = $("body");
			mainbox = $('#woozonelite-debugbar');

			bar_settings = get_bar_settings();

			triggers();
		});
		
	})();

	function get_bar_settings() {
		// get params
		var _params = maincontainer.find('.woozonelite-debugbar-settings').html();
		//_params = JSON.stringify(_params);
		_params = typeof _params != 'undefined'
			? JSON && JSON.parse(_params) || $.parseJSON(_params) : _params;
		if (DEBUG) console.log( mod_alias, 'params', _params );

		return _params;
	};


	//====================================================
	//== :: FUNC

	function build_menu_adminbar() {

		if ( $('#wp-admin-bar-woozonelite-debugbar').length ) {

			var admin_bar_menu_wrapper = document.createDocumentFragment();

			if ( bar_settings ) {

				var top_title = misc.decodeEntities( bar_settings.menu.top.title, true );

				$('#wp-admin-bar-woozonelite-debugbar')
					.addClass( bar_settings.menu.top.classname )
					.find('a').eq(0)
					.html( top_title )
				;

				$.each( bar_settings.menu.sub, function( i, el ) {

					var new_menu = $('#wp-admin-bar-woozonelite-debugbar-placeholder')
						.clone()
						.attr('id','wp-admin-bar-' + el.id)
					;
					new_menu
						.find('a').eq(0)
						.html( el.title )
						.attr( 'href', el.href )
					;

					admin_bar_menu_wrapper.appendChild( new_menu.get(0) );

				} );

				$('#wp-admin-bar-woozonelite-debugbar ul').append(admin_bar_menu_wrapper);
			}

			$('#wp-admin-bar-woozonelite-debugbar, #wp-admin-bar-woozonelite-debugbar-default').show();
		}
		else {

			$('#woozonelite-debugbar')
				.addClass('woozonelite-debugbar-force-show')
				.removeClass('woozonelite-debugbar-hide')
			;

			$('#woozonelite-debugbar-dashboard')
				.addClass('woozonelite-debugbar-row-show')
			;
		}
	};

	function show_box_row( row_id ) {

		var box_row = $( row_id );

		// show box selected row
		mainbox.addClass('woozonelite-debugbar-show').removeClass('woozonelite-debugbar-hide');
		$( '.woozonelite-debugbar-row' ).removeClass('woozonelite-debugbar-row-show');
		$( '#woozonelite-debugbar-rows' ).scrollTop(0);
		box_row.addClass('woozonelite-debugbar-row-show');

		// show box selected menu item
		$('#woozonelite-debugbar-menu').find('a').removeClass('woozonelite-debugbar-selected-menu');
		var selected_menu = $('#woozonelite-debugbar-menu').find('a[href="' + row_id + '"]').addClass('woozonelite-debugbar-selected-menu');

		$('.woozonelite-debugbar-title-heading select').val( row_id );

		if ( localStorage.getItem( box_row_key_pinned ) ) {
			localStorage.setItem( box_row_key_pinned, row_id );
		}

		// box size
		if ( mainbox.height() < box_minheight ) {
			mainbox.height( box_minheight );
		}
	};

	function do_resize_box() {
		var mouse_posY 		= 0,
			box_minheight_ 	= 0;

		$(document).on('mousedown', '#woozonelite-debugbar-title', function(e) {
			box_minheight_ = $(this).outerHeight() - 1; // sub border bottom
			mouse_posY = mainbox.outerHeight() + e.clientY;

			$(document).on('mousemove', resize_go);
			$(document).on('mouseup', resize_stop);
		});

		function resize_go(e) {
			var box_height = ( mouse_posY - e.clientY );
			if ( box_height >= box_minheight_ && box_height < ( $(window).height() - wpadminbar_height ) ) {
				mainbox.height( box_height );
			}
		}

		function resize_stop(e) {
			$(document).off('mousemove', resize_go);
			$(document).off('mouseup', resize_stop);

			localStorage.setItem( box_row_key_height, mainbox.height() );
		}
	}


	//====================================================
	//== :: TRIGGERS
	function triggers() {

		build_menu_adminbar();

		maincontainer.on('change', '.woozonelite-debugbar-title-heading select', function(e) {
			show_box_row( $(this).val() );
		});

		//:: wp adminbar
		maincontainer.on('click', '#wp-admin-bar-woozonelite-debugbar a', function (e) {
			e.preventDefault();

			show_box_row( $(this).attr('href') );
		});

		//:: box menu
		maincontainer.on('click', '#woozonelite-debugbar #woozonelite-debugbar-menu a', function (e) {
			e.preventDefault();

			show_box_row( $(this).attr('href') );
		});

		//:: get box pinned status from storage
		var box_pinned = localStorage.getItem( box_row_key_pinned );
		if ( box_pinned && $( box_pinned ).length ) {
			show_box_row( box_pinned );
			$('.woozonelite-debugbar-button-pin').addClass( 'woozonelite-debugbar-button-active' );
		}

		//:: get box height from storage
		var box_height = localStorage.getItem( box_row_key_height );
		if ( box_height !== null ) {
			if ( box_height < box_minheight ) {
				box_height = box_minheight;
			}
			if ( box_height > box_maxheight ) {
				box_height = box_maxheight;
			}
			mainbox.height( box_height );
		}

		//:: resize window & ( reset box height & save it in storage )
		$(window).on('resize', function() {
			var box_maxheight 	= $(window).height() - wpadminbar_height,
				box_height 		= mainbox.height();

			if ( box_height < box_minheight ) {
				mainbox.height( box_minheight );
			}
			if ( box_height > box_maxheight ) {
				mainbox.height( box_maxheight );
			}

			localStorage.setItem( box_row_key_height, mainbox.height() );
		});

		//:: resize box & save it's height in storage
		do_resize_box();

		//:: box button close
		maincontainer.on('click', '.woozonelite-debugbar-button-close', function(e) {

			mainbox.removeClass('woozonelite-debugbar-show');
			localStorage.removeItem( box_row_key_pinned );
			$('.woozonelite-debugbar-button-pin').removeClass( 'woozonelite-debugbar-button-active' );
		});

		//:: box button pin it
		maincontainer.on('click', '.woozonelite-debugbar-button-pin', function(e) {

			if ( $(this).hasClass( 'woozonelite-debugbar-button-active' ) ) {
				localStorage.removeItem( box_row_key_pinned );
			}
			else {
				localStorage.setItem( box_row_key_pinned, '#' + $('.woozonelite-debugbar-row-show:first').attr('id') );
			}

			$(this).toggleClass( 'woozonelite-debugbar-button-active' );
		});

		//:: box button fullscreen
		var box_height = 0;
		maincontainer.on('click', '.woozonelite-debugbar-button-fullscreen', function(e) {

			if ( ! screenfull.isFullscreen ) {
				box_height = mainbox.height();
			}

			if ( screenfull.enabled ) {
				screenfull.toggle( document.getElementById('woozonelite-debugbar') );
				//screenfull.toggle();
			}
		});
		
		if (screenfull.enabled) {

			screenfull.on('change', function() {

				//console.log( screenfull.element  );
				if ( screenfull.isFullscreen ) {
					mainbox.height( screen.height );
				}
				else {
					mainbox.height( box_height );
					localStorage.setItem( box_row_key_height, mainbox.height() );
				}
			});

			screenfull.on('error', function() {
				console.error('Failed to enable fullscreen');
				alert( 'Failed to enable fullscreen' );
			});
		}
	};


	//====================================================
	//== :: MISC
	var misc = {
	
		hasOwnProperty: function(obj, prop) {
			var proto = obj.__proto__ || obj.constructor.prototype;
			return (prop in obj) &&
			(!(prop in proto) || proto[prop] !== obj[prop]);
		},

		// preserve = choose yes if you want to preserve html tags
		decodeEntities: (function() {
			var preserve = false;
   
			// create a new html document (doesn't execute script tags in child elements)
			// this also prevents any overhead from creating the object each time
			var doc = document.implementation.createHTMLDocument("");
			var element = doc.createElement('div');
				
			// regular expression matching HTML entities
			var entity = /&(?:#x[a-f0-9]+|#[0-9]+|[a-z0-9]+);?/ig;
		
			function getText(str) {
				if ( preserve ) {
					// find and replace all the html entities
					str = str.replace(entity, function(m) {
						element.innerHTML = m;
						return element.textContent;
					});
				} else {
					element.innerHTML = str;
					str = element.textContent;
				}
				element.textContent = ''; // reset the value
				return str;
			}
		
			function decodeHTMLEntities(str, _preserve) {
				preserve = _preserve || false;
				if (str && typeof str === 'string') {

					str = getText(str);
					if ( preserve ) {
						return str;
					} else {
						// called twice because initially text might be encoded like this: &lt;img src=fake onerror=&quot;prompt(1)&quot;&gt;
						return getText(str);
					}
				}
			}
			return decodeHTMLEntities;
		})(),
		decodeEntities2: function(str, preserve) {
			var preserve = preserve || false;

			if ( preserve )
				return $("<textarea/>").html(str).text();
			else
				return $("<div/>").html(str).text();
		}
	};

	// external usage
	return {
	}
})(jQuery);