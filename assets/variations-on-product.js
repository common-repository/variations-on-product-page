jQuery(function ($) {
    var ajaxurl = '<?= site_url(); ?>';
    var variations = $('.variations_form').data('product_variations');
    var attributes = {};
    var perPage = variations_on_product_settings.options.per_page > 0 ? variations_on_product_settings.options.per_page : 10;
    var page = 1;

    var lastVariations = [];
    var lastPage = 0;

    refreshVariations();

    $('.variations select').change(function () {
        attributes[$(this).attr('name')] = $(this).val();
        page = 1;
        refreshVariations();
    })

    function refreshVariations() {

        var variations_index = 0;
        var available_variations = [];
        for (var i in variations) {
            var isVisible = true;
            var variation = variations[i];

            // filter by variation
            for(var j in attributes){
                if( attributes[j] && attributes[j] !== variation.attributes[j] ){
                    isVisible = false;
                }
            }
            if( isVisible ) {
                available_variations.push(variation);
            }
        }

        // no need to refresh
        if( JSON.stringify(lastVariations) === JSON.stringify(available_variations) && lastPage === page ){
            return;
        }
        lastVariations = available_variations;
        lastPage = page;
        $('#product-variations tbody').html('');


        for (var i in available_variations) {
            var variationAvailable = available_variations[i];

            var $newElement = variationAvailable.c_template;
            $('#product-variations tbody').append($newElement);
            variations_index++;

            if( variations_index >= perPage*page ){
                break;
            }
        }

        if( variations_index >= available_variations.length ){
            $('#product-variations-load-more').hide();
        }else{
            $('#product-variations-load-more').show();
        }
    }
    window.rfs = refreshVariations;

    $(document).on('click', '.c_variation_add_to_cart', function(e){
        e.preventDefault();

        var product_id = $(this).data('product-id');
        var variation_id = $(this).data('variation-id');

        var DATA = {
            'add-to-cart': product_id,
            'product_id': product_id,
            'variation_id': variation_id,
        }
        $.post(ajaxurl, DATA, function(){

        });
    })

    $(document).on('click', '#product-variations-load-more', function(e){
        e.preventDefault();
        page++;
        refreshVariations();
    });

})