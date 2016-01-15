(function(){

    var app = angular.module("shorturlApp");
    
    function getRootUrl() {
        return window.location.origin?window.location.origin+'/':window.location.protocol+'/'+window.location.host+'/';
        
    }

    var massShortenController = function($scope, $http){

        $scope.divHtmlVar = '<h3>List</h3>';
        
        var onUserLoadComplete = function(response){
            $scope.shorturl = response.data;
            $scope.divHtmlVar = $scope.divHtmlVar + '<p>'+ getRootUrl() + $scope.shorturl.shortcode +'</p>';
            $scope.error = null;
        };

        var onError = function(response){
            $scope.error = "Could not fetch data";
        };
        
        
        
        $scope.massShorten = function(){
            
            $scope.divHtmlVar = '<h3>List</h3>';
            
            var urls = $scope.massurlentry.longurl;
            var urls = urls.split('\n');
            var urlentry;
            
            angular.forEach(urls, function(urlentry, i){
                $scope.urlentry = {};
                $scope.urlentry.longurl = urlentry;
                $http.post(getRootUrl().concat('app_dev.php/api/urls'), $scope.urlentry)
                .then(onUserLoadComplete, onError);
              });
            
            // urls.forEach(function(urlentry){
            //     $scope.urlentry.longurl = urlentry;
            //     alert($scope.urlentry.longurl);
            //     $http.post(getRootUrl().concat('app_dev.php/api/urls'), $scope.urlentry)
            //     .then(onUserLoadComplete, onError);
            // });
            // for(var i=0;i<totalno;i++){
            //     urlentry = urls[i];
            //     if(urlentry){
            //         $scope.urlentry.longurl = urlentry;
            //         alert($scope.urlentry.longurl);
            //         $http.post(getRootUrl().concat('app_dev.php/api/urls'), $scope.urlentry)
            //         .then(onUserLoadComplete, onError);
            //     }

            // }

            
            
        };

        $scope.message = "hello world , Mass-Shorten is working";
        $scope.rooturl = getRootUrl();
        
        $scope.copyToClipboard = function(text){
              // Create a "hidden" input
              var aux = document.createElement("input");
            
              // Assign it the value of the specified element
              aux.setAttribute("value", getRootUrl().concat(text));
            
              // Append it to the body
              document.body.appendChild(aux);
            
              // Highlight its content
              aux.select();
            
              // Copy the highlighted text
              document.execCommand("copy");
            
              // Remove it from the body
              document.body.removeChild(aux);
            
            }
    };

    app.controller("massShortenController", ["$scope", "$http", massShortenController]);

}());