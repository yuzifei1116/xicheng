<template>
	<view class="contents">
		<scroll-view scroll-with-animation scroll-y  class="right-aside" :style="{height:scrollHeight+'px'}" @scrolltolower="scroAddr">
			<view v-for="(item,index) in noticeInfo">
				<view class="notice-item" @click="prize(item)">
					<text class="time"></text>
					<view class="content">
						<text class="title">{{item.title}}</text>
						<view class="img-wrapper">
							<image class="pic" :src="indexUrl(item.image_input)"></image>
						</view>
						<text class="introduce">
							{{item.synopsis}}
						</text>
						<view class="bot b-t">
							<text>查看详情</text>
							<text class="more-icon yticon icon-you"></text>
						</view>
					</view>
				</view>
			</view>
			<uni-load-more :status="status" :icon-size="16" :content-text="contentText" />
		</scroll-view>
		<!-- <view class="notice-item">
			<text class="time">昨天 12:30</text>
			<view class="content">
				<text class="title">新品上市，全场满199减50</text>
				<view class="img-wrapper">
					<image class="pic" src="https://ss1.bdstatic.com/70cFvXSh_Q1YnxGkpoWK1HF6hhy/it/u=3761064275,227090144&fm=26&gp=0.jpg"></image>
					<view class="cover">
						活动结束
					</view>
				</view>
				<view class="bot b-t">
					<text>查看详情</text>
					<text class="more-icon yticon icon-you"></text>
				</view>
			</view>
		</view> -->
	</view>
</template>

<script>
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
	
	export default {
		components: {
			uniLoadMore
		},
		data() {
			return {
				noticeInfo:[],
				source: 0,
				addressList: [],
				scrollHeight: 100,
				tabScrollTop: 0,
				contentText: {
					contentdown: '上拉加载更多',
					contentrefresh: '加载中',
					contentnomore: '我是有底线的'
				},
				page:1,
				limit:20,
				status: 'more',
			}
		},
		onLoad() {
			getApp().user_api(true);
			this.noticeIist();
			this.calcSize();
		},
		methods: {
			noticeIist(){
				let self = this;
				let path = '/gzh/article_api/get_cid_article'
				let data = {page:self.page,limit:self.limit};
				getApp().http(path, data, function(res) {
					console.log('个人信息',res)
					
					let list = res;
					if (list.length > 0) {
						console.log(list.length)
						
						self.noticeInfo = self.noticeInfo.concat(list);
						self.status = list.length >= self.limit ? "more" : "";
						if (self.status == "more") {
							self.page += 1;
						}
						
						// self.status = list.length <= 20 ? "more" : "";
						// if (self.status == "more") {
						// 	self.page += 1;
						// }
						// self.noticeInfo = self.noticeInfo.concat(list);
					} else {
						self.status = "";
					}
					// self.noticeInfo = res;
				})
			},
			prize(item){
				console.log('水电费水电费的',item);
				
				uni.navigateTo({
					url:'/pages/article/article_detail?id='+item.id
				})  
			},
			indexUrl(icon){
				let http = icon.indexOf('http')
				if(http == -1){
					return this.webUrl +'/'+icon
				}else{
					return icon;
				}
			},
			
			// 到底触发
			scroAddr(){
				let self = this;
				if(self.status){
					self.noticeIist();
				}
			},
			//计算右侧栏每个tab的高度等信息
			calcSize(){
				var self = this;
				uni.getSystemInfo({
					success: function(res) { // res - 各种参数
						let info = uni.createSelectorQuery().select(".contents");
						self.scrollHeight = res.windowHeight
						// self.scrollHeight = 200
						console.log(info);
						console.log('啊啊啊啊',res,self.scrollHeight);
					}
				});
			},

		}
	}
</script>

<style lang='scss'>
	page {
		background-color: #f7f7f7;
		padding-bottom: 30upx;
		/* height:100%; */
		/* border:1px solid red; */
	}
	page .contents{
		/* height:100%; */
		/* border:1px solid red; */
	}
	.notice-item {
		display: flex;
		flex-direction: column;
		align-items: center;
	}

	.time {
		display: flex;
		align-items: center;
		justify-content: center;
		height: 80upx;
		padding-top: 10upx;
		font-size: 26upx;
		color: #7d7d7d;
	}

	.content {
		width: 710upx;
		/* border:1px solid red; */
		padding: 0 24upx;
		background-color: #fff;
		border-radius: 4upx;
	}

	.title {
		display: flex;
		align-items: center;
		height: 90upx;
		font-size: 32upx;
		color: #303133;
	}

	.img-wrapper {
		width: 100%;
		/* height: 560upx; */
		/* height:100%; */
		position: relative;
	}

	.pic {
		display: block;
		width: 100%;
		/* height: 100%; */
		border-radius: 6upx;
	}

	.cover {
		display: flex;
		justify-content: center;
		align-items: center;
		position: absolute;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;
		background-color: rgba(0, 0, 0, .5);
		font-size: 36upx;
		color: #fff;
	}

	.introduce {
		display: inline-block;
		padding: 16upx 0;
		font-size: 28upx;
		color: #606266;
		line-height: 38upx;
	}

	.bot {
		display: flex;
		align-items: center;
		justify-content: space-between;
		height: 80upx;
		font-size: 24upx;
		color: #707070;
		position: relative;
	}

	.more-icon {
		font-size: 32upx;
	}
</style>
