/*
 *
 *   MAIN js file
 *
 */

$(document).ready(function () {



    $('.autocomplete-append').autocomplete({
        serviceUrl: '/autocomplete',
        max: 10,
        onSelect: function (suggestion) {
            //alert('You selected: ' + suggestion.value + ', ' + suggestion.data);
            location.href = '/device/'+suggestion.data.replace(/[^0-9\.]+/g, "");
            $('#url_site').val(suggestion.data);
            $('.site-nr').html(suggestion.data);
        }
    });

    $("form").each(function() {
        var csrf = $('#token').val();
        var tokenElement = $(document.createElement('input'));
        tokenElement.attr('type', 'hidden');
        tokenElement.attr('name', 'csrf');
        tokenElement.val(csrf);
        $(this).append(tokenElement);
    });

	$('.showform').click(function() {
		$('#signinForm').animate({
			height: "toggle",
			opacity: "toggle"
		}, "slow");
		$("#show_form").animate({
			height: "toggle",
			opacity: "toggle"
		}, "slow");
	}); 

	var url = window.location.pathname;
	var url_id = 'ul.nav a[href="'+ url +'"]';
	// Will only work if string in href matches with location
	$(url_id).parent().addClass('active');
	$(url_id).parent().parent().closest('li').addClass('active');	
	$(url_id).closest('ul').addClass('in');
		
	// Set lang code for i18next
	var lang_code = $('html').attr('lang');

	$.i18n.init({
		resGetPath: '/src/lang/__lng__.json',
		load: 'unspecific',
		fallbackLng: false,
		lng: lang_code
	}, function (t){
		$('#i18container').i18n();

		// Allow translation in tooltip
		$('[data-toggle="tooltip"]').tooltip({
            container: "body"
        });

        // Init select2 dropdown with autocomplete and i18n support
        $(".select2").select2({
            placeholder: i18n.t('placeholders.select'),
            allowClear: true
        });
		// Translate cookie msg
		$('#cookiemsg').html(i18n.t('cookie.msg'));
		$('#cookiemsglink').html(i18n.t('cookie.msg_link'));
	});

    // Add body-small class if window less than 768px
    if ($(this).width() < 769) {
        $('body').addClass('body-small'),
            $('body').removeClass('mini-navbar'),
            $('#menu_bar').removeClass('hidden'),
            $('#user_span').addClass('hidden'),
            $('#log_out_span').addClass('hidden')
    } else {
        $('body').addClass('mini-navbar'),
            $('body').removeClass('body-small'),
            $('#menu_bar').addClass('hidden'),
            $('#user_span').removeClass('hidden'),
            $('#log_out_span').removeClass('hidden')
    }

    // MetsiMenu
    $('#side-menu').metisMenu();

    // Collapse ibox function
    $('.collapse-link').on('click', function () {
        var ibox = $(this).closest('div.ibox');
        var button = $(this).find('i');
        var content = ibox.children('.ibox-content');
        content.slideToggle(200);
        button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
        ibox.toggleClass('').toggleClass('border-bottom');
        setTimeout(function () {
            ibox.resize();
            ibox.find('[id^=map-]').resize();
        }, 50);
    });

    // Close ibox function
    $('.close-link').on('click', function () {
        var content = $(this).closest('div.ibox');
        content.remove();
    });

    // Fullscreen ibox function
    $('.fullscreen-link').on('click', function () {
        var ibox = $(this).closest('div.ibox');
        var button = $(this).find('i');
        $('body').toggleClass('fullscreen-ibox-mode');
        button.toggleClass('fa-expand').toggleClass('fa-compress');
        ibox.toggleClass('fullscreen');
        setTimeout(function () {
            $(window).trigger('resize');
        }, 100);
    });

    // Close menu in canvas mode
    $('.close-canvas-menu').on('click', function () {
        $("body").toggleClass("mini-navbar");
        SmoothlyMenu();
    });

    // Run menu of canvas
    $('body.canvas-menu .sidebar-collapse').slimScroll({
        height: '100%',
        railOpacity: 0.9
    });

    // Open close right sidebar
    $('.right-sidebar-toggle').on('click', function () {
        $('#right-sidebar').toggleClass('sidebar-open');
    });

    // Initialize slimscroll for right sidebar
    $('.sidebar-container').slimScroll({
        height: '100%',
        railOpacity: 0.4,
        wheelStep: 10
    });

    // Open close small chat
    $('.open-small-chat').on('click', function () {
        $(this).children().toggleClass('fa-comments').toggleClass('fa-remove');
        $('.small-chat-box').toggleClass('active');
    });

    // Initialize slimscroll for small chat
    $('.small-chat-box .content').slimScroll({
        height: '234px',
        railOpacity: 0.4
    });

    // Small todo handler
    $('.check-link').on('click', function () {
        var button = $(this).find('i');
        var label = $(this).next('span');
        button.toggleClass('fa-check-square').toggleClass('fa-square-o');
        label.toggleClass('todo-completed');
        return false;
    });


    // Minimalize menu
    $('.navbar-minimalize').on('click', function () {
        $("body").toggleClass("mini-navbar");
        SmoothlyMenu();

    });

    // Tooltips demo
    $('.tooltip-demo').tooltip({
        selector: "[data-toggle=tooltip]",
        container: "body"
    });


    // Full height of sidebar
    function fix_height() {
        var heightWithoutNavbar = $("body > #wrapper").height() - 61;
        $(".sidebar-panel").css("min-height", heightWithoutNavbar + "px");

        var navbarHeigh = $('nav.navbar-default').height();
        var wrapperHeigh = $('#page-wrapper').height();

        if (navbarHeigh > wrapperHeigh) {
            $('#page-wrapper').css("min-height", navbarHeigh + "px");
        }

        if (navbarHeigh < wrapperHeigh) {
            $('#page-wrapper').css("min-height", $(window).height() + "px");
        }

        if ($('body').hasClass('fixed-nav')) {
            if (navbarHeigh > wrapperHeigh) {
                $('#page-wrapper').css("min-height", navbarHeigh - 60 + "px");
            } else {
                $('#page-wrapper').css("min-height", $(window).height() - 60 + "px");
            }
        }

    }

    fix_height();

    // Fixed Sidebar
    $(window).bind("load", function () {
        if ($("body").hasClass('fixed-sidebar')) {
            $('.sidebar-collapse').slimScroll({
                height: '100%',
                railOpacity: 0.9
            });
        }
    });

    // Move right sidebar top after scroll
    $(window).scroll(function () {
        if ($(window).scrollTop() > 0 && !$('body').hasClass('fixed-nav')) {
            $('#right-sidebar').addClass('sidebar-top');
        } else {
            $('#right-sidebar').removeClass('sidebar-top');
        }
    });

    $(window).bind("load resize scroll", function () {
        if (!$("body").hasClass('body-small')) {
            fix_height();
        }
    });

    $("[data-toggle=popover]")
        .popover();

    // Add slimscroll to element
    $('.full-height-scroll').slimscroll({
        height: '100%'
    })
});

