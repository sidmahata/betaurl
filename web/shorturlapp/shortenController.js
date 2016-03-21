(function(){

    var app = angular.module("shorturlApp");
    
    //get root url of current webpage
    function getRootUrl() {
        return window.location.origin?window.location.origin+'/':window.location.protocol+'/'+window.location.host+'/';
        
    }
    
    //extract the root domain of any given url
    function extractDomain(url) {
	    var domain;
	    //find & remove protocol (http, ftp, etc.) and get domain
	    if (url.indexOf("://") > -1) {
	        domain = url.split('/')[2];
	    }
	    else {
	        domain = url.split('/')[0];
	    }
	
	    //find & remove port number
	    domain = domain.split(':')[0];
	
	    return domain;
	}
	
	//get url parameter from any url by name
	function getParameterByName(name, url) {
	    if (!url) url = window.location.href;
	    name = name.replace(/[\[\]]/g, "\\$&");
	    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
	        results = regex.exec(url);
	    if (!results) return null;
	    if (!results[2]) return '';
	    return decodeURIComponent(results[2].replace(/\+/g, " "));
	}
	

    var shortenController = function($scope, $http){

        var onUserLoadComplete = function(response){
            $scope.shorturl = response.data;
            $scope.error = null;
            
            //if extracted domain of longurl is youtube, then fetch youtube related videos
            if(extractDomain($scope.shorturl.longurl) == "www.youtube.com" || extractDomain($scope.shorturl.longurl) == "youtube.com" || extractDomain($scope.shorturl.longurl) == "m.youtube.com"){
            	$scope.youtubeRelatedIsCollapsed = false;
            	console.log(getParameterByName('v', $scope.shorturl.longurl));
            	
            	//get youtube video related data from api
            	$http.get(getRootUrl().concat('app_dev.php/api/url/youtube/related?id=').concat(getParameterByName('v', $scope.shorturl.longurl)))
                	.then(function(response){
                		$scope.youtubeRelated = response.data;
                	});	
            }else{
            	$scope.youtubeRelatedIsCollapsed = true;
            	console.log('Not a youtube link');
            }
            
        };

        var onError = function(response){
            $scope.error = "Could not fetch data";
        };

        $scope.shorten = function(){
            $http.post(getRootUrl().concat('app_dev.php/api/urls'), $scope.urlentry)
                .then(onUserLoadComplete, onError);
        };

        // $scope.shorten = function(){
        //     $http.get("https://api.github.com/users/sidmahata")
        //         .then(onUserLoadComplete, onError);
        // };

        $scope.message = "hello world , Shorten is working, but still";
        $scope.rooturl = getRootUrl();
        $scope.youtubeRelatedIsCollapsed = true;
        //$scope.youtubeRelatedIsCollapsed = !$scope.youtubeRelatedIsCollapsed;
        // console.log(extractDomain('youtube.com/watch?v=mZqawa_lmSo'));
        
        
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

    app.controller("shortenController", ["$scope", "$http", shortenController]);

}());