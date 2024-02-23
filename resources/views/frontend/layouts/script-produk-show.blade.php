<script>
    // Jika anda membagikan link Affiliate ini, maka anda akan mendapatkan komisi Affiliate ketika ada member yang membeli produk ini menggunakan link Affiliate yang anda bagikan
    let itemsProdukToko = $('.produk-toko').children().length;
    let itemsProdukSerupa = $('.produk-serupa').children().length;
    console.log(itemsProdukSerupa)
    $(document).ready(function(){
        $(".produk-serupa").owlCarousel({
            items:2,
            margin: 8,
            autoplay: true,
            autoplayTimeout: 5000,
            autoWidth:true,
            responsive:{
                0:{
                    items:2
                },
                600:{
                    items: 3
                },
                1000:{
                    items: 5
                }
            }
        });
    });
    $(document).ready(function(){
        $(".produk-toko").owlCarousel({
            items:2,
            margin: 8,
            autoplay: true,
            autoplayTimeout: 5000,
            autoWidth:true,
            responsive:{
                0:{
                    items:2
                },
                600:{
                    items:3
                },
                1000:{
                    items: 5
                }
            }
        });
    });
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