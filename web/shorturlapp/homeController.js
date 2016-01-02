(function(){

    var app = angular.module("shorturlApp");

    var homeController = function($scope, $http){

//        var ongeturlComplete = function(response){
//            $scope.shorturl = response.data;
//        };
//
//        var onError = function(response){
//            $scope.error = "Could not fetch data";
//        };
//
//        $http.get("http://localhost/ngshin/web/app_dev.php/api/urls/1")
//            .then(ongeturlComplete, onError);

        $scope.homemessage = "hello homepage , Its working";
    };

    app.controller("homeController", ["$scope", "$http", homeController]);

}());