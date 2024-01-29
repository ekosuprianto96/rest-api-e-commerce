<li class="nav-item d-flex align-items-center py-0 position-relative {{ $activeClass }}">
    <a class="nav-link text-nowrap py-2" href="{{ $route }}">
        <i class="{{ $icon }}"></i>
        <span class="text-nowrap">{{ $menuName }}</span>
    </a>
    {{-- @dd($alias) --}}
    @if(isset($notif) && $notif)
        <span id="{{ $alias ?? '' }}" class="text-light {{ $countNotif > 0 ? 'd-flex' : 'd-none' }} justify-content-center align-items-center position-absolute bg-danger" style="font-size: 0.6em;right: -9px;;width:20px;height:20px;border-radius: 50%;">{{ $countNotif > 0 ? $countNotif : 0  }}</span>
    @endif
    @if(isset($alias))
        @if(\Route::current()->getName() != str_replace('/', '.', $url))
        <script>
            $(function() {
                var pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
                    cluster: "ap1",
                });
        
                var channel = pusher.subscribe("notification");
                channel.bind('{{ $alias ?? '' }}', function(response) {
                    const audio = new Audio();
                    audio.src = '{{ asset("assets/admin/audio/notification.wav") }}';
                    audio.play();
        
                    let countMessage = parseInt($('#count_message').text());
                
                    countMessage += 1;
                    $('#count_message').text(countMessage);
                    $('#wrapper_notification').append(`
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <div class="mr-3">
                                <div class="icon-circle bg-primary">
                                    <i class="fas fa-file-alt text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500">December 12, 2019</div>
                                <span class="font-weight-bold">A new monthly report is ready to download!</span>
                            </div>
                        </a>
                    `);
                    $.toast({
                        heading: 'Order Masuk',
                        text: response,
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'success'
                    });
                    counterNotification('{{ $alias ?? '' }}');
                    console.log('order:', response);
                })
            })
        
            function counterNotification(target) {
                const elTarget = $(`#${target}`);
                console.log(target, elTarget);
                if(elTarget != undefined) {
                    let currentCount = parseInt($(`#${target}`).text());
                    currentCount += 1;
                    $(`#${target}`).text(currentCount);
        
                    if($(`#${target}`).hasClass('d-none')) {
                        $(`#${target}`).removeClass('d-none');
                        $(`#${target}`).addClass('d-flex');
                    }
                }
            }
        </script>
    @endif
    @endif
</li>