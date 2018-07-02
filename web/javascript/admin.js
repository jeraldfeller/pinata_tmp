/*
 Toggle Plugin
 */
(function ( $ ) {

    $.fn.toggleTarget = function() {
        return this.each(function() {

            var $showTarget = $('#'+$(this).data('target-show'));
            var $showTargetParent = $('#'+$(this).data('target-show')).parents('.control-group');

            var $hideTarget = $('#'+$(this).data('target-hide'));
            var $hideTargetParent = $('#'+$(this).data('target-hide')).parents('.control-group');

            if(!$(this).is(':checked')){
                $showTargetParent.hide();
                $hideTargetParent.show();
            }

            $(this).on('change',function(){
                $showTargetParent.slideToggle();
                $hideTargetParent.slideToggle();
                if(!$(this).is(':checked')){
                    $showTarget.val('');
                    $showTarget.prop('required',false);
                }else{
                    $showTarget.prop('required',true);
                }
            });
        });
    };

}( jQuery ));

$(function () {
    $('[data-toggle-target="true"]').toggleTarget();
});