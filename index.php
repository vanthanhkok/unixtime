<?php
    $expires = time() + (60 * 60 * 24 * 7); // one week

    if (!empty($_POST['tz'])) {
        $_COOKIE['tz'] = $_POST['tz'];
    } elseif (empty($_COOKIE['tz'])) {
        $_COOKIE['tz'] = 'America/Chicago';
    }
    $tz = $_COOKIE['tz'];
    setcookie("tz", $tz, $expires);

    if (!empty($_POST['textform'])) {
        $_COOKIE['textform'] = $_POST['textform'];
    } elseif (empty($_COOKIE['textform'])) {
        $_COOKIE['textform'] = 'Y-m-d H:i:s';
    }
    $textform = $_COOKIE['textform'];
    setcookie("textform", $textform, $expires);

    date_default_timezone_set($tz);
    $timezones = DateTimeZone::listIdentifiers();

    if (empty($_POST['unixtime'])) {
        $_POST['unixtime']   = time();
    }

    if (empty($_POST['texttime'])) {
        $_POST['texttime'] = date($textform, time());
    }

    if (!empty($_POST['totexttime'])) {
        $unixtime = intval($_POST['unixtime']);
        $texttime = date($textform, $unixtime);
    } else {
        $texttime = $_POST['texttime'];
        $unixtime = strtotime($texttime);
    }

    $midnight = strtotime(date('Y-m-d') . ' 00:00:00');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Unixtime</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Tools dealing with unix timestamps">
        <meta name="author" content="Andrew Shell">

        <!-- Le styles -->
        <link href="//netdna.bootstrapcdn.com/bootswatch/3.1.0/flatly/bootstrap.min.css" rel="stylesheet">

        <style type="text/css">
            html, body {
                height: 100%;
                margin: 0;
                padding: 0;
            }
            #wrap {
                min-height: 100%;
                height: auto;
                /* Negative indent footer by its height */
                margin: 0 auto -60px;
                /* Pad bottom by footer height */
                padding: 0 0 60px;
            }
            /* Set the fixed height of the footer here */
            #footer {
                height: 60px;
                background-color: #f5f5f5;
            }
            .container .text-muted {
                margin: 20px 0;
            }
        </style>

        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!-- Le fav and touch icons -->
        <link rel="shortcut icon" href="/favicon.ico">
    </head>

    <body>
        <div id="wrap">

        <form role="form" method="post">

            <div class="container">

                <div class="page-header">
                    <h1><span class="glyphicon glyphicon-time"></span> Unixtime</h1>
                </div>

                <fieldset>
                    <legend>Settings</legend>
                    <div class="row">
                        <div class="form-group col-xs-12 col-md-6">
                            <label for="tz">Timezone</label>
                            <select name="tz" id="tz" class="form-control">
                            <?php foreach ($timezones as $tz): ?>
                                <option value="<?php echo $tz; ?>"<?php echo (0 == strcmp($_COOKIE['tz'], $tz) ? ' selected="selected"' : ''); ?>><?php echo htmlentities($tz, ENT_COMPAT, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-md-6">
                            <label for="textform">Text Format</label>
                            <input type="text" class="form-control" name="textform" id="textform" value="<?php echo $textform; ?>" />
                            <span class="help-block">Format of time output (see <a href="http://us.php.net/manual/en/function.date.php">PHP: date - Manual</a>)</span>
                        </div>
                    </div>
                </fieldset>


                <fieldset>
                    <legend>Convert</legend>
                    <div class="row">
                        <div class="form-group col-xs-12 col-md-6">
                            <label for="unixtime">Unixtime</label>
                            <input type="text" class="form-control" name="unixtime" id="unixtime" value="<?php echo $unixtime; ?>" />
                        </div>
                        <div class="form-group col-xs-12 col-md-6">
                            <label for="texttime">Text Time</label>
                            <input type="text" class="form-control" name="texttime" id="texttime" value="<?php echo $texttime; ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 col-md-3">
                            <div class="form-group">
                                <label>Midnight</label>
                                <p class="form-control-static"><?php echo $midnight; ?></p>
                            </div>
                        </div>
                        <div class="col-xs-6 col-md-3">
                            <div class="form-group">
                                <label>Now</label>
                                <p class="form-control-static"><?php echo time(); ?></p>
                            </div>                        </div>
                        <div class="col-xs-6 col-md-3">
                            <input type="submit" name="tounixtime" class="btn btn-primary btn-block" value="Convert To Unixtime" />
                        </div>
                        <div class="col-xs-6 col-md-3">
                            <input type="submit" name="totexttime" class="btn btn-primary btn-block" value="Convert To Text Time" />
                        </div>
                    </div>
                </fieldset>

            </div> <!-- /container -->

        </form>

        </div>

        <div id="footer">
            <div class="container text-center">
                <p class="text-muted">
                    Created by <a href="http://blog.andrewshell.org/">Andrew Shell</a>
                    &#8226; <a href="https://github.com/geekitycom/unixtime">Fork on GitHub</a>
                </p>
            </div>
        </div>

    </body>
</html>
