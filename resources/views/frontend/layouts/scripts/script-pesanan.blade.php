<script>
    type = '';

    $(function() {
        
        $('#inputSearch').keyup(function() {
            const value = $(this).val();

            renderSearchElement(value, type);
        });

        $('#produkAuto').click(function(event) {
            assignTypeProduk('AUTO');
            $(this).addClass('text-slate-50 bg-blue-400').removeClass('text-black');
            $('#produkManual').removeClass('text-slate-50 bg-blue-400').addClass('text-black');
            $('#semuaProduk').removeClass('text-slate-50 bg-blue-400').addClass('text-black');

            renderElementWithType(type);
        });

        $('#produkManual').click(function(event) {
            assignTypeProduk('MANUAL');
            $(this).addClass('text-slate-50 bg-blue-400').removeClass('text-black');
            $('#produkAuto').removeClass('text-slate-50 bg-blue-400').addClass('text-black');
            $('#semuaProduk').removeClass('text-slate-50 bg-blue-400').addClass('text-black');

            renderElementWithType(type);
        });

        $('#semuaProduk').click(function(event) {
            assignTypeProduk('');
            $(this).addClass('text-slate-50 bg-blue-400').removeClass('text-black');
            $('#produkAuto').removeClass('text-slate-50 bg-blue-400').addClass('text-black');
            $('#produkManual').removeClass('text-slate-50 bg-blue-400').addClass('text-black');

            renderSearchElement('', type)
        });

        $('#filterTypeProduk').click(function() {
            if($(this).find('ul').hasClass('active')) {
                $(this).find('ul').hide().removeClass('active');

                return;
            }

            $(this).find('ul').show().addClass('active');
            return;
        })
    });

    function renderSearchElement(key, type) {
        const listPesanan = $('.list-pesanan');

        listPesanan.each(function(index, value) {
            const element = $(value);
            if(key != '') {
                const namaProduk = element.find('.nama-produk').text()?.toLowerCase();
                const typeProduk = element.find('.type-produk').text()?.toLowerCase();
                if(namaProduk.includes(key.toLowerCase()) && typeProduk.includes(type.toLowerCase())) {
                    element.show().addClass('active');
                }else {
                    element.hide().removeClass('active');
                }
    
                if($('.list-pesanan.active').length <= 0) {
                    $('#tidakAdaProduk').show();
                }else {
                    $('#tidakAdaProduk').hide();
                }
            }else {
                if(type === '') {
                    element.show().addClass('active');
                }else {
                    renderElementWithType(type);
                }
            }
        });

    }

    function renderElementWithType(type) {
        const listPesanan = $('.list-pesanan');

        listPesanan.each(function(index, value) {
            const element = $(value);
            const typeProduk = element.find('.type-produk').text()?.toLowerCase();
            if(typeProduk.includes(type.toLowerCase())) {
                element.show().addClass('active');
            }else {
                element.hide().removeClass('active');
            }

            if($('.list-pesanan.active').length <= 0) {
                $('#tidakAdaProduk').show();
            }else {
                $('#tidakAdaProduk').hide();
            }
        });

    }

    function assignTypeProduk(key) {
        type = key;
    }
</script>