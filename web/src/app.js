angular.module('ExpensesApp', ['ngRoute', 'ngResource', 'ngMessages']) //, 'ng-currency'
    .config(["$httpProvider", "$routeProvider", "$locationProvider", function ($httpProvider, $routeProvider, $locationProvider) {
        $routeProvider
            .when('/expenses', {
                controller: 'ListController',
                templateUrl: 'views/list.html'
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
        //options.displayed_fields = data;
        expenses = Expense.query(function (data) {});
    });
