<template>
	<view class="container">
		<view class="con-hed">
			<view class="con-hede">
				<image class="con-img" src="../../static/vip-card-bg.png"></image>
				<view class="con-car aces-space-between">
					<view class="hed-zong con-color">总资产(元)</view>
					<view class="hed-chong" @click="navTo('/pages/user/user_money_recharge')">充值</view>
				</view>
				<view class="con-num con-col acea-row" > 
					<view class="con-num1">
						{{userInfo.now_money || 0}}
					</view>
				</view>
				<view class="hed-l acea-row">
					<view class="hed-lmon">
						<view class="con-color hed-leij">累计充值(元)</view>
						<view class="con-col hed-num">{{userInfo.recharge || 0}}</view>
					</view>
					<view class="hed-lmon ">
						<view class="con-color hed-leij">累计消费(元)</view>
						<view class="con-col hed-num">{{userInfo.orderStatusSum || 0}}</view>
					</view>
				</view>
			</view>
			<view class="acea-row con-list">
				<view class="con-list-zd" @click="navTo('/pages/user/user_bill?type=0')">
					<view class="yticon icon-daifukuan con-list-icon" ></view>
					<view class="con-list-tit">账单记录</view>
				</view>
				<view class="con-list-zd" @click="navTo('/pages/user/user_bill?type=1')">
					<view class="yticon icon-tuijian con-list-icon"></view>
					<view class="con-list-tit">消费记录</view>
				</view>
				<view class="con-list-zd" @click="navTo('/pages/user/user_bill?type=2')">
					<view class="yticon icon-shouhoutuikuan con-list-icon"></view>
					<view class="con-list-tit">充值记录</view>
				</view>
				<view class="con-list-zd" @click="navTo('/pages/user/user_integral')">
					<view class="yticon icon-iLinkapp- con-list-icon"></view>
					<view class="con-list-tit">积分中心</view>
				</view>
			</view>
		</view>
		<view class="aces-space-between con-jf">
			<view class="aces-space-between con-jf-qd con-jf-bg" @click="navTo('/pages/user/user_sgin')">
				<view class="">
					<view class="con-jf-tit">签到领积分</view>
					<view class="con-jf-det">赚积分抵现金</view>
				</view>
				<view>
					<image class="con-jf-img" src='../../static/gift.png'></image>
				</view>
			</view>
			<view class="aces-space-between con-jf-qd con-quan-bg" @click="navTo('/pages/user/coupon')">
				<view class="">
					<view class="con-jf-tit">领取优惠券</view>
					<view class="con-jf-det">满减享优惠</view>
				</view>
				<view>
					<image class="con-jf-img" src='../../static/money.png'></image>
				</view>
			</view>
		</view>
		<!-- <view class=" con-jf">
			<view class="acea-row con-ptlist">
				<view>
					<image class="con-jf-img" src="../../static/gift.png"></image>
				</view>
				<view class="con-pt">
					<view class="con-pt-tit">最新拼团活动</view>
					<view class="con-pt-det">最新的优惠商品上架拼团</view>
				</view>
				<view class="con-btn">
					已结束
				</view>
			</view>
			<view class="acea-row con-ptlist">
				<view>
					<image class="con-jf-img" src="../../static/gift.png"></image>
				</view>
				<view class="con-pt">
					<view class="con-pt-tit">当前限时秒杀</view>
					<view class="con-pt-det">最新商品秒杀进行中</view>
				</view>
				<view class="con-btn">
					已结束
				</view>
			</view>
			<view class="acea-row con-ptlist">
				<view>
					<image class="con-jf-img" src="../../static/gift.png"></image>
				</view>
				<view class="con-pt">
					<view class="con-pt-tit">砍价活动</view>
					<view class="con-pt-det">呼朋友来砍价</view>
				</view>
				<view class="con-btn">
					已结束
				</view>
			</view>
			 -->
		</view>
		
	</view>
</template>

<script>
	import uniListItem from '@/components/uni-list-item/uni-list-item';
	export default{
		components: {
			uniListItem
		},
		data(){
			return{
				userInfo:[],
			}
		},
		onLoad() {
			getApp().user_api(true);
		},
		onShow() {
			this.money();
		},
		onHide() {
			
		},
		methods:{
			money(){
				let self = this;
				let path = '/gzh/user_api/my'
				getApp().http(path, {}, function(res) {
					console.log('个人信息',res)
					self.userInfo = res;
				})
				console.log('是大风胜多负少的',self.userInfo)
			},
			navTo(e){
				uni.navigateTo({
					url: e,
				})
			},
		}
	}
</script>

<style>
	@import url("user_money.css");
</style>