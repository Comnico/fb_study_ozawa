<html>

<head>
    <meta charset="utf8">
    <title>
        FB Study
    </title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <div class="header">
        <div id="title">FB Study</div>
    </div>
    <div class="contents">
        ソーシャルアカウントでログイン
        <br/>
        <div id="button"><a href="login.php">Facebookログイン</a></div>
        <script>
            window.intercomSettings = {
                app_id: "bxs0xbfr",
                name: "Nikola Tesla", // Full name
                email: "nikola@example.com", // Email address
                created_at: 1312182000 // Signup date as a Unix timestamp
            };
        </script>
        <script>
            (function() {
                var w = window;
                var ic = w.Intercom;
                if (typeof ic === "function") {
                    ic('reattach_activator');
                    ic('update', intercomSettings);
                } else {
                    var d = document;
                    var i = function() {
                        i.c(arguments)
                    };
                    i.q = [];
                    i.c = function(args) {
                        i.q.push(args)
                    };
                    w.Intercom = i;

                    function l() {
                        var s = d.createElement('script');
                        s.type = 'text/javascript';
                        s.async = true;
                        s.src = 'https://widget.intercom.io/widget/bxs0xbfr';
                        var x = d.getElementsByTagName('script')[0];
                        x.parentNode.insertBefore(s, x);
                    }
                    if (w.attachEvent) {
                        w.attachEvent('onload', l);
                    } else {
                        w.addEventListener('load', l, false);
                    }
                }
            })()
        </script>


    </div>

    <div class="footer">
        &copy; 2015 comnico inc.
    </div>

</body>

</html>