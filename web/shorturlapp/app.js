
(function(){
    var app = angular.module('shorturlApp', ['ui.router', 'ngSanitize', 'ngAnimate', 'ui.bootstrap']).config(function($interpolateProvider){
        $interpolateProvider.startSymbol('[[').endSymbol(']]');
        });

    // get root url of website
    function getRootUrl() {
        return window.location.origin?window.location.origin+'/':window.location.protocol+'/'+window.location.host+'/';
        
    }
    

    //alert(getRootUrl());

    app.config(function($stateProvider, $urlRouterProvider) {

        $urlRouterProvider.otherwise('/shorten');

        $stateProvider

            // HOME STATES AND NESTED VIEWS ========================================
            .state('home', {
                url: '/home',
                templateUrl: getRootUrl().concat('shorturlapp/home.html'),
                controller : "homeController"
            })
            .state('list', {
                url: '/list',
                templateUrl: getRootUrl().concat('shorturlapp/list.html'),
                controller : "listController"
            })
            .state('shorten', {
                url: '/shorten',
                templateUrl: getRootUrl().concat('shorturlapp/shorten.html'),
                controller : "shortenController"
            })
            .state('mass-shorten', {
                url: '/mass-shorten',
                templateUrl: getRootUrl().concat('shorturlapp/mass-shorten.html'),
                controller : "massShortenController"
            });

    });

}());