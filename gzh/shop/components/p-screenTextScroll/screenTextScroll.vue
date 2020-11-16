<!-- 
展示屏幕文字滚动效果
 
使用方法：

 -->
<!-- 
展示屏幕文字滚动效果

usage:
 list:数组数据
 @getCurrentIndex  获取当前swiper的下标
 <textscroll :list="list" @getCurrentIndex="getIndex"/>

 -->

<template>
	<view class="_notice">
		<swiper class="_swiper tc" @change="slideChange" indicator-dots="false" autoplay="true" :interval="interval" circular="true"
		 display-multiple-items="1" :duration="duration">
			
			<swiper-item>
				<view class="swiper-item uni-bg-red"></view>
			</swiper-item>
			<swiper-item v-for="(item,index) in list" :key="index">
				<view class="swiper-item uni-bg-red">{{item}}</view>
			</swiper-item>
		</swiper>
	</view>
</template>

<script>
	export default {
		props: {
			//显示数据
			list: {
				type: Array,
				defual:function(){
					return [];
				}
			},
		},
		data() {
			return {
				interval:3000,
				duration:25000,
			};
		},
		methods:{
			slideChange(e){
				this.$emit('getCurrentIndex',e.detail.current); 
				
			}
		}

	};
</script>

<style lang="scss">
	
	._notice {
		// position: fixed;
		// left: 0;
		// top: 0;
		/* #ifdef H5 */
		// top: 88upx;
		/* #endif */
		// background: rgba(72, 1, 1, 0.3);
		// border-radius: 6upx;
		z-index: 9;
		// width: 100%;
		font-size: 25rpx;
		height: 62rpx;
		color: #000;
		overflow: hidden;
		box-sizing: border-box;
		// border:1px solid red;
		margin-left:100rpx;
	}

	._swiper {
		line-height: 62rpx;
	}
</style>
