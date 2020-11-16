<template>
	 <view>
		<view class="page-body">
			<view class="page-section page-section-gap">
				<map style="width: 100%; height: 1300upx" :latitude="latitude" :longitude="longitude" :markers="covers">
				</map>
			</view>
			<!-- <image src="../../static/5e130b55e5331.png"></image> -->
		</view>
	</view>
</template>


<script>
	export default{
		data(){
			return{
				title: 'map',
				latitude: 39.909,
				longitude: 116.39742,
				covers: [{
					latitude:39.909,
					longitude: 116.39742,
					iconPath: '../../static/user-service07.png',
					title:'阿打算',//标注点名
					
					callout:{//自定义标记点上方的气泡窗口 点击有效
						content:'地址',
						color:'#F76350',
						fontSize:12,
						borderRadius:5,
					},
				}],
			}
		},
		onLoad(option) {
			// 117.105442,36.689793
			let self = this;
			self.latitude = option.latitude;
			self.longitude = option.longitude
			
			console.log(self.covers[0].latitude,'顶顶顶顶',option);
			
			self.covers[0].latitude = option.latitude;
			self.covers[0].longitude = option.longitude
			
		},
		onShow() {
		},
		onHide() {
			
		},
		methods:{
			
		}
	}
</script>

<style>
</style>
