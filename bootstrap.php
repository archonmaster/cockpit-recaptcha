<?php
$this->module("recaptcha")->extend([
    'widgets' => [],
    'verify' => function($code) use($app) {
        
        $secret = $app->db->getKey("cockpit/settings", "cockpit.recaptcha.secret", "");
        
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
                'content' => http_build_query([
                    'secret' => $secret,
                    'response' => $code
                ])
            ]
        ]);
        
        $result = file_get_contents("https://www.google.com/recaptcha/api/siteverify", false, $context);
        $result = json_decode($result, true);
        
        return $result["success"];
    },
    'widget' => function($callback=false, $class=null) use($app) {
        $id = "recaptcha".uniqid().rand(1000, 9999);
        $widgets = $this->widgets;
        $widgets[] = [
            "id" => $id,
            "callback" => $callback
        ];
        $this->widgets = $widgets;
        $class = isset($class) ? " class=\"{$class}\"" : "";
        echo "<div id=\"{$id}\"{$class} recaptcha></div>";
        return $id;
    },
    'script' => function() use($app) {
        
        if (empty($this->widgets)) return;
        
        $key = $app->db->getKey("cockpit/settings", "cockpit.recaptcha.key", "");
        
echo <<<EOD
<script type="text/javascript">
    var RECAPTCHA = {};
    var recaptchaOnload = function() {
EOD;
        foreach ($this->widgets as $w) {
            $callback = $w["callback"] ? "'callback': {$w["callback"]}," : "";
echo <<<EOD
        RECAPTCHA["{$w["id"]}"] = grecaptcha.render(document.getElementById('{$w["id"]}'), {
            'callback': function(response) {
                document.getElementById('{$w["id"]}').setAttribute("data-response", response);
                {$callback}
            },
            'sitekey': '{$key}'
        });
EOD;
        }
echo <<<EOD
    };
</script>
<script src="https://www.google.com/recaptcha/api.js?onload=recaptchaOnload&render=explicit" async defer></script>
EOD;
    }
]);

if (!function_exists('recaptcha')) {
    function recaptcha() {
        return cockpit('recaptcha');
    }
}

// ADMIN
if (COCKPIT_ADMIN && !COCKPIT_REST) include_once(__DIR__.'/admin.php');
?>