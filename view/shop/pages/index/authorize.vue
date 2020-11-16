<template>
	<view class="auth" v-if="isHid">
		<view class="auth-zhezhao" @click="hideAllClass"></view>
		<img class="auth-img" src="../../../../public/web/shouquanBg.png" alt="">
		<button class="auth-btn" @click="auth"></button>
	</view>
</template>

<script>
	export default{
		// props: {
		// 	isHid: {
		// 		type: Boolean,
		// 		defual:true,
		// 	},
		// },
		data(){
			return{
				isHid:true,
			}
		},
		onLoad() {
			
		},
		onShow() {
			var self = this;
			self.isHid = true
		},
		onHide() {
			
		},
		methods:{
			auth(){
				console.log('授权授权授权授权授权')
				
				var self=this;
					uni.showLoading({
						mask:true,
						title: '正在登录···',
						complete:()=>{}
					});
					uni.login({
					  provider: 'weixin',
					  success: function (loginRes) {
						let js_code=loginRes.code;//js_code可以给后台获取unionID或openID作为用户标识
						// 获取用户信息
						uni.getUserInfo({
						  provider: 'weixin',
						  success: function (infoRes) {
							  console.log(infoRes,'水电费水电费水电费')
							//infoRes里面有用户信息需要的话可以取一下
							let username=infoRes.userInfo.nickName;//用户名
							let gender=infoRes.userInfo.gender;//用户性别
							let formdata={code:js_code,username:username,sex:gender};
							//login是接口地址，看下面PHP代码
							// self.$go.post("/login",formdata).then(res=>{//这是我封装的请求方法
							// 	if(res.code==200){
							// 		//登录成功
							// 	}
							// })
						  },
						  fail:function(res){
							  console.log(res,'算得上是所所')
						  }
						})
					  },
					  fail:function(res){}
					})
				
			},
			hideAllClass(){
				var self = this;
				self.isHid = false
			},
		}
	}
</script>

<style>
	.auth-zhezhao{
		position: fixed;
		top:0upx;
		left:0upx;
		width:100%;
		height:100%;
		background-color: #000000;
		opacity: 0.5;
	}
	.auth{
		/* po */
	}
	.auth-img{
		position: fixed;
		top: 23%;
		left:130upx;
		width:500upx;
		z-index: 9;
	}
	.auth-btn{
		position: fixed;
		top: 46%;
		left:130upx;
		width:500upx;
		height:200upx;
		/* background: red; */
		/* border:1px solid red; */
		z-index: 10;
		opacity: 0;
	}
</style>