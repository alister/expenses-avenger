angular.module('ExpensesApp')
    .controller('ListController', function ($scope, $rootScope, Expense, $location, options) {
        $rootScope.PAGE = "all";
        $scope.expenses = Expense.query();
        $scope.fields = ['created_at', 'amount', 'description', 'comment'].concat(options.displayed_fields);
        $scope.fieldNames = ['Date', 'Amount', 'Description', 'Comment'].concat(options.displayed_fields);

        $scope.sort = function (field) {
            $scope.sort.field = field;
            $scope.sort.order = !$scope.sort.order;
        };

        $scope.sort.field = 'created_at';
        $scope.sort.order = false;

        $scope.show = function (id) {
            //redirectTo: '/expenses' + id;
            $location.url('/expense/' + id);
        };
    })
    .controller('NewController', function ($scope, $rootScope, Expense, $location) {
        $rootScope.PAGE = "new";
        $scope.expense = new Expense({
            created_at:  new Date(),
            amount:      '0.00',
            description: '',
            comment:     ''
        });
        $scope.save = function () {
            if ($scope.newExpense.$invalid) {
                $scope.$broadcast('record:invalid');
            } else {
                $scope.expense.$save();
                $location.url('/expenses');
            }
        };
    })
    .controller('SingleController', function ($scope, $rootScope, $location, Expense, $routeParams) {
        $rootScope.PAGE = "single";
        $scope.expense = new Expense({
            created_at:  new Date(),
            amount:      '',
            description: '',
            comment:     ''
        });
        $scope.expense = Expense.get({ id: parseInt($routeParams.id, 10) });
        $scope.expense.created_at = new Date($scope.expense.created_at);

        $scope.update = function() {
            $scope.expense.$update();
            //redirectTo: '/expenses';
            $location.url('/expenses');
        };
        $scope.delete = function () {
            $scope.expense.$delete();
            $location.url('/expenses');            
            redirectTo: '/expenses';
        };
    })
    //.controller('FilterDateController', function ($scope, $rootScope) {
    .controller('FilterDateController', ['$filter', function($filter) {
            this.activeDate;
            this.selectedDates = [new Date().setHours(0, 0, 0, 0)];
            this.type = 'range';
    
            this.identity = angular.identity;
    
            this.removeFromSelected = function(dt) {
                this.selectedDates.splice(this.selectedDates.indexOf(dt), 1);
            }
            this.filterByTimestamps = function() {
                var startDate = $filter('orderBy')(this.selectedDates, '', false)[0];
                var endDate   = $filter('orderBy')(this.selectedDates, '', true)[0];
                
                var startDateStr = moment(startDate).format('YYYY-MM-DD');
                var endDateStr = moment(endDate).format('YYYY-MM-DD');
                
                //alert(startDateStr + ' / ' + endDateStr);
            }
            //$rootScope.PAGE = "filter-date";
            // $scope.delete = function () {
            //     $scope.expense.$delete();
            //     $location.url('/expenses');
            //     redirectTo: '/expenses';
            // };
        }]);
;
