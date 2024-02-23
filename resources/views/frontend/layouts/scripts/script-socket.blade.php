@php
    $user = null;

    if(Auth::check()) {
        $user = auth::user();
    }

@endphp
<script src="{{ env('URL_SOCKET_LINGGA') }}/socket.io/socket.io.js"></script>
<script>
    let socket = io.connect('{{ env('URL_SOCKET_LINGGA') }}');
    $(function() {
        socket.on('order::'+'{{ isset($user) ? $user->toko->kode_toko : null }}', function(response) {
            notifyMe('Ada order yang masuk');
            console.log('Ada Order Masuk')
        });
    });

    function notifyMe(message) {
        if (!("Notification" in window)) {
            // Check if the browser supports notifications
            alert("This browser does not support desktop notification");
        } else if (Notification.permission === "granted") {
            // Check whether notification permissions have already been granted;
            // if so, create a notification
            const options = {
                body: message,
                icon: '{{ config('app.logo') }}'
            }
            const notification = new Notification("Pesan Masuk", options);
            // …
        } else if (Notification.permission !== "denied") {
            // We need to ask the user for permission
            Notification.requestPermission().then((permission) => {
            // If the user accepts, let's create a notification
            if (permission === "granted") {
                const notification = new Notification("Hi there!");
                // …
            }
            });
        }

        // At last, if the user has denied notifications, and you
        // want to be respectful there is no need to bother them anymore.
    }
</script>