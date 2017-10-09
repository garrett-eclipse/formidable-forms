/**
 * Default settings for jQuery UI Autocomplete for use with non-hierarchical taxonomies.
 */
( function( $ ) {
  if ( typeof window.tagsSuggestL10n === 'undefined' || typeof window.uiAutocompleteL10n === 'undefined' ) {
    return;
  }

  /**
   * Add UI Autocomplete to an input or textarea element with presets for use
   * with non-hierarchical taxonomies.
   *
   * Example: `$( element ).frmPageSuggest( options )`.
   *
   * The taxonomy can be passed in a `data-wp-taxonomy` attribute on the element or
   * can be in `options.taxonomy`.
   *
   * @since 4.7.0
   *
   * @param {object} options Options that are passed to UI Autocomplete. Can be used to override the default settings.
   * @returns {object} jQuery instance.
   */
  $.fn.frmPageSuggest = function( options ) {
    var cache;
    var $element = $( this );

    options = options || {};

    options = $.extend( {
      source: function( request, response ) {

        $.get( window.ajaxurl, {
          action: 'frm_page_search',
          q: request.term
        } )
          .always(removeSpinner)
          .done(saveData);

        function saveData(data) {
          var pages = [];

          if ( data ) {
            data = JSON.parse(data);

            pages = data;
            cache = pages;

            response( pages );
          } else {
            response( pages );
          }
        }

        function removeSpinner() {
          $element.removeClass( 'ui-autocomplete-loading' ); // UI fails to remove this sometimes?
        }
      },
      focus: function( event, ui ) {
        $element.attr( 'aria-activedescendant', 'frm-page-autocomplete-' + ui.item.id );

        // Don't empty the input field when using the arrow keys to
        // highlight items. See api.jqueryui.com/autocomplete/#event-focus
        event.preventDefault();
      },
      select: function( event, ui ) {
        $element.val( ui.item.post_title );
        // TODO set page ID hidden field

        if ( $.ui.keyCode.TAB === event.keyCode ) {
          // Audible confirmation message when a tag has been selected.
          window.wp.a11y.speak( window.tagsSuggestL10n.termSelected, 'assertive' );// TODO see if there is an equivalent of "Page Selected" in formidable or WordPress
          event.preventDefault();
        } else if ( $.ui.keyCode.ENTER === event.keyCode ) {
          // Do not close Quick Edit / Bulk Edit
          event.preventDefault();
          event.stopPropagation();
        }

        return false;
      },
      open: function() {
        $element.attr( 'aria-expanded', 'true' );
      },
      close: function() {
        $element.attr( 'aria-expanded', 'false' );
      },
      minLength: 2,
      position: {
        my: 'left top+2',
        at: 'left bottom',
        collision: 'none'
      },
      messages: {
        noResults: window.uiAutocompleteL10n.noResults,
        results: function( number ) {
          if ( number > 1 ) {
            return window.uiAutocompleteL10n.manyResults.replace( '%d', number );
          }

          return window.uiAutocompleteL10n.oneResult;
        }
      }
    }, options );

    $element.on( 'keydown', function() {
      $element.removeAttr( 'aria-activedescendant' );
    } )
      .autocomplete( options )
      .autocomplete( 'instance' )._renderItem = function( ul, item ) {
      return $( '<li role="option" id="frm-page-autocomplete-' + item.id + '">' )
        .text( item.post_title )
        .appendTo( ul );
    };

    $element.attr( {
      'role': 'combobox',
      'aria-autocomplete': 'list',
      'aria-expanded': 'false',
      'aria-owns': $element.autocomplete( 'widget' ).attr( 'id' )
    } )
      .on( 'focus', function() {
        var inputValue = $element.val().trim();

        // Don't trigger a search if the field is empty.
        // Also, avoids screen readers announce `No search results`.
        if ( inputValue ) {
          $element.autocomplete( 'search' );
        }
      } )
      // Returns a jQuery object containing the menu element.
      .autocomplete( 'widget' )
      .addClass( 'frm-page-autocomplete' )
      .attr( 'role', 'listbox' )
      .removeAttr( 'tabindex' ) // Remove the `tabindex=0` attribute added by jQuery UI.

      // Looks like Safari and VoiceOver need an `aria-selected` attribute. See ticket #33301.
      // The `menufocus` and `menublur` events are the same events used to add and remove
      // the `ui-state-focus` CSS class on the menu items. See jQuery UI Menu Widget.
      .on( 'menufocus', function( event, ui ) {
        ui.item.attr( 'aria-selected', 'true' );
      })
      .on( 'menublur', function() {
        // The `menublur` event returns an object where the item is `null`
        // so we need to find the active item with other means.
        $( this ).find( '[aria-selected="true"]' ).removeAttr( 'aria-selected' );
      });

    return this;
  };

}( jQuery ) );
