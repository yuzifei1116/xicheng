function carModel(id) {
    var bg = $('.model-bg'),
        model = $('.card-model'),
        close = $('.model-close, .model-bg'),
        options = $('.options span');
    var _that = id;
    
    bg.addClass('on');
    model.addClass('up');
    bg.css('z-index', '999');
    $('.footer-car').on('click',function() {
        off();
        if(_that != undefined){
            _that.addClass('active');
        }
    })

    close.on('click', function() {
        off();
    });
    function off() {
        bg.removeClass('on');
        model.removeClass('up');
        bg.css('z-index', '-1');
    }
    options.on('click', function() {
        $(this).addClass('on').siblings().removeClass('on');
    });
};
$('.card-model,.model-bg,.thickness-wrapper').on("touchmove",function(event){
    event.preventDefault;
}, false);
// 动画
$('.sign_in-hock').on('click',function() {
    $('.model-bg').addClass('on');
    $('.thickness-wrapper').addClass('active');
});
$('.confirm-hock').on('click', function(){
    $('.model-bg').removeClass('on');
    $('.thickness-wrapper').removeClass('active');
})