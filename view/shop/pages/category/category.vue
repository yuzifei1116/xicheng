<template>
	<view class="content">
		<scroll-view scroll-y class="left-aside">
			<view v-for="item in getData" :key="item.id" class="f-item b-b" :class="{active: item.id === currentId}" @click="tabtap(item)">
				{{item.cate_name}}
			</view>
		</scroll-view>
		<scroll-view scroll-with-animation scroll-y class="right-aside"  :style="{height:scrollHeight+'px'}" @scroll="asideScroll" :scroll-top="tabScrollTop">
			<view  class="s-list acea-row" >
				<view class="list-img" v-if="itemPic">
					<image class="item-img" :src="indexUrl(itemPic)"></image>
				</view>
				<view class="t-list" v-for="item in slist" :key="item.id">
					<view class="t-item"  @click="navToList(item)">
						<image :src="indexUrl(item.pic) "></image>
						<text>{{item.cate_name}}</text>
					</view>
				</view>
			</view>
		</scroll-view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				sizeCalcState: false,
				tabScrollTop: 0,
				currentId: 0,
				flist: [],
				slist: [],
				tlist: [],
				itemPic:'/public/uploads/attach/2020/01/06/5e130e7217a21.jpg',
				getData:[
					{
					},
				],
				scrollHeight: 100,
			}
		},
		onLoad(){
			// getApp().user_api(true);
			this.getCategoryOneTwo()
			getApp().user_api(true);
		},
		onReady() {
			
		},
		methods: {
			getCategoryOneTwo(){
				this.calcSize();
				let self = this;
				self.slist = self.getData[0].child;
				self.currentId = self.getData[0].id
				self.itemPic = self.getData[0].pic;
				// console.log(self.getData[0]);
				// console.log('==========')
				let path = '/gzh/store_api/get_category_one_two'
				getApp().http(path, {}, function(result) {
					self.getData = result;
					self.slist = result[0].child;
					self.currentId = result[0].id
					self.itemPic = result[0].pic;
					
					console.log(self.currentId);
					console.log('==========')
					
				});
			},
			//一级分类点击
			tabtap(item){
				this.currentId = item.id;
				this.slist = item.child;
				this.itemPic = item.pic;
				console.log('aaaaaaaa1',this.itemPic);
			},
			
			indexUrl(icon){
				let http = icon.indexOf('http')
				console.log('图谱爱',http)
				if(http == -1){
					return this.webUrl+icon
				}else{
					return icon;
				}
				
			},

			//右侧栏滚动
			asideScroll(e){
				let scrollTop = e.detail.scrollTop;
				let tabs = this.slist.filter(item=>item.top <= scrollTop).reverse();
				
			},
			//计算右侧栏每个tab的高度等信息
			calcSize(){
				var self = this;
				uni.getSystemInfo({
					success: function(res) { // res - 各种参数
						let info = uni.createSelectorQuery().select(".content");
						self.scrollHeight = res.windowHeight
						console.log(info);
						console.log('战速',res.windowHeight);
						console.log('啊啊啊啊',res);
					}
				});
				// this.sizeCalcState = true;
			},
			navToList(item){
				
				console.log('跳转',item);
				
				
				uni.navigateTo({
					url: '/pages/product/list?sid='+item.id
				})
			}
		}
	}
</script>

<style lang='scss'>
	@import url("category.css");
	
	page,
	.content {
		height: 100%;
		background-color: #f8f8f8;
	}

	.content {
		display: flex;
	}
	.left-aside {
		flex-shrink: 0;
		width: 200upx;
		height: 100%;
		background-color: #fff;
	}
	.f-item {
		display: flex;
		align-items: center;
		justify-content: center;
		width: 100%;
		height: 100upx;
		font-size: 28upx;
		color: $font-color-base;
		position: relative;
		&.active{
			color: $base-color;
			background: #f8f8f8;
			&:before{
				content: '';
				position: absolute;
				left: 0;
				top: 50%;
				transform: translateY(-50%);
				height: 36upx;
				width: 8upx;
				background-color: $base-color;
				border-radius: 0 4px 4px 0;
				opacity: .8;
			}
		}
	}

	.right-aside{
		/* border:1px solid red; */
		flex: 1;
		overflow: hidden;
		padding-left: 20upx;
	}
	.s-item{
		display: flex;
		align-items: center;
		height: 70upx;
		padding-top: 8upx;
		font-size: 28upx;
		/* border:1px solid red; */
		color: $font-color-dark;
	}
	.t-list{
		/* display: flex;
		flex-wrap: wrap; */
		width: 170upx;
		/* width:200upx; */
		background: #fff;
		padding-top: 12upx;
		/* border:1px solid red; */
		&:after{
			content: '';
			flex: 99;
			height: 0;
		}
	}
	.t-item{
		flex-shrink: 0;
		display: flex;
		justify-content: center;
		align-items: center;
		flex-direction: column;
		width: 176upx;
		font-size: 26upx;
		color: #666;
		padding-bottom: 20upx;
		
		image{
			width: 130upx;
			height: 130upx;
			border-radius: 65upx;
			margin-bottom:5upx;
		}
	}
</style>
