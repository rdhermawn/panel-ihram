/* custom */
$(document).ready(function() {
    $('#players_btn').click(function(event) {
        $('.rt_btn').removeClass('rt_btn_active');
        $('.rt_table').removeClass('active');
        $('.rt_table.players').toggleClass('active');
        $('#players_btn').toggleClass('rt_btn_active');
        $('.newbies_date').removeClass('active');
    });
    $('#guilds_btn').click(function(event) {
        $('.rt_btn').removeClass('rt_btn_active');
        $('.rt_table').removeClass('active');
        $('.rt_table.guilds').toggleClass('active');
        $('#guilds_btn').toggleClass('rt_btn_active');
        $('.newbies_date').removeClass('active');
    });
    $('#newbies_btn').click(function(event) {
        $('.rt_btn').removeClass('rt_btn_active');
        $('.rt_table').removeClass('active');
        $('.rt_table.newbies').toggleClass('active');
        $('#newbies_btn').toggleClass('rt_btn_active');
        $('.newbies_date').toggleClass('active');
    });
    $('.btns_switch .btn').click(function(event) {
        $('.btns_switch .btn').toggleClass('active');
        $('.rating_wrapper').toggleClass('active');
        $('.rating_wrapper.daily').removeClass('active');
    });
    $('.btns_switch .btn_blue').click(function(event) {
        $('.btns_switch .btn').removeClass('active');
        $('.btns_switch .btn_blue').toggleClass('active');
        $('.rating_wrapper').removeClass('active');
        $('.rating_wrapper.daily').toggleClass('active');
    });
});