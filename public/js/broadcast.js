window.Echo.private('EventTriggerChannel')
    .listen('.EventTrigger', (e) => {
        flash("There is a new " + e.instanceType + " order");
        $('#'+ e.instanceType + '-menu.notifiable').addClass("has-notification");
    });
