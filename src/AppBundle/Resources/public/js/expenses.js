/* expenses.js */

var expensesApp = angular.module('expensesApp', [ 'ngRoute', 'ngTable' ]);
expensesApp.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/expenses', {
        templateUrl: '/partials/expense_list.html',
        controller: 'ExpensesCtrl',
        controllerAs: 'expenses'
      }).
      when('/about', {
        templateUrl: '/partials/about.html',
      }).
      when('/alister', {
        templateUrl: '/partials/alister.html',
      }).

      // go to /expenses by default
      otherwise({
        redirectTo: '/expenses'
      });
  }]);

expensesApp.controller('ExpensesCtrl', ['$scope', '$http', function ($scope, $http) {
    $scope.expenses = [];
    $http.get('/api/v1/expenses.json').success(function(data) {
        $scope.expenses = data.expenses;
    });
}]);
