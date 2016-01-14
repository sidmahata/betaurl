(function(){

    var app = angular.module("shorturlApp");
    
    function getRootUrl() {
        var rooturl =  window.location.origin?window.location.origin+'/':window.location.protocol+'/'+window.location.host+'/';
        return rooturl.concat('betaurl/web/');
        //var re = new RegExp(/^.*\//);
        //return re.exec(window.location.href);
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

    app.controller("listController", ["$scope", "$http", listController]);

}());