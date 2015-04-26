angular.module('ExpensesApp', ['ngRoute', 'ngResource', 'ngMessages', 'ui.bootstrap', 'gm.datepickerMultiSelect', 'ng-currency']) //
    .config(['$httpProvider', '$routeProvider', '$locationProvider', function ($httpProvider, $routeProvider, $locationProvider) {
        
        // $httpProvider.defaults.transformRequest = function (data) {
        //     var str = [];
        //     for (var p in data) {
        //         data[p] !== undefined && str.push(encodeURIComponent(p) + '=' + encodeURIComponent(data[p]));
        //     }
        //     return str.join('&');
        // };
        // $httpProvider.defaults.headers.put['Content-Type'] = $httpProvider.defaults.headers.post['Content-Type'] =
        //     'application/x-www-form-urlencoded; charset=UTF-8';

        $routeProvider
            .when('/expenses', {
                controller: 'ListController',
                templateUrl: 'views/list.html'
            })
            .when('/expense/weekly-summary', {
                controller: 'SummaryController',
                templateUrl: 'views/weekly-summary.html'
            })
            .when('/expense/new', {
                controller: 'NewController',
                templateUrl: 'views/new.html'
            })
            .when('/expense/:id', {
                controller: 'SingleController',
                templateUrl: 'views/single.html'
            })
            .otherwise({
                redirectTo: '/expenses'
            });
            $locationProvider.html5Mode(true);
        }
    ])
    .value('options', {})
    .run(function (options, Expense) {
        //Fields.get(function (data) {
        //  options.displayed_fields = data;
        //}

        paramsObj= {};
        // Expense.query(function(data) {
        //     console.log('Got run() Data:');
        //     console.log(data);
        //     expenses = data;
        // }, function() {
        //     console.log('Fetch run() fail.');
        // });
    });
angular.module('ExpensesApp')
    .factory('Expense', function ($resource) {
        return $resource('/api/v1/expenses/:id.json',  //http://nas.abulman.co.uk:8000/app_dev.php
            {  id: '@id' },
            {
                'update':      { method: 'PUT', isArray: true },
                'get':         { method: 'GET' },
                'options':     {/*method: 'GET'*/ }
            }
        );

    })
    .factory('WeeklySpend', function ($resource) {
        return $resource('/api/v1/summary.json', 
            {  id: '@id' },
            {}
        );
    });
;
