angular.module('ExpensesApp', ['ngRoute', 'ngResource', 'ngMessages', 'ui.bootstrap', 'ng-currency', 'datePicker', 'UserApp'])
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
                templateUrl: 'views/about.html', public: true
            })
            .when('/alister', {
                templateUrl: 'views/alister.html', public: true
            })

            .when('/login', {templateUrl: 'partials/login.html', login: true})
            .when('/signup', {templateUrl: 'partials/signup.html', public: true})
            .when('/verify-email', {templateUrl: 'partials/verify-email.html', verify_email: true})
            .when('/reset-password', {templateUrl: 'partials/reset-password.html', public: true})
            .when('/set-password', {templateUrl: 'partials/set-password.html', set_password: true})
            .when('/view1', {templateUrl: 'partials/partial1.html', controller: 'MyCtrl1'})
            .when('/view2', {templateUrl: 'partials/partial2.html', controller: 'MyCtrl2'})

            .otherwise({
                redirectTo: '/expenses'
            });
            $locationProvider.html5Mode(true);
        }
    ])
    .value('options', {})
    .run(['user', function (user) { //$rootScope, options, Expense, 
        paramsObj = {};
        user.init({ appId: '553e4bb6566ea' });
    }]);
angular.module('ExpensesApp')
    .factory('Expense', ['$resource', function ($resource) {
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
        }])
    .factory('WeeklySpend', ['$resource', function ($resource) {
        return $resource('/api/v1/summary.json', 
            {  id: '@id' },
            {}
        );
    }])
;
