$(document).ready(()=>{
    pageGrabber($('.admin:first').attr('id'), $('.card .header'), 'after');
    $('.admin').click(function(){
        if($('.card .header').first().next().attr('id') !== this.id){
            $.when($('.card .header').first().next().slideUp('slow')).done(()=>{
                $('.card .header').first().next().remove();
                $('.card .header').first().next().remove();
                pageGrabber(this.id, $('.card .header'), 'after');
            });
        }
    });
});