window.Echo.private('InboundCreatedEvent')
        .listen('.InboundCreated', (e) => {
            flash("New inbound order created");
            $('.dot1').show(0);
        });

window.Echo.private('OutboundCreatedEvent')
        .listen('.OutboundCreated', (e) => {
            flash("New Outbound order created");
            $('.dot2').show(0);
        });

$(function() {
      $('#inbound-menu').click(function() {
        $('.dot1').hide(0);
        });
    });  

$(function() {
      $('#outbound-menu').click(function() {
        $('.dot2').hide(0);
        });
    });  