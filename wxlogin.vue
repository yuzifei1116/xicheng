<template>
	<form class='loginView' @submit="login">
		<view class="input-view">
			<view class="label-view">
				<text class="label">账号 </text>
			</view>
			<input class="input" type="text" placeholder="请输入用户名" name="nameValue" />
		</view>
		<view class="input-view">
			<view class="label-view">
				<text class="label">密码</text>
			</view>
			<input class="input" type="password" placeholder="请输入密码" name="passwordValue" />
		</view>
		<view class="button-view">
			<button type="default" class="login" hover-class="hover" formType="submit">登录</button>
			<button type="default" class="register" hover-class="hover" @click="weixinLogin">免费注册</button>
		</view>
	</form>
</template>

<script>
	export default {
		data() {
			return {
				weixinCode: ''
			};
		},
		methods: {
			login(e) {
				console.log("得到账号:"+ e.detail.value.nameValue + ';得到密码:' + e.detail.value.passwordValue)
			},
			weixinLogin() {
				var _this = this;
				// #ifdef APP-PLUS
				var weixinService = null;
				// http://www.html5plus.org/doc/zh_cn/oauth.html#plus.oauth.getServices
				plus.oauth.getServices(function(services) {
					console.log(services)
					if (services && services.length) {
						for (var i = 0, len = services.length; i < len; i++) {
							if (services[i].id === 'weixin') {
								weixinService = services[i];
								break;
							}
						}
						if (!weixinService) {
							console.log('没有微信登录授权服务');
							return;
						}
						// http://www.html5plus.org/doc/zh_cn/oauth.html#plus.oauth.AuthService.authorize
						weixinService.authorize(function(event) {
							console.log(2222222);
							console.log(event);
							console.log(333333333);
							_this.weixinCode = event.code; //用户换取 access_token 的 code
							_this.requestLogin();
						}, function(error) {
							console.error('authorize fail:' + JSON.stringify(error));
						}, {
							// http://www.html5plus.org/doc/zh_cn/oauth.html#plus.oauth.AuthOptions
							appid: 'com.uni.www' //开放平台的应用标识。暂时填个假的充数，仅做演示。
						});
					} else {
						console.log('无可用的登录授权服务');
					}
				}, function(error) {
					console.error('getServices fail:' + JSON.stringify(error));
				});
				// #endif
			},
			register() {
				console.log("前往注册页面");
				// handleThirdLoginApp(){
				console.log("App微信拉起授权")
				var that=this
				uni.getProvider({
				    service: 'oauth',
				    success: function(res) {
				        console.log(res.provider);
				        //支持微信、qq和微博等
				        if (~res.provider.indexOf('weixin')) {
				            uni.login({
				              provider: 'weixin',
				              success: function (loginRes) {
				  console.log("App微信获取用户信息成功",loginRes);
				                  that.getApploginData(loginRes)  //请求登录接口方法
				              },
				              fail:function(res){
				              console.log("App微信获取用户信息失败",res);
				              }
				            })
				        }
				    }
				});
				// },
			}
		}
	}
</script>

<style>

</style>
