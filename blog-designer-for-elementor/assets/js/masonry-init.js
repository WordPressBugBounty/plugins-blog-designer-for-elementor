function initMasonryBlogLayouts() {
    var $ = jQuery;
    if ($.fn.masonry) {
        var blogMasonry = jQuery('.masonaryactive');
        blogMasonry.masonry({
            itemSelector: '.blog-grid-layout',
        });
    }
}

(function($) {
    initMasonryBlogLayouts();
})(jQuery);

jQuery(document).ready(function() {
    initMasonryBlogLayouts();
});

// Call the function when the window is loaded
jQuery(window).on('load', function() {
    initMasonryBlogLayouts();
});

jQuery(window).on('elementor/frontend/init', function() {
    elementorFrontend.hooks.addAction('frontend/element_ready/post_layouts.default', function() {
        var masonryMainDiv = jQuery('.masonaryactive');
        var masonryChildItems = masonryMainDiv.find('.blog-grid-layout');

        setTimeout(function() {
            initMasonryBlogLayouts();
            if (masonryMainDiv.attr('style') && masonryMainDiv.attr('style').includes('height')) {
                // Extract the height from the style attribute
                const heightFromStyleMatch = masonryMainDiv.attr('style').match(/height:\s*([^;]+)/);
                const heightFromStyle = heightFromStyleMatch ? heightFromStyleMatch[1].trim() : null;

                if (heightFromStyle === '0px') {
                    // Option 1: Remove the height property
                    masonryMainDiv.css('height', ''); // Removes inline height property
                    masonryMainDiv.parent('.elementor-widget-post_layouts').removeClass('.elementor-widget-empty');
                    masonryChildItems.removeAttr('style');
                    masonryChildItems.addClass('align-self-stretch');
                    console.log('Height was 0px. Adjustments applied.');
                    // masonryMainDiv.masonry('layout');
                } else {
                    console.log('Height is not 0px. Current height:', heightFromStyle);
                }
            } else {
                console.log('Height is not explicitly defined in the style attribute.');
            }
        }, 2000); // Timeout of 4 seconds
    });
});