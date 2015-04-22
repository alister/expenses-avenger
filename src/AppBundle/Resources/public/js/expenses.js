/* expenses.js */

var expensesApp = angular.module('expensesApp', [
  'ngRoute',
  //'expensesControllers'
]);
expensesApp.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/expenses', {
        templateUrl: 'partials/demo.html',
        controller: 'ExpensesCtrl',
        controllerAs: 'expenses'
      }).
      when('/about', {
        templateUrl: '/partials/about.html',
        //controller: 'PhoneDetailCtrl'
      }).
      when('/alister', {
        templateUrl: '/partials/alister.html',
        //controller: 'PhoneDetailCtrl'
      }).
      // when('/expenses/:phoneId', {
      //   templateUrl: 'partials/phone-detail.html',
      //   controller: 'PhoneDetailCtrl'
      // }).

      // go to /expenses by default
      otherwise({
        redirectTo: '/expenses'
      });
  }]);

expensesApp.controller('ExpensesCtrl', ['$scope', '$http', function ($scope, $http) {
    $scope.expenses = [];

    //$http.get('/api/v1/demo/expenses.json').success(function(data) {
    $http.get('/api/v1/expenses.json').success(function(data) {
        $scope.expenses = data.expenses;
    });

    $scope.orderProp = 'created_at';
}]);
