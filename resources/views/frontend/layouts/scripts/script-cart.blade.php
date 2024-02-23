<script>
    $(function() {
         itemCart = $('.item-cart');
         listPayment = $('.list-payment');
         checkListPaymentMobile = $('.checklist_payment_mobile');
         checkListPaymentDesktop = $('.checklist_payment_desktop');
         typePayment = null;
         bankTransfer = null;

        $.each(itemCart, function(index, value) {
            $(value).click(function(event) {
                if($(`#checklist_${index}`)[0].checked) {
                    $(`#checklist_${index}`).unCheck();
                    return true;
                }

                $(`#checklist_${index}`).check();
                return true;
            })
        })

        $.each(listPayment, function(index, value) {
            $(value).click(function(event) {
                $(this).unCheckedAll('checkbox-payment');

                if($(this).attr('data-check') == 'linggaPay') {
                    $('#linggaPay').check();

                    assignPayment('linggaPay');

                    $(this).unCheckedAll('chekbox-list-payment');
                    initRenderManualTransfer('close');

                    return;
                }else if ($(this).attr('data-check') == 'manualTransfer') {
                    $('#manualTransfer').check();
                    
                    assignPayment('manual');
                    
                    initRenderManualTransfer();
                    return;
                }else {

                    assignPayment('gateway');
                    $('#gateway').check();
                }

                $.fn.modalPayment('.modal-payment').hide();
            })
        });

        $('#buttonCheckout').click(function(event) {
            const itemProduk = $('.checkbox-cart');
            const kodeProduk = [];

            $.each(itemProduk, function(index, value) {
                const attrData = $(value).attr('data-produk');
                if($(value)[0].checked) {
                    kodeProduk.push(attrData);
                }
            });
            

            // Validate Type Payment
            const validateTypePayment = validateData({
                message: 'Metode pembayaran belum dipilih.',
                type: 'required',
                data: 'string',
                value: typePayment
            }, function(result) {
                if(!result.status) {
                    Swal.fire({
                        title: "Gagal!",
                        text: result.message,
                        icon: "warning",
                        confirmButtonClass: "bg-red-400",
                        buttonsStyling: true,
                        showCloseButton: true,
                    });

                    return result;
                }
            });

            if(!validateTypePayment.status) {
                return false;
            }

            // Validate Data Produk
            const validateKodeProduk = validateData({
                message: 'Produk belum dipilih.',
                type: 'required',
                data: 'array',
                value: kodeProduk
            }, function(result) {
                if(!result.status) {
                    Swal.fire({
                        title: "Gagal!",
                        text: result.message,
                        icon: "warning",
                        confirmButtonClass: "bg-red-400",
                        buttonsStyling: true,
                        showCloseButton: true,
                    });

                    return result;
                }
            });

            if(!validateKodeProduk.status) {
                return false;
            }

            let validateBank = {
                    status: true
            };
            if(typePayment == 'manual') {
                // Validate Data Produk
                validateBank = validateData({
                    message: 'Bank belum dipilih.',
                    type: 'required',
                    data: 'string',
                    value: bankTransfer
                }, function(result) {
                    if(!result.status) {
                        Swal.fire({
                            title: "Gagal!",
                            text: result.message,
                            icon: "warning",
                            confirmButtonClass: "bg-red-400",
                            buttonsStyling: true,
                            showCloseButton: true,
                        });

                        return result;
                    }
                });

                if(!validateBank.status) {
                    return false;
                }
            }
            
            const objectForm = createDataForm(kodeProduk, typePayment, bankTransfer);
            
            if(validateTypePayment.status && validateKodeProduk.status && validateBank.status) {
                postCart(objectForm).then(response => {
                    const { status, error, message, detail} = response;

                    if(status && !error) {
                        Swal.fire({
                            title: "Sukses!",
                            text: message,
                            icon: "success",
                            allowOutsideClick: false,
                            buttonsStyling: true,
                            showCloseButton: true,
                        }).then(result => {
                            // kirim socket order
                            // const dataOrder = result.dataOrder;
                            // socket.emit('order::'+dataOrder.kode_toko, dataOrder);

                            if(result.isConfirmed) {
                                window.location.href = detail.redirect;
                            }else {
                                return false;
                            }
                        });
                    }else {
                        Swal.fire({
                            title: "Gagal!",
                            text: message,
                            icon: "warning",
                            confirmButtonClass: "bg-red-400",
                            allowOutsideClick: false,
                            buttonsStyling: true,
                            showCloseButton: true,
                        });

                        return false;
                    }
                }).catch(err => {
                    Swal.fire({
                        title: "Gagal!",
                        text: 'Terjadi Kesalahan System',
                        icon: "error",
                        confirmButtonClass: "bg-red-400",
                        buttonsStyling: true,
                        showCloseButton: true,
                    });
                });
            }

        });

        const screen = window.innerWidth;
        if(screen <= 780) {
            $.each(checkListPaymentMobile, function(index, value) {
                
                $(value).click(function(event) {
                    const bank = $(value).attr('data-payment');

                    assignBank(bank);
                    $(this).unCheckedAll('chekbox-list-payment');

                    $(this).find(':last-child').check();

                    initRenderManualTransfer('close');
                })
            });
        }else {
            $.each(checkListPaymentDesktop, function(index, value) {
                
                $(value).click(function(event) {
                    const bank = $(value).attr('data-payment');

                    assignBank(bank);

                    $(this).unCheckedAll('chekbox-list-payment');

                    $(this).find(':last-child').check();

                    initRenderManualTransfer('close');
                })
            });
        }

        $('#selectAllItemCart').click(function(event) {
            $(this).checkedAll('checkbox-cart');
        });

        $('#close-modal').click(function(event) {
            initRenderManualTransfer('close');
        })
    });

    function initRenderManualTransfer(type = 'open') {
        const screen = window.innerWidth;

        if(type == 'open') {
            if(screen <= 780) {
                showPayment('#listManualTransferMobile');
                return;
            }

            showPayment('#listManualTransferDesktop');
            return;
        }

        if(type == 'close') {
            if(screen <= 780) {
                hidePayment('#listManualTransferMobile');
                return;
            }

            hidePayment('#listManualTransferDesktop');
            return;
        }

    }

    function showPayment(selector) {
        $(selector).show();

        return;
    }

    function hidePayment(selector) {
        $(selector).hide();
        return;
    }

    function assignPayment(type) {
        typePayment = type;
    }

    function assignBank(bank) {
        if(typePayment == 'manual') {
            bankTransfer = bank;
        }

        return;
    }

    function createDataForm(arrayProduk = [], typePayment = 'manual', bank = null) {
        return {
            kode_produk: arrayProduk,
            typePayment,
            bank,
            _token: '{{ csrf_token() }}'
        };
    }

    function postCart(data) {
        return new Promise((resolve, reject) => {
            $.post("{{ route('user.keranjang.checkout') }}", data)
            .done(function(response) {
                resolve(response);
            })
            .fail(function(err) {
                reject(err);
            })
        })
    }

    function validateData(params = {}, callback = null) {
        const {value, type, data, message} = params;
        if(type == 'required') {
            if(data === 'array') {
                if(value.length <= 0) {
                    params.status = false;
                }else {
                    params.status = true;
                }
            }else if(data === 'string') {
                if(value === null || value === '') {
                    params.status = false;
                }else {
                    params.status = true;
                }
            }
            
            if(callback) {
                callback(params);
            }

            return params;
        }

        params.status = true;
        if(callback) {
            callback(params);
        }

        return params;

    }

    function deleteCart(event) {
        const idCart = $(event).attr('data-id-cart');
        
        destroyCart(idCart).then(response => {
            const {status, error, message, detail} = response;

            if(status && !error) {
                Swal.fire({
                    title: "Sukses!",
                    text: message,
                    icon: "success",
                    allowOutsideClick: false,
                    buttonsStyling: true,
                    showCloseButton: true,
                });

                $(`#cart_${idCart}`).remove();

                const itemCart = $('.item-cart');
                if(itemCart.length <= 0) {
                    $('#wrapperCart').html(emptyCart());
                }
                
                setCountCart(itemCart.length)
                return;
            }

            Swal.fire({
                title: "Gagal!",
                text: message,
                icon: "warning",
                allowOutsideClick: false,
                buttonsStyling: true,
                showCloseButton: true,
            });
        }).catch(err => {
            const {message} = err;
            Swal.fire({
                title: "Gagal!",
                text: message,
                icon: "error",
                allowOutsideClick: false,
                buttonsStyling: true,
                showCloseButton: true,
            });
        })
    }

    function destroyCart(id) {
        return new Promise((resolve, reject) => {
            $.post('{{ route("user.cart.dsstroy") }}', {
                id,
                _token: '{{ csrf_token() }}'
            })
            .done(response => {
                resolve(response);
            }).fail(err => {
                reject(err);
            });
        })
    }

    function emptyCart() {
        return `<div class="w-full h-[400px] overflow-hidden">
                    <div class="flex flex-col justify-center items-center p-6 h-full rounded-lg">
                        <img src="{{ asset('assets/frontend/images/no-cart.svg') }}" class="h-[200px]" alt="Keranjang Anda Masih Kosong">
                        <div class="py-3 text-center">
                            <span class="font-bold text-lg">Keranjang Anda Masih Kosong</span>
                        </div>
                        <a href="{{ route('home') }}" class="px-6 text-center block py-2 bg-blue-600 text-slate-50 rounded-md">Lihat Produk</a>
                    </div>
                </div>`;
    }

    function setCountCart(value) {
        $('#countCartMobile').text(value);
        $('#countCartDesktop').text(value);

        return true;
    }
</script>