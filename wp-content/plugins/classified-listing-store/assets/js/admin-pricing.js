;(function ($) {

    function pricing_type() {
        var val = $('#pricing-type').val();
        if (val == "membership") {
            $(".form-group.allowed").slideUp();
            $(".form-group.regular-listings").slideDown();
            $(".form-group.membership-categories").slideDown();
            $(".form-group.rtcl-membership-promotions").slideDown();
        } else {
            $(".form-group.rtcl-promotions").slideUp();
            $(".form-group.membership-categories").slideUp();
            $(".form-group.regular-listings").slideUp();
            $(".form-group.rtcl-membership-promotions").slideUp();
            $(".form-group.allowed").slideDown();
        }
    }

    $(function () {
        pricing_type();
        $("#pricing-type").on('change', pricing_type);
    });

}(jQuery));