window.paceOptions = {
    ajax: false,
    restartOnRequestAfter: false,
};

// Minimalize menu when screen is less than 768px
$(window).bind("resize", function () {
    if ($(this).width() < 769) {
        $('body').addClass('body-small'),
            $('body').removeClass('mini-navbar'),
            $('#menu_bar').removeClass('hidden'),
            $('#user_span').addClass('hidden'),
            $('#log_out_span').addClass('hidden')
    } else {
        $('body').addClass('mini-navbar'),
            $('body').removeClass('body-small'),
            $('#menu_bar').addClass('hidden'),
            $('#user_span').removeClass('hidden'),
            $('#log_out_span').removeClass('hidden')
    }
});

// Local Storage functions
// Set proper body class and plugins based on user configuration
$(document).ready(function () {
    if (localStorageSupport()) {

        var collapse = localStorage.getItem("collapse_menu");
        var fixedsidebar = localStorage.getItem("fixedsidebar");
        var fixednavbar = localStorage.getItem("fixednavbar");
        var boxedlayout = localStorage.getItem("boxedlayout");
        var fixedfooter = localStorage.getItem("fixedfooter");

        var body = $('body');

        if (fixedsidebar == 'on') {
            body.addClass('fixed-sidebar');
            $('.sidebar-collapse').slimScroll({
                height: '100%',
                railOpacity: 0.9
            });
        }

        if (collapse == 'on') {
            if (body.hasClass('fixed-sidebar')) {
                if (!body.hasClass('body-small')) {
                    body.addClass('mini-navbar');
                }
            } else {
                if (!body.hasClass('body-small')) {
                    body.addClass('mini-navbar');
                }

            }
        }

        if (fixednavbar == 'on') {
            $(".navbar-static-top").removeClass('navbar-static-top').addClass('navbar-fixed-top');
            body.addClass('fixed-nav');
        }

        if (boxedlayout == 'on') {
            body.addClass('boxed-layout');
        }

        if (fixedfooter == 'on') {
            $(".footer").addClass('fixed');
        }
    }
});

// check if browser support HTML5 local storage
function localStorageSupport() {
    return (('localStorage' in window) && window['localStorage'] !== null)
}

// For demo purpose - animation css script
function animationHover(element, animation) {
    element = $(element);
    element.hover(
        function () {
            element.addClass('animated ' + animation);
        },
        function () {
            //wait for animation to finish before removing classes
            window.setTimeout(function () {
                element.removeClass('animated ' + animation);
            }, 2000);
        });
}

function SmoothlyMenu() {
    if (!$('body').hasClass('mini-navbar') || $('body').hasClass('body-small')) {
        // Hide menu in order to smoothly turn on when maximize menu
        $('#side-menu').hide();
        // For smoothly turn on menu
        setTimeout(
            function () {
                $('#side-menu').fadeIn(400);
            }, 200);
    } else if ($('body').hasClass('fixed-sidebar')) {
        $('#side-menu').hide();
        setTimeout(
            function () {
                $('#side-menu').fadeIn(400);
            }, 100);
    } else {
        // Remove all inline style from jquery fadeIn function to reset menu state
        $('#side-menu').removeAttr('style');
    }
}

// Dragable panels
function WinMove() {
    var element = "[class*=col]";
    var handle = ".ibox-title";
    var connect = "[class*=col]";
    $(element).sortable(
        {
            handle: handle,
            connectWith: connect,
            tolerance: 'pointer',
            forcePlaceholderSize: true,
            opacity: 0.8
        })
        .disableSelection();
}

// External popupwindow
function popupWindow(url, title, w, h) {
	// Create reference to new window
	var newWindow = window.open(url, title, 'titlebar=no, toolbar=no, location=no, directories=no, status=no, menubar=no, resizable=yes, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
	// Position secondary screen
	var left = 2100;
	var top = 100;
	
	if(newWindow.location.href === 'about:blank')
	{
		newWindow.location.href = url
	}
	//console.log(newWindow.location.href);
	return newWindow;
}

