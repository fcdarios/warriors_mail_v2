(function($) {
    "use strict";

    // Add active state to sidbar nav links
    var path = window.location.href; // because the 'href' property of the DOM element is the absolute path
        $("#layoutSidenav_nav .sb-sidenav a.nav-link").each(function() {
            if (this.href === path) {
                $(this).addClass("active");
            }
        });

    // Toggle the side navigation
    $("#sidebarToggle").on("click", function(e) {
        e.preventDefault();
        $("body").toggleClass("sb-sidenav-toggled");
    });
})(jQuery);

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

$("#message_actions_check").change(function() {
    let check = this.checked;

    let checkboxs_emails = document.querySelectorAll("input[type='checkbox']")
    
    for(i=0; i<checkboxs_emails.length; i++) {
        if (checkboxs_emails[i].type=='checkbox') {
            if ( checkboxs_emails[i].id !== 'message_actions_check') {
                let checkbox_name = checkboxs_emails[i].name;
                checkbox_name = checkbox_name.split("-");
                let [ check_name ] = checkbox_name;
                if( check_name === 'check')
                    checkboxs_emails[i].checked = check;
            }
        }
    }
    
});
