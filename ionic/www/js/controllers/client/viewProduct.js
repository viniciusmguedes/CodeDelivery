angular.module('starter.controllers')
    .controller('ClientViewProductCtrl', [
        '$scope', '$state', 'Product', '$ionicLoading', '$cart', '$localStorage',
        function ($scope, $state, Product, $ionicLoading, $cart, $localStorage) {
            $scope.products = [];
            $ionicLoading.show({
                template: 'Carregando...'
            });

            Product.query({}, function (data) {
                $scope.products = data.data;
                $ionicLoading.hide();
            }, function (dataError) {
                $ionicLoading.hide();
            });
            $scope.addItem = function (item) {
                item.qtd = 1;
                $cart.addItem(item);
                $state.go('client.checkout');
            };
        }]);