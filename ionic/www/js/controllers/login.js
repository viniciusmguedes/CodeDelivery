angular.module('starter.controllers')
    .controller('LoginCtrl', [
              '$scope', 'OAuth','OAuthToken', '$ionicPopup', '$state','User' , 'UserData',
        function ($scope, OAuth, OAuthToken, $ionicPopup, $state,User , UserData) {
        $scope.user = {
            username: '',
            password: ''
        };

        $scope.login = function () {
            var promise = OAuth.getAccessToken($scope.user)
                promise.then(function (data) {
                    return User.authenticated({include: 'client'}).$promise;
                })
                .then(function(data){
                    UserData.set(data.data);
                    $state.go('client.checkout');
                },function(responseError){

                    OAuthToken.removeToken();
                    $ionicPopup.alert({
                        title: 'Advertência',
                        template: 'Login e/ou senha inválidos'
                    });
                    console.debug(responseError);
                });
        }
    }]);