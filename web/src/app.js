angular.module('ExpensesApp', ['ngRoute', 'ngResource', 'ngMessages', 'ui.bootstrap', 'ng-currency', 'datePicker']) // 'gm.datepickerMultiSelect', 
    .config(['$httpProvider', '$routeProvider', '$locationProvider', function ($httpProvider, $routeProvider, $locationProvider) {
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
            .when('/about', {
                templateUrl: 'views/about.html'
            })
            .when('/alister', {
                templateUrl: 'views/alister.html'
            })
            .otherwise({
                redirectTo: '/expenses'
            });
            $locationProvider.html5Mode(true);
        }
    ])
    .value('options', {})
    .run(function ($rootScope, options, Expense) {
        paramsObj= {};
    });
angular.module('ExpensesApp')
    .factory('Expense', function ($resource) {
        return $resource('/api/v1/expenses/:id.json',  //http://nas.abulman.co.uk:8000/app_dev.php
            {
                id: '@id'
            },
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
