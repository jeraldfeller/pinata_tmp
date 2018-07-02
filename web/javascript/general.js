$.fn.randomize = function(selector){
    var $elems = selector ? $(this).find(selector) : $(this).children(),
        $parents = $elems.parent();

    $parents.each(function(){
        $(this).children(selector).sort(function(){
            return Math.round(Math.random()) - 0.5;
            // }). remove().appendTo(this); // 2014-05-24: Removed `random` but leaving for reference. See notes under 'ANOTHER EDIT'
        }).detach().appendTo(this);
    });

    return this;
};

$(function() {

    var $window = $(window);

    /*
     Custom Select add down arrow
     */
    //$('form select').customSelect();
    $('select.styled').customSelect();
    $('.customSelect').append('<span class="arrow"><i class="pin-down-chevron"></i></span>');


    $('.title-select select').change(function(){
        var $select = $(this),
            $option = $select.find('option:selected'),
            $href = $option.attr('data-link');

        location.href = $href;
    });


    // Homepage slideshow
    var $homepageHeroSlideshowContainer = $('.hero-slider'),
        $infinite = true;

    if($homepageHeroSlideshowContainer.find('li').size() <= 1){
        $infinite = false;
    }

    $homepageHeroSlideshowContainer.randomize();

    var $heroSlider = $homepageHeroSlideshowContainer.bxSlider({
        controls: false,
        pager: false,
        auto: true,
        mode: 'fade',
        pause: 6000,
        speed: 2000,
        infiniteLoop: $infinite,
        onSliderLoad: function(){
            $homepageHeroSlideshowContainer.removeClass('loading');
        }
    });

    var $homepageSlideshowContainer = $('.home-slideshow'),
        $homepageSliderControls = $homepageSlideshowContainer.find('.slider-controls'),
        $homeSlideshowInfinite = true;

    if($homepageSlideshowContainer.find('.bxslider li').size() <= 1){
        $homepageSliderControls.hide();
        $homeSlideshowInfinite = false;
    }

    function setSliderChildrenHeight() {
        var children = $homepageSlideshowContainer.find('.bxslider li');
        var height = Math.max.apply(Math, children.map(function() {
            return $(this).outerHeight(false);
        }).get());

        children.height(height);
    }
    if($window.height() <= 650) {
        setSliderChildrenHeight();
    }


    var $homepageSlideshow = $homepageSlideshowContainer.find('.bxslider').bxSlider({
        controls: false,
        pager: false,
        auto: $homeSlideshowInfinite,
        autoStart: $homeSlideshowInfinite,
        infiniteLoop: $homeSlideshowInfinite,
        pause: 10000,
        speed: 2000,
        mode: 'fade',
        easing: 'linear',
        //adaptiveHeight: true,
        onSliderLoad: function(){
            if($window.width() <= 650) {
                $homepageSlideshowContainer.find('li').each(function(){
                    var $slide = $(this),
                        $slideHeight = $slide.outerHeight(true);

                    var $overlayHeight = $slideHeight - 190;

                    $slide.find('.overlay').height($overlayHeight);
                });
            }
            $homepageSlideshowContainer.removeClass('loading');
        }
    });

    $homepageSliderControls.find('.slide-next').click(function(){
        $homepageSlideshow.goToNextSlide();
    });

    $homepageSliderControls.find('.slide-prev').click(function(){
        $homepageSlideshow.goToPrevSlide();
    });

    var $fruitMenu = $('.fruit-menu');
    if($fruitMenu.length){
        $('li.fruit a').each(function(i,e){
           if($(e).data('url') == window.location.href){
               $(e).parent().addClass('active');
               $fruitMenu.removeClass('active');
           }
        });
    }


    // Timeline

    var timelineBlocks = $('.cd-timeline-block'),
        offset = 0.8;

    if(timelineBlocks.size() > 0){
        //hide timeline blocks which are outside the viewport
        hideBlocks(timelineBlocks, offset);

        //on scolling, show/animate timeline blocks when enter the viewport
        $window.on('scroll', function(){
            (!window.requestAnimationFrame)
                ? setTimeout(function(){ showBlocks(timelineBlocks, offset); }, 100)
                : window.requestAnimationFrame(function(){ showBlocks(timelineBlocks, offset); });
        });

        function hideBlocks(blocks, offset) {
            blocks.each(function(){
                ( $(this).offset().top > $window.scrollTop()+$window.height()*offset ) && $(this).find('.cd-timeline-img, .cd-timeline-content').addClass('is-hidden');
            });
        }

        function showBlocks(blocks, offset) {
            blocks.each(function(){
                ( $(this).offset().top <= $window.scrollTop()+$window.height()*offset && $(this).find('.cd-timeline-img').hasClass('is-hidden') ) && $(this).find('.cd-timeline-img, .cd-timeline-content').removeClass('is-hidden').addClass('bounce-in');
            });
        }
    }




    // FAQ Accordion

    $('.faq-faq .faq-title').each(function(){
        var $title = $(this);

        $title.click(function(){

            var $faq = $(this).closest('.faq-faq'),
                $title = $faq.find('.faq-title'),
                $answer = $faq.find('.faq-answer'),
                $icon = $title.find('span');


            $answer.slideToggle();

            if($icon.text() == '+'){
                $icon.text('-');
            } else {
                $icon.text('+');
            }

        });


    });

    // Menu Nav

    $('.mobile-menu-trigger').click(function(){
        var $trigger = $(this),
            $titleBar = $trigger.closest('nav'),
            $nav = $titleBar.find('>ul');

        $nav.slideToggle();

    });


    // Mobile Subnav

    $('.mobile-sub-menu-trigger').click(function(){
        var $trigger = $(this),
            $titleBar = $trigger.closest('.title-bar'),
            $nav = $titleBar.find('nav >ul');

        $nav.slideToggle();

    });

    /* Ajax Submit */
    $('form.ajax-submit').submit(function(){
        if ($(this).hasClass('force-submit')) {
            return true;
        }
        if(!$(this).hasClass('loading')){
            var form = $(this);

            form.addClass('loading');
            var loadingText = form.find('.form-submit').text();
            form.find('.form-submit').attr('disabled','disabled').text('Loading...');

            $.ajax({
                type: "POST",
                url: form.attr('action'),
                data: form.serialize(),
                error: function(response, error){
                    form.removeClass('loading');
                    form.removeClass('modified');
                    form.find('.form-submit').removeAttr('disabled').text(loadingText);

                    if ("abort" != error) {
                        alert("An unexpected error occurred. Please try again later.")
                    }
                },
                success: function(response){
                    if (response.payload) {
                        var newForm = $('<div/>').html($(response.payload));
                        form.html(newForm.find('form:first').html());
                        $(window).trigger('form-error');
                    } else if (response.payload_success) {
                        form.slideUp('fast', function() {
                            form.html(response.payload_success);
                            form.slideDown('fast');
                        });
                    } else if (response.redirect){
                        window.location.href = response.redirect;
                    }

                    if (response.hasOwnProperty('gaq') && typeof ga == 'function'){
                        var category = response.gaq.hasOwnProperty('category') ? response.gaq.category : 'unknown-category',
                            action = response.gaq.hasOwnProperty('action') ? response.gaq.action : 'unknown-action',
                            label = response.gaq.hasOwnProperty('label') ? response.gaq.label : null,
                            value = response.gaq.hasOwnProperty('value') ? parseInt(response.gaq.value) : null;

                        ga('send', 'event', category, action, label, value);
                    }

                    if(response.eval) {
                        eval(response.eval);
                    }

                    form.removeClass('loading');
                    form.removeClass('modified');
                    form.find('.form-submit').removeAttr('disabled').text(loadingText);
                }
            });
        }

        return false;
    });



  /* Ajax Job Submit */

    $('#app_core_job_submit').click(function(){
      $btnNode = $(this);
      if(!$(this).hasClass('loading')) {
        $(this).addClass('loading');
        var loadingText = $(this).text();
        $(this).attr('disabled','disabled').text('Loading...');

        $fname = $('#app_core_job_fname');
        $sname = $('#app_core_job_sname');
        $phone = $('#app_core_job_contact_phone_number');
        $email = $('#app_core_job_email');
        $availFrom = $('#app_core_job_available_from');
        $availTo = $('#app_core_job_available_to');
        $wamuran = $('#app_core_job_wamuran');
        $mareeba = $('#app_core_job_mareeba');
        $katherine = $('#app_core_job_katherine');
        $darwin = $('#app_core_job_darwin');
        $stanthorpe = $('#app_core_job_stanthorpe');
        $farm5 = $('#app_core_job_farm5');
        $eligible = $('#app_core_job_eligible');
        $share = $('#app_core_job_share_contact');
        $other = $('#app_core_job_other');

        $errors = [];

        if($fname.val() == ''){
            $errors.push({
              node: $fname,
              type: 'empty',
              message: 'Please enter your first name'
            });
        }else{
          $fname.parent().parent().removeClass('error');
          $fname.next('.help-inline').remove();
        }

        if($sname.val() == ''){
          $errors.push({
            node: $sname,
            type: 'empty',
            message: 'Please enter your surname'
          });
        }else{
          $sname.parent().parent().removeClass('error');
          $sname.next('.help-inline').remove();
        }

        if($phone.val() == ''){
          $errors.push({
            node: $phone,
            type: 'empty',
            message: 'Please enter your contact phone number'
          });
        }else{
          $phone.parent().parent().removeClass('error');
          $phone.next('.help-inline').remove();
        }

        if($email.val() == ''){
          $errors.push({
            node: $email,
            type: 'empty',
            message: 'Please enter your email'
          });
        }else{
          var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          if(!re.test($email.val())){
            $errors.push({
              node: $email,
              type: 'invalid email',
              message: 'Please enter valid email'
            });
          }else{
            $email.parent().parent().removeClass('error');
            $email.next('.help-inline').remove();
          }
        }

        if($availFrom.val() == ''){
          $errors.push({
            node: $availFrom,
            type: 'date',
            message: 'Please enter valid date'
          });
        }else{
          $availFrom.parent().parent().removeClass('error');
          $availFrom.next('.help-inline').remove();
        }

        if($availTo.val() == ''){
          $errors.push({
            node: $availTo,
            type: 'date',
            message: 'Please enter valid date'
          });
        }else{
          $availTo.parent().parent().removeClass('error');
          $availTo.next('.help-inline').remove();
        }

        if($other.val().length > 50){
          $errors.push({
            node: $other,
            type: 'max chars',
            message: 'Please maximum of 50 characters only'
          });
        }else{
          $other.parent().parent().removeClass('error');
          $other.next('.help-inline').remove();
        }

        if($errors.length > 0){
            for($x = 0; $x < $errors.length; $x++){
                $node = $errors[$x].node;
                $message = $errors[$x].message;
                $node.parent().parent().addClass('error');
                if($node.next('span.help-inline').length == 0){
                  $node.parent().append('<span class="help-inline"><span class="text-error">'+$message+'<br></span></span>');
                }
            }
        }else{
          $('.error').removeClass('error');
          $('.help-inline').remove();

          $wamuran = ($wamuran.is(":checked") ? 1 : 0);
          $mareeba = ($mareeba.is(":checked") ? 1 : 0);
          $katherine = ($katherine.is(":checked") ? 1 : 0);
          $darwin = ($darwin.is(":checked") ? 1 : 0);
          $stanthorpe = ($stanthorpe.is(":checked") ? 1 : 0);
          $farm5 = ($farm5.is(":checked") ? 1 : 0);
          $eligible = ($eligible.is(":checked") ? 1 : 0);
          $share = ($share.is(":checked") ? 1 : 0);
          $data = {
            fname: $fname.val(),
            sname: $sname.val(),
            phone: $phone.val(),
            email: $email.val(),
            availFrom: $availFrom.val(),
            availTo: $availTo.val(),
            wamuran: $wamuran,
            mareeba: $mareeba,
            katherine: $katherine,
            darwin: $darwin,
            stanthorpe: $stanthorpe,
            farm5: $farm5,
            eligible: $eligible,
            share: $share,
            other: $other.val(),
          };


          var ret = [];
          for (var d in $data)
            ret.push(encodeURIComponent(d) + '=' + encodeURIComponent($data[d]));
          $queryString = ret.join('&');

          location.href = '/current-jobs?'+$queryString;

        }




      }
    });


});