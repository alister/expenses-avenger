angular.module('ExpensesApp')
    .controller('ListController', ['$scope', 'Expense', 'WeeklySpend', '$location', '$filter', '$routeParams', 'options', 
        function($scope, Expense, WeeklySpend, $location, $filter, $routeParams, ngCurrency, options) {  //, 

            $scope.fields = ['created_at', 'amount', 'description', 'comment'];
            $scope.fieldNames = ['Date', 'Amount', 'Description', 'Comment'];
            $scope.sort = function (field) {
                //console.log('sort by '+field);
                $scope.sort.field = field;
                $scope.sort.order = !$scope.sort.order;
            };
            $scope.sort.field = 'created_at';
            $scope.sort.order = true;   // DESC, recent first

            // console.log('routeParams:');
            // console.log($routeParams);

            $scope.show = function (id) {
                //console.log('show()');
                $location.path('/expense/' + id);
            };
            $scope.loadAll = function(params) {
                //console.log('Load all.');
                $scope.isDataSubset = false;
                // console.log('params:');
                // console.log(params);
                if (params.startDate) {
                    $scope.isDataSubset = true;
                    // console.log('isDataSubset = true');
                }
                Expense.query(params, function(expenses) {
                    $scope.expenses = expenses;
                });
            };
            $scope.loadAll($routeParams);
            //console.log('ran through controller');
            //console.log($scope.expenses);
    }])
    .controller('SummaryController', ['$scope', 'WeeklySpend', 'Expense', '$location',
        function ($scope, WeeklySpend, Expense, $location) {
            //console.log('SummaryController');
            $scope.weeklySummary = function() {
                //console.log('call weeklySummary');
                WeeklySpend.query({}, function(data) {
                    $scope.weeklySpend = data;
                }, function() {
                    //console.log('Fetch of weeklySpend failed.');
                });
            }

            // I can't figure out how to get the results of the query displayed on the main page
            // without it being reset and all the records being fetched
            $scope.filterSpendByDate = function(weekSummary) {
                //console.log('filterSpendByDate()');
                //console.log(weekSummary);
                //console.log(weekSummary.earliestDate);
                //console.log(weekSummary.latestDate);
                $location.path('/expenses').search({'startDate': weekSummary.earliestDate, 'endDate': weekSummary.latestDate});
            };

            //$scope.weeklySummary();
            WeeklySpend.query({}, function(data) {
                $scope.weeklySpend = data;
            });
    }])
    .controller('NewController', ['$scope', '$rootScope', 'Expense', '$location',
        function ($scope, $rootScope, Expense, $location) {
        //$rootScope.PAGE = "new";
        //console.log('NewController');
        $scope.expense = new Expense();
        $scope.save = function () {
            if ($scope.newExpense.$invalid) {
                $scope.$broadcast('record:invalid');
            } else {
                $scope.expense.$save();
                $location.path('/expenses');
            }
        };
    }])
    .controller('SingleController', function ($scope, $rootScope, $location, Expense, $routeParams) {
        //$rootScope.PAGE = "single";
        //console.log('expens Id:' + $routeParams.id);
        Expense.get({ id: parseInt($routeParams.id, 10) }, function(data) {
            $scope.expense = data;
            //console.log(data);
            $scope.expense.amount = parseFloat(data.amount).toFixed(2);
            //$scope.expense.created_at = new Date($scope.expense.created_at);
            //console.log($scope.expense);
        });

        $scope.update = function() {
            $scope.expense.$update();
            $location.path('/expenses');
        };
        $scope.delete = function () {
            $scope.expense.$delete();
            $location.path('/expenses');
        };
    })
    //.controller('FilterDateController', function ($scope, $rootScope) {
    .controller('FilterDateController', ['$filter', '$location', function($filter, $location) {
        //$rootScope.PAGE = "filter";
    }]);
;
