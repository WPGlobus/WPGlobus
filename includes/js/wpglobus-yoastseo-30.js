/*global wpseoReplaceVarsL10n, YoastSEO*/

// @see YoastSEO.app.analyzerData for 'content', 'title', 'data_page_title', 'data_meta_desc'
// @see YoastSEO.app.rawData for 'data_page_title', 'data_meta_desc'

// @see YoastSEO.SnippetPreview func to generate preview

// @todo
// 1. excerpt
// 2. jQuery('.wpglobus-editor').focusout(function () { 
// 3. YoastSEO.app.rawData.usedKeywords

var WPGlobusYoastSeo;
jQuery(document).ready(function ($) {
	'use strict';

	var api;
	api = WPGlobusYoastSeo = {
		attrs: 	$('#wpglobus-wpseo-attr'),
		iB: 	$('#wpseo-meta-section-content'), // insert before element
		t:		$('#wpseo-meta-section-content'), // source
		ids: 	'',
		names:  '',
		init: function() {
			api.start();
		},	
		start: function() {

			// tabs on
			$('#wpglobus-wpseo-tabs').tabs();
			
			api.ids 	= api.attrs.data('ids');
			api.names 	= api.attrs.data('names');
			
			api.ids 	= api.ids + ',' + api.attrs.data('qtip');
			api.ids 	= api.ids.split(',');
			api.names 	= api.names.split(',');
			
			$('#wpglobus-wpseo-tabs').insertBefore( api.iB );
			$('.wpseo-metabox-tabs').css({'height':'26px'});

			$('.wpglobus-wpseo-general').each(function(i,e){
				var $e = $(e);
				var l = $e.data('language');
				var sectionID = 'wpseo-meta-section-content_'+l;
				
				$e.html('<div id="'+sectionID+'" class="wpseo-meta-section wpglobus-wpseo-meta-section" style="width:100%" data-language="'+l+'">' + api.t.html() + '</div>');
				$('#'+sectionID+' .wpseo-metabox-tabs').attr( 'id', 'wpseo-metabox-tabs_'+l );
				$('#'+sectionID+' .wpseotab').attr( 'id', 'wpseo_content_'+l );
				$('#'+sectionID).css({'display':'block'});
				$('#wpseo_meta').css({'overflow':'hidden'});
				
				$('#'+sectionID+' .snippet_container').addClass('wpglobus-snippet_container');
				
				if ( l !== WPGlobusCoreData.default_language ) {
					// hide plus sign
					$('#'+sectionID+' .wpseo-add-keyword').addClass('hidden');
				}
				
				$.each( api.names, function(i,name) {
					$( '#'+name ).attr( 'name', name+'_'+l );
				}); 
				
				$.each( api.ids, function(i,id) {
					var $id = $('#'+id);
					if ( 'wpseosnippet' == id ) {
						$id.addClass('wpglobus-wpseosnippet');
					}
					if ( 'snippet_title' == id ) {
						$id.addClass('wpglobus-snippet_title');
					}
					if ( 'snippet_meta' == id ) {
						$id.addClass('wpglobus-snippet_meta');
					}
					/** url **/ 
					if ( 'snippet_cite' == id ) {
						$id.addClass('wpglobus-snippet_cite');
					}
					if ( 'snippet_citeBase' == id ) {
						$id.addClass('wpglobus-snippet_citeBase');
					}
					/** focuskw **/	
					if ( 'yoast_wpseo_focuskw_text_input' == id ) {
						$id.addClass('wpglobus-yoast_wpseo_focuskw_text_input');
					}
	
					$id.attr('id',id+'_'+l);
					$('#'+id+'_'+l).attr('data-language',l);
				});
				
				// set focus keywords for every language
				var focuskw = WPGlobusCore.TextFilter( $('#yoast_wpseo_focuskw_text_input').val(), l, 'RETURN_EMPTY' );
				$( '#yoast_wpseo_focuskw_text_input_'+l ).val( focuskw );
				$( '#yoast_wpseo_focuskw_'+l ).val( focuskw );

				if ( l !== WPGlobusCoreData.default_language ) {
					$('#'+sectionID+' #yoast_wpseo_focuskw_text_input_'+l).addClass('hidden');
					$('#'+sectionID+' #wpseo-pageanalysis_'+l).addClass('hidden');
				}
				
			}); // end each .wpglobus-wpseo-general	
	
			// hide original section content
			api.iB.addClass( 'hidden' );
			api.iB.css({'height':0,'overflow':'hidden'});
			
			// set focuskw to default language
			var focuskw_d = WPGlobusCore.TextFilter( $('#yoast_wpseo_focuskw_text_input').val(), WPGlobusCoreData.default_language, 'RETURN_EMPTY' );
			$( '#yoast_wpseo_focuskw_text_input' ).val( focuskw_d );
			$( '#yoast_wpseo_focuskw' ).val( focuskw_d );
			
			// wpseo-metabox-sidebar 
			$('.wpseo-metabox-sidebar .wpseo-meta-section-link').on('click',function(ev){
				if ( $(this).attr('href') == '#wpseo-meta-section-content' ) {
					$('#wpglobus-wpseo-tabs').css({'display':'block'});
				} else {
					$('#wpglobus-wpseo-tabs').css({'display':'none'});
				}	
			});
			
			// make synchronization click on "Post tab" with seo tab 
			$('body').on('click', '.wpglobus-post-body-tabs-list li', function(event){
				var $this = $(this);
				if ( $this.hasClass('wpglobus-post-tab') ) {
					$('#wpglobus-wpseo-tabs').tabs('option','active',$this.data('order'));
					
					// set keyword
					var k = $( '#yoast_wpseo_focuskw_text_input_' + $(this).data('language') ).val();
					YoastSEO.app.rawData.keyword = k ;
					$('#yoast_wpseo_focuskw_text_input').val( k );
					$('input[name="yoast_wpseo_focuskw"]').val( k );			
					
					YoastSEO.app.analyzeTimer(YoastSEO.app);
				}
			});
			
			//wpglobus_qtip();		
			
		}	
	}
	
	// ***** //
	var _this;
	var WPGlobusYoastSeoPlugin = function() {
		
		this.replaceVars 	= wpseoReplaceVarsL10n.replace_vars;
		this.language 	 	= WPGlobusCoreData.default_language;
		this.tab 	 	 	= WPGlobusCoreData.default_language;
		this.wpseoTab 	 	= WPGlobusCoreData.default_language;
		
		this.title_template = wpseoPostScraperL10n.title_template;
		
		this.focuskw		= $('#yoast_wpseo_focuskw_text_input');
		this.focuskw_hidden	= $('input[name="yoast_wpseo_focuskw"]');
		this.focuskwKeep	= false;
		
		YoastSEO.app.registerPlugin( 'wpglobusYoastSeoPlugin', {status: 'ready'} );

		/**
		* @param modification    {string}    The name of the filter
		* @param callable        {function}  The callable
		* @param pluginName      {string}    The plugin that is registering the modification.
		* @param priority        {number}    (optional) Used to specify the order in which the callables
		*                                    associated with a particular filter are called. Lower numbers
		*                                    correspond with earlier execution.
		*/
		YoastSEO.app.registerModification( 'content', this.contentModification, 'wpglobusYoastSeoPlugin', 0 );
		YoastSEO.app.registerModification( 'title', this.titleModification, 'wpglobusYoastSeoPlugin', 0 );
		
		YoastSEO.app.registerModification( 'snippet_title', this.snippetModification, 'wpglobusYoastSeoPlugin', 0 );
		YoastSEO.app.registerModification( 'snippet_meta', this.snippetModification, 'wpglobusYoastSeoPlugin', 0 );		
		
		YoastSEO.app.registerModification( 'data_page_title', this.pageTitleModification, 'wpglobusYoastSeoPlugin', 0 );
		YoastSEO.app.registerModification( 'data_meta_desc', this.metaDescModification, 'wpglobusYoastSeoPlugin', 0 );	
		
		
		
		$(document).on('blur', '.wpglobus-snippet_title', function(ev){
			var $t = $(this);
			var s = WPGlobusCore.getString( $('#yoast_wpseo_title').val(), $t.text(), $t.data('language') );

			YoastSEO.app.rawData.pageTitle = s;  // @todo maybe set at start js ?

			//$('#yoast_wpseo_title').val( s );  // @todo don't work with id
			$('input[name="yoast_wpseo_title"]').val( s );
			$('#snippet_title').text( s );
		});
		
		$(document).on('blur', '.wpglobus-snippet_meta', function(ev){		
			var $t = $(this);
			var s = WPGlobusCore.getString( $('#yoast_wpseo_metadesc').val(), $t.text(), $t.data('language') );
			$('#yoast_wpseo_metadesc').val( s );
			$('#snippet_meta').text( s );
			
		});
		
		$(document).on('keyup', '.wpglobus-yoast_wpseo_focuskw_text_input', function(ev){		
			var $t = $(this);
				
			//var s = WPGlobusCore.getString( t.val(), $t.val(), $t.data('language') );
			var s = $t.val();
			_this.focuskw.val( s );
			_this.focuskw_hidden.val( s );

			_this.updateWpseoKeyword( s, $t.data('language') );
			
			YoastSEO.app.analyzeTimer(YoastSEO.app);
			// document.getElementById('yoast_wpseo_focuskw_text_input_en').addEventListener( 'input', YoastSEO.app.analyzeTimer.bind( YoastSEO.app ) );

		});		
		
		$('#publish,#save-post').on('mouseenter', function(event){
			var $t, s = '';
			$('.wpglobus-yoast_wpseo_focuskw_text_input').each( function(i,e){
				$t = $(this);
				s = WPGlobusCore.getString( s, $t.val(), $t.data('language') );
			});
			_this.focuskw.val( s );
			_this.focuskw_hidden.val( s );
			//console.log(s);
		}).on('mouseleave', function(event) {
			if ( ! _this.focuskwKeep ) {
				_this.wpseoTab = $('.wpglobus-wpseo-tabs-list .ui-tabs-active').data('language');
				var $t = $(this);
				//console.log( _this.wpseoTab );	
				_this.focuskw.val( $('#yoast_wpseo_focuskw_text_input_'+_this.wpseoTab ).val() );
				_this.focuskw_hidden.val( $('#yoast_wpseo_focuskw_text_input_'+_this.wpseoTab ).val() );				
			}	
			//console.log( $('#yoast_wpseo_focuskw_text_input').val() );	
		}).on('click', function(event){
			_this.focuskwKeep = true;
		});			
		
		$('body').on('click', '.wpglobus-wpseo-tabs-list li', function(event){
			// set keyword
			var k = $( '#yoast_wpseo_focuskw_text_input_' + $(this).data('language') ).val();
			YoastSEO.app.rawData.keyword = k ;
			_this.focuskw.val( k );
			_this.focuskw_hidden.val( k );

			YoastSEO.app.analyzeTimer(YoastSEO.app);
		});		
		
		_this = this;

	}
	
	WPGlobusYoastSeoPlugin.prototype.getWPseoTab = function() {
		return $('.wpglobus-wpseo-tabs-list .ui-tabs-active').data('language');
	}
	
	WPGlobusYoastSeoPlugin.prototype.citeModification = function(l) {
		var citeBase = '#snippet_citeBase_'+l;
		var cite = '#snippet_cite_'+l;
		var cb = $('#wpseo-tab-'+l).data('yoast-cite-base');
		var e  = $('#wpseo-tab-'+l).data('cite-contenteditable');
		//var permalink = $('#wpseo-tab-'+l).data('permalink');
		if ( e === false ) {
			$(cite).attr('contenteditable','false');
		}
		$(citeBase).text( cb );
		// ???????????????//
		// console.log( $('#wpseo-meta-section-content_'+l+' .wpseo_keyword').length );
		
	}
	
	WPGlobusYoastSeoPlugin.prototype.pageTitleModification = function(data) {
		
		//console.log( '1. pageTitleModification: ' + _this.getWPseoTab() );

		
		//_this.tab = $('.wpglobus-post-body-tabs-list .ui-tabs-active').data('language');
		//_this.wpseoTab = $('.wpglobus-wpseo-tabs-list .ui-tabs-active').data('language');
		
		var id = '#snippet_title_',
			text = '', tr, return_text = '';

		if ( _this.title_template == data ) {
			/**
			 * meta key _yoast_wpseo_title is empty or doesn't exists 
			 */
			$.each(WPGlobusCoreData.enabled_languages, function(i,l) {
				_this.language = l;
				text = _this.replaceVariablesPlugin( data );
				$(id+l).text( text );
				if ( l == _this.getWPseoTab() ) {
					return_text = text;	
				}	
			});
		} else {
			tr = WPGlobusCore.getTranslations( data );
			$.each(WPGlobusCoreData.enabled_languages, function(i,l) {
				_this.language = l;
				if ( '' === tr[l] ) {
					text = _this.replaceVariablesPlugin( _this.title_template );
				} else {
					text = tr[l];
				}	
				$(id+l).text( text );
				if ( l == _this.getWPseoTab() ) {
					return_text = text;	
				}	
			});
		}
		//console.log( return_text );
		return return_text;		
	}		
	
	WPGlobusYoastSeoPlugin.prototype.metaDescModification = function(data) {
		
		//console.log( '2. metaDescModification: ' + _this.getWPseoTab() );
		
		var id = '#snippet_meta_',
			text = '';

		$.each(WPGlobusCoreData.enabled_languages, function(i,l) {
			$(id+l).text( WPGlobusCore.TextFilter( data, l, 'RETURN_EMPTY' ) );
			_this.citeModification( l );
		});			

		//console.log( WPGlobusCore.TextFilter( data, _this.getWPseoTab(), 'RETURN_EMPTY' ) );
		return WPGlobusCore.TextFilter( data, _this.getWPseoTab(), 'RETURN_EMPTY' );

	}	
	
	WPGlobusYoastSeoPlugin.prototype.snippetModification = function(data) {
		//console.log( '3. snippetModification: ' + _this.getWPseoTab() );
		return WPGlobusCore.TextFilter( data, _this.getWPseoTab(), 'RETURN_EMPTY' );
	}
	
	/**
	 * Adds some text to the data...
	 *
	 * @param data The data to modify
	 */
	WPGlobusYoastSeoPlugin.prototype.contentModification = function(data) {
		//console.log( '4. contentModification: ' + _this.getWPseoTab() );
		//console.log( data );
		
		if ( _this.getWPseoTab() == WPGlobusCoreData.default_language ) {
			return data;
		}
		return $( '#content_' + _this.getWPseoTab() ).val();
		//return WPGlobusCore.TextFilter( data, _this.getWPseoTab(), 'RETURN_EMPTY' );
	};	
	
	WPGlobusYoastSeoPlugin.prototype.titleModification = function(data) {
		//console.log( '5. titleModification: ' + _this.getWPseoTab() );

		setTimeout( _this.updatePageAnalysis, 1000 );
		
		if ( _this.getWPseoTab() == WPGlobusCoreData.default_language ) {
			//console.log( data );
			return data;
		}
		//console.log( $( '#title_' + _this.getWPseoTab() ).val() );	
		return $( '#title_' + _this.getWPseoTab() ).val();

	};
	

	
	/**
	 * replaces default variables with the values stored in the wpseoMetaboxL10n object.
	 *
	 * @see YoastReplaceVarPlugin.prototype.defaultReplace	 
	 *
	 * @param {String} textString
	 * @return {String}
	 */	 
	WPGlobusYoastSeoPlugin.prototype.defaultReplace = function( textString ) {
		return textString.replace( /%%sitedesc%%/g, this.replaceVars.sitedesc )
			.replace( /%%sitename%%/g, WPGlobusCore.TextFilter( this.replaceVars.sitename, this.language ) )
			.replace( /%%term_title%%/g, this.replaceVars.term_title )
			.replace( /%%term_description%%/g, this.replaceVars.term_description )
			.replace( /%%category_description%%/g, this.replaceVars.category_description )
			.replace( /%%tag_description%%/g, this.replaceVars.tag_description )
			.replace( /%%searchphrase%%/g, this.replaceVars.searchphrase )
			.replace( /%%sep%%/g, this.replaceVars.sep )
			.replace( /%%date%%/g, this.replaceVars.date )
			.replace( /%%id%%/g, this.replaceVars.id )
			.replace( /%%page%%/g, this.replaceVars.page )
			.replace( /%%currenttime%%/g, this.replaceVars.currenttime )
			.replace( /%%currentdate%%/g, this.replaceVars.currentdate )
			.replace( /%%currentday%%/g, this.replaceVars.currentday )
			.replace( /%%currentmonth%%/g, this.replaceVars.currentmonth )
			.replace( /%%currentyear%%/g, this.replaceVars.currentyear )
			.replace( /%%focuskw%%/g, YoastSEO.app.stringHelper.stripAllTags( YoastSEO.app.rawData.keyword ) );
	};	
	
	/**
	 * runs the different replacements on the data-string
	 *
	 * @see YoastReplaceVarPlugin.prototype.replaceVariablesPlugin
	 *
	 * @param {String} data
	 * @returns {string}
	 */
	WPGlobusYoastSeoPlugin.prototype.replaceVariablesPlugin = function( data ) {
		if( typeof data !== 'undefined' ) {
			data = this.titleReplace( data );
			data = this.defaultReplace( data );
			//data = this.parentReplace( data );
			data = this.doubleSepReplace( data );
			//data = this.excerptReplace( data );
		}
		return data;
	};	

	/**
	 * Replaces %%title%% with the title
	 *
	 * @see YoastReplaceVarPlugin.prototype.titleReplace
	 *
	 * @param {String} data
	 * @returns {string}
	 */
	WPGlobusYoastSeoPlugin.prototype.titleReplace = function( data ) {
		//var title = YoastSEO.app.rawData.title;
		var title = '', t = '';
		if ( this.language == WPGlobusCoreData.default_language ) {
			title = $('#title').val();
		} else {
			title = $('#title_'+this.language).val();
		}
		if ( typeof title === 'undefined' ) {
			title = YoastSEO.app.rawData.pageTitle;
		}

		data = data.replace( /%%title%%/g, title );

		return data;
	};
	
	/**
	 * removes double seperators and replaces them with a single seperator
	 *
	 * @see YoastReplaceVarPlugin.prototype.doubleSepReplace
	 *
	 * @param {String} data
	 * @returns {String}
	 */
	WPGlobusYoastSeoPlugin.prototype.doubleSepReplace = function( data ) {
		var escaped_seperator = YoastSEO.app.stringHelper.addEscapeChars( this.replaceVars.sep );
		var pattern = new RegExp( escaped_seperator + ' ' + escaped_seperator, 'g' );
		data = data.replace( pattern, this.replaceVars.sep );
		return data;
	};	
	
	WPGlobusYoastSeoPlugin.prototype.updateWpseoKeyword = function(kw,l) {
		if ( l  == WPGlobusCoreData.default_language ) {
			return;	
		}	
		if ( $('#wpseo-meta-section-content_'+l+' .wpseo_keyword').length == 1 ) {
			$('#wpseo-meta-section-content_'+l+' .wpseo_keyword').removeClass('wpseo_keyword').addClass('wpglobus-wpseo_keyword_'+l);
		}
		$('.wpglobus-wpseo_keyword_'+l).text(kw);
	}
	
	WPGlobusYoastSeoPlugin.prototype.updatePageAnalysis = function() {
		$( '#wpseo-pageanalysis_' + _this.getWPseoTab() ).html( $('#wpseo-pageanalysis').html() );
	};

	window.WPGlobusYoastSeoPlugin = new WPGlobusYoastSeoPlugin();	
	
});