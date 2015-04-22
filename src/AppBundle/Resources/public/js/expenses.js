/* expenses.js */

var expensesApp = angular.module('expensesApp', [ 'ngRoute', 'ngTable', 'ngResource' ]);
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
    // initial fetch
    $http.get('/api/v1/expenses.json').success(function(data) {
        $scope.expenses = data.expenses;
    });
    $scope.addExpense = function() { //create a new expense. Issues a POST to /api/expenses
      $scope.expense.$save(function() {
          $state.go('expenses'); // on success go back to home i.e. expenses state.
      });
    };
}]);
