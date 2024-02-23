<script>
    $(function() {
        $(document.body).click(function(event) {
            target = $(event.target).attr('data-target');
            toggleMenuMobile('menuMobile', target);
            toggleDropKategori('dropDownKategori', target);
            return;
        })

        $('#dropDownKategori').mouseleave(toggleDropKategori)
    });
    
    function toggleDropKategori(data = null, target = null) {
        var dropDownKategori = $('#dropDownKategori');
        if(data && target && (data == target)) {

            if (dropDownKategori.hasClass('active')) {
                // Jika sedang diperluas, animasikan kembali ke tinggi 0
                dropDownKategori.animate({height: 0}, 200, function() {
                    dropDownKategori.removeClass('active');
                    setTimeout(() => {
                        dropDownKategori.hide();
                    }, 100);
                });
            } else {
                // Jika tidak diperluas, ambil tinggi kontennya dan animasikan ke tinggi tersebut
                dropDownKategori.css('height', 'auto');
                var contentHeight = dropDownKategori.outerHeight() + "px";
                dropDownKategori.show();
                dropDownKategori.css('height', 0).animate({height: contentHeight}, 300, function() {
                    dropDownKategori.addClass('active');
                });
            }
        }else {
            dropDownKategori.animate({height: 0}, 200, function() {
                dropDownKategori.removeClass('active');
                setTimeout(() => {
                    dropDownKategori.hide();
                }, 100);
            });
        }
    }
    function toggleMenuMobile(target, data) {
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