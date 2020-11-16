<template>
	<view class="container">
		<view v-if="applyStatus == 1">
			<view class="yitg clo-ytg">审核已通过</view>
		</view>
		<view v-else-if="applyStatus == 4 ">
			<view class="yitg clo-wtg">审核未通过</view>
			<view class="kef">客服联系电话:</view>
		</view>
		<view v-else-if="applyStatus == 3 ">
			<view class="yitg">审核中</view>
		</view>
		<view v-else-if="applyStatus == 0 ">
			<form @submit="formSubmit" @reset="formReset">
			<view class="pad">
				<view class="pad-inp">
					<view>商家名称：</view>
					<view> 
						<input class="uni-input s-inp"  name="mer_name" placeholder="请为您的店铺起一个闪亮的名字" />
					</view>
				</view>
				<view class="pad-inp">
					<view>姓名：</view>
					<view> 
						<input class="uni-input s-inp"  name="real_name" placeholder="请输入商家姓名" />
					</view>
				</view>
				<view class="pad-inp">
					<view>手机号：</view>
					<view> 
						<input class="uni-input s-inp" type="number" @input="phoneCodeInput" name="phone" placeholder="请输入手机号" />
					</view>
				</view>
				<view class="pad-inp aces-space-between">
					<view> 
						<input class="uni-input s-inp" type="number" name="verify" placeholder="请输入验证码" />
					</view>
					<view class="inp-yz" @click="send">{{getText}}</view>
				</view>
				
				<view class="pad-inp s-rei">
					<checkbox-group @change="checkboxChange">
						<label>
							<checkbox value="cb" color="#FA436A" style="transform:scale(0.7)"/> 阅读并同意 <text @click="allClass" class="s-xy">《商家入驻协议》</text> 
						</label>
					</checkbox-group>
				</view>
				<button form-type="submit" class="sub-btn">提交申请</button>
			</view>
			</form>
		</view>
		
		
		<view class="modal" v-if="allhidden">
			<view class="modal-box"  @click="hideAllClass" ></view>
			<view class="modal-body " >
				<view class=" mod-pad">
					<view class="mod-xy">商家入住协议</view>
					
					<scroll-view scroll-with-animation scroll-y  class="right-aside mod-scr"  >
						<view class="aces-space-between mod-ace">
							<view class="mod-d"></view>
							<view class="mod-det">本服务协议双方为本网站与本网站客户，本服务协议具有合同效力。您确认本服务协议后，本服务协议即在您和本网站之间产生法律效</view>
						</view>
						<view class="aces-space-between mod-ace">
							<view class="mod-d"></view>
							<view class="mod-det">本服务协议双方为本网站与本网站客户，本服务协议具有合同效力。您确认本服务协议后，本服务协议即在您和本网站之间产生法律效</view>
						</view>
						<view class="aces-space-between mod-ace">
							<view class="mod-d"></view>
							<view class="mod-det">本服务协议双方为本网站与本网站客户，本服务协议具有合同效力。您确认本服务协议后，本服务协议即在您和本网站之间产生法律效</view>
						</view>
					</scroll-view>
				</view>
			</view>
		</view>

		
	</view>
</template>

<script>
	export default{
		data(){
			return{
				check:false,
				isSend:false,
				getText: '获取验证码',
				phoneTime: 0,
				phoneCodeText:'',
				applyStatus:0,
				allhidden:false,
			}
		},
		onLoad() {
			this.apply_status();
		},
		onShow() {
		},
		onHide() {
			
		},
		methods:{
			apply_status(){
				let self = this;
				let path = "/gzh/merchant_api/apply_status";
				getApp().http(path, {}, function(res) {
					self.applyStatus = res
					console.log(self.applyStatus,'水电费水电费')
				});
			},
			
			
			allClass(){
				var that = this;
				that.allhidden = true
			},
			hideAllClass(){
				console.log('隐藏隐藏隐藏隐藏')
				var that = this;
				console.log(this)
				that.allhidden = false
			},
			
			
			phoneCodeInput(event) {
				this.phoneCodeText = event.target.value;
				console.log(this.phoneCodeText);
			},
			send(){
				let self = this;
				if (self.phoneTime > 0) {
					return;
				}
				if (self.phoneCodeText == '') {
					return;
				}
				self.phoneTime = 60;
				var phoneInterval = setInterval(function() {
					if (self.phoneTime > 0) {
						self.phoneTime -= 1;
						self.getText = self.phoneTime + "s后重新获取"
					} else {
						self.phoneTime = 0;
						clearInterval(phoneInterval);
						self.getText = "获取验证码";
					}
				}, 1000)
				
				let path = "/gzh/merchant_api/send";
				let data = {
					phone: self.phoneCodeText,
				};
				getApp().http(path, data, function(result) {
					let code = result.code;
					console.log("获取手机验证码", result)
					if (code == 400) {
						self.phoneTime = 0;
						getApp().showTip(result.msg, "none")
					} else {
						getApp().showTip(result.msg, "none")
					}
				
				}, true, "POST")
				
			},
			checkboxChange(e){
				let self = this;
				self.check = !self.check
				console.log(self.check)
			},
			formSubmit(e) {
				let self = this;
				let val = e.detail.value;
				console.log(val,self.check);
				if(!val.mer_name){
					self.$api.msg('请填写商铺名称');
					return;
				}
				if(!val.real_name){
					self.$api.msg('请填写商家姓名');
					return;
				}
				if(!/(^1[3|4|5|7|8][0-9]{9}$)/.test(val.phone)){
					self.$api.msg('请输入正确的手机号码');
					return;
				}
				if(!val.verify){
					self.$api.msg('请填写手机验证码');
					return;
				}
				if(!self.check){
					self.$api.msg('请填勾选商家入驻协议');
					return;
				}
				getApp().showTip();
				
				let path = "/gzh/merchant_api/apply_merchant";
				let data = {
					mer_name: val.mer_name,
					real_name: val.real_name,
					phone: val.phone,
					verify: val.verify,
				};
				getApp().http(path, data, function(res) {
					console.log(res,'水电费地方');
					if (res.code == 400) {
					// 	self.phoneTime = 0;
						getApp().showTip(res.msg, "none")
					} else {
						self.apply_status();
						getApp().showTip(res.msg, "none")
					}
				},true,'post')
				
			},
		}
	}
</script>

<style lang='scss'>
	@import url("apply.css");
	.inp-yz{
		/* mae */
		width:300upx;
		background:#F3F3F3;
		color:$base-color;
		height:70upx;
		text-align: center;
		line-height: 70upx;
		margin-top:15upx;
	}
	.s-xy{
		color:$base-color;
	}
	.sub-btn{
		border-radius: 50upx;
		background:$base-color;
		color:#fff;
	}
	.clo-ytg{
		color:$base-color;
	}
</style>
