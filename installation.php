<!DOCTYPE html>
<html lang="en">

<head>
    <title> Appointment Schedule System Installation</title>

    <script src="public/js/jquery-2.0.3.min.js"></script>

    <!-- Bootstrap Core CSS -->
    <link media="all" type="text/css" rel="stylesheet" href="public/css/bootstrap.min.css">


    <!-- Custom CSS -->
    <link media="all" type="text/css" rel="stylesheet" href="public/css/sb-admin-2.css">


    <!-- Custom Fonts -->
    <link media="all" type="text/css" rel="stylesheet" href="public/font-awesome/css/font-awesome.min.css">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-60038966-1', 'auto');
        ga('send', 'pageview');

    </script>
</head>

<body>

<div class="container">
<div class="row">
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- Category - 1 (www.kingpabel.com) -->
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-3284345266094241"
             data-ad-slot="1234392416"
             data-ad-format="auto"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Kingpabel Scheduler Installation</h3>
                </div>
                <div class="panel-body">
                    <form method="POST" role="form">
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="Host Name" name="host_name" type="text"
                                       autofocus required>
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="Database Name" name="database_name" type="text"
                                       required>
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="User Name" name="user_name" type="text" required>
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="Password" name="password" type="password">
                            </div>

                            <div class="form-group input-group input-group-sm">
                                <input class="form-control" placeholder="Project URL" name="project_url" type="text">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" >/public
                                    </button>
                                </span>
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-lg btn-success btn-block" value="Install">
                            </div>
                            <!-- Change this to a button or input when using this as a form -->

                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Core JavaScript -->
<script src="public/js/bootstrap.min.js"></script>


<!-- Custom Theme JavaScript -->
<script src="public/js/sb-admin-2.js"></script>

</body>

</html>
<?php
if ($_POST) {
    $envFile = file_get_contents('.env');

    if (isset($_POST['host_name']) && $_POST['host_name'])
        $envFile = str_replace('DB_HOST=localhost', "DB_HOST={$_POST['host_name']}", $envFile);

    if (isset($_POST['database_name']) && $_POST['database_name'])
        $envFile = str_replace('DB_DATABASE=homestead', "DB_DATABASE={$_POST['database_name']}", $envFile);

    if (isset($_POST['user_name']) && $_POST['user_name'])
        $envFile = str_replace('DB_USERNAME=homestead', "DB_USERNAME={$_POST['user_name']}", $envFile);

    if (isset($_POST['password']) && $_POST['password'])
        $envFile = str_replace('DB_PASSWORD=secret', "DB_PASSWORD={$_POST['password']}", $envFile);

    file_put_contents('.env', $envFile);

    if (isset($_POST['project_url']) && $_POST['project_url']) {
        file_get_contents('index.php');
        file_put_contents('index.php', "<?php
    header('Location: {$_POST['project_url']}/public');
    ");
    }
}
