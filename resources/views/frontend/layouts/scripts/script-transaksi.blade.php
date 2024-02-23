<script>
    $(function() {
        const listTranskasi = $('.list-transaksi');

        $.each(listTranskasi, function(index, value) {
            $(value).click(function(event) {
                // console.log(value., event.target.id)
                $.each($('.arrow-transaksi'), function(index2, value2) {
                    if(index != index2) {
                        $(this).rotate(0);
                    }
                })
                $.each($('.detail-transaksi'), function(index2, value2) {
                    if(index != index2) {
                        $(this).removeClass('show');
                        $(this).hide();
                    }
                })

                $('#buttonArrowTransaksi_'+index).rotate();
                handleShowDetailTranskasi('#detailTransaksi_'+index);
            })
        })
    });

    function handleShowDetailTranskasi(target) {
        let status = false;
        if($(target).hasClass('show')) {
            $(target).removeClass('show');
            $(target).hide();
            
            status = true;
            return;
        }
        
        $(target).addClass('show');
        $(target).show();

        return;
    }

</script>