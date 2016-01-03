(function(){

    var app = angular.module("shorturlApp");

    var shortenController = function($scope, $http){

        var onUserLoadComplete = function(response){
            $scope.shorturl = response.data;
            $scope.error = null;
        };

        var onError = function(response){
            $scope.error = "Could not fetch data";
        };

        //$scope.shorten1 = function(){
        //    $http.post("http://localhost/shin/web/app_dev.php/api/urls", $scope.urlentry)
        //        .then(onUserLoadComplete, onError);
        //};

        $scope.shorten = function(){
            $http.get("https://api.github.com/users/sidmahata")
                .then(onUserLoadComplete, onError);
        };

        $scope.message = "hello world , Shorten is working, but still";

    };

    app.controller("shortenController", ["$scope", "$http", shortenController]);

}());