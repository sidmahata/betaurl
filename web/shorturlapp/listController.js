(function(){

    var app = angular.module("shorturlApp");
    
    function getRootUrl() {
        return window.location.origin?window.location.origin+'/':window.location.protocol+'/'+window.location.host+'/';
    }

    var listController = function($scope, $http){

        var onUserLoadComplete = function(response){
            $scope.shorturlList = response.data;
            $scope.error = null;
        };

        var onError = function(response){
            $scope.error = "Could not fetch data";
        };

        $http.get(getRootUrl().concat('app_dev.php/api/urls'))
                .then(onUserLoadComplete, onError);
        
        $scope.message1 = "hello world , List is working";
        $scope.rooturl = getRootUrl();
    };

    app.controller("listController", ["$scope", "$http", listController]);

}());