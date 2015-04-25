angular.module('expenseApp.services',[]).factory('Expense',function($resource){
    return $resource(
        '/api/v1/expenses/:id.json',
        {
            id:'@id',
            // limit: '@limit',
            // offset: '@offset',
            // startDate: '@startDate',
            // endDate: '@endDate'
        },
        {
            update: { method: 'PUT', params: {}, isArray: false }
        }
    );
})

.service('popupService',function($window){
    this.showPopup=function(message){
        return $window.confirm(message);
    }
});
