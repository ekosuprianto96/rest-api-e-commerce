<script>
    $(function() {
        $(document.body).click(function(event) {
            target = $(event.target).attr('data-target');
            toggleMenuMobile('menuMobile', target);
        })
    });

    function toggleMenuMobile(target, data) {
        console.log(data)
        if(data == target) {
            if($('#'+target).hasClass('hide-menu')) {
                $('#'+target).removeClass('hide-menu').addClass('show-menu');
                $('#wrapperMenu').scrollTop(0);
                return true;
            }else {
                $('#'+target).removeClass('show-menu').addClass('hide-menu');
                return false;
            }
        }else {
            $('#'+target).removeClass('show-menu').addClass('hide-menu');
            return false;
        }
    }
</script>