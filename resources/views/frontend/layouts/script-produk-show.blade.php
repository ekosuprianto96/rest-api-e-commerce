<script>
    // Jika anda membagikan link Affiliate ini, maka anda akan mendapatkan komisi Affiliate ketika ada member yang membeli produk ini menggunakan link Affiliate yang anda bagikan
    $(function() {
        $('#buttonAddCart').click(function(event) {
            const kodeproduk = $(this).attr('data-produk');
            postCart(kodeproduk).then(response => {
                const {status, error, message, detail} = response;
                if(status && !error) {
                    Swal.fire({
                        title: "Sukses!",
                        text: message,
                        icon: "success",
                        confirmButtonClass: "bg-red-400",
                        buttonsStyling: true,
                        showCloseButton: true,
                    });

                    setCountCart(1);
                    return false;
                }

                Swal.fire({
                    title: "Gagal!",
                    text: message,
                    icon: "warning",
                    confirmButtonClass: "bg-red-400",
                    buttonsStyling: true,
                    showCloseButton: true,
                });
                return false;
            }).catch(err => {
                console.log(err);
            })
        })
    })

    function postCart(kodeproduk) {
        return new Promise((resolve, reject) => {
            const _token = '{{ csrf_token() }}';
            $.post("{{ route('user.keranjang.store') }}", {kodeproduk, _token})
             .done(function(response) {
                resolve(response);
             })
             .fail(function(err) {
                reject(err);
             })
        })
    }

    function setCountCart(value = 1) {
        let countCartMobile = parseInt($('#countCartMobile').text());
        let countCartDesktop = parseInt($('#countCartDesktop').text());

        countCartDesktop += value;
        countCartMobile += value;

        $('#countCartMobile').text(countCartMobile);
        $('#countCartDesktop').text(countCartDesktop);

        return true;
    }
</script>