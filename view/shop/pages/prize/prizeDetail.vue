<template>
	<view class="content">
		<form @submit="formSubmit" >
		<view class="hed acea-row">
			<view>
				<image class="con-img" :src="my.avatar"></image>
			</view>
			<view class="hed-t">
				<view class="hed-wx">{{my.username}}</view>
				<view>
					<view>{{my.realname}}</view>
					<view>{{my.tel}}</view>
					<view>{{my.addr}}</view>
				</view>
			</view>
		</view>
		<view class="prizt">
			<view class="prize-tit">我的奖品</view>
			<view class="prize-det">
				<image class="prize-img" :src="indexUrl(my.prize_image)"></image>
				<view class="prize-det-tit">{{my.prize_name}}</view>
			</view>
		</view>
		<view class="prizt"  v-if="!my.addr">
			<view class="prize-tit">我的地址</view>
			<view class="peizt-d">
				<view class="row b-b">
					<text class="tit">姓名</text>
					<input class="input" type="text" name="name" placeholder="收货人姓名" placeholder-class="placeholder" />
				</view>
				<view class="row b-b">
					<text class="tit">手机号</text>
					<input class="input" type="number" name="tel" placeholder="收货人手机号码" placeholder-class="placeholder" />
				</view>
				<view class="row b-b">
					<text class="tit">地址</text>
					<text class="btns" type="primary" @tap="openAddres">{{pickerText}}</text>
					<simple-address ref="simpleAddress" :pickerValueDefault="cityPickerValueDefault" @onConfirm="onConfirm" themeColor="#007AFF"></simple-address>
				</view>
				<view class="row b-b"> 
					<text class="tit">门牌号</text>
					<input class="input" type="text" name="det"  placeholder="楼号、门牌" placeholder-class="placeholder" />
				</view>
			</view>
		</view>
		<view class="hei"></view>
		
		<view class="feed">
			<!-- <view @click="confirm">领取</view> -->
			<text v-if="!my.addr">
				<button form-type="submit">完善信息</button>
			</text>
			<text v-else>
				<button>已完善</button>
			</text>
		</view>
		</form>
	</view>
</template>

<script>
	import simpleAddress from '@/components/simple-address/simple-address.vue';
	export default {
		components: {
		    simpleAddress
		},
		data() {
			return {
				cityPickerValueDefault: [0, 0, 0],
				pickerText: '请选择',
				id:0,
				my:[],
			};
		},
		onLoad(options){
			this.id = options.id
			this.myPrizeDetail();
		},
		onShow(){
		},
		onPageScroll(e){
			//兼容iOS端下拉时顶部漂移
			if(e.scrollTop>=0){
				this.headerPosition = "fixed";
			}else{
				this.headerPosition = "absolute";
			}
		},
		//下拉刷新
		onPullDownRefresh() {
		},
		methods: {
			
			myPrizeDetail(){
				let self = this;
				var path = '/gzh/activity_api/my_prize_detail'
				var data = {id:self.id};
				getApp().http(path, data, function(res) {
					self.my = res;
					console.log('向平',res)
				});
			},
			indexUrl(icon){
				let http = icon.indexOf('http')
				if(http == -1){
					return this.webUrl +'/'+icon
				}else{
					return icon;
				}
			},
			
			openAddres() {
				this.$refs.simpleAddress.open();
			},
			onConfirm(e) {
				console.log(e);
				this.pickerText = e.label
			},
			
			formSubmit(e){
				console.log('dsdf ',e)
				let self = this;
				let data = e.detail.value;
				if(!data.name){
					this.$api.msg('请填写收货人姓名');
					return;
				}
				if(!/(^1[3|4|5|7|8][0-9]{9}$)/.test(data.tel)){
					this.$api.msg('请输入正确的手机号码');
					return;
				}
				if(!self.pickerText){
					this.$api.msg('请输入地址');
					return;
				}
				if(!data.det){
					this.$api.msg('请填写门牌号信息');
					return;
				}
				// getApp().showTip('none');
				
				
				uni.showModal({
					title: '完善信息',
					content: '确定后信息无法修改，请确定信息填写是否正确',
					success: (md) => {
						console.log('sssss')
						if(md.confirm == true){
							console.log('确定')
							
							let path = '/gzh/activity_api/up_prize_info';
							let datas ={
								id:self.id,
								realname:data.name,
								tel:data.tel,
								addr:self.pickerText
							}
							getApp().http(path, datas, function(res) {
								console.log('修改',res);
								getApp().showTip('完善信息成功','none');
								self.myPrizeDetail();
							},false,'post');
							
						}
					}
				})
				
				
				
				
				
			}
			
		},
	}
</script>

<style lang="scss">
	@import url("prizeDetail.css");
	page, .content{
		background: $page-color-base;
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
	
	/* 底部 */
	.feed{
		background:$base-color;
		color: #fff;
		position: fixed;
		width: 100%;
		height: 90upx;
		line-height: 90upx;
		text-align: center;
		bottom:0;
		z-index: 10;
	}
	uni-button{
		background:$base-color;
		border:0;
		color: #fff;
		padding: 0upx;
		height: 90upx;
		margin:0upx;
	}
	
</style>
