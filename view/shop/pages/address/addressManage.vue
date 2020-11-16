<template>
	<view class="content">
		<view class="row b-b">
			<text class="tit">姓名</text>
			<input class="input" type="text" v-model="addressData.real_name" placeholder="收货人姓名" placeholder-class="placeholder" />
		</view>
		<view class="row b-b">
			<text class="tit">手机号</text>
			<input class="input" type="number" v-model="addressData.phone" placeholder="收货人手机号码" placeholder-class="placeholder" />
		</view>
		<view class="row b-b">
			<text class="tit">地址</text>
			<!-- <text @click="chooseLocation" class="input">
				{{addressData.addressName}}
			</text> -->
			<text class="btns" type="primary" @tap="openAddres">{{addressData.province}}-{{addressData.city}}-{{addressData.district}}</text>
			<simple-address ref="simpleAddress" :pickerValueDefault="cityPickerValueDefault" @onConfirm="onConfirm" themeColor="#007AFF"></simple-address>
			<text class="yticon icon-shouhuodizhi"></text>
		</view>
		<view class="row b-b"> 
			<text class="tit">门牌号</text>
			<input class="input" type="text" v-model="addressData.detail" placeholder="楼号、门牌" placeholder-class="placeholder" />
		</view>
		<view class="row default-row">
			<text class="tit">设为默认</text>
			<switch :checked="addressData.is_default" color="#fa436a" @change="switchChange" />
		</view>
		<button class="add-btn" @click="confirm">提交</button>
	</view>
</template>

<script>
	import simpleAddress from '@/components/simple-address/simple-address.vue';
	export default {
		
		data() {
			return {
				addressData: {
					real_name: '',
					phone: '',
					// addressName: '在地图选择',
					province:'省',
					city:'市',
					district:'区',
					// address: '',
					detail: '',
				},
				is_default: 0,
				cityPickerValueDefault: [0, 0, 0],
				pickerText: '省-市-区',
				picker:null,
				adrid:0,
				opType:'',
			}
		},
		components: {
		    simpleAddress
	    },
		onLoad(option){
			console.log(option);
			console.log('optionoptionoptionoptionoptionoption')
			let self = this;
			self.opType = option.type
			getApp().user_api(true);
			let title = '新增收货地址';
			if(option.type==='edit'){
				title = '编辑收货地址'
				self.adrid = option.data
				self.getUserAddress();
			}
			uni.setNavigationBarTitle({
				title
			})
		},
		methods: {
			openAddres() {
				this.$refs.simpleAddress.open();
			},
			onConfirm(e) {
				console.log(e);
				this.pickerText = e.label
				this.addressData.province = e.labelArr[0];
				this.addressData.city = e.labelArr[1];
				this.addressData.district = e.labelArr[2];
				// this.picker = e;
			},
			switchChange(e){
				this.addressData.is_default = 1;
			},
			getUserAddress(){
				let self = this;
				let path = '/gzh/user_api/get_user_address';
				let data ={ addressId :self.adrid}
				
				// self.addressData = self.addressData
				
				console.log(self.addressData);
				console.log('============')
				getApp().http(path, data, function(result) {
					console.log('修改',result);
					self.addressData = result
				});
			},

			
			//提交
			confirm(){
				let self = this;
				let data = self.addressData;
				if(!data.real_name){
					this.$api.msg('请填写收货人姓名');
					return;
				}
				if(!/(^1[3|4|5|7|8][0-9]{9}$)/.test(data.phone)){
					this.$api.msg('请输入正确的手机号码');
					return;
				}
				if(!data.province){
					this.$api.msg('请输入地址');
					return;
				}
				if(!data.detail){
					this.$api.msg('请填写门牌号信息');
					return;
				}
				getApp().showTip();
				// getApp().showTip('ok','success');
				// uni.navigateBack(2)
				console.log('地址',data);
				let path = '/gzh/user_api/edit_user_address'
				let datas = data;
				getApp().http(path, datas, function(result) {
					if(result.code == 200){
						console.log('sdfsdfsdfsdsdf')
						getApp().showTip(result.msg,'success');
						// setTimeout(()=>{
							uni.navigateBack(1500)
						// })
					}else{
						getApp().showTip(result.msg);
					}
				},true,"POST")
				
				
				
				//this.$api.prePage()获取上一页实例，可直接调用上页所有数据和方法，在App.vue定义
				// this.$api.prePage().refreshList(data, this.manageType);
				// this.$api.msg(`地址${this.manageType=='edit' ? '修改': '添加'}成功`);
				// setTimeout(()=>{
				// 	uni.navigateBack()
				// }, 800)
			},
			
			//地图选择地址
			chooseLocation(){
				uni.chooseLocation({
					success: (data)=> {
						this.addressData.addressName = data.name;
						this.addressData.address = data.name;
					}
				})
			},
			
		}
	}
</script>

<style lang="scss">
	@import url("addressManage.css");
	page{
		background: $page-color-base;
		padding-top: 16upx;
	}

	.row{
		display: flex;
		align-items: center;
		position: relative;
		padding:0 30upx;
		height: 110upx;
		background: #fff;
		
		.tit{
			flex-shrink: 0;
			width: 120upx;
			font-size: 30upx;
			color: $font-color-dark;
		}
		.input{
			flex: 1;
			font-size: 30upx;
			color: $font-color-dark;
		}
		.icon-shouhuodizhi{
			font-size: 36upx;
			color: $font-color-light;
		}
	}
	.default-row{
		margin-top: 16upx;
		.tit{
			flex: 1;
		}
		switch{
			transform: translateX(16upx) scale(.9);
		}
	}
	.add-btn{
		display: flex;
		align-items: center;
		justify-content: center;
		width: 690upx;
		height: 80upx;
		margin: 60upx auto;
		font-size: $font-lg;
		color: #fff;
		background-color: $base-color;
		border-radius: 10upx;
		box-shadow: 1px 2px 5px rgba(219, 63, 96, 0.4);
	}
</style>
