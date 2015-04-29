<?php
    $expires = time() + (60 * 60 * 24 * 7); // one week

    if (!empty($_POST['tz'])) {
        $_COOKIE['tz'] = $_POST['tz'];
    } elseif (empty($_COOKIE['tz'])) {
        $_COOKIE['tz'] = 'America';
    }
    $tz = $_COOKIE['tz'];
    setcookie("tz", $tz, $expires);

    if (!empty($_POST['tz2'])) {
        $_COOKIE['tz2'] = $_POST['tz2'];
    } elseif (empty($_COOKIE['tz2'])) {
        $_COOKIE['tz2'] = 'Chicago';
    }
    $tz2 = $_COOKIE['tz2'];
    setcookie("tz2", $tz2, $expires);

    if (!empty($_POST['textform'])) {
        $_COOKIE['textform'] = $_POST['textform'];
    } elseif (empty($_COOKIE['textform'])) {
        $_COOKIE['textform'] = 'Y-m-d H:i:s T';
    }
    $textform = $_COOKIE['textform'];
    setcookie("textform", $textform, $expires);

    $rawTimezones = DateTimeZone::listIdentifiers();
    $timezones = array();
    foreach ($rawTimezones as $rawTimezone) {
        $tzParts = explode('/', $rawTimezone);
        if (empty($timezones[$tzParts[0]])) {
            $timezones[$tzParts[0]] = array();
        }
        if (empty($tzParts[1])) {
            $timezones[$tzParts[0]][] = '';
        } else {
            $timezones[$tzParts[0]][] = $tzParts[1];
        }
    }

    if (empty($timezones[$tz])) {
        $tz  = $_COOKIE['tz']  = 'America';
        $tz2 = $_COOKIE['tz2'] = 'Chicago';
    }

    if (false === array_search($tz2, $timezones[$tz])) {
        $tz2 = $_COOKIE['tz2'] = $timezones[$tz][0];
    }

    date_default_timezone_set($tz . (empty($tz2) ? '' : '/' . $tz2));

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

    function getT($tzstring)
    {
        $tz = new DateTimeZone($tzstring);
        $ts = new DateTime('now', $tz);
        return $ts->format('T');
    }

    $shortcuts = array(
        'UTC',
        'America/Los_Angeles',
        'America/Denver',
        'America/Chicago',
        'America/New_York',
    );
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Unixtime</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Tools dealing with unix timestamps">
        <meta name="author" content="Andrew Shell">

        <link href="//netdna.bootstrapcdn.com/bootswatch/3.3.4/flatly/bootstrap.min.css" rel="stylesheet">

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
          <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!-- Le fav and touch icons -->
        <link rel="shortcut icon" href="/favicon.ico">

        <script type="text/javascript">
        if (!Date.now) {
            Date.now = function now() {
                return new Date().getTime();
            };
        }

        var timezones = <?php echo json_encode($timezones); ?>;
        function assignTz2() {
            var i, tz2;
            var etz = document.getElementById("tz");
            var etz2 = document.getElementById("tz2");
            var tz = etz.options[etz.selectedIndex].value;

            etz2.options.length=0;
            for (i = 0; i < timezones[tz].length; i++) {
                tz2 = timezones[tz][i];
                etz2.options[etz2.options.length] = new Option(tz2, tz2, i===0, i===0);
            }
        }

        function assignTimezone(tzstring) {
            var etz = document.getElementById("tz");
            var etz2 = document.getElementById("tz2");
            var tzparts = tzstring.split('/');

            if (undefined === tzparts[1]) {
                tzparts[1] = '';
            }

            etz.value = tzparts[0];
            assignTz2()
            etz2.value = tzparts[1];
        }

        function assignUnixtime(value) {
            var eunixtime = document.getElementById('unixtime');
            eunixtime.value = value;
        }

        function assignUnixtimeNow() {
            assignUnixtime(Math.floor(Date.now() / 1000));
        }
        </script>
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
                        <div class="form-group col-xs-12 col-md-3">
                            <label for="tz">Timezone</label>
                            <select name="tz" id="tz" class="form-control" onchange="assignTz2()">
                            <?php foreach (array_keys($timezones) as $tz): ?>
                                <option value="<?php echo $tz; ?>"<?php echo (0 == strcmp($_COOKIE['tz'], $tz) ? ' selected="selected"' : ''); ?>><?php echo htmlentities($tz, ENT_COMPAT, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                            </select>
                            <span class="help-block">
                                <?php foreach ($shortcuts as $tzstring): ?>
                                <a href="javascript:assignTimezone('<?=$tzstring?>')"><?=getT($tzstring)?></a>
                                <?php endforeach; ?>
                            </span>
                        </div>
                        <div class="form-group col-xs-12 col-md-3">
                            <label for="tz2">&nbsp;</label>
                            <select name="tz2" id="tz2" class="form-control">
                            <?php foreach ($timezones[$_COOKIE['tz']] as $tz2): ?>
                                <option value="<?php echo $tz2; ?>"<?php echo (0 == strcmp($_COOKIE['tz2'], $tz2) ? ' selected="selected"' : ''); ?>><?php echo htmlentities($tz2, ENT_COMPAT, 'UTF-8'); ?></option>
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
                                <p class="form-control-static" id="tzmidnight"><a href="javascript:assignUnixtime(<?php echo $midnight; ?>)"><?php echo $midnight; ?></a></p>
                            </div>
                        </div>
                        <div class="col-xs-6 col-md-3">
                            <div class="form-group">
                                <label>Now</label>
                                <p class="form-control-static"><a href="javascript:assignUnixtimeNow()" id="tznow"><?php echo time(); ?></a></p>
                            </div>
                        </div>
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

        <script type="text/javascript">
            window.onload = function () {
                var etznow = document.getElementById('tznow');
                setInterval(function () {
                    etznow.innerHTML = Math.floor(Date.now() / 1000);
                }, 1000);
            }
        </script>
    </body>
</html>
