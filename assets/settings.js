App.module.controller("recaptcha-settings", function($scope, $http, $timeout){

    $scope.key = RECAPTCHA.key;
    $scope.secret = RECAPTCHA.secret;

    $scope.saveKeys = function() {
        $http.post(App.route("/api/recaptcha/saveKeys"), {"settings": {"key": angular.copy($scope.key), "secret": angular.copy($scope.secret)}}).success(function(data){
            App.notify("Keys saved", "success");
        }).error(App.module.callbacks.error.http);
    };
    
});