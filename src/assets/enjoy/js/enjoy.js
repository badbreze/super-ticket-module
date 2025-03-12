jQuery(document).ready(function() {
    /*jQuery('#globalSearch').on('focus', function() {
        if ($('#globalSearch').siblings('.dropdown-menu').is(":hidden")){
            $('#globalSearch').dropdown('toggle');
        }
    });*/

    $(".linked-row").click(function() {
        window.location = $(this).data("href");
    });

    //Focus on Search
    jQuery('#filterBlocks').focus();


    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    jQuery('.linked-block').on('click', function() {
        if(jQuery(this).find('.card-link').length)
            window.location.href = jQuery(this).find('.card-link').attr('href');
    });

    //Use #globalSearch to call API endpoint and put results in #globalSearchResults dropdown
    jQuery('#globalSearch').keyup(function(e) {
        e.stopPropagation();

        //delay to check the user has stopped typing
        clearTimeout(jQuery.data(this, 'timer'));

        if (e.keyCode == 13) {
            //enter pressed
            jQuery(this).trigger('search');
        } else {
            jQuery(this).data('timer', setTimeout(function() {
                jQuery('#globalSearch').trigger('search');
            }, 500));
        }
    });

    jQuery('#globalSearch').on('search', function(e) {
        e.stopPropagation();

        var input = jQuery(this);
        var query = input.val().toLowerCase();
        var projects = jQuery('.blocks');

        if(jQuery(this).val() != "" && jQuery(this).val().length >= 3) {
            jQuery.getJSON(
                '/super/ticket/search',
                {
                    q: jQuery(this).val()
                },
                function(data) {
                    jQuery('#globalSearchResults').html('');

                    if ($('#globalSearch').siblings('.dropdown-menu').is(":hidden")){
                        $('#globalSearch').dropdown('toggle');
                    }

                    if(data.length == 0) {
                        jQuery('#globalSearchResults').html('<span class="dropdown-item">No results found</span>');
                    }

                    jQuery(data).each(function(index, value) {
                        var element = jQuery('<a class="dropdown-item"/>');

                        element.attr('href', '/o/'+value.id);
                        element.text(value.name);

                        jQuery('#globalSearchResults').append(element);

                        if(value.platforms && value.platforms.length != 0) {
                            var subblock = jQuery('<nav class="nav nav-pills flex-column"/>');
                            jQuery(value.platforms).each(function(index, value) {
                                var subelement = jQuery('<a class="nav-link ml-3"/>');
                                subelement.attr('href', '/t/'+value.id);
                                subelement.text(value.name);

                                var statusIcon = jQuery('<i class="fas status-icon-'+value.status+'"/>');

                                subelement.prepend(statusIcon);

                                //jQuery('#globalSearchResults').append('<a href="/platforms/platforms/' + value.id + '" class="dropdown-item" id="' + value.id + '">' + value.name + '</a>');
                                subblock.append(subelement);
                            });

                            jQuery('#globalSearchResults').append(subblock);
                        }
                    });
                }
            );
        } else {
            if ($('#globalSearch').siblings('.dropdown-menu').is(":visible")){
                $('#globalSearch').dropdown('hide');
            }
        }
    });

    var n_elements = $(".project-block").length;
    var random = Math.floor(Math.random()*n_elements);

    $(".project-block").eq(random).mouseover(function () {
        var timeOfDay = new Date().getHours();
        if (timeOfDay >= 9 && timeOfDay <= 16) {
            return false;
        }

        $(this).animate({
            position: 'absolute',
            top: ((Math.random()-0.5) * 300),
            left: ((Math.random()-0.5) * 600)
        }, 100);

    });

});