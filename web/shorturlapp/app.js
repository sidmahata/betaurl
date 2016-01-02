
(function(){
    var app = angular.module('shorturlApp', ['ui.router']).config(function($interpolateProvider){
        $interpolateProvider.startSymbol('[[').endSymbol(']]');
        });

    app.config(function($stateProvider, $urlRouterProvider) {

        $urlRouterProvider.otherwise('/home');

        $stateProvider

            // HOME STATES AND NESTED VIEWS ========================================
            .state('home', {
                url: '/home',
                templateUrl: 'http://192.168.33.10/shorturlapp/home.html',
                controller : 'homeController'
            })
            //.state('home.dashboard', {
            //    url: '/dashboard',
            //    templateUrl: 'http://localhost/ngshin/web/shorturlapp/dashboard.html',
            //    controller : 'dashboardController'
            //})
            .state('home.shorten', {
                url: '/shorten',
                templateUrl: 'http://192.168.33.10/shorturlapp/shorten.html',
                controller : "shortenController"
            });

    });

}());