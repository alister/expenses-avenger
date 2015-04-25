angular.module('ExpensesApp', ['ngRoute', 'ngResource', 'ngMessages', 'ui.bootstrap', 'gm.datepickerMultiSelect']) //, 'ng-currency'
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
            .when('/expense/filter', {
                controller: 'FilterDateController',
                templateUrl: 'views/filter-date.html'
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
        data = Expense.query(function (data) {});
        expenses = data;
    });
angular.module('ExpensesApp')
    .factory('Expense', function ($resource) {
        return $resource('http://nas.abulman.co.uk:8000/app_dev.php/api/v1/expenses/:id.json', { id: '@id' }, {
            'update':  { method: 'PUT', isArray: true },
            'get':     { method: 'GET' },
            'options': {/*method: 'GET'*/ }
        });
    })
;
