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

//https://github.com/g00fy-/angular-datepicker/blob/master/dist/index.min.js
"use strict";!function(a){var b=a.module("datePicker",[]);b.constant("datePickerConfig",{template:"templates/datepicker.html",view:"month",views:["year","month","date","hours","minutes"],step:5}),b.filter("time",function(){function a(a){return("0"+a.getHours()).slice(-2)+":"+("0"+a.getMinutes()).slice(-2)}return function(b){return b instanceof Date||(b=new Date(b),!isNaN(b.getTime()))?a(b):void 0}}),b.directive("datePicker",["datePickerConfig","datePickerUtils",function(b,c){return{require:"?ngModel",template:'<div ng-include="template"></div>',scope:{model:"=datePicker",after:"=?",before:"=?"},link:function(d,e,f,g){function h(){var a=d.view;d.model&&!j&&(d.date=new Date(d.model),j=!1);var b=d.date;switch(a){case"year":d.years=c.getVisibleYears(b);break;case"month":d.months=c.getVisibleMonths(b);break;case"date":d.weekdays=d.weekdays||c.getDaysOfWeek(),d.weeks=c.getVisibleWeeks(b);break;case"hours":d.hours=c.getVisibleHours(b);break;case"minutes":d.minutes=c.getVisibleMinutes(b,k)}}function i(){return"date"!==d.view?d.view:d.date?d.date.getMonth():null}var j=!1;d.date=new Date(d.model||new Date),d.views=b.views.concat(),d.view=f.view||b.view,d.now=new Date,d.template=f.template||b.template;var k=parseInt(f.step||b.step,10),l=!!f.partial;if(g){if(a.isDefined(f.minDate)){var m;g.$validators.min=function(b){return!c.isValidDate(b)||a.isUndefined(m)||b>=m},f.$observe("minDate",function(a){m=new Date(a),g.$validate()})}if(a.isDefined(f.maxDate)){var n;g.$validators.max=function(b){return!c.isValidDate(b)||a.isUndefined(n)||n>=b},f.$observe("maxDate",function(a){n=new Date(a),g.$validate()})}}d.views=d.views.slice(d.views.indexOf(f.maxView||"year"),d.views.indexOf(f.minView||"minutes")+1),(1===d.views.length||-1===d.views.indexOf(d.view))&&(d.view=d.views[0]),d.setView=function(a){-1!==d.views.indexOf(a)&&(d.view=a)},d.setDate=function(a){if(!f.disabled){d.date=a;var b=d.views[d.views.indexOf(d.view)+1];if(!b||l||d.model){d.model=new Date(d.model||a),g&&g.$setViewValue(d.date);var c=l?"minutes":d.view;switch(c){case"minutes":d.model.setMinutes(a.getMinutes());case"hours":d.model.setHours(a.getHours());case"date":d.model.setDate(a.getDate());case"month":d.model.setMonth(a.getMonth());case"year":d.model.setFullYear(a.getFullYear())}d.$emit("setDate",d.model,d.view)}b&&d.setView(b),b||"true"!==f.autoClose||(e.addClass("hidden"),d.$emit("hidePicker"))}},d.$watch(i,h),d.next=function(a){var b=d.date;switch(a=a||1,d.view){case"year":case"month":b.setFullYear(b.getFullYear()+a);break;case"date":b.setMonth(b.getMonth()+a);break;case"hours":case"minutes":b.setHours(b.getHours()+a)}j=!0,h()},d.prev=function(a){return d.next(-a||-1)},d.isAfter=function(a){return d.after&&c.isAfter(a,d.after)},d.isBefore=function(a){return d.before&&c.isBefore(a,d.before)},d.isSameMonth=function(a){return c.isSameMonth(d.model,a)},d.isSameYear=function(a){return c.isSameYear(d.model,a)},d.isSameDay=function(a){return c.isSameDay(d.model,a)},d.isSameHour=function(a){return c.isSameHour(d.model,a)},d.isSameMinutes=function(a){return c.isSameMinutes(d.model,a)},d.isNow=function(a){var b=!0,c=d.now;switch(d.view){case"minutes":b&=~~(a.getMinutes()/k)===~~(c.getMinutes()/k);case"hours":b&=a.getHours()===c.getHours();case"date":b&=a.getDate()===c.getDate();case"month":b&=a.getMonth()===c.getMonth();case"year":b&=a.getFullYear()===c.getFullYear()}return b}}}}]),a.module("datePicker").factory("datePickerUtils",function(){return{getVisibleMinutes:function(a,b){a=new Date(a||new Date),a=new Date(a.getFullYear(),a.getMonth(),a.getDate(),a.getHours());for(var c=[],d=a.getTime()+36e5;a.getTime()<d;)c.push(a),a=new Date(a.getTime()+60*b*1e3);return c},getVisibleWeeks:function(a){a=new Date(a||new Date);var b=a.getMonth(),c=a.getYear();a.setDate(1),a.setHours(0),a.setMinutes(0),a.setSeconds(0),a.setMilliseconds(0),a.setDate(0===a.getDay()?-5:a.getDate()-(a.getDay()-1)),1===a.getDate()&&a.setDate(-6);for(var d=[];d.length<6&&!(a.getYear()===c&&a.getMonth()>b);){for(var e=[],f=0;7>f;f++)e.push(new Date(a)),a.setDate(a.getDate()+1);d.push(e)}return d},getVisibleYears:function(a){var b=[];a=new Date(a||new Date),a.setFullYear(a.getFullYear()-a.getFullYear()%10);for(var c=0;12>c;c++)b.push(new Date(a.getFullYear()+(c-1),0,1));return b},getDaysOfWeek:function(a){a=new Date(a||new Date),a=new Date(a.getFullYear(),a.getMonth(),a.getDate()),a.setDate(a.getDate()-(a.getDay()-1));for(var b=[],c=0;7>c;c++)b.push(new Date(a)),a.setDate(a.getDate()+1);return b},getVisibleMonths:function(a){a=new Date(a||new Date);for(var b=a.getFullYear(),c=[],d=0;12>d;d++)c.push(new Date(b,d,1));return c},getVisibleHours:function(a){a=new Date(a||new Date),a.setHours(0),a.setMinutes(0),a.setSeconds(0),a.setMilliseconds(0);for(var b=[],c=0;24>c;c++)b.push(a),a=new Date(a.getTime()+36e5);return b},isAfter:function(a,b){return a=void 0!==a?new Date(a):a,b=new Date(b),a&&a.getTime()<=b.getTime()},isBefore:function(a,b){return a=void 0!==a?new Date(a):a,b=new Date(b),a.getTime()>=b.getTime()},isSameYear:function(a,b){return a=void 0!==a?new Date(a):a,b=new Date(b),a&&a.getFullYear()===b.getFullYear()},isSameMonth:function(a,b){return a=void 0!==a?new Date(a):a,b=new Date(b),this.isSameYear(a,b)&&a.getMonth()===b.getMonth()},isSameDay:function(a,b){return a=void 0!==a?new Date(a):a,b=new Date(b),this.isSameMonth(a,b)&&a.getDate()===b.getDate()},isSameHour:function(a,b){return a=void 0!==a?new Date(a):a,b=new Date(b),this.isSameDay(a,b)&&a.getHours()===b.getHours()},isSameMinutes:function(a,b){return a=void 0!==a?new Date(a):a,b=new Date(b),this.isSameHour(a,b)&&a.getMinutes()===b.getMinutes()},isValidDate:function(a){return a&&!(a.getTime&&a.getTime()!==a.getTime())}}});var b=a.module("datePicker");b.directive("dateRange",function(){return{templateUrl:"templates/daterange.html",scope:{start:"=",end:"="},link:function(a,b,c){a.start=new Date(a.start||new Date),a.end=new Date(a.end||new Date),c.$observe("disabled",function(b){a.disableDatePickers=!!b}),a.$watch("start.getTime()",function(b){b&&a.end&&b>a.end.getTime()&&(a.end=new Date(b))}),a.$watch("end.getTime()",function(b){b&&a.start&&b<a.start.getTime()&&(a.start=new Date(b))})}}});var c="ng-pristine",d="ng-dirty",b=a.module("datePicker");b.constant("dateTimeConfig",{template:function(a){return'<div date-picker="'+a.ngModel+'" '+(a.view?'view="'+a.view+'" ':"")+(a.maxView?'max-view="'+a.maxView+'" ':"")+(a.autoClose?'auto-close="'+a.autoClose+'" ':"")+(a.template?'template="'+a.template+'" ':"")+(a.minView?'min-view="'+a.minView+'" ':"")+(a.partial?'partial="'+a.partial+'" ':"")+(a.step?'step="'+a.step+'" ':"")+'class="date-picker-date-time"></div>'},format:"yyyy-MM-dd HH:mm",views:["date","year","month","hours","minutes"],dismiss:!1,position:"relative"}),b.directive("dateTimeAppend",function(){return{link:function(a,b){b.bind("click",function(){b.find("input")[0].focus()})}}}),b.directive("dateTime",["$compile","$document","$filter","dateTimeConfig","$parse","datePickerUtils",function(b,e,f,g,h,i){var j=e.find("body"),k=f("date");return{require:"ngModel",scope:!0,link:function(e,f,l,m){function n(a){return k(a,s)}function o(){return m.$modelValue}function p(a){a.stopPropagation(),m.$pristine&&(m.$dirty=!0,m.$pristine=!1,f.removeClass(c).addClass(d),t&&t.$setDirty(),m.$render())}function q(){y&&(y.remove(),y=null),A&&(A.remove(),A=null)}function r(){if(!y){if(y=b(D)(e),e.$digest(),e.$on("setDate",function(a,b,c){p(a),x&&u[u.length-1]===c&&q()}),e.$on("hidePicker",function(){f.triggerHandler("blur")}),e.$on("$destroy",q),"absolute"===z){var c=a.extend(f.offset(),{height:f[0].offsetHeight});y.css({top:c.top+c.height,left:c.left,display:"block",position:z}),j.append(y)}else A=a.element("<div date-picker-wrapper></div>"),f[0].parentElement.insertBefore(A[0],f[0]),A.append(y),y.css({top:f[0].offsetHeight+"px",display:"block"});y.bind("mousedown",function(a){a.preventDefault()})}}var s=l.format||g.format,t=f.inheritedData("$formController"),u=h(l.views)(e)||g.views.concat(),v=l.view||u[0],w=u.indexOf(v),x=l.dismiss?h(l.dismiss)(e):g.dismiss,y=null,z=l.position||g.position,A=null;if(-1===w&&u.splice(w,1),u.unshift(v),m.$formatters.push(n),m.$parsers.unshift(o),a.isDefined(l.minDate)){var B;m.$validators.min=function(b){return!i.isValidDate(b)||a.isUndefined(B)||b>=B},l.$observe("minDate",function(a){B=new Date(a),m.$validate()})}if(a.isDefined(l.maxDate)){var C;m.$validators.max=function(b){return!i.isValidDate(b)||a.isUndefined(C)||C>=b},l.$observe("maxDate",function(a){C=new Date(a),m.$validate()})}var D=g.template(l);f.bind("focus",r),f.bind("blur",q)}}}]),a.module("datePicker").run(["$templateCache",function(a){a.put("templates/datepicker.html",'<div ng-switch="view">\r\n  <div ng-switch-when="date">\r\n    <table>\r\n      <thead>\r\n      <tr>\r\n        <th ng-click="prev()">&lsaquo;</th>\r\n        <th colspan="5" class="switch" ng-click="setView(\'month\')">{{date|date:"yyyy MMMM"}}</th>\r\n        <th ng-click="next()">&rsaquo;</i></th>\r\n      </tr>\r\n      <tr>\r\n        <th ng-repeat="day in weekdays" style="overflow: hidden">{{ day|date:"EEE" }}</th>\r\n      </tr>\r\n      </thead>\r\n      <tbody>\r\n      <tr ng-repeat="week in weeks">\r\n        <td ng-repeat="day in week">\r\n          <span\r\n            ng-class="{\'now\':isNow(day),\'active\':isSameDay(day),\'disabled\':(day.getMonth()!=date.getMonth()),\'after\':isAfter(day),\'before\':isBefore(day)}"\r\n            ng-click="setDate(day)" ng-bind="day.getDate()"></span>\r\n        </td>\r\n      </tr>\r\n      </tbody>\r\n    </table>\r\n  </div>\r\n  <div ng-switch-when="year">\r\n    <table>\r\n      <thead>\r\n      <tr>\r\n        <th ng-click="prev(10)">&lsaquo;</th>\r\n        <th colspan="5" class="switch">{{years[0].getFullYear()}}-{{years[years.length-1].getFullYear()}}</th>\r\n        <th ng-click="next(10)">&rsaquo;</i></th>\r\n      </tr>\r\n      </thead>\r\n      <tbody>\r\n      <tr>\r\n        <td colspan="7">\r\n          <span ng-class="{\'active\':isSameYear(year),\'now\':isNow(year)}"\r\n                ng-repeat="year in years"\r\n                ng-click="setDate(year)" ng-bind="year.getFullYear()"></span>\r\n        </td>\r\n      </tr>\r\n      </tbody>\r\n    </table>\r\n  </div>\r\n  <div ng-switch-when="month">\r\n    <table>\r\n      <thead>\r\n      <tr>\r\n        <th ng-click="prev()">&lsaquo;</th>\r\n        <th colspan="5" class="switch" ng-click="setView(\'year\')">{{ date|date:"yyyy" }}</th>\r\n        <th ng-click="next()">&rsaquo;</i></th>\r\n      </tr>\r\n      </thead>\r\n      <tbody>\r\n      <tr>\r\n        <td colspan="7">\r\n          <span ng-repeat="month in months"\r\n                ng-class="{\'active\':isSameMonth(month),\'after\':isAfter(month),\'before\':isBefore(month),\'now\':isNow(month)}"\r\n                ng-click="setDate(month)"\r\n                ng-bind="month|date:\'MMM\'"></span>\r\n        </td>\r\n      </tr>\r\n      </tbody>\r\n    </table>\r\n  </div>\r\n  <div ng-switch-when="hours">\r\n    <table>\r\n      <thead>\r\n      <tr>\r\n        <th ng-click="prev(24)">&lsaquo;</th>\r\n        <th colspan="5" class="switch" ng-click="setView(\'date\')">{{ date|date:"dd MMMM yyyy" }}</th>\r\n        <th ng-click="next(24)">&rsaquo;</i></th>\r\n      </tr>\r\n      </thead>\r\n      <tbody>\r\n      <tr>\r\n        <td colspan="7">\r\n          <span ng-repeat="hour in hours"\r\n                ng-class="{\'now\':isNow(hour),\'active\':isSameHour(hour)}"\r\n                ng-click="setDate(hour)" ng-bind="hour|time"></span>\r\n        </td>\r\n      </tr>\r\n      </tbody>\r\n    </table>\r\n  </div>\r\n  <div ng-switch-when="minutes">\r\n    <table>\r\n      <thead>\r\n      <tr>\r\n        <th ng-click="prev()">&lsaquo;</th>\r\n        <th colspan="5" class="switch" ng-click="setView(\'hours\')">{{ date|date:"dd MMMM yyyy" }}\r\n        </th>\r\n        <th ng-click="next()">&rsaquo;</i></th>\r\n      </tr>\r\n      </thead>\r\n      <tbody>\r\n      <tr>\r\n        <td colspan="7">\r\n          <span ng-repeat="minute in minutes"\r\n                ng-class="{active:isSameMinutes(minute),\'now\':isNow(minute)}"\r\n                ng-click="setDate(minute)"\r\n                ng-bind="minute|time"></span>\r\n        </td>\r\n      </tr>\r\n      </tbody>\r\n    </table>\r\n  </div>\r\n</div>\r\n'),a.put("templates/daterange.html",'<div>\r\n    <table>\r\n        <tr>\r\n            <td valign="top">\r\n                <div date-picker="start" ng-disabled="disableDatePickers"  class="date-picker" date after="start" before="end" min-view="date" max-view="date"></div>\r\n            </td>\r\n            <td valign="top">\r\n                <div date-picker="end" ng-disabled="disableDatePickers"  class="date-picker" date after="start" before="end"  min-view="date" max-view="date"></div>\r\n            </td>\r\n        </tr>\r\n    </table>\r\n</div>\r\n')}])}(angular);
