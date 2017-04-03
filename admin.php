<?php

namespace Recaptcha\Controller;

class Api extends \Cockpit\Controller {

	public function saveKeys() {
        $app = cockpit();
        
        $settings = $this->param("settings", null);    
        
        if ($settings) {
            
            if (!isset($settings["key"]) || !isset($settings["secret"])) return '{}';
            
            $app->db->setKey("cockpit/settings", "cockpit.recaptcha.key", $settings["key"]);
            $app->db->setKey("cockpit/settings", "cockpit.recaptcha.secret", $settings["secret"]);
            
        }
        
        return $settings ? json_encode($settings) : '{}';
	}

}

$app->on("cockpit.settings.general.menu", function() {        

    $app = cockpit();
    
    echo "<li><a href=\"#RECAPTCHA\">".$app("i18n")->get("Recaptcha")."</a></li>";

});

$app->on("cockpit.settings.general.panels", function() {        

    $app = cockpit();
    
    $title = $app("i18n")->get("Recaptcha settings");
    $title_save = $app("i18n")->get("Save");
    
    $key = $app->db->getKey("cockpit/settings", "cockpit.recaptcha.key", "");
    $secret = $app->db->getKey("cockpit/settings", "cockpit.recaptcha.secret", "");
    
    $app->start('header');
    echo "<script>var RECAPTCHA = ".json_encode(["key"=>$key, "secret"=>$secret]).";</script>";
    echo $app->assets(['recaptcha:assets/settings.js'], $app['cockpit/version']);
    $app->end('header');
    
echo <<<EOD
    <div>
        <span class="uk-badge app-badge">{$title}</span>
        <hr>
        
        <div data-ng-controller="recaptcha-settings">
            <form class="uk-form uk-form-horizontal" ng-submit="saveKeys()">
                <div class="uk-form-row">
                    <label class="uk-form-label">Site key</label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-width-1-1" placeholder="Enter key here..." ng-model="key">
                        </div>
                </div>
                <div class="uk-form-row">
                    <label class="uk-form-label">Secret key</label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-width-1-1" placeholder="Enter key here..." ng-model="secret">
                        </div>
                </div>
                <div class="uk-form-row">
                    <button class="uk-button uk-button-primary">{$title_save}</button>
                </div>
            </form>
        </div>

    </div>
EOD;

});

$app->on('admin.init', function() {

    $this->bindClass("Recaptcha\\Controller\\Api", "api/recaptcha");


});
?